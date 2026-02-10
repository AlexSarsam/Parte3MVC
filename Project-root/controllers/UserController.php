<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../utils/Security.php';

class UserController {
    
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

 
    public function login() {
        $error = null; 

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $error = "Token CSRF invalido.";
            }
            
            $email = trim($_POST['email'] ?? ''); 
            $pass = $_POST['password'] ?? '';

            // Validaciones básicas
            if ($error) {
                // Error de CSRF, dejamos caer al formulario
            } elseif (empty($email) || empty($pass)) {
                $error = "Todos los campos son obligatorios.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El email no es válido.";
            } else {
                $user = $this->userModel->findByEmail($email);
                
                if ($user && password_verify($pass, $user['password_hash'])) {
                    session_regenerate_id(true);
                    // Login correcto: Guardamos datos en sesión
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['role'] = $user['role'];
                    $_SESSION['username'] = $user['username'];
                    
                    // Redirigir según rol
                    if ($user['role'] === 'admin') {
                        header("Location: index.php?action=admin_dashboard");
                    } else {
                        header("Location: index.php?action=posts");
                    }
                    exit(); 
                } else {
                    $error = "Email o contraseña incorrectos.";
                }
            }
        } 
        
        include __DIR__ . '/../views/auth/login.php';
    }

    
    public function register() {
        $error = null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $error = "Token CSRF invalido.";
            }
            
            $username = trim($_POST['username'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            // Validaciones
            if ($error) {
                // Error de CSRF, dejamos caer al formulario
            } elseif (empty($username) || empty($email) || empty($password)) {
                $error = "Todos los campos son obligatorios.";
            } elseif (strlen($username) < 3) {
                $error = "El nombre de usuario debe tener al menos 3 caracteres.";
            } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $error = "El email no es válido.";
            } elseif (strlen($password) < 6) {
                $error = "La contraseña debe tener al menos 6 caracteres.";
            } elseif ($this->userModel->findByEmail($email)) {
                $error = "El email ya está registrado.";
            } else {
                // Si todo es válido, registrar
                if ($this->userModel->register($username, $email, $password)) {
                    $_SESSION['success'] = "Registro exitoso. Ahora puedes iniciar sesión.";
                    header("Location: index.php?action=login");
                    exit();
                } else {
                    $error = "Error al registrar el usuario.";
                }
            }
        } 
        
        include __DIR__ . '/../views/auth/register.php';
    }
}
?>