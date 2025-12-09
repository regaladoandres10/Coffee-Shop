<?php

require_once __DIR__ . '/../models/Order/OrderModel.php';
require_once __DIR__ . '/../models/Reservation/ReservationModel.php';
require_once __DIR__ . '/../models/Product/ProductModel.php';
require_once __DIR__ . '/../../includes/classes/AuthManager.php'; 
require_once __DIR__ . '/../../includes/classes/Response.php'; 

class DashboardController {
    private $orderModel;
    private $reservationModel;
    private $productModel;

    public function __construct() {
        $this->orderModel = new OrderModel();
        $this->reservationModel = new ReservationModel();
        $this->productModel = new ProductModel(); 
    }
}