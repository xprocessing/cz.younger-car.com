<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class OrderOtherCosts {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取单个订单其他费用记录
    public function getById($id) {
        $sql = "SELECT * FROM order_other_costs WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 获取所有订单其他费用记录，支持分页
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM order_other_costs ORDER BY cost_date DESC, order_id";
        $params = [];
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取订单其他费用记录总数
    public function getCount() {
        $sql = "SELECT COUNT(*) as total FROM order_other_costs";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // 创建订单其他费用记录
    public function create($data) {
        $sql = "INSERT INTO order_other_costs (cost_date, order_id, platform_name, store_name, cost_type, cost, remark, create_at, update_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $params = [
            $data['cost_date'],
            $data['order_id'],
            $data['platform_name'],
            $data['store_name'],
            $data['cost_type'],
            $data['cost'],
            $data['remark'] ?? null
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 更新订单其他费用记录
    public function update($id, $data) {
        $sql = "UPDATE order_other_costs SET cost_date = ?, order_id = ?, platform_name = ?, store_name = ?, cost_type = ?, cost = ?, remark = ?, update_at = NOW() WHERE id = ?";
        $params = [
            $data['cost_date'],
            $data['order_id'],
            $data['platform_name'],
            $data['store_name'],
            $data['cost_type'],
            $data['cost'],
            $data['remark'] ?? null,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 删除订单其他费用记录
    public function delete($id) {
        $sql = "DELETE FROM order_other_costs WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    // 根据筛选条件搜索订单其他费用记录
    public function searchWithFilters($platformName, $storeName, $costType, $orderId, $startDate, $endDate, $limit, $offset) {
        $sql = "SELECT * FROM order_other_costs WHERE 1=1";
        $params = [];
        
        if (!empty($platformName)) {
            $sql .= " AND platform_name = ?";
            $params[] = $platformName;
        }
        
        if (!empty($storeName)) {
            $sql .= " AND store_name = ?";
            $params[] = $storeName;
        }
        
        if (!empty($costType)) {
            $sql .= " AND cost_type = ?";
            $params[] = $costType;
        }
        
        if (!empty($orderId)) {
            $sql .= " AND order_id = ?";
            $params[] = $orderId;
        }
        
        if (!empty($startDate)) {
            $sql .= " AND cost_date >= ?";
            $params[] = $startDate;
        }
        
        if (!empty($endDate)) {
            $sql .= " AND cost_date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY cost_date DESC, order_id";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取筛选条件下的订单其他费用记录总数
    public function getSearchWithFiltersCount($platformName, $storeName, $costType, $orderId, $startDate, $endDate) {
        $sql = "SELECT COUNT(*) as total FROM order_other_costs WHERE 1=1";
        $params = [];
        
        if (!empty($platformName)) {
            $sql .= " AND platform_name = ?";
            $params[] = $platformName;
        }
        
        if (!empty($storeName)) {
            $sql .= " AND store_name = ?";
            $params[] = $storeName;
        }
        
        if (!empty($costType)) {
            $sql .= " AND cost_type = ?";
            $params[] = $costType;
        }
        
        if (!empty($orderId)) {
            $sql .= " AND order_id = ?";
            $params[] = $orderId;
        }
        
        if (!empty($startDate)) {
            $sql .= " AND cost_date >= ?";
            $params[] = $startDate;
        }
        
        if (!empty($endDate)) {
            $sql .= " AND cost_date <= ?";
            $params[] = $endDate;
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // 根据筛选条件获取所有订单其他费用记录（用于导出）
    public function getAllWithFilters($platformName = '', $storeName = '', $costType = '', $orderId = '', $startDate = '', $endDate = '') {
        $sql = "SELECT * FROM order_other_costs WHERE 1=1";
        $params = [];
        
        if (!empty($platformName)) {
            $sql .= " AND platform_name = ?";
            $params[] = $platformName;
        }
        
        if (!empty($storeName)) {
            $sql .= " AND store_name = ?";
            $params[] = $storeName;
        }
        
        if (!empty($costType)) {
            $sql .= " AND cost_type = ?";
            $params[] = $costType;
        }
        
        if (!empty($orderId)) {
            $sql .= " AND order_id = ?";
            $params[] = $orderId;
        }
        
        if (!empty($startDate)) {
            $sql .= " AND cost_date >= ?";
            $params[] = $startDate;
        }
        
        if (!empty($endDate)) {
            $sql .= " AND cost_date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY cost_date DESC, order_id";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 批量插入订单其他费用记录
    public function batchInsert($data) {
        if (empty($data)) return true;
        
        $sql = "INSERT INTO order_other_costs (cost_date, order_id, platform_name, store_name, cost_type, cost, remark, create_at, update_at) VALUES ";
        $params = [];
        
        foreach ($data as $item) {
            $sql .= "(?, ?, ?, ?, ?, ?, ?, NOW(), NOW()),";
            $params[] = $item['cost_date'];
            $params[] = $item['order_id'];
            $params[] = $item['platform_name'];
            $params[] = $item['store_name'];
            $params[] = $item['cost_type'];
            $params[] = $item['cost'];
            $params[] = $item['remark'] ?? null;
        }
        
        $sql = rtrim($sql, ',');
        return $this->db->query($sql, $params);
    }
    
    // 获取平台名称列表
    public function getPlatformList() {
        $sql = "SELECT DISTINCT platform_name FROM order_other_costs ORDER BY platform_name";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        
        $platforms = [];
        foreach ($result as $row) {
            $platforms[] = $row['platform_name'];
        }
        
        return $platforms;
    }
    
    // 获取店铺名称列表
    public function getStoreList() {
        $sql = "SELECT DISTINCT store_name FROM order_other_costs ORDER BY store_name";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        
        $stores = [];
        foreach ($result as $row) {
            $stores[] = $row['store_name'];
        }
        
        return $stores;
    }
    
    // 获取费用类型列表
    public function getCostTypeList() {
        $sql = "SELECT DISTINCT cost_type FROM order_other_costs ORDER BY cost_type";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        
        $costTypes = [];
        foreach ($result as $row) {
            $costTypes[] = $row['cost_type'];
        }
        
        return $costTypes;
    }
}