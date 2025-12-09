<?php

require_once __DIR__ . '/../../../includes/classes/classConnection.php';

class UserModel
{
    private $connection;

    public function __construct()
    {
        $db = new ConnectionMySQL();
        //Creamos una conexión a la base de datos
        $this->connection = $db->CreateConnection();
    }

    //---REGISTRO (SIGN UP)---

    /**
     * Registra un nuevo usuario en la base de datos.
     * @param string $name Nombre del usuario.
     * @param string $hashedPassword Contraseña ya hasheada.
     * @param string $phone Teléfono.
     * @return int|bool ID del nuevo usuario o false si falla.
     */
    public function createUser($name, $nameUser, $hashedPassword, $phone, $role, $is_active = 1)
    {

        //Obtener el id del rol
        $roleId = $this->getRoleIdByName($role);
        //Si el role del id no existe
        if (!$roleId) {
            error_log("Rol no encontrado: $role");
            return false;
        }

        //Insertar en la tabla 'users'
        $sqlUser = "INSERT INTO users (name, nameUser, password, phone, idRol, created_at, is_active) VALUES (?, ?, ?, ?, ?, NOW(), ?)";

        error_log("Ejecutando SQL createUser()");

        $stmtUser = $this->connection->prepare($sqlUser);
        if (!$stmtUser) {
            error_log("Error preparing statement: " . $this->connection->error);
            return false;
        }

        //name(s), nameUser(s), password(s), phone(s), roleId(i)
        $stmtUser->bind_param('ssssii', $name, $nameUser, $hashedPassword, $phone, $roleId, $is_active);
        //Ejecutar consulta
        $stmtUser->execute();
        error_log("Resultado execute(): " . ($stmtUser->num_rows()));
        //Si la consulta jala insertar el id a user_id
        if ($stmtUser->affected_rows > 0) {
            $newId = $stmtUser->insert_id;
            $stmtUser->close();
            return $newId;
        } else {
            error_log("Error al crear usuario: " . $stmtUser->error);
        }
        $stmtUser->close();
        return false;
    }

    /**
     * Función auxiliar para obtener el ID de un rol por su nombre.
     */
    private function getRoleIdByName($roleName)
    {
        $sql = "SELECT idRole FROM roles WHERE name = ?";
        $stmt = $this->connection->prepare($sql);
        //Si la consulta no jala que truene
        if (!$stmt) {
            error_log("Error preparando statement: " . $this->connection->error);
            return false;
        }

        $stmt->bind_param('s', $roleName);
        $stmt->execute();
        $resultRole = $stmt->get_result()->fetch_assoc();

        if (!$resultRole) {
            error_log("Rol no encontrado: " . $roleName);
            return false;
        }
        $roleId = $resultRole['idRole'];
        return $roleId;
    }

    /**
     * Función auxiliar para asignar el rol al nuevo usuario.
     */
    private function assignRoleToUser($userId, $roleId)
    {
        $sql = "INSERT INTO user_roles (userId, roleId) VALUES (?, ?)";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('ii', $userId, $roleId);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Obtiene una lista de nombres de permisos para un rol específico.
     * @param string $roleName Nombre del rol (ej: 'Administrador').
     * @return array Array de nombres de permisos (ej: ['products_full', 'users_manage']).
     */
    public function getPermissionsByRoleName($roleName)
    {
        $sql = "SELECT p.name FROM roles r
                JOIN role_permissions rp ON r.idRole = rp.roleId
                JOIN permissions p ON rp.permissionId = p.idPermission
                WHERE r.name = ?";

        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            error_log("Error de prepared statement (Permisos): " . $this->connection->error);
            return [];
        }

        $stmt->bind_param('s', $roleName);
        $stmt->execute();

        $result = $stmt->get_result();

        $permissions = [];
        while ($row = $result->fetch_assoc()) {
            $permissions[] = $row['name'];
        }

        $stmt->close();
        return $permissions;
    }


    //---LOGIN---

    /**
     * Busca un usuario por su nombre
     * @param string $name Nombre (o identificador) del usuario.
     * @return array|null Información del usuario incluyendo el hash de la contraseña, o null.
     */
    public function getUserByUsername($name)
    {
        //Hacemos la busqueda
        $sql = "SELECT u.idUser, u.name, u.nameUser, u.password, u.phone, u.idRol, r.name as RoleName
        FROM users u
        JOIN roles r
        ON u.idRol = r.idRole
        WHERE LOWER(u.nameUser) = LOWER(?) AND u.is_active = 1"; //Solo usuarios activos

        //Realizo la consulta
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) {
            error_log("Error preparando statement: " . $this->connection->error);
            return null;
        }

