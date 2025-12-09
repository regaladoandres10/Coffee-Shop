<?php

require_once __DIR__ . '/../models/User/UserModel.php';
require_once __DIR__ . '/../../includes/classes/Response.php';
require_once __DIR__ . '/../../includes/SessionManager.php';

class UserController
{
    private $userModel;
    private $sessionManager;

    public function __construct()
    {
        //Creando objeto para el modelo de usuario
        $this->userModel = new UserModel();
        //Creando objeto para el modelo de sessionManager
        $this->sessionManager = SessionManager::getInstance();
    }

    //---REGISTRO (SIGN UP)---
    public function register()
    {
        $data = $this->getJsonInput(); //Obtener datos del AJAX

        //Sanitización
        $name = filter_var($data['name'] ?? '', FILTER_SANITIZE_STRING);
        $nameUser = filter_var($data['nameUser'] ?? '', FILTER_SANITIZE_STRING);
        $phone = filter_var($data['phone'] ?? '', FILTER_SANITIZE_STRING);
        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirmPassword'] ?? '';

        // Obtener rol (usando 'Cliente' como valor por defecto)
        $role = filter_var($data['role'] ?? 'Cliente', FILTER_SANITIZE_STRING);

        //Validación de datos
        if (empty($name) || empty($nameUser) || empty($phone) || empty($password)) {
            Response::sendJson(false, 'Todos los campos son obligatorios.', [], 400);
            return;
        }
        if ($password !== $confirmPassword) {
            Response::sendJson(false, 'Las contraseñas no coinciden.', [], 400);
            return;
        }

        //Seguridad utilizando Hashing en la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        //Llama al Modelo para guardar
        $newUserId = $this->userModel->createUser($name, $nameUser, $hashedPassword, $phone, $role);

        if ($newUserId) {
            Response::sendJson(true, 'Usuario registrado exitosamente.', [
                'userId' => $newUserId,
                'name' => $nameUser
            ], 201);
        } else {
            Response::sendJson(false, 'Error al registrar el usuario.', [], 500);
        }
    }

    /**
     * Creación de usuario desde el panel de admin
     */

    public function createUserFromAdmin()
    {
        error_log("Entrando a createUserFromAdmin()");
        $data = $this->getJsonInput(); //Obtener datos del AJAX

        //Sanitización
        $name = filter_var($data['name'] ?? '', FILTER_SANITIZE_STRING);
        $nameUser = filter_var($data['nameUser'] ?? '', FILTER_SANITIZE_STRING);
        $phone = filter_var($data['phone'] ?? '', FILTER_SANITIZE_STRING);
        $password = $data['password'] ?? '';
        $confirmPassword = $data['confirmPassword'] ?? '';
        $is_active = $data["is_active"] ?? 1;

        // Obtener rol (usando 'Cliente' como valor por defecto)
        $role = filter_var($data['role'] ?? 'Cliente', FILTER_SANITIZE_STRING);

        error_log("POST recibido: " . print_r($data, true));

        //Validación de datos
        if (empty($name) || empty($nameUser) || empty($phone) || empty($password)) {
            Response::sendJson(false, 'Todos los campos son obligatorios.', [], 400);
            return;
        }
        if ($password !== $confirmPassword) {
            Response::sendJson(false, 'Las contraseñas no coinciden.', [], 400);
            return;
        }

        if (strlen($password) < 6) {
            return Response::sendJson(false, "La contraseña debe tener mínimo 6 caracteres.");
        }

        //Seguridad utilizando Hashing en la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        //Llama al Modelo para guardar
        $newUserId = $this->userModel->createUser($name, $nameUser, $hashedPassword, $phone, $role, $is_active);

        if ($newUserId) {
            Response::sendJson(true, 'Usuario registrado exitosamente.', [
                'userId' => $newUserId,
                'name' => $nameUser
            ], 201);
        } else {
            Response::sendJson(false, 'Error al registrar el usuario.', [], 500);
        }
    }

