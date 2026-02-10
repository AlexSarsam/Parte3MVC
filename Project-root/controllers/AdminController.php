<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/User.php'; // ¡Importante!
require_once __DIR__ . '/../utils/Security.php';

class AdminController {

    private $postModel;
    private $userModel; // Variable nueva

    public function __construct() {
        $this->postModel = new Post();
        $this->userModel = new User(); // Inicializamos modelo usuario
    }

    private function requireAdmin() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php?action=login");
            exit();
        }
    }

    private function requireCsrfToken($token, $redirectAction) {
        if (!Security::validateCsrfToken($token)) {
            $_SESSION['error'] = 'Token CSRF invalido.';
            header("Location: index.php?action=$redirectAction");
            exit();
        }
    }

    private function validateAndUploadImage($file) {
        if (!isset($file) || $file['error'] !== 0) {
            return null;
        }

        // Validar tamaño (máximo 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($file['size'] > $maxSize) {
            $_SESSION['error'] = "La imagen no puede superar los 5MB.";
            return false;
        }

        // Validar tipo de archivo
        $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($file['tmp_name']);
        
        if (!in_array($fileType, $allowedTypes)) {
            $_SESSION['error'] = "Solo se permiten imágenes JPG, PNG o GIF.";
            return false;
        }

        // Validar extensión
        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
            $_SESSION['error'] = "Extensión de archivo no permitida.";
            return false;
        }

        // Generar nombre único y seguro
        $fileName = uniqid() . '_' . time() . '.' . $extension;
        $uploadDir = __DIR__ . '/../uploads/';
        
        // Crear directorio si no existe
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        if (move_uploaded_file($file['tmp_name'], $uploadDir . $fileName)) {
            return $fileName;
        }

        return false;
    }

    // --- DASHBOARD ---
    public function dashboard() {
        $this->requireAdmin();
        include __DIR__ . '/../views/admin/dashboard.php';
    }

    // --- GESTIÓN DE POSTS (Lo que ya tenías) ---
    public function posts() {
        $this->requireAdmin();
        $posts = $this->postModel->all();
        include __DIR__ . '/../views/admin/post.php';
    }

    public function create() {
        $this->requireAdmin();
        include __DIR__ . '/../views/admin/create.php';
    }

    public function store() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrfToken($_POST['csrf_token'] ?? null, 'admin_create_post');
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $status = $_POST['status'] ?? 'published';
            $userId = $_SESSION['user_id'];

            // Validaciones
            if (empty($title) || empty($content)) {
                $_SESSION['error'] = "El título y el contenido son obligatorios.";
                header("Location: index.php?action=admin_create_post");
                exit();
            }

            if (strlen($title) < 5) {
                $_SESSION['error'] = "El título debe tener al menos 5 caracteres.";
                header("Location: index.php?action=admin_create_post");
                exit();
            }

            if (strlen($content) < 10) {
                $_SESSION['error'] = "El contenido debe tener al menos 10 caracteres.";
                header("Location: index.php?action=admin_create_post");
                exit();
            }

            // Procesar imagen
            $imagePath = $this->validateAndUploadImage($_FILES['image'] ?? null);
            
            if ($imagePath === false) {
                // Error en la imagen, ya se estableció el mensaje
                header("Location: index.php?action=admin_create_post");
                exit();
            }

            $postId = $this->postModel->create($title, $content, $userId, $status, $imagePath);
            if ($postId) {
                // Disparar webhook si el post es publicado
                if ($status === 'published') {
                    $this->enviarAWebhook($postId, $title, $content, $imagePath);
                }
                $_SESSION['success'] = "Post creado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al crear el post.";
            }
            
            header("Location: index.php?action=admin_posts");
            exit();
        }
    }

    public function edit() {
        $this->requireAdmin();
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$id) {
            $_SESSION['error'] = "ID de post no valido.";
            header("Location: index.php?action=admin_posts");
            exit();
        }
        $post = $this->postModel->find($id);
        include __DIR__ . '/../views/admin/edit.php';
    }

    public function update() {
        $this->requireAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->requireCsrfToken($_POST['csrf_token'] ?? null, 'admin_posts');
            $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
            $title = trim($_POST['title'] ?? '');
            $content = trim($_POST['content'] ?? '');
            $status = $_POST['status'] ?? 'published';

            if (!$id) {
                $_SESSION['error'] = "ID de post no válido.";
                header("Location: index.php?action=admin_posts");
                exit();
            }

            // Validaciones
            if (empty($title) || empty($content)) {
                $_SESSION['error'] = "El título y el contenido son obligatorios.";
                header("Location: index.php?action=admin_edit_post&id=$id");
                exit();
            }

            if (strlen($title) < 5) {
                $_SESSION['error'] = "El título debe tener al menos 5 caracteres.";
                header("Location: index.php?action=admin_edit_post&id=$id");
                exit();
            }

            if (strlen($content) < 10) {
                $_SESSION['error'] = "El contenido debe tener al menos 10 caracteres.";
                header("Location: index.php?action=admin_edit_post&id=$id");
                exit();
            }

            // Procesar imagen si se subió
            $imagePath = $this->validateAndUploadImage($_FILES['image'] ?? null);
            
            if ($imagePath === false) {
                // Error en la imagen
                header("Location: index.php?action=admin_edit_post&id=$id");
                exit();
            }

            $this->postModel->update($id, $title, $content, $status, $imagePath);
            $_SESSION['success'] = "Post actualizado exitosamente.";
            
            header("Location: index.php?action=admin_posts");
            exit();
        }
    }

    public function delete() {
        $this->requireAdmin();
        $this->requireCsrfToken($_GET['csrf_token'] ?? null, 'admin_posts');
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        if ($id) {
            try {
                if ($this->postModel->delete($id)) {
                    $_SESSION['success'] = "Post eliminado exitosamente.";
                } else {
                    $_SESSION['error'] = "Error al eliminar el post.";
                }
            } catch (\Exception $e) {
                $_SESSION['error'] = "Error al eliminar: " . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = "ID de post no válido.";
        }
        header("Location: index.php?action=admin_posts");
        exit();
    }

    public function togglePostStatus() {
        $this->requireAdmin();
        $this->requireCsrfToken($_GET['csrf_token'] ?? null, 'admin_posts');
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        $status = $_GET['status'] ?? null;

        if (!$id || !in_array($status, ['published', 'draft'], true)) {
            $_SESSION['error'] = "Parámetros inválidos para cambiar estado.";
            header("Location: index.php?action=admin_posts");
            exit();
        }

        if ($this->postModel->setStatus($id, $status)) {
            $_SESSION['success'] = "Estado del post actualizado.";
        } else {
            $_SESSION['error'] = "No se pudo actualizar el estado del post.";
        }

        header("Location: index.php?action=admin_posts");
        exit();
    }


    
    public function users() {
        $this->requireAdmin();
        $users = $this->userModel->all(); // Pedimos todos los usuarios
        include __DIR__ . '/../views/admin/users.php';
    }

    public function deleteUser() {
        $this->requireAdmin();
        $this->requireCsrfToken($_GET['csrf_token'] ?? null, 'admin_users');
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        
        if ($id) {
            if ($this->userModel->delete($id)) {
                $_SESSION['success'] = "Usuario eliminado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al eliminar el usuario.";
            }
        }
        header("Location: index.php?action=admin_users");
        exit();
    }

    // --- FUNCIÓN DEL WEBHOOK ---
    private function enviarAWebhook($postId, $titulo, $contenido, $imagen = null) {
        require_once __DIR__ . '/../config/Webhook.php';
        if (WEBHOOK_URL === '') {
            return;
        }

        $excerpt = strip_tags($contenido);
        $excerpt = preg_replace('/\s+/', ' ', $excerpt);
        $excerpt = substr(trim($excerpt), 0, 160) . '...';
        $postUrl = rtrim(APP_BASE_URL, '/') . '/index.php?action=show_post&id=' . $postId;
        $imageUrl = $imagen ? rtrim(APP_BASE_URL, '/') . '/uploads/' . $imagen : null;

        $datos = [
            'titulo' => $titulo,
            'resumen' => $excerpt,
            'url' => $postUrl,
            'imagen' => $imageUrl,
            'autor' => $_SESSION['username'] ?? 'Admin',
            'fecha' => date('Y-m-d H:i:s'),
            'html' => $this->buildWebhookHtml($titulo, $excerpt, $postUrl, $imageUrl)
        ];

        $ch = curl_init(WEBHOOK_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Webhook-Token: ' . WEBHOOK_SECRET
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_exec($ch);
        curl_close($ch);
    }

    private function buildWebhookHtml($titulo, $resumen, $url, $imageUrl = null) {
        $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
        $resumen = htmlspecialchars($resumen, ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        $imgHtml = '';
        if ($imageUrl) {
            $imageUrl = htmlspecialchars($imageUrl, ENT_QUOTES, 'UTF-8');
            $imgHtml = '<img src="' . $imageUrl . '" alt="' . $titulo . '" style="max-width:100%;border-radius:8px;margin-bottom:12px;">';
        }

        return '<div style="font-family: Arial, sans-serif; color:#1f2937;">'
            . $imgHtml
            . '<h2 style="margin:0 0 8px 0;">' . $titulo . '</h2>'
            . '<p style="margin:0 0 12px 0; color:#4b5563;">' . $resumen . '</p>'
            . '<a href="' . $url . '" style="display:inline-block; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:6px;">Ver noticia</a>'
            . '</div>';
    }

    // --- GESTIÓN DE COMENTARIOS ---
    public function comments() {
        $this->requireAdmin();
        require_once __DIR__ . '/../models/Comment.php';
        $commentModel = new Comment();
        
        // Obtener todos los comentarios con información del usuario y post
        $query = "SELECT c.*, u.username, p.title as post_title 
                  FROM comments c 
                  INNER JOIN users u ON c.user_id = u.id 
                  INNER JOIN posts p ON c.post_id = p.id 
                  ORDER BY c.created_at DESC";
        
        $db = (new Database())->getConnection();
        $stmt = $db->query($query);
        $comments = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../views/admin/comments.php';
    }

    public function deleteComment() {
        $this->requireAdmin();
        $this->requireCsrfToken($_GET['csrf_token'] ?? null, 'admin_comments');
        $id = filter_var($_GET['id'] ?? null, FILTER_VALIDATE_INT);
        
        if ($id) {
            require_once __DIR__ . '/../models/Comment.php';
            $commentModel = new Comment();
            
            if ($commentModel->delete($id)) {
                $_SESSION['success'] = "Comentario eliminado exitosamente.";
            } else {
                $_SESSION['error'] = "Error al eliminar el comentario.";
            }
        }
        header("Location: index.php?action=admin_comments");
        exit();
    }
}
?>