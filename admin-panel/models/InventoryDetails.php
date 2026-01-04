<?php
require_once APP_ROOT . '/includes/database.php';

class InventoryDetails {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM inventory_details WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT id, wid, sku, product_valid_num, quantity_receive, average_age, 
                       purchase_price, head_stock_price, stock_price 
                FROM inventory_details 
                ORDER BY id DESC";
        $params = [];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getCount() {
        $sql = "SELECT COUNT(*) FROM inventory_details";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
    
    public function create($data) {
        $sql = "INSERT INTO inventory_details (wid, sku, product_valid_num, quantity_receive, average_age, 
                purchase_price, head_stock_price, stock_price) 
               VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['wid'],
            $data['sku'],
            $data['product_valid_num'],
            $data['quantity_receive'],
            $data['average_age'],
            $data['purchase_price'],
            $data['head_stock_price'],
            $data['stock_price']
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE inventory_details SET 
                wid = ?, 
                sku = ?, 
                product_valid_num = ?, 
                quantity_receive = ?, 
                average_age = ?, 
                purchase_price = ?, 
                head_stock_price = ?, 
                stock_price = ? 
                WHERE id = ?";
        
        $params = [
            $data['wid'],
            $data['sku'],
            $data['product_valid_num'],
            $data['quantity_receive'],
            $data['average_age'],
            $data['purchase_price'],
            $data['head_stock_price'],
            $data['stock_price'],
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM inventory_details WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function search($keyword, $limit = null, $offset = 0) {
        $sql = "SELECT id, wid, sku, product_valid_num, quantity_receive, average_age, 
                       purchase_price, head_stock_price, stock_price 
                FROM inventory_details 
                WHERE sku LIKE ? 
                ORDER BY id DESC";
        $params = ["%$keyword%"];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getSearchCount($keyword) {
        $sql = "SELECT COUNT(*) FROM inventory_details WHERE sku LIKE ?";
        $stmt = $this->db->query($sql, ["%$keyword%"]);
        return $stmt->fetchColumn();
    }
    
    public function getByWid($wid, $limit = null, $offset = 0) {
        $sql = "SELECT id, wid, sku, product_valid_num, quantity_receive, average_age, 
                       purchase_price, head_stock_price, stock_price 
                FROM inventory_details 
                WHERE wid = ?
                ORDER BY id DESC";
        $params = [$wid];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getCountByWid($wid) {
        $sql = "SELECT COUNT(*) FROM inventory_details WHERE wid = ?";
        $stmt = $this->db->query($sql, [$wid]);
        return $stmt->fetchColumn();
    }
}
