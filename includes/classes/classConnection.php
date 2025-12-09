<?php
//Crear conexion a una base de datos en MySQL

class ConnectionMySQL {
    private $host;
    private $user;
    private $password;
    private $database;
    private $conn;

    public function __construct()
    {
        // Constructor
        require_once __DIR__ . '/../../includes/config/database.php';
        $this->host = DB_HOST;
        $this->user = DB_USER;
        $this->password = DB_PASS;
        $this->database = DB_NAME;
    }

    public function CreateConnection() {

        // Método que crea y retorna la conexión a la BD.
        $this->conn = new mysqli($this->host, $this->user, $this->password, $this->database);
        if ($this->conn->connect_error) {
            die("Error al conectarse a MySQL: (" . $this->conn->connect_errno . ") " . $this->conn->connect_error);
        }
        $this->conn->autocommit(TRUE);
        return $this->conn;
    }

    
}

    $db = new ConnectionMySQL();
    //Establecer la conexión
    $db->CreateConnection();

    /*
    if($db) {
        echo "<pre>";
        var_dump($db);
        echo "</pre>";
    } else {
        echo "No conexión";
    }*/

?>
