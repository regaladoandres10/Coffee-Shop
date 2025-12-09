<?php

require_once __DIR__ . '/../models/Product/ProductModel.php'; 
require_once __DIR__ . '/../../includes/classes/AuthManager.php';
require_once __DIR__ . '/../../includes/classes/Response.php';

class ProductController {
    private $productModel;

    public function __construct() {
        //Inicializar el modelo
        $this->productModel = new ProductModel();
    }

    //---LECTURA(GET)---

    /**
     * Obtiene y devuelve la lista de todos los productos en formato JSON.
     * Usado por la petición GET /api.php?resource=products
     */
    public function index() {
        $products = $this->productModel->getAllProducts();
        Response::sendJson(true, 'Lista de productos obtenida.', $products);
    }
    
    //---CREACIÓN (POST)---
    /**
     * Crea un nuevo producto.
     * 1. Obtiene la data JSON. 
     * 2. Sanitiza. 
     * 3. Valida. 
     * 4. Llama al Modelo.
     */
    public function createProduct() {
        //Permisos
        if (!AuthManager::hasPermission('products_create')) { 
            //Si no tiene permiso específico para crear
            Response::sendJson(false, 'Acceso denegado. No tiene permiso para crear productos.', [], 403);
            return;
        }        

        $data_raw = $this->getJsonInput();
        //Sanitización
        //Limpia los datos de entrada
        $data_sanitized = $this->sanitizeProductData($data_raw);

        //Validacion de datos
        if (empty($data_sanitized['name']) || $data_sanitized['categoryId'] === false) {
            Response::sendJson(false, 'Error de validación: Nombre o Categoría no válidos.', [], 400);
            return;
        }

        //Llamada al modelo: Envía datos limpios a la capa de persistencia
        $newProductId = $this->productModel->createProduct($data_sanitized);

        if ($newProductId) {
            Response::sendJson(true, 'Producto creado exitosamente.', ['id' => $newProductId], 201);
        } else {
            Response::sendJson(false, 'Error al guardar el producto en la base de datos.', [], 500);
        }
    }
    
    //--ACTUALIZACIÓN(PUT)---

    /**
     * Actualiza un producto existente.
     * @param int $idProduct ID del producto a actualizar.
     */
    public function updateProduct($idProduct) {
        $data_raw = $this->getJsonInput();

        //SANITIZACIÓN
        $data_sanitized = $this->sanitizeProductData($data_raw);
        
        //VALIDACIÓN DEL ID
        $id = filter_var($idProduct, FILTER_VALIDATE_INT);

        if ($id === false) {
            Response::sendJson(false, 'Error: ID de producto no válido.', [], 400);
            return;
        }
        
        //Validacion de datos (Asegurar que al menos hay datos para actualizar)
        if (empty($data_sanitized['name']) || $data_sanitized['categoryId'] === false) {
            Response::sendJson(false, 'Error de validación: Datos incompletos o incorrectos.', [], 400);
            return;
        }

        //Llamada al modelo
        $updated = $this->productModel->updateProduct($id, $data_sanitized);

        if ($updated) {
            Response::sendJson(true, 'Producto actualizado exitosamente.');
        } else {
            //Error 500 si falló la DB, o 404 si el producto no existe o 304 si no hubo cambios
            Response::sendJson(false, 'No se pudo actualizar el producto o no hubo cambios.', [], 500);
        }
    }
    
    //---ELIMINACIÓN(DELETE)---

    /**
     * Elimina un producto.
     * @param int $idProduct ID del producto a eliminar.
     */
    public function deleteProduct($idProduct) {

        //Requerir permisos
        if (!AuthManager::hasPermission('products_delete')) {
            Response::sendJson(false, 'Acceso denegado. No tiene permiso para eliminar productos.', [], 403);
            return;
        }

        //Validacion del id
        $id = filter_var($idProduct, FILTER_VALIDATE_INT);
        
        if ($id === false) {
            Response::sendJson(false, 'Error: ID de producto no válido.', [], 400);
            return;
        }

        //llamada al modelo
        $deleted = $this->productModel->deleteProduct($id);

        if ($deleted) {
            Response::sendJson(true, 'Producto eliminado exitosamente.');
        } else {
            Response::sendJson(false, 'No se pudo eliminar el producto. Quizás ya fue eliminado o el ID es incorrecto.', [], 404);
        }
    }

    //---Funciones privadas de utilidad ---

    /**
     * Obtiene y decodifica el JSON enviado en el cuerpo de la petición (para POST/PUT).
     * @return array
     */
    private function getJsonInput() {
        $json_data = file_get_contents('php://input');
        //El operador ?? (null coalescing) asegura que se devuelva un array vacío si falla la decodificación.
        return json_decode($json_data, true) ?? [];
    }

    /**
     *Realiza la sanitización de los datos de entrada para prevenir ataques XSS.
     */
    private function sanitizeProductData($data_raw) {
        //Asegurarse de que las claves del array existen con valores por defecto
        $data_raw = array_merge([
            'name' => '', 
            'description' => '', 
            'price' => 0.0, 
            'categoryId' => 0, 
            'image' => '', 
            'ingredients' => '', 
            'isAvailable' => 0
        ], $data_raw);
        
        $data_sanitized = [];

        //Sanear cadenas (eliminando etiquetas HTML)
        $data_sanitized['name'] = filter_var($data_raw['name'], FILTER_SANITIZE_STRING);
        $data_sanitized['description'] = filter_var($data_raw['description'], FILTER_SANITIZE_STRING);
        $data_sanitized['ingredients'] = filter_var($data_raw['ingredients'], FILTER_SANITIZE_STRING);
        $data_sanitized['image'] = filter_var($data_raw['image'], FILTER_SANITIZE_URL); 

        //Sanear y validar el precio
        $data_sanitized['price'] = filter_var($data_raw['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

        //Sanear y validar IDs (enteros)
        $data_sanitized['categoryId'] = filter_var($data_raw['categoryId'], FILTER_SANITIZE_NUMBER_INT);
        $data_sanitized['categoryId'] = filter_var($data_sanitized['categoryId'], FILTER_VALIDATE_INT);
        
        //Sanear booleanos/estados de disponibilidad
        $data_sanitized['isAvailable'] = (int) filter_var($data_raw['isAvailable'], FILTER_VALIDATE_BOOLEAN);

        return $data_sanitized;
    }
}