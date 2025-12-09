<?php
// app/controllers/CategoryController.php

require_once __DIR__ . '/../models/Category/CategoryModel.php';
require_once __DIR__ . '/../../includes/classes/AuthManager.php';
require_once __DIR__ . '/../../includes/classes/Response.php';

class CategoryController
{
    private $categoryModel;

    public function __construct()
    {
        $this->categoryModel = new CategoryModel();
    }

    //---LECTURA (GET)---

    public function index()
    {
        $categories = $this->categoryModel->getAllCategories();
        Response::sendJson(true, 'Lista de categorías obtenida.', $categories);
    }

    public function getCategory($id)
    {
        // Validar ID
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            Response::sendJson(false, 'ID de categoría inválido.', [], 400);
            return;
        }

        // Consultar categoría en modelo
        $category = $this->categoryModel->getCategoryById($id);

        if ($category) {
            Response::sendJson(true, 'Detalles de la categoría obtenidos.', $category);
        } else {
            Response::sendJson(false, 'Categoría no encontrada.', [], 404);
        }
    }

    //---CREACIÓN(POST)---
    public function createCategory()
    {

        $data_raw = $this->getJsonInput();

        //SANITIZACIÓN
        $data_sanitized = $this->sanitizeCategoryData($data_raw);

        //VALIDACIÓN BÁSICA
        if (empty($data_sanitized['name'])) {
            Response::sendJson(false, 'Error de validación: El nombre de la categoría es obligatorio.', [], 400);
            return;
        }

        //LLAMADA AL MODELO
        $newCategoryId = $this->categoryModel->createCategory($data_sanitized);

        if ($newCategoryId) {
            Response::sendJson(true, 'Categoría creada exitosamente.', ['id' => $newCategoryId], 201);
        } else {
            Response::sendJson(false, 'Error al guardar la categoría en la base de datos.', [], 500);
        }
    }

    //---ACTUALIZACIÓN PUT---

    /**
     * Actualiza una categoría existente.
     * @param int $idCategorie ID de la categoría a actualizar.
     */
    public function updateCategory($idCategorie)
    {

        // Validar ID
        $id = filter_var($idCategorie, FILTER_VALIDATE_INT);
        if ($id === false) {
            Response::sendJson(false, 'ID de categoría inválido.', [], 400);
            return;
        }

        // Leer JSON
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            Response::sendJson(false, "No se recibieron datos JSON válidos.", []);
            return;
        }

        // Extraer campos (coinciden con tu formulario)
        $name        = $data['name'] ?? null;
        $description = $data['description'] ?? null;
        $isActive    = isset($data['isActive']) ? (int)$data['isActive'] : null;

        // Valida campos mínimos
        if (!$name) {
            Response::sendJson(false, "El nombre de la categoría es obligatorio.", []);
            return;
        }

        // Llamada al modelo
        $result = $this->categoryModel->updateCategory(
            $id,$name,$description, $isActive
        );

        if ($result) {
            Response::sendJson(true, "Categoría actualizada correctamente.");
        } else {
            Response::sendJson(false, "Error al actualizar categoría.");
        }
    }
    
    //---ELIMINACIÓN (DELETE)---
    /**
     * Elimina una categoría.
     * @param int $idCategorie ID de la categoría a eliminar.
     */
    public function deleteCategory($idCategorie)
    {

        //VALIDACIÓN Y SANITIZACIÓN DEL ID
        $id = filter_var($idCategorie, FILTER_VALIDATE_INT);
        if ($id === false) {
            Response::sendJson(false, 'Error: ID de categoría no válido.', [], 400);
            return;
        }

        //LLAMADA AL MODELO
        $deleted = $this->categoryModel->deleteCategory($id);

        if ($deleted) {
            Response::sendJson(true, 'Categoría eliminada exitosamente.');
        } else {
            Response::sendJson(false, 'No se pudo eliminar la categoría. El ID puede ser incorrecto o la categoría no existe.', [], 404);
        }
    }

    //---Funciones privadas de utilidad---

    private function getJsonInput()
    {
        $json_data = file_get_contents('php://input');
        return json_decode($json_data, true) ?? [];
    }

    private function sanitizeCategoryData($data_raw)
    {
        $data_raw = array_merge([
            'name' => '',
            'description' => '',
            'isAvailable' => 0
        ], $data_raw);

        $data_sanitized = [];

        //Sanitizar cadenas
        $data_sanitized['name'] = filter_var($data_raw['name'], FILTER_SANITIZE_STRING);
        $data_sanitized['description'] = filter_var($data_raw['description'], FILTER_SANITIZE_STRING);

        //Sanitizar booleanos/estados de disponibilidad
        $data_sanitized['isAvailable'] = (int) filter_var($data_raw['isAvailable'], FILTER_VALIDATE_BOOLEAN);

        return $data_sanitized;
    }
}
