<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class TrackCosts {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取单个成本记录
    public function getById($id) {
        $sql = "SELECT * FROM track_costs WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 获取所有成本记录，支持分页
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM track_costs ORDER BY cost_date DESC, track_name";
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
        $sql = "SELECT COUNT(*) as total FROM track_costs";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // 创建成本记录
    public function create($data) {
        $sql = "INSERT INTO track_costs (track_name, cost, cost_type, cost_date, remark, create_at, update_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())";
        $params = [
            $data['track_name'],
            $data['cost'],
            $data['cost_type'],
            $data['cost_date'],
            $data['remark'] ?? null
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 更新成本记录
    public function update($id, $data) {
        $sql = "UPDATE track_costs SET track_name = ?, cost = ?, cost_type = ?, cost_date = ?, remark = ?, update_at = NOW() WHERE id = ?";
        $params = [
            $data['track_name'],
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
        $sql = "DELETE FROM track_costs WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    // 根据筛选条件搜索成本记录
    public function searchWithFilters($trackName, $costType, $startDate, $endDate, $limit, $offset) {
        $sql = "SELECT * FROM track_costs WHERE 1=1";
        $params = [];
        
        if (!empty($trackName)) {
            $sql .= " AND track_name = ?";
            $params[] = $trackName;
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
        
        $sql .= " ORDER BY cost_date DESC, track_name LIMIT ? OFFSET ?";
        $params[] = $limit;
        $params[] = $offset;
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取筛选条件下的成本记录总数
    public function getSearchWithFiltersCount($trackName, $costType, $startDate, $endDate) {
        $sql = "SELECT COUNT(*) as total FROM track_costs WHERE 1=1";
        $params = [];
        
        if (!empty($trackName)) {
            $sql .= " AND track_name = ?";
            $params[] = $trackName;
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
    public function getAllWithFilters($trackName, $costType, $startDate, $endDate) {
        $sql = "SELECT * FROM track_costs WHERE 1=1";
        $params = [];
        
        if (!empty($trackName)) {
            $sql .= " AND track_name = ?";
            $params[] = $trackName;
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
        
        $sql .= " ORDER BY cost_date DESC, track_name";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 批量插入成本记录（用于导入）
    public function batchInsert($data) {
        if (empty($data)) {
            return true;
        }
        
        $sql = "INSERT INTO track_costs (track_name, cost, cost_type, cost_date, remark, create_at, update_at) VALUES ";
        $params = [];
        $values = [];
        
        foreach ($data as $row) {
            $values[] = "(?, ?, ?, ?, ?, NOW(), NOW())";
            $params[] = $row['track_name'];
            $params[] = $row['cost'];
            $params[] = $row['cost_type'];
            $params[] = $row['cost_date'];
            $params[] = $row['remark'] ?? null;
        }
        
        $sql .= implode(", ", $values);
        
        return $this->db->query($sql, $params);
    }
    
    // 获取赛道列表
    public function getTrackList() {
        $sql = "SELECT DISTINCT track_name FROM track_costs ORDER BY track_name";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll();
        return array_column($results, 'track_name');
    }
    
    // 获取费用类型列表
    public function getCostTypeList() {
        $sql = "SELECT DISTINCT cost_type FROM track_costs ORDER BY cost_type";
        $stmt = $this->db->query($sql);
        $results = $stmt->fetchAll();
        return array_column($results, 'cost_type');
    }
    
    // 获取上个月按赛道名称的费用统计
    public function getLastMonthByTrack() {
        $sql = "SELECT track_name, SUM(cost) as total_cost FROM track_costs 
                WHERE cost_date >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') 
                AND cost_date < DATE_FORMAT(NOW(), '%Y-%m-01') 
                GROUP BY track_name 
                ORDER BY total_cost DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // 获取上个月按费用类型的费用统计
    public function getLastMonthByType() {
        $sql = "SELECT cost_type, SUM(cost) as total_cost FROM track_costs 
                WHERE cost_date >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') 
                AND cost_date < DATE_FORMAT(NOW(), '%Y-%m-01') 
                GROUP BY cost_type 
                ORDER BY total_cost DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
