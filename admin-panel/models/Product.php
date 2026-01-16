<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class Product {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取产品
    public function getById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 获取所有产品
    public function getAll($filters = []) {
        $sql = "SELECT * FROM products";
        $params = [];
        
        // 添加过滤条件
        if (!empty($filters)) {
            $whereClause = [];
            
            if (isset($filters['search'])) {
                $whereClause[] = "(name LIKE ? OR description LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (isset($filters['category'])) {
                $whereClause[] = "category = ?";
                $params[] = $filters['category'];
            }
            
            if (isset($filters['status'])) {
                $whereClause[] = "status = ?";
                $params[] = $filters['status'];
            }
            
            if (!empty($whereClause)) {
                $sql .= " WHERE " . implode(" AND ", $whereClause);
            }
        }
        
        $sql .= " ORDER BY id DESC";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取所有分类
    public function getAllCategories() {
        $sql = "SELECT DISTINCT category FROM products ORDER BY category";
        $stmt = $this->db->query($sql);
        $categories = $stmt->fetchAll();
        return array_column($categories, 'category');
    }
    
    // 创建产品
    public function create($data) {
        $sql = "INSERT INTO products (name, description, price, category, status) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['category'],
            $data['status'] ?? 'active'
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // 更新产品
    public function update($id, $data) {
        $sql = "UPDATE products SET name = ?, description = ?, price = ?, category = ?, status = ? WHERE id = ?";
        $params = [
            $data['name'],
            $data['description'] ?? '',
            $data['price'],
            $data['category'],
            $data['status'],
            $id
        ];
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
    
    // 删除产品
    public function delete($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }
}
?>