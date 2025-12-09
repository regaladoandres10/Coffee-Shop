<?php

require_once __DIR__ . '/../../../includes/classes/classConnection.php';

class OrderModel
{
    private $connection;

    public function __construct()
    {
        $db = new ConnectionMySQL();
        $this->connection = $db->CreateConnection();
    }
    

    //---CREACIÓN DE ORDEN(TRANSACCIÓN)---

    /**
     * Crea una nueva orden y sus ítems asociados en una transacción.
     * @param array $orderData Datos de la cabecera: userId, totalAmount, orderNumber, orderType, customerNotes, status.
     * @param array $orderItems Array de ítems [{productId, quantity, unitPrice, specialInstructions}, ...].
     * @return int|bool El ID de la nueva orden o false si la transacción falla.
     */
    public function createOrder($orderData, $orderItems)
    {

        //INICIAR TRANSACCIÓN
        $this->connection->begin_transaction();

        try {
            //Insertar la Cabecera de la Orden (orders) ---
            $sqlOrder = "INSERT INTO orders (userId, orderNumber, orderType, status, totalAmount, customerNotes, createdAt) 
                         VALUES (?, ?, ?, ?, ?, ?, NOW())";

            $stmtOrder = $this->connection->prepare($sqlOrder);
            if (!$stmtOrder) throw new Exception("Error al preparar cabecera: " . $this->connection->error);

            // userId(i), orderNumber(s), orderType(s), totalAmount(d), customerNotes(s), status(s)
            $stmtOrder->bind_param(
                'isssds',
                $orderData['userId'],
                $orderData['orderNumber'],
                $orderData['orderType'],
                $orderData['status'],
                $orderData['totalAmount'],
                $orderData['customerNotes'],
            );
            $stmtOrder->execute();
            $orderId = $stmtOrder->insert_id;
            $stmtOrder->close();

            if (!$orderId) throw new Exception("No se pudo obtener el ID de la orden.");


            //Insertar los Ítems de la Orden (order_items) ---
            $sqlItem = "INSERT INTO order_items (orderId, productId, quantity, unitPrice, specialInstructions) 
                          VALUES (?, ?, ?, ?, ?)";

            $stmtItem = $this->connection->prepare($sqlItem);
            if (!$stmtItem) throw new Exception("Error al preparar ítems: " . $this->connection->error);

            foreach ($orderItems as $item) {
                //orderId(i), productId(i), quantity(i), unitPrice(d), specialInstructions(s)
                $stmtItem->bind_param(
                    'iiids',
                    $orderId,
                    $item['productId'],
                    $item['quantity'],
                    $item['unitPrice'],
                    $item['specialInstructions']
                );

                if (!$stmtItem->execute()) {
                    throw new Exception("Error al insertar ítem del producto ID " . $item['productId']);
                }
            }
            $stmtItem->close();

            //CONFIRMAR TRANSACCIÓN
            $this->connection->commit();
            return $orderId;
        } catch (Exception $e) {
            //ANULAR TRANSACCIÓN
            $this->connection->rollback();
            error_log("Fallo en la transacción de la orden: " . $e->getMessage());
            return false;
        }
    }
    
    //---LECTURA DE ÓRDENES(Para el dashboard de Staff)---

    /**
     * Obtiene una orden y sus detalles completos.
     */
    public function getOrderDetails($orderId)
    {
        $sqlOrder = "SELECT idOrder, userId, orderNumber, orderType, totalAmount, customerNotes, status, createdAt 
                     FROM orders WHERE idOrder = ?";

        $stmtOrder = $this->connection->prepare($sqlOrder);
        if (!$stmtOrder) return null;
        $stmtOrder->bind_param('i', $orderId);
        $stmtOrder->execute();
        $resultOrder = $stmtOrder->get_result();
        $order = $resultOrder->fetch_assoc();
        $stmtOrder->close();

        if (!$order) return null;

        $sqlItems = "SELECT oi.productId, p.name AS productName, oi.quantity, oi.unitPrice, oi.specialInstructions
                     FROM order_items oi
                     JOIN products p ON oi.productId = p.idProduct
                     WHERE oi.orderId = ?";

        $stmtItems = $this->connection->prepare($sqlItems);
        if (!$stmtItems) return $order;

        $stmtItems->bind_param('i', $orderId);
        $stmtItems->execute();
        $resultItems = $stmtItems->get_result();

        $items = [];
        while ($row = $resultItems->fetch_assoc()) {
            $items[] = $row;
        }
        $stmtItems->close();

        $order['items'] = $items;
        return $order;
    }

