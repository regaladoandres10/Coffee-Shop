<?php

require_once __DIR__ . '/../models/Order/OrderModel.php';
require_once __DIR__ . '/../../includes/classes/AuthManager.php'; 
require_once __DIR__ . '/../../includes/classes/Response.php'; 
require_once __DIR__ . '/../../app/models/Product/ProductModel.php';

class OrderController {
    private $orderModel;
    private $productModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->productModel = new ProductModel();
    }

    /**
     * Genera un número de orden simple de 5 caracteres (ej. A0001).
     * NOTA: En producción, esto debe ser una secuencia única y robusta.
     */
    private function generateOrderNumber() {
        return strtoupper(substr(md5(uniqid(rand(), true)), 0, 5));
    }

    /**
     * Obtiene una lista de órdenes (para Admin/Staff, solo propias para Cliente).
     */
    public function getOrders($id = null) {
        //VERIFICAR AUTENTICACIÓN
        if (!AuthManager::isLoggedIn()) {
            Response::sendJson(false, 'Debe iniciar sesión para ver órdenes.', [], 401);
            return;
        }

        $userId = AuthManager::getUserId();
        $userRole = AuthManager::getUserRole();
        
        $orders = [];

        if ($id !== null) {
            //Petición de un solo detalle: GET /api.php?resource=orders&id=123
            $id = filter_var($id, FILTER_VALIDATE_INT);
            if ($id === false) {
                 Response::sendJson(false, 'ID de orden no válido.', [], 400);
                 return;
            }
            $orders = $this->orderModel->getOrderDetails($id);
            
            //Seguridad: Si es cliente, solo puede ver sus propias órdenes.
            if ($userRole === 'Cliente' && $orders && $orders['userId'] !== $userId) {
                Response::sendJson(false, 'No tiene permiso para ver esta orden.', [], 403);
                return;
            }
            
            //Retorna un solo objeto, no un array
            Response::sendJson(true, 'Detalles de la orden obtenidos.', $orders);
            return;

        } else {
            // Petición de lista: GET /api.php?resource=orders
            
            //Si es Admin/Staff, obtiene todas las órdenes.
            if (AuthManager::hasPermission('orders_view_all')) { // Necesitas este permiso
                 $orders = $this->orderModel->getAllOrders(null); // Necesitas crear este método en el modelo
            } else {
                 //Si es Cliente, solo obtiene sus propias órdenes.
                 $orders = $this->orderModel->getAllOrders($userId); // Necesitas crear este método en el modelo
            }
            
            Response::sendJson(true, 'Lista de órdenes obtenida.', $orders);
        }
    }

    /**
     * Procesa la creación de una nueva orden.
     */
    public function createOrder() {
        //VERIFICAR AUTENTICACIÓN
        if (!AuthManager::isLoggedIn()) {
            Response::sendJson(false, 'Debe iniciar sesión para realizar una orden.', [], 401);
            return;
        }
        
        $data_raw = $this->getJsonInput();

        //SANITIZACIÓN Y OBTENCIÓN DE DATOS
        $userId = AuthManager::getUserId();
        
        //Sanear datos de la cabecera
        $totalAmountClient = filter_var($data_raw['totalAmount'] ?? 0.0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $orderType = filter_var($data_raw['orderType'] ?? 'takeaway', FILTER_SANITIZE_STRING);
        $customerNotes = filter_var($data_raw['customerNotes'] ?? '', FILTER_SANITIZE_STRING);
        $orderItemsRaw = $data_raw['orderItems'] ?? [];
        
        //VALIDACIÓN CRÍTICA DE PRECIOS Y CÁLCULO DE TOTAL (Backend)
        $calculatedTotal = 0.0;
        $orderItemsValidated = [];

        foreach ($orderItemsRaw as $item) {
            //Aseguramos que los IDs y cantidades sean enteros y los precios flotantes
            $productId = filter_var($item['productId'] ?? 0, FILTER_VALIDATE_INT);
            $quantity = filter_var($item['quantity'] ?? 0, FILTER_VALIDATE_INT);

            //Saltar ítems inválidos
            if ($productId <= 0 || $quantity <= 0) continue; 
            
            //Verificación de Precio contra la DB
            $productDB = $this->productModel->getProductById($productId);
            if (!$productDB) continue; 

            $unitPrice = (float)$productDB['price']; 
            
            $calculatedTotal += $unitPrice * $quantity;

            $orderItemsValidated[] = [
                'productId' => $productId,
                'quantity' => $quantity,
                'unitPrice' => $unitPrice, //Precio real de venta
                'specialInstructions' => filter_var($item['specialInstructions'] ?? '', FILTER_SANITIZE_STRING)
            ];
        }

        //VALIDACIÓN FINAL
        if (empty($orderItemsValidated)) {
            Response::sendJson(false, 'La orden no contiene productos válidos.', [], 400);
            return;
        }

        $orderData = [
            'userId' => $userId,
            'orderNumber' => $this->generateOrderNumber(),
            'orderType' => $orderType,
            'totalAmount' => $calculatedTotal,
            'customerNotes' => $customerNotes,
            'status' => 'Pendiente' 
        ];

        //LLAMADA AL MODELO con Transacción
        $newOrderId = $this->orderModel->createOrder($orderData, $orderItemsValidated);

        if ($newOrderId) {
            Response::sendJson(true, 'Orden creada exitosamente.', ['id' => $newOrderId, 'orderNumber' => $orderData['orderNumber']], 201);
        } else {
            Response::sendJson(false, 'Fallo al procesar la orden. Intente de nuevo.', [], 500);
        }
    }
    
    /**
     *Permite a Staff o Admin actualizar el estado de una orden (Pendiente -> Preparando -> Entregada).
     */
    public function updateOrderStatus($orderId) {
        //VERIFICAR PERMISO (Solo Staff y Admin pueden hacer esto)
        if (!AuthManager::hasPermission('orders_status_update')) {
            Response::sendJson(false, 'Acceso denegado. No tiene permiso para actualizar estados.', [], 403);
            return;
        }

        $data_raw = $this->getJsonInput();
        
        $id = filter_var($orderId, FILTER_VALIDATE_INT);
        $newStatus = filter_var($data_raw['status'] ?? '', FILTER_SANITIZE_STRING);

        if ($id === false || empty($newStatus)) {
            Response::sendJson(false, 'ID de orden o estado no válido.', [], 400);
            return;
        }
        
        //VALIDAR EL NUEVO ESTADO (Debe ser un valor permitido por el ENUM de la DB)
        $allowedStatuses = ['Pendiente', 'Preparando', 'Entregada', 'Cancelada'];
        if (!in_array($newStatus, $allowedStatuses)) {
            Response::sendJson(false, 'Estado de orden no permitido.', [], 400);
            return;
        }

        //LLAMADA AL MODELO
        $updated = $this->orderModel->updateOrderStatus($id, $newStatus);

        if ($updated) {
            Response::sendJson(true, 'Estado de la orden actualizado a ' . $newStatus);
        } else {
            Response::sendJson(false, 'No se pudo actualizar el estado de la orden.', [], 500);
        }
    }

    private function getJsonInput() {
        $json_data = file_get_contents('php://input');
        return json_decode($json_data, true) ?? [];
    }
}