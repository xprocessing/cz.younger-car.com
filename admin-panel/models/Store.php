<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class Store {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取店铺信息
    public function getById($storeId) {
        $sql = "SELECT * FROM store WHERE store_id = ?";
        $stmt = $this->db->query($sql, [$storeId]);
        return $stmt->fetch();
    }
    
    // 获取所有店铺信息
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM store ORDER BY store_name ASC";
        $params = [];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取店铺总数
    public function getCount() {
        $sql = "SELECT COUNT(*) FROM store";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
    
    // 创建店铺
    public function create($data) {
        $sql = "INSERT INTO store (store_id, sid, store_name, platform_code, platform_name, currency, is_sync, status, country_code, track_name) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['store_id'],
            $data['sid'] ?? null,
            $data['store_name'],
            $data['platform_code'],
            $data['platform_name'],
            $data['currency'],
            $data['is_sync'],
            $data['status'],
            $data['country_code'] ?? null,
            $data['track_name'] ?? null
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 更新店铺
    public function update($storeId, $data) {
        $sql = "UPDATE store SET sid = ?, store_name = ?, platform_code = ?, platform_name = ?, currency = ?, is_sync = ?, status = ?, country_code = ?, track_name = ? 
               WHERE store_id = ?";
        
        $params = [
            $data['sid'] ?? null,
            $data['store_name'],
            $data['platform_code'],
            $data['platform_name'],
            $data['currency'],
            $data['is_sync'],
            $data['status'],
            $data['country_code'] ?? null,
            $data['track_name'] ?? null,
            $storeId
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 删除店铺
    public function delete($storeId) {
        $sql = "DELETE FROM store WHERE store_id = ?";
        return $this->db->query($sql, [$storeId]);
    }
    
    // 搜索店铺
    public function search($keyword, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM store WHERE store_id LIKE ? OR sid LIKE ? OR store_name LIKE ? OR platform_name LIKE ? ORDER BY store_name ASC";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取搜索结果数量
    public function getSearchCount($keyword) {
        $sql = "SELECT COUNT(*) FROM store WHERE store_id LIKE ? OR sid LIKE ? OR store_name LIKE ? OR platform_name LIKE ?";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchColumn();
    }
}
