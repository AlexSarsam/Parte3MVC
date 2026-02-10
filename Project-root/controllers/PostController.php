<?php

require_once __DIR__ . '/../models/Post.php';
require_once __DIR__ . '/../models/Comment.php';
require_once __DIR__ . '/../utils/Security.php';
require_once __DIR__ . '/../config/Webhook.php';

class PostController {
    
    private $postModel;
    private $commentModel;

    public function __construct() {
        $this->postModel = new Post();
        $this->commentModel = new Comment();
    }

    public function index() {
        $posts = $this->postModel->allPublished();
        include __DIR__ . '/../views/posts/index.php';
    }

    public function show($id) {
        $postId = filter_var($id, FILTER_VALIDATE_INT);
        if (!$postId) {
            die("ID de post no valido");
        }

        $post = $this->postModel->find($postId);
        
        if (!$post) {
            die("Post no encontrado");
        }

        $isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
        if (!$isAdmin && ($post['status'] ?? 'published') !== 'published') {
            die("Post no disponible");
        }
        
      
        $relatedPosts = $this->postModel->getRelated($postId, $post['title']);

        $comments = $this->commentModel->getByPostId($postId);


        include __DIR__ . '/../views/posts/show.php';
    }

    public function addComment() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $_SESSION['error'] = 'Token CSRF invalido.';
                header("Location: index.php");
                exit();
            }

            if (!isset($_SESSION['user_id'])) {
                $_SESSION['error'] = 'Debes iniciar sesion para comentar.';
                header("Location: index.php?action=login");
                exit();
            }

            $post_id = filter_var($_POST['post_id'] ?? null, FILTER_VALIDATE_INT);
            $user_id = $_SESSION['user_id'] ?? null;
            $content = Security::normalizeText($_POST['content'] ?? '', 1000);
            $imagePath = null;

            if (!$post_id || !$user_id || empty($content)) {
                $_SESSION['error'] = 'El comentario no puede estar vacío.';
                $redirectId = $post_id ? $post_id : '';
                header("Location: index.php?action=show_post&id=$redirectId");
                exit();
            }
        
            if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                $maxSize = 5 * 1024 * 1024;
                if ($_FILES['image']['size'] > $maxSize) {
                    $_SESSION['error'] = 'La imagen no puede superar los 5MB.';
                    header("Location: index.php?action=show_post&id=$post_id");
                    exit();
                }

                $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                $fileType = mime_content_type($_FILES['image']['tmp_name']);
                
                if (!in_array($fileType, $allowedTypes)) {
                    $_SESSION['error'] = 'Solo se permiten imágenes JPG, PNG o GIF.';
                    header("Location: index.php?action=show_post&id=$post_id");
                    exit();
                }

                $extension = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
                $uploadDir = __DIR__ . '/../uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $fileName = uniqid() . '_' . time() . '.' . $extension;
                if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadDir . $fileName)) {
                    $imagePath = $fileName;
                }
            }

            if ($this->commentModel->save($post_id, $user_id, $content, $imagePath)) {
                $_SESSION['success'] = 'Comentario agregado exitosamente';
            } else {
                $_SESSION['error'] = 'Error al agregar el comentario';
            }

            header("Location: index.php?action=show_post&id=$post_id");
            exit();
        }
    }

  
    public function create() {
        if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
            header("Location: index.php");
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrfToken($_POST['csrf_token'] ?? null)) {
                $_SESSION['error'] = 'Token CSRF invalido.';
                header("Location: index.php?action=create");
                exit();
            }

            $title = $_POST['title'] ?? '';
            $content = $_POST['content'] ?? '';
            $status = $_POST['status'] ?? 'published';
            $user_id = $_SESSION['user_id'];

            $title = Security::normalizeText($title, 200);
            $content = Security::normalizeText($content, 5000);

            if (strlen($title) < 5 || strlen($content) < 10) {
                $_SESSION['error'] = "El titulo o el contenido es demasiado corto.";
                header("Location: index.php?action=create");
                exit;
            }

            if (!in_array($status, ['published', 'draft'], true)) {
                $_SESSION['error'] = "Estado no valido.";
                header("Location: index.php?action=create");
                exit;
            }

            // Guardamos el post y disparamos el Webhook
            $postId = $this->postModel->create($title, $content, $user_id, $status);
            if ($postId) {
                if ($status === 'published') {
                    $this->enviarAWebhook($postId, $title, $content);
                    $_SESSION['success'] = "Noticia creada y enviada al sistema automatico.";
                } else {
                    $_SESSION['success'] = "Noticia creada en borrador.";
                }
                header("Location: index.php");
                exit;
            } else {
                $_SESSION['error'] = "Error al guardar la noticia.";
            }
        }

        include __DIR__ . '/../views/admin/create.php';
    }

    // --- FUNCIÓN DEL WEBHOOK ---
    private function enviarAWebhook($postId, $titulo, $contenido) {
        if (WEBHOOK_URL === '') {
            return;
        }

        $excerpt = $this->buildExcerpt($contenido);
        $postUrl = rtrim(APP_BASE_URL, '/') . '/index.php?action=show_post&id=' . $postId;

        $datos = [
            'titulo' => $titulo,
            'resumen' => $excerpt,
            'url' => $postUrl,
            'autor' => $_SESSION['username'] ?? 'Admin',
            'fecha' => date('Y-m-d H:i:s'),
            'html' => $this->buildWebhookHtml($titulo, $excerpt, $postUrl)
        ];

        $ch = curl_init(WEBHOOK_URL);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'X-Webhook-Token: ' . WEBHOOK_SECRET
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 2);
        curl_exec($ch);
        curl_close($ch);
    }

    private function buildExcerpt($contenido) {
        $clean = strip_tags($contenido);
        $clean = preg_replace('/\s+/', ' ', $clean);
        return substr(trim($clean), 0, 160) . '...';
    }

    private function buildWebhookHtml($titulo, $resumen, $url) {
        $titulo = htmlspecialchars($titulo, ENT_QUOTES, 'UTF-8');
        $resumen = htmlspecialchars($resumen, ENT_QUOTES, 'UTF-8');
        $url = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');

        return '<div style="font-family: Arial, sans-serif; color:#1f2937;">'
            . '<h2 style="margin:0 0 8px 0;">' . $titulo . '</h2>'
            . '<p style="margin:0 0 12px 0; color:#4b5563;">' . $resumen . '</p>'
            . '<a href="' . $url . '" style="display:inline-block; padding:10px 16px; background:#2563eb; color:#fff; text-decoration:none; border-radius:6px;">Ver noticia</a>'
            . '</div>';
    }

    public function ragAsk() {
        include __DIR__ . '/../views/rag/ask.php';
    }

    public function ragAnswer() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: index.php?action=rag_ask');
            exit();
        }

        if (!Security::validateCsrfToken($_POST['csrf_token'] ?? null)) {
            $_SESSION['error'] = 'Token CSRF invalido.';
            header('Location: index.php?action=rag_ask');
            exit();
        }

        $query = Security::normalizeText($_POST['q'] ?? '', 200);
        $results = [];
        if ($query !== '') {
            $results = $this->postModel->searchByQuery($query, 5);
        }

        include __DIR__ . '/../views/rag/answer.php';
    }
}