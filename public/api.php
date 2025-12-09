<?php
//Este archivo maneja GET, POST, PUT y DELETE

ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../error.log');

// Iniciar buffer de salida (para eliminar HTML inesperado)
ob_start();
error_reporting(E_ALL);     //activar errores
ini_set('display_errors', 0); // No mostrar errores al cliente

//Habilitar CORS
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=utf-8');

//INCLUIR EL CONTROLADOR
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/CategoryController.php';
require_once __DIR__ . '/../app/controllers/OrderController.php';
require_once __DIR__ . '/../app/controllers/ReservationController.php';

require_once __DIR__ . '/../includes/classes/Response.php';
require_once __DIR__ . '/../includes/classes/AuthManager.php';

//OBTENER MÉTODO, RECURSO, ID y action
$method = $_SERVER['REQUEST_METHOD'];
$resource = $_GET['resource'] ?? '';
$id = $_GET['id'] ?? null; 
$action = $_GET['action'] ?? null;

if($method == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$productController = new ProductController();
$userController = new UserController();
$orderController = new OrderController();
$categoryController = new CategoryController();
$reservationController = new ReservationController();

//Ver que esta llegando 
error_log("API Request: method=$method, resource=$resource, id=$id, action=$action");

switch ($resource) {
    case 'products':
        if ($method === 'GET') {
            $productController->index();
        } elseif ($method === 'POST') {
            $productController->createProduct();
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            //Requiere un ID en la URL para actualizar
            if ($id) {
                $productController->updateProduct($id);
            } else {
                Response::sendJson(false, 'Falta el ID del producto para actualizar.', [], 400);
            }
        } elseif ($method === 'DELETE') {
            //Requiere un ID en la URL para eliminar
            if ($id) {
                $productController->deleteProduct($id);
            } else {
                Response::sendJson(false, 'Falta el ID del producto para eliminar.', [], 400);
            }
        }
        break;
    
    case 'orders':
        if ($method === 'POST') {
            $orderController->createOrder();
        } elseif (($method === 'PUT' || $method === 'PATCH')) {
            // Actualización de estado: /api.php?resource=orders&id=123
            if ($id) {
                $orderController->updateOrderStatus($id); 
            } else {
                Response::sendJson(false, 'Falta el ID de la orden para actualizar el estado.', [], 400);
            }
        }
        break;
        
    case 'categories':
        if ($method === 'GET') {
            $categoryController->getCategory($id);
        } elseif ($method === 'POST') {
            $categoryController->createCategory();
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            if ($id) {
                $categoryController->updateCategory($id);
            } else {
                Response::sendJson(false, 'Falta el ID de la categoría para actualizar.', [], 400);
            }
        } elseif ($method === 'DELETE') {
            if ($id) {
                $categoryController->deleteCategory($id);
            } else {
                Response::sendJson(false, 'Falta el ID de la categoría para eliminar.', [], 400);
            }
        }
        break;

    case 'reservations':
        if ($method === 'GET') {
            //GET /api.php?resource=reservations
            $reservationController->index(); 
        } elseif ($method === 'POST') {
            $reservationController->createReservation();
        } elseif (($method === 'PUT' || $method === 'PATCH')) {
            //Actualización de estado: /api.php?resource=reservations&id=123
            if ($id) {
                $reservationController->updateReservationStatus($id); 
            } else {
                Response::sendJson(false, 'Falta el ID de la reserva para actualizar el estado.', [], 400);
            }
        }
        break;

    case 'auth':
        if ($method === 'POST') {
            //ej: ?resource=auth&action=login    
            if ($action === 'login') {
                $userController->login();
            } elseif ($action === 'register') {
                $userController->register();
            } else {
                 http_response_code(400);
                 echo json_encode(['success' => false, 'message' => 'Acción de autenticación no válida.']);
            }
        } elseif ($method === 'GET' && $action === 'logout') {
            $userController->logout();
        }
        break;
        
    case 'users':
        if ($method === 'GET') {
            if ($id) {
                //GET /api.php?resource=users&id=X (Detalles de un usuario)
                $userController->getUser($id);
            } else {
                //GET /api.php?resource=users (Lista de todos los usuarios)
                $userController->index(); 
            }
        } elseif ($method === 'POST') {
            //POST para crear nuevos Staff/Admin 
             $userController->createUserFromAdmin();
        } elseif ($method === 'PUT' || $method === 'PATCH') {
            if ($id) {
                $userController->updateUser($id);
            } else {
                Response::sendJson(false, 'Falta el ID del usuario para actualizar.', [], 400);
            }
        } elseif ($method === 'DELETE') {
            if ($id) {
                $userController->deleteUser($id);
            } else {
                Response::sendJson(false, 'Falta el ID del usuario para eliminar.', [], 400);
            }
        }
        break;

    default:
        Response::sendJson(false, 'Recurso no encontrado.', [], 404);
        break;
}

// ---- LIMPIAR SALIDA ANTES DE ENVIAR JSON ----
ob_end_flush();

?>