    /**
     * Actualiza el estado de una orden.
     */
    public function updateOrderStatus($orderId, $newStatus)
    {
        $sql = "UPDATE orders SET status = ? WHERE idOrder = ?";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('si', $newStatus, $orderId);
        $executed = $stmt->execute();
        $rows_affected = $stmt->affected_rows;
        $stmt->close();

        return $executed && $rows_affected > 0;
    }

    /**
     * Obtiene una lista de órdenes. 
     * Si se proporciona un userId, filtra por ese usuario (para Clientes).
     * Si es null, obtiene todas las órdenes (para Staff/Admin).
     * @param int|null $userId ID del usuario para filtrar, o null para obtener todas las órdenes.
     * @return array Array de órdenes (cabeceras).
     */
    public function getAllOrders($userId = null)
    {
        $orders = [];

        $sql = "SELECT idOrder, orderNumber, orderType, totalAmount, status, createdAt, userId 
                FROM orders";

        $params = [];
        $types = '';

        //Lógica de filtrado dinámico
        if ($userId !== null) {
            $sql .= " WHERE userId = ?";
            $params[] = $userId;
            $types .= 'i'; // 'i' para integer
        }

        $sql .= " ORDER BY createdAt DESC"; //Muestra las más recientes primero

        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            error_log("Error al preparar getAllOrders: " . $this->connection->error);
            return $orders;
        }

        //Si hay parámetros, los vinculamos
        if ($userId !== null) {
            $stmt->bind_param($types, ...$params);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        $stmt->close();

        return $orders;
    }

    /**
     * Obtiene las órdenes más recientes filtradas por estado.
     * Usado para la lista de "Tareas Pendientes" en el dashboard.
     * @param string $status Estado de la orden ('Pendiente', 'Preparando', etc.).
     * @param int $limit Número máximo de registros a retornar.
     * @return array
     */
    public function getLatestOrdersByStatus($status, $limit = 5)
    {
        $sql = "SELECT idOrder, orderNumber, totalAmount, orderType, createdAt 
                FROM orders 
                WHERE status = ? 
                ORDER BY createdAt ASC 
                LIMIT ?";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return [];

        $stmt->bind_param('si', $status, $limit); // 's' para string (status), 'i' para integer (limit)
        $stmt->execute();
        $result = $stmt->get_result();

        $orders = [];
        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }
        $stmt->close();
        return $orders;
    }

    /**
     * Calcula la suma de totalAmount (ingresos) para una fecha específica.
     * @param string $date Fecha en formato 'YYYY-MM-DD'.
     * @return float Total de ingresos.
     */
    public function getRevenueByDate($date)
    {
        // Solo sumamos órdenes que ya están 'Entregada' o 'Completed' (ajusta según tus estados finales)
        $sql = "SELECT COALESCE(SUM(totalAmount), 0.00) AS totalRevenue 
                FROM orders 
                WHERE DATE(createdAt) = ? AND status IN ('Entregada', 'Preparando')";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0.00;

        $stmt->bind_param('s', $date);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (float) $row['totalRevenue'];
    }

    /**
     * Cuenta el número de órdenes que se encuentran en un estado específico.
     * @param string $status Estado de la orden.
     * @return int Conteo de órdenes.
     */
    public function countOrdersByStatus($status)
    {
        $sql = "SELECT COUNT(idOrder) AS count FROM orders WHERE status = ?";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;

        $stmt->bind_param('s', $status);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int) $row['count'];
    }

    /**
     * Total de ordenes del dia actual
     */

    public function getOrdersToday()
    {
        $sql = "SELECT COUNT(*) as total FROM orders WHERE DATE(createdAt) = CURDATE()";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;

        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        return (int)$total;
    }

    /**
     * Total de ventas del dia
     */

    public function getSellsToday()
    {
        $sql = "SELECT SUM(totalAmount) as total FROM orders WHERE DATE(createdAt) = CURDATE() AND status = 'entregada'";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;

        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        return (float)$total;
    }

    public function getRecentOrders($limit = 10){
        $sql = "SELECT o.*, u.name as cliente, t.tableNumber as mesa 
            FROM orders o 
            LEFT JOIN users u ON o.userId = u.idUser 
            LEFT JOIN tables t ON o.tableId = t.idTable 
            ORDER BY o.createdAt DESC 
            LIMIT ?";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return [];

        //Vincular el parámetro límite
        $stmt->bind_param("i", $limit);
        $stmt->execute();

        // Obtener resultados
        $result = $stmt->get_result();
        $orders = [];

        while ($row = $result->fetch_assoc()) {
            $orders[] = $row;
        }

        $stmt->close();
        return $orders;
    }

    public function __destruct()
    {
        $this->connection->close();
    }
}
