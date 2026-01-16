<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class ShopCosts {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取单个成本记录
    public function getById($id) {
        $sql = "SELECT * FROM shop_costs WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 获取所有成本记录，支持分页
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM shop_costs ORDER BY cost_date DESC, platform_name, store_name";
        $params = [];
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取成本记录总数
    public function getCount() {
        $sql = "SELECT COUNT(*) as total FROM shop_costs";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // 创建成本记录
    public function create($data) {
        $sql = "INSERT INTO shop_costs (platform_name, store_name, cost, cost_type, cost_date, remark, create_at, update_at) VALUES (?, ?, ?, ?, ?, ?, NOW(), NOW())";
        $params = [
            $data['platform_name'],
            $data['store_name'],
            $data['cost'],
            $data['cost_type'],
            $data['cost_date'],
            $data['remark'] ?? null
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 更新成本记录
    public function update($id, $data) {
        $sql = "UPDATE shop_costs SET platform_name = ?, store_name = ?, cost = ?, cost_type = ?, cost_date = ?, remark = ?, update_at = NOW() WHERE id = ?";
        $params = [
            $data['platform_name'],
            $data['store_name'],
            $data['cost'],
            $data['cost_type'],
            $data['cost_date'],
            $data['remark'] ?? null,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 删除成本记录
    public function delete($id) {
        $sql = "DELETE FROM shop_costs WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    // 根据筛选条件搜索成本记录
    public function searchWithFilters($platformName, $storeName, $costType, $startDate, $endDate, $limit, $offset) {
        $sql = "SELECT * FROM shop_costs WHERE 1=1";
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
        
        if (!empty($startDate)) {
            $sql .= " AND cost_date >= ?";
            $params[] = $startDate;
        }
        
        if (!empty($endDate)) {
            $sql .= " AND cost_date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY cost_date DESC, platform_name, store_name LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取筛选条件下的成本记录总数
    public function getSearchWithFiltersCount($platformName, $storeName, $costType, $startDate, $endDate) {
        $sql = "SELECT COUNT(*) as total FROM shop_costs WHERE 1=1";
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
    
    // 获取所有符合筛选条件的成本记录（用于导出）
    public function getAllWithFilters($platformName, $storeName, $costType, $startDate, $endDate) {
        $sql = "SELECT * FROM shop_costs WHERE 1=1";
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
        
        if (!empty($startDate)) {
            $sql .= " AND date >= ?";
            $params[] = $startDate;
        }
        
        if (!empty($endDate)) {
            $sql .= " AND date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY cost_date DESC, platform_name, store_name";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 批量插入成本记录（用于导入）
    public function batchInsert($data) {
        if (empty($data)) {
            return true;
        }
        
        $sql = "INSERT INTO shop_costs (platform_name, store_name, cost, cost_type, cost_date, remark, create_at, update_at) VALUES ";
        $params = [];
        $values = [];
        
        foreach ($data as $row) {
            $values[] = "(?, ?, ?, ?, ?, ?, NOW(), NOW())";
            $params[] = $row['platform_name'];
            $params[] = $row['store_name'];
            $params[] = $row['cost'];
            $params[] = $row['cost_type'];
            $params[] = $row['cost_date'];
            $params[] = $row['remark'] ?? null;
        }
        
        $sql .= implode(", ", $values);
        
        return $this->db->query($sql, $params);
    }
    
    // 获取平台列表（用于筛选）
    public function getPlatformList() {
        $sql = "SELECT DISTINCT platform_name FROM shop_costs ORDER BY platform_name";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        return array_column($result, 'platform_name');
    }
    
    // 获取店铺列表（用于筛选）
    public function getStoreList() {
        $sql = "SELECT DISTINCT store_name FROM shop_costs ORDER BY store_name";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        return array_column($result, 'store_name');
    }
    
    // 获取费用类型列表（用于筛选）
    public function getCostTypeList() {
        $sql = "SELECT DISTINCT cost_type FROM shop_costs ORDER BY cost_type";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        return array_column($result, 'cost_type');
    }
}