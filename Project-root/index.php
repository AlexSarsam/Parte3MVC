<?php

ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');
ini_set('session.use_strict_mode', 1);
session_start();

require_once __DIR__ . '/utils/Security.php';

// SEGURIDAD CSRF
Security::csrfToken();

require_once __DIR__ . '/config/Database.php'; 
require_once __DIR__ . '/controllers/PostController.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/AdminController.php';

$action = $_GET['action'] ?? 'posts';
$id = $_GET['id'] ?? null;

switch ($action) {
    
    // --- ZONA PÚBLICA ---
    case 'posts': 
        (new PostController())->index();
        break;

    case 'show_post':
        
        if ($id) {
            (new PostController())->show($id);
        } else {
            die("ID de post no proporcionado");
        }
        break;

    case 'add_comment':
        (new PostController())->addComment();
        break;

    case 'rag_ask':
        (new PostController())->ragAsk();
        break;

    case 'rag_answer':
        (new PostController())->ragAnswer();
        break;

    // --- ZONA USUARIOS (AUTH) ---
    case 'register':
        (new UserController())->register();
        break;

    case 'login':
        (new UserController())->login();
        break;

    case 'logout':
        session_destroy();
        header("Location: index.php?action=login");
        exit();
        break;

    
    case 'admin_create_post': 
        (new AdminController())->create();
        break;

    case 'admin_store_post': 
        (new AdminController())->store(); 
        break;

   
    case 'admin_dashboard':
        (new AdminController())->dashboard();
        break;

    case 'admin_posts':
        (new AdminController())->posts();
        break;

    case 'admin_edit_post': 
        (new AdminController())->edit();
        break;

    case 'admin_update_post': 
        (new AdminController())->update();
        break;

    case 'admin_delete_post':
        (new AdminController())->delete();
        break;

    case 'admin_toggle_post_status':
        (new AdminController())->togglePostStatus();
        break;
    
    case 'admin_users': 
        (new AdminController())->users();
        break;

    case 'admin_delete_user': 
        (new AdminController())->deleteUser();
        break;

    case 'admin_comments':
        (new AdminController())->comments();
        break;

    case 'admin_delete_comment':
        (new AdminController())->deleteComment();
        break;

    default:
        (new PostController())->index();
        break;
}
?>