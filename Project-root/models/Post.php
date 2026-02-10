<?php

require_once __DIR__ . '/../config/Database.php';

class Post {
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
    }


    public function all() {
        $stmt = $this->db->query("SELECT * FROM posts ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function allPublished() {
        $stmt = $this->db->query("SELECT * FROM posts WHERE status = 'published' ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    
    public function find($id) {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    
    public function create($title, $content, $userId, $status = 'published', $image = null) {
        $sql = "INSERT INTO posts (title, content, user_id, status, image) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$title, $content, $userId, $status, $image]);
        return $this->db->lastInsertId();
    }

    public function update($id, $title, $content, $status = 'published', $image = null) {
        if ($image) {
            
            $sql = "UPDATE posts SET title = ?, content = ?, status = ?, image = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$title, $content, $status, $image, $id]);
        } else {
            
            $sql = "UPDATE posts SET title = ?, content = ?, status = ? WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$title, $content, $status, $id]);
        }
    }

    public function setStatus($id, $status) {
        $stmt = $this->db->prepare("UPDATE posts SET status = ? WHERE id = ?");
        return $stmt->execute([$status, $id]);
    }


    public function delete($id) {
        // Borrar comentarios asociados primero (foreign key constraint)
        $stmt = $this->db->prepare("DELETE FROM comments WHERE post_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ?");
        return $stmt->execute([$id]);
    }


    public function getRelated($id, $title) {
        return $this->searchByQuery($title, 3, $id);
    }

    public function searchByQuery($query, $limit = 5, $excludeId = null) {
        $keywords = $this->extractKeywords($query);
        if (empty($keywords)) {
            return [];
        }

        $conditions = [];
        $params = [];

        foreach ($keywords as $word) {
            $conditions[] = '(title LIKE ? OR content LIKE ?)';
            $term = '%' . $word . '%';
            $params[] = $term;
            $params[] = $term;
        }

        $sql = "SELECT * FROM posts WHERE (" . implode(' OR ', $conditions) . ")";

        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }

        $limit = (int) $limit;
        $sql .= " AND status = 'published' ORDER BY created_at DESC LIMIT " . $limit;

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    private function extractKeywords($query) {
        $query = strtolower($query);
        $query = preg_replace('/[^a-z0-9\s]/i', ' ', $query);
        $parts = preg_split('/\s+/', $query, -1, PREG_SPLIT_NO_EMPTY);
        $keywords = [];

        foreach ($parts as $part) {
            if (strlen($part) >= 3) {
                $keywords[] = $part;
            }
        }

        return array_values(array_unique($keywords));
    }
}
?>