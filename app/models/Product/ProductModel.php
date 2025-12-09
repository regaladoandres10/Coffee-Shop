<?php

require_once __DIR__ . '/../../../includes/classes/classConnection.php';

class ProductModel {
    private $connection;

    public function __construct() {
        //Crear instancia de la clase de conexión
        $db = new ConnectionMySQL();
        
        //Establecer la conexión
        $this->connection = $db->CreateConnection(); 
    }

    /**
     * Obtiene todos los productos disponibles.
     * @return array Array de productos o array vacío.
     */
    public function getAllProducts() {
        $sql = "SELECT p.idProduct, p.name, p.description, p.price, p.ingredients, p.isAvailable, c.name AS categoryName 
        FROM products p
        JOIN categories c ON p.categoryId = c.idCategorie
        ORDER BY p.name ASC";
        
        // Ejecuta la consulta 
        $result = $this->connection->query($sql);

        $products = [];
        if ($result) {
            //Recorre los resultados y los almacena en un array asociativo
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
            $result->free(); //Libera el resultado
        }
        
        return $products;
    }

    /**
     * Obtiene un producto por su ID.
     * UTILIZA CONSULTAS PREPARADAS (ExecutePreparedQuery) para seguridad.
     * @param int $idProduct El ID del producto.
     * @return array|null El producto como array asociativo, o null si no se encuentra.
     */
    public function getProductById($idProduct) {
        $sql = "SELECT idProduct, name, description, price, categoryId, ingredients, isAvailable 
                FROM products 
                WHERE idProduct = ?";
        
        //Llamada a tu método ExecutePreparedQuery
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
             //Deberías manejar este error, quizás con un log.
             error_log("Error de prepared statement: " . $this->connection->error);
             return null;
        }

        $stmt->bind_param('i', $idProduct);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        
        $stmt->close();
        
        return $product;
    }
    
    //--- MÉTODOS CRUD DE ESCRITURA ---

    /**
     * Agrega un nuevo producto a la base de datos.
     * @param array $data Array asociativo con los datos del producto.
     * @return int|bool El ID del nuevo producto insertado, o false si falla.
     */
    public function createProduct($data) {
        $sql = "INSERT INTO products (name, description, price, categoryId, image, ingredients, isAvailable, createdAt) 
                VALUES (?, ?, ?, ?, ?, ?, ?, NOW())";
        
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            error_log("Error de prepared statement (CREATE): " . $this->connection->error);
            return false;
        }

        // Determinar el estado de disponibilidad
        $isAvailable = $data['isAvailable'] ? 1 : 0;

        // BIND_PARAM: TIPOS (s: string, d: double/float, i: integer)
        // name(s), description(s), price(d), categoryId(i), image(s), ingredients(s), isAvailable(i)
        // Nota: Asumimos que 'image' es un string (la ruta del archivo) como se sugirió.
        $stmt->bind_param(
            'ssdisii', 
            $data['name'], 
            $data['description'], 
            $data['price'], 
            $data['categoryId'], 
            $data['image'], 
            $data['ingredients'], 
            $isAvailable
        );

        if ($stmt->execute()) {
            $last_id = $stmt->insert_id;
            $stmt->close();
            return $last_id;
        } else {
            error_log("Error de ejecución (CREATE): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    # Actualizar productos
    /**
     * Actualiza un producto existente en la base de datos.
     * @param int $idProduct El ID del producto a actualizar.
     * @param array $data Array asociativo con los nuevos datos del producto.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
     */
    public function updateProduct($idProduct, $data) {
        $sql = "UPDATE products 
                SET name = ?, description = ?, price = ?, categoryId = ?, image = ?, ingredients = ?, isAvailable = ?
                WHERE idProduct = ?";
        
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            error_log("Error de prepared statement (UPDATE): " . $this->connection->error);
            return false;
        }

        //Determinar el estado de disponibilidad
        $isAvailable = $data['isAvailable'] ? 1 : 0;
        
        //BIND_PARAM: TIPOS (s: string, d: double/float, i: integer)
        //name(s), description(s), price(d), categoryId(i), image(s), ingredients(s), isAvailable(i), idProduct(i)
        $stmt->bind_param(
            'ssdisiii', 
            $data['name'], 
            $data['description'], 
            $data['price'], 
            $data['categoryId'], 
            $data['image'], 
            $data['ingredients'], 
            $isAvailable,
            $idProduct //ID al final para la cláusula WHERE
        );

        if ($stmt->execute()) {
            $rows_affected = $stmt->affected_rows;
            $stmt->close();
            // etorna true si se afectó al menos una fila
            return $rows_affected > 0;
        } else {
            error_log("Error de ejecución (UPDATE): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * Elimina un producto de la base de datos por su ID.
     * @param int $idProduct El ID del producto a eliminar.
     * @return bool True si la eliminación fue exitosa, false en caso contrario.
     */
    public function deleteProduct($idProduct) {
        $sql = "DELETE FROM products WHERE idProduct = ?";
        
        $stmt = $this->connection->prepare($sql);
        
        if (!$stmt) {
            error_log("Error de prepared statement (DELETE): " . $this->connection->error);
            return false;
        }
        
        //BIND_PARAM: TIPOS (i: integer)
        $stmt->bind_param('i', $idProduct);
        
        if ($stmt->execute()) {
            $rows_affected = $stmt->affected_rows;
            $stmt->close();
            //Retorna true si se eliminó una fila
            return $rows_affected > 0;
        } else {
            error_log("Error de ejecución (DELETE): " . $stmt->error);
            $stmt->close();
            return false;
        }
    }

    /**
     * Cuenta el número total de productos disponibles.
     * @return int Conteo total de productos.
     */
    public function countAllProducts() {
        $sql = "SELECT COUNT(idProduct) AS count FROM products WHERE isAvailable = 1"; //Contamos solo los disponibles
        
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();

        return (int) $row['count'];
    }

    /**
     * Total de productos
     */

    public function countProducts() {
        $sql = "SELECT COUNT(*) as total FROM products";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;
        
        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();
        
        return (int)$total;
    }
    
    //Cerrar la conexión cuando se destruye el objeto
    public function __destruct() {
         $this->connection->close();
    }
}