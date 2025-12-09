<?php

//modelo de usuario para consultar la DB sobre los permisos del rol
require_once __DIR__ . '/../../app/models/User/UserModel.php';

class AuthManager {
    
    //Almacena los permisos del rol actual para no consultar la DB en cada llamada
    private static $permissions = null; 


    public function login($user) {
        if (session_status() === PHP_SESSION_NONE) {
        session_start();
        }

        $_SESSION['user_id'] = $user['idUser'];
        //$_SESSION['user_role'] = $user['RoleName'];
        $_SESSION['user_name'] = $user['name'];
    }

    /**
     * Verifica si el usuario actual ha iniciado sesión.
     * @return bool
     */
    public static function isLoggedIn() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        //Verifica si el ID de usuario está en la sesión
        return isset($_SESSION['user_id']);
    }

    /**
     * Obtiene el rol del usuario actual desde la sesión.
     * @return string|null Nombre del rol o null si no está logueado.
     */
    public static function getUserRole() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_role'] ?? null;
    }

    /**
     * Carga todos los permisos asociados al rol del usuario logueado.
     * Consulta la base de datos si es la primera vez en la sesión.
     */
    private static function loadPermissions() {
        $roleName = self::getUserRole();

        if (!self::isLoggedIn() || !$roleName) {
            self::$permissions = [];
            return;
        }

        //Siempre carga desde la BD
        $userModel = new UserModel(); 
        self::$permissions = $userModel->getPermissionsByRoleName($roleName);
    }

    /**
     * Verifica si el usuario actual tiene un permiso específico.
     * @param string $permissionName El nombre del permiso a verificar (ej: 'products_create').
     * @return bool True si tiene el permiso, false en caso contrario.
     */
    public static function hasPermission($permissionName) {
        //Cargar permisos (si no están ya en caché)
        self::loadPermissions();

        //Comprobar si el permiso está en la lista del rol
        return in_array($permissionName, self::$permissions);
    }
    
    /**
     * Redirige al usuario si no tiene el permiso requerido.
     * @param string $permissionName El permiso requerido.
     * @param string $redirectUrl URL a donde redirigir si falla (ej: '/error/forbidden.php').
     */
    public static function requirePermission($permissionName, $redirectUrl = '/index.php') {

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!self::isLoggedIn()) {
            //No logueado, redirigir al login
            header("Location: /views/auth/logIn.php");
            exit();
        }

        if (!self::hasPermission($permissionName)) {
            //Logueado pero sin permiso, redirigir a página de acceso denegado
            header("Location: " . $redirectUrl);
            exit();
        }
        
    }

    public static function getUserId() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user_id'] ?? null;
    }

    public function logout() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    session_destroy();
}
}