    //---INICIO DE SESIÓN (LOGIN)---
    public function login()
    {
        $data = $this->getJsonInput();

        //Sanitización (solo necesitamos el nombre)
        $nameUser = filter_var($data['nameUser'] ?? '', FILTER_SANITIZE_STRING);
        $password = $data['password'] ?? '';

        if (empty($nameUser) || empty($password)) {
            Response::sendJson(false, 'Nombre de usuario y contraseña son obligatorios.', [], 400);
            return;
        }

        //Llama al Modelo para obtener el usuario
        $user = $this->userModel->getUserByUsername($nameUser);
        //Verificación de credenciales y seguridad
        if ($user && password_verify($password, $user['password'])) {
            //Si la contraseña esta correcta crear la sesión
            $this->sessionManager->login($user);

            //Retornar información del usuario (sin la contraseña hasheada)
            unset($user['password']);

            //Ver que datos se estan enviando
            error_log("Login successful for user: " . $user['nameUser']);

            Response::sendJson(true, 'Inicio de sesión exitoso.', $user);
        } else {
            //Ver qué está fallando
            error_log("Login failed for user: $nameUser");
            Response::sendJson(false, 'Credenciales incorrectas o usuario inactivo.', [], 401);
        }
    }

    //---CERRAR SESIÓN (LOGOUT)---
    public function logout()
    {
        $this->sessionManager->logout();
        Response::sendJson(true, 'Sesión cerrada exitosamente.', [], 200);
    }

    //Verificar la sesión
    public function checkSession()
    {
        if ($this->sessionManager->isLoggedIn()) {
            $user = $this->sessionManager->getCurrentUser();
            Response::sendJson(true, 'Sesión activa.', $user);
        } else {
            Response::sendJson(false, 'No hay sesión activa.', [], 401);
        }
    }

    /**
     * Lista todos los usuarios. Requiere permiso de Staff/Admin.
     * GET /api.php?resource=users
     */
    public function index()
    {
        // Permiso: Sólo usuarios con este permiso pueden ver la lista completa.
        if (!AuthManager::hasPermission('users_view_all')) {
            Response::sendJson(false, 'Acceso denegado. Permiso requerido: users_view_all.', [], 403);
            return;
        }

        $users = $this->userModel->getAllUsers();
        Response::sendJson(true, 'Lista de usuarios obtenida.', $users);
    }

    /**
     * Obtiene los detalles de un solo usuario.
     * GET /api.php?resource=users&id=X
     */
    public function getUser($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            Response::sendJson(false, 'ID de usuario inválido.', [], 400);
            return;
        }

        $user = $this->userModel->getUserById($id);

        if ($user) {
            Response::sendJson(true, 'Detalles del usuario obtenidos.', $user);
        } else {
            Response::sendJson(false, 'Usuario no encontrado.', [], 404);
        }
    }

    /**
     * Actualiza un usuario. Requiere permiso de gestión de usuarios.
     * PUT/PATCH /api.php?resource=users&id=X
     */
    public function updateUser($id)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            Response::sendJson(false, 'ID de usuario inválido.', [], 400);
            return;
        }

        //Leer JSON
        $data = json_decode(file_get_contents("php://input"), true);

        if (!$data) {
            Response::sendJson(false, "No se recibieron datos JSON válidos.", []);
            return;
        }

        $name = $data['name'] ?? null;
        $nameUser = $data['nameUser'] ?? null;
        $phone = $data['phone'] ?? null;
        $role = $data['role'] ?? null;
        $is_active = $data['is_active'] ?? null;
        $password = $data['password'] ?? null;

        // Valida campos mínimos
        if (!$name || !$nameUser) {
            Response::sendJson(false, "Campos requeridos faltantes.", []);
            return;
        }

        // Llama al modelo
        $result = $this->userModel->updateUser(
            $id,
            $name,
            $nameUser,
            $phone,
            $role,
            $is_active,
            $password
        );

        if ($result) {
            Response::sendJson(true, "Usuario actualizado correctamente.");
        } else {
            Response::sendJson(false, "Error al actualizar usuario.");
        }
    }

    /**
     * Elimina un usuario. Requiere permiso de gestión de usuarios.
     * DELETE /api.php?resource=users&id=X
     */
    public function deleteUser($id)
    {

        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            Response::sendJson(false, 'ID de usuario inválido.', [], 400);
            return;
        }

        $deleted = $this->userModel->deleteUser($id);

        if ($deleted) {
            Response::sendJson(true, 'Usuario eliminado permanentemente.');
        } else {
            Response::sendJson(false, 'Usuario no encontrado o fallo al eliminar.', [], 404);
        }
    }

    /**
     * Buscar usuario
     */


    //--- Funciones auxialiares 
    private function getJsonInput()
    {
        $json_data = file_get_contents('php://input');
        $data = json_decode($json_data, true);

        //Ver que datos llegan
        error_log("Received data: " . print_r($data, true));

        return $data ?? [];
    }
}
