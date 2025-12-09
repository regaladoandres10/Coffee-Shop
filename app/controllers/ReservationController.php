<?php

require_once __DIR__ . '/../models/Reservation/ReservationModel.php';
require_once __DIR__ . '/../../includes/classes/AuthManager.php'; 
require_once __DIR__ . '/../../includes/classes/Response.php'; 

class ReservationController {
    private $reservationModel;

    public function __construct() {
        $this->reservationModel = new ReservationModel();
    }

    /**
     * Obtiene la lista de reservas (según el rol del usuario).
     * Usado por GET /api.php?resource=reservations
     */
    public function index() {
        if (!AuthManager::isLoggedIn()) {
            Response::sendJson(false, 'Debe iniciar sesión para ver reservas.', [], 401);
            return;
        }

        $userId = AuthManager::getUserId();
        $userRole = AuthManager::getUserRole();
        $reservations = [];

        //Admin/Staff ve todas las reservas
        if (AuthManager::hasPermission('reservations_view_all')) {
            $reservations = $this->reservationModel->getAllReservations(null);
        } else {
            //Cliente solo ve sus propias reservas
            $reservations = $this->reservationModel->getAllReservations($userId);
        }
        
        Response::sendJson(true, 'Lista de reservas obtenida.', $reservations);
    }


    /**
     * Procesa la creación de una nueva reserva.
     * Usado por POST /api.php?resource=reservations
     */
    public function createReservation() {
        if (!AuthManager::isLoggedIn()) {
            Response::sendJson(false, 'Debe iniciar sesión para reservar.', [], 401);
            return;
        }
        
        $data_raw = $this->getJsonInput();

        //SANITIZACIÓN Y VALIDACIÓN
        $userId = AuthManager::getUserId();
        $tableId = filter_var($data_raw['tableId'] ?? 0, FILTER_VALIDATE_INT);
        $date = filter_var($data_raw['reservationDate'] ?? '', FILTER_SANITIZE_STRING);
        $time = filter_var($data_raw['reservationTime'] ?? '', FILTER_SANITIZE_STRING);
        $people = filter_var($data_raw['numberOfPeople'] ?? 0, FILTER_VALIDATE_INT);
        $specialRequests = filter_var($data_raw['specialRequests'] ?? '', FILTER_SANITIZE_STRING);

        if ($tableId <= 0 || empty($date) || empty($time) || $people <= 0) {
            Response::sendJson(false, 'Datos de reserva incompletos o inválidos.', [], 400);
            return;
        }
        
        //VERIFICACIÓN DE DISPONIBILIDAD (Seguridad y Lógica de Negocio)
        if (!$this->reservationModel->isTableAvailable($tableId, $date, $time)) {
             Response::sendJson(false, 'La mesa no está disponible en esa fecha y hora.', [], 409); // Conflict
             return;
        }

        $reservationData = [
            'userId' => $userId,
            'tableId' => $tableId,
            'reservationDate' => $date,
            'reservationTime' => $time,
            'numberOfPeople' => $people,
            'specialRequests' => $specialRequests
        ];

        //LLAMADA AL MODELO
        $newId = $this->reservationModel->createReservation($reservationData);

        if ($newId) {
            Response::sendJson(true, 'Reserva creada exitosamente y pendiente de confirmación.', ['id' => $newId], 201);
        } else {
            Response::sendJson(false, 'Fallo al procesar la reserva.', [], 500);
        }
    }
    
    /**
     * Permite a Staff o Admin actualizar el estado de una reserva.
     * Usado por PUT/PATCH /api.php?resource=reservations&id=123
     */
    public function updateReservationStatus($idReservation) {
        //Permiso: Solo Staff y Admin pueden actualizar el estado de las reservas.
        if (!AuthManager::hasPermission('reservations_status_manage')) {
            Response::sendJson(false, 'Acceso denegado. No tiene permiso para gestionar reservas.', [], 403);
            return;
        }

        $data_raw = $this->getJsonInput();
        
        $id = filter_var($idReservation, FILTER_VALIDATE_INT);
        $newStatus = filter_var($data_raw['status'] ?? '', FILTER_SANITIZE_STRING);

        if ($id === false || empty($newStatus)) {
            Response::sendJson(false, 'ID de reserva o estado no válido.', [], 400);
            return;
        }
        
        //Validar el nuevo estado (debe coincidir con el ENUM)
        $allowedStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (!in_array($newStatus, $allowedStatuses)) {
            Response::sendJson(false, 'Estado de reserva no permitido.', [], 400);
            return;
        }

        $updated = $this->reservationModel->updateReservationStatus($id, $newStatus);

        if ($updated) {
            Response::sendJson(true, 'Estado de la reserva actualizado a ' . $newStatus);
        } else {
            Response::sendJson(false, 'No se pudo actualizar el estado de la reserva. ID no encontrado.', [], 404);
        }
    }


    private function getJsonInput() {
        $json_data = file_get_contents('php://input');
        return json_decode($json_data, true) ?? [];
    }
}