<?php 
class SessionManager { 
    private static $instance = null; 
    
    private function __construct() { 
        //Verificamos en que estado esta la sesión 
        if(session_status() === PHP_SESSION_NONE) { 
            session_start(); 
        } 
    } 
    
    public static function getInstance() { 
        if (self::$instance === null) { 
            self::$instance = new SessionManager(); 
        } 
        return self::$instance; 
    } 
    
    //Verificar si el usuario esta autenticado 
    public function isLoggedIn() { 
        return isset($_SESSION['user_id']); 
    } 
    
    //Obtener información del usuario actual desde sesión 
    public function getCurrentUser() { 
        if ($this->isLoggedIn()) { 
            return [ 
                'id' => $_SESSION['user_id'] ?? null, 
                'name' => $_SESSION['user_name'] ?? null, 
                'role' => $_SESSION['user_role'] ?? null, 
                'role_id' => $_SESSION['user_role_id'] ?? null 
            ]; 
        } 
        return null; 
    } 
    
    // Iniciar sesión 
    public function login($userData) { 
        $_SESSION['user_id'] = $userData['idUser']; 
        $_SESSION['user_name'] = $userData['name']; 
        $_SESSION['user_role'] = $userData['roleName']; 
        $_SESSION['user_role_id'] = $userData['idRol'] ?? null; 
        $_SESSION['login_time'] = time(); 
    } 
    
    // Cerrar sesión 
    public function logout() { 
        session_unset(); 
        session_destroy(); 
    } 
    
    // Verificar rol y redirigir si no tiene permisos 
    public function requireRole($allowedRoles) { 
        if (!$this->isLoggedIn()) { 
            $this->redirectToLogin(); 
        } 
        
        $user = $this->getCurrentUser(); 
        
        if (!in_array($user['role'], $allowedRoles)) { 
            $this->redirectToHome(); 
        } 
    } 
    
    // Redirigir a login 
    public function redirectToLogin() { 
        header('Location: /login.php'); 
        exit(); 
    } 
    
    // Redirigir a home según rol 
    public function redirectToHome() { 
        $user = $this->getCurrentUser(); 
        
        switch($user['role']) { 
            case 'Administrador': 
                header('Location: /admin/index.php'); 
                break; 
                
            case 'Staff': 
            case 'Mesero': 
                header('Location: /staff/index.php'); 
                break; 
                
            case 'Cliente': 
                header('Location: /cliente/index.php'); 
                break; 
                
            default: 
                header('Location: /index.php'); 
        } 
        
        exit(); 
    } 
} 
?>
