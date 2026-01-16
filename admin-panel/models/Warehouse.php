<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class Warehouse {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getById($wid) {
        $sql = "SELECT * FROM warehouses WHERE wid = ?";
        $stmt = $this->db->query($sql, [$wid]);
        return $stmt->fetch();
    }
    
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM warehouses WHERE is_delete = 0 ORDER BY wid DESC";
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
        $sql = "SELECT COUNT(*) FROM warehouses WHERE is_delete = 0";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
    
    public function create($data) {
        $sql = "INSERT INTO warehouses (wid, type, sub_type, name, is_delete, country_code, wp_id, wp_name, t_warehouse_name, t_warehouse_code, t_country_area_name, t_status) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['wid'],
            $data['type'],
            $data['sub_type'],
            $data['name'],
            $data['is_delete'] ?? 0,
            $data['country_code'] ?? null,
            $data['wp_id'] ?? null,
            $data['wp_name'] ?? null,
            $data['t_warehouse_name'] ?? null,
            $data['t_warehouse_code'] ?? null,
            $data['t_country_area_name'] ?? null,
            $data['t_status'] ?? null
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function update($wid, $data) {
        $sql = "UPDATE warehouses SET 
                type = ?, 
                sub_type = ?, 
                name = ?, 
                is_delete = ?, 
                country_code = ?, 
                wp_id = ?, 
                wp_name = ?, 
                t_warehouse_name = ?, 
                t_warehouse_code = ?, 
                t_country_area_name = ?, 
                t_status = ? 
                WHERE wid = ?";
        
        $params = [
            $data['type'],
            $data['sub_type'],
            $data['name'],
            $data['is_delete'] ?? 0,
            $data['country_code'] ?? null,
            $data['wp_id'] ?? null,
            $data['wp_name'] ?? null,
            $data['t_warehouse_name'] ?? null,
            $data['t_warehouse_code'] ?? null,
            $data['t_country_area_name'] ?? null,
            $data['t_status'] ?? null,
            $wid
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($wid) {
        $sql = "UPDATE warehouses SET is_delete = 1 WHERE wid = ?";
        return $this->db->query($sql, [$wid]);
    }
    
    public function forceDelete($wid) {
        $sql = "DELETE FROM warehouses WHERE wid = ?";
        return $this->db->query($sql, [$wid]);
    }
    
    public function search($keyword, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM warehouses 
                WHERE is_delete = 0 
                AND (name LIKE ? OR t_warehouse_name LIKE ? OR t_warehouse_code LIKE ?)
                ORDER BY wid DESC";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getSearchCount($keyword) {
        $sql = "SELECT COUNT(*) FROM warehouses 
                WHERE is_delete = 0 
                AND (name LIKE ? OR t_warehouse_name LIKE ? OR t_warehouse_code LIKE ?)";
        $stmt = $this->db->query($sql, ["%$keyword%", "%$keyword%", "%$keyword%"]);
        return $stmt->fetchColumn();
    }
}