        $stmt->bind_param('s', $name);
        $stmt->execute();

        $result = $stmt->get_result();
        #Regresa el usuario de la consulta
        $user = $result->fetch_assoc();

        $stmt->close();

        return $user;
    }

    //---CRUD DE GESTIÓN (Administrativo)---

    /**
     * Obtiene la lista de todos los usuarios, incluyendo su rol.
     * @return array Array de objetos usuario.
     */
    public function getAllUsers()
    {
        $sql = "SELECT 
                    u.idUser, u.name, u.nameUser, u.phone, u.idRol, u.is_active, u.created_at, r.name AS RoleName
                    FROM users u
                    JOIN roles r
                    ON u.idRol = r.idRole
                    ORDER BY u.created_at DESC";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return [];

        $stmt->execute();
        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
        return $users;
    }

    /**
     * Obtiene los detalles de un usuario por ID.
     */
    public function getUserById($id)
    {
        $sql = "SELECT u.idUser, u.name, u.nameUser, u.password, u.phone, u.idRol, u.created_at, u.is_active, r.name AS RoleName
        FROM users u
        JOIN roles r
        ON u.idRol = r.idRole
        WHERE u.idUser = ? AND u.is_active = 1";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return null;

        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        $stmt->close();
        return $user;
    }

    /**
     * Actualiza los detalles del usuario (nombre, teléfono, estado activo, y opcionalmente contraseña).
     */
    public function updateUser($id, $name, $nameUser, $phone, $role, $is_active, $password = null)
    {
        if ($password) {
            $sql = "UPDATE users SET name=?, nameUser=?, phone=?, idRol=?, is_active=?, password=? WHERE idUser=?";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$name, $nameUser, $phone, $role, $is_active, password_hash($password, PASSWORD_DEFAULT), $id]);
        } else {
            $sql = "UPDATE users SET name=?, nameUser=?, phone=?, idRol=?, is_active=? WHERE idUser=?";
            $stmt = $this->connection->prepare($sql);
            return $stmt->execute([$name, $nameUser, $phone, $role, $is_active, $id]);
        }
    }


    /**
     * Elimina permanentemente un usuario de la base de datos.
     */
    public function deleteUser($id)
    {
        $sql = "DELETE FROM Users WHERE idUser = ?";

        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return false;

        $stmt->bind_param('i', $id);
        $executed = $stmt->execute();
        $rows_affected = $stmt->affected_rows;
        $stmt->close();

        return $executed && $rows_affected > 0;
    }

    /**
     * Total de usuarios
     */

    public function countUser()
    {
        $sql = "SELECT COUNT(*) as total FROM users";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;

        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        return (int)$total;
    }

    /**
     * Total de clientes
     */

    public function countCustomers()
    {
        $sql = "SELECT count(*) as Total FROM users WHERE idRol = 3";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;

        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        return (int)$total;
    }

    /**
     * Total de staff/meseros
     */

    public function countStaff()
    {
        $sql = "SELECT count(*) as Total FROM users WHERE idRol = 2";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;

        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        return (int)$total;
    }

    /**
     * Total de admins
     */

    public function countAdmins()
    {
        $sql = "SELECT count(*) as Total FROM users WHERE idRol = 1";
        $stmt = $this->connection->prepare($sql);
        if (!$stmt) return 0;

        $stmt->execute();
        $stmt->bind_result($total);
        $stmt->fetch();
        $stmt->close();

        return (int)$total;
    }

    public function searchUsers($query)
    {
        $query = "%$query%";

        $sql = "SELECT u.idUser, u.name, u.nameUser, u.phone, u.idRol, u.is_active,
                    u.created_at, r.name AS RoleName
                FROM users u
                JOIN roles r ON u.idRol = r.idRole
                WHERE u.name LIKE ?
                OR u.nameUser LIKE ?
                OR u.phone LIKE ?
                ORDER BY u.created_at DESC";

        $stmt = $this->connection->prepare($sql);

        if (!$stmt) {
            error_log("Error preparando searchUsers(): " . $this->connection->error);
            return [];
        }

        $stmt->bind_param('sss', $query, $query, $query);
        $stmt->execute();

        $result = $stmt->get_result();

        $users = [];
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }

        $stmt->close();
        return $users;
    }

    //Cerrar la conexión
    public function __destruct()
    {
        $this->connection->close();
    }
}
