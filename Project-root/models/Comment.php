<?php

require_once __DIR__ . '/../config/Database.php';

class Comment {
    
    private $conn;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getByPostId($post_id) {
        $query = "SELECT c.*, u.username, u.email 
                  FROM comments c 
                  INNER JOIN users u ON c.user_id = u.id 
                  WHERE c.post_id = ? 
                  ORDER BY c.created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$post_id]);
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function save($post_id, $user_id, $content, $image = null) {
        $query = "INSERT INTO comments (post_id, user_id, content, image) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$post_id, $user_id, $content, $image]);
    }

    public function delete($id) {
        $stmt = $this->conn->prepare("DELETE FROM comments WHERE id = ?");
        return $stmt->execute([$id]);
    }
}