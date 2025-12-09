<?php

require_once __DIR__ . '/../../../includes/classes/classConnection.php'; 


class CategoryModel {
    private $connection;

    public function __construct() {
        $db = new ConnectionMySQL();
        $this->connection = $db->CreateConnection();
    }
    
    //---LECTURA(GET)---
    /**
     * Obtiene todas las categorías.
     * @return array Array de categorías.
     */
    public function getAllCategories() {
        $sql = "SELECT idCategorie, name, description, isActive FROM categories ORDER BY name ASC";
        
        $result = $this->connection->query($sql);

        $categories = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
            $result->free();
        }
        
        return $categories;
    }

    /**
     * Obtiene una categoría por su ID.
     * @param int $idCategorie El ID de la categoría.
     * @return array|null La categoría como array asociativo.
     */
    public function getCategoryById($idCategorie) {
        $sql = "SELECT idCategorie, name, description, isActive FROM categories WHERE idCategorie = ?";
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param('i', $idCategorie);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $category = $result->fetch_assoc();
        
        $stmt->close();
        
        return $category;
    }
    
    //---CREACIÓN(POST)---

    /**
     * Agrega una nueva categoría.
     * @param array $data Datos: name, description, isAvailable.
     * @return int|bool El ID de la nueva categoría o false si falla.
     */
    public function createCategory($data) {
        $sql = "INSERT INTO categories (name, description, isActive) VALUES (?, ?, ?)";
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return false;
        
        $isAvailable = $data['isActive'] ? 1 : 0;
        
        // name(s), description(s), isAvailable(i)
        $stmt->bind_param('ssi', $data['name'], $data['description'], $isAvailable);

        if ($stmt->execute()) {
            $last_id = $stmt->insert_id;
            $stmt->close();
            return $last_id;
        } else {
            error_log("Error al crear categoría: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }
    
    //---ACTUALIZACIÓN(PUT)---

    public function updateCategory($idCategorie, $name, $description, $isAvailable)
    {
        $sql = "UPDATE categories SET name=?, description=?, isActive=? WHERE idCategorie=?";
        $stmt = $this->connection->prepare($sql);

        return $stmt->execute([
            $name,
            $description,
            $isAvailable,
            $idCategorie
        ]);
    }

    
    //---ELIMINACIÓN(DELETE)---

    /**
     * Elimina una categoría por su ID.
     * @param int $idCategorie ID de la categoría a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function deleteCategory($idCategorie) {
        $sql = "DELETE FROM categories WHERE idCategorie = ?";
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return false;
        
        // idCategorie(i)
        $stmt->bind_param('i', $idCategorie);
        
        if ($stmt->execute()) {
            $rows_affected = $stmt->affected_rows;
            $stmt->close();
            return $rows_affected > 0;
        } else {
            error_log("Error al eliminar categoría: " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * Contar las categorias
     */
    public function countCategories()
    {
        $sql = "SELECT COUNT(*) as total FROM categories";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;

        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        return (int)$total;
    }

    public function __destruct() {
        $this->connection->close();
    }
}