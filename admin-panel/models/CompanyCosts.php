<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class CompanyCosts {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取单个公司费用记录
    public function getById($id) {
        $sql = "SELECT * FROM company_costs WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 获取所有公司费用记录，支持分页
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM company_costs ORDER BY cost_date DESC";
        $params = [];
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取公司费用记录总数
    public function getCount() {
        $sql = "SELECT COUNT(*) as total FROM company_costs";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // 创建公司费用记录
    public function create($data) {
        $sql = "INSERT INTO company_costs (cost_date, cost_type, cost, remark, create_at, update_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $params = [
            $data['cost_date'],
            $data['cost_type'],
            $data['cost'],
            $data['remark'] ?? null
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 更新公司费用记录
    public function update($id, $data) {
        $sql = "UPDATE company_costs SET cost_date = ?, cost_type = ?, cost = ?, remark = ?, update_at = NOW() WHERE id = ?";
        $params = [
            $data['cost_date'],
            $data['cost_type'],
            $data['cost'],
            $data['remark'] ?? null,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 删除公司费用记录
    public function delete($id) {
        $sql = "DELETE FROM company_costs WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    // 根据筛选条件搜索公司费用记录
    public function searchWithFilters($costType, $startDate, $endDate, $limit, $offset) {
        $sql = "SELECT * FROM company_costs WHERE 1=1";
        $params = [];
        
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
        
        $sql .= " ORDER BY cost_date DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取筛选条件下的公司费用记录总数
    public function getSearchWithFiltersCount($costType, $startDate, $endDate) {
        $sql = "SELECT COUNT(*) as total FROM company_costs WHERE 1=1";
        $params = [];
        
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
    
    // 根据筛选条件获取所有公司费用记录（用于导出）
    public function getAllWithFilters($costType = '', $startDate = '', $endDate = '') {
        $sql = "SELECT * FROM company_costs WHERE 1=1";
        $params = [];
        
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
        
        $sql .= " ORDER BY cost_date DESC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 批量插入公司费用记录
    public function batchInsert($data) {
        if (empty($data)) return true;
        
        $sql = "INSERT INTO company_costs (cost_date, cost_type, cost, remark, create_at, update_at) VALUES ";
        $params = [];
        
        foreach ($data as $item) {
            $sql .= "(?, ?, ?, ?, NOW(), NOW()),";
            $params[] = $item['cost_date'];
            $params[] = $item['cost_type'];
            $params[] = $item['cost'];
            $params[] = $item['remark'] ?? null;
        }
        
        $sql = rtrim($sql, ',');
        return $this->db->query($sql, $params);
    }
    
    // 获取费用类型列表
    public function getCostTypeList() {
        $sql = "SELECT DISTINCT cost_type FROM company_costs ORDER BY cost_type";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        
        $costTypes = [];
        foreach ($result as $row) {
            $costTypes[] = $row['cost_type'];
        }
        
        return $costTypes;
    }
    
    // 获取过去12个月的月度费用总额统计
    public function getMonthlyStatistics() {
        $sql = "SELECT 
                DATE_FORMAT(cost_date, '%Y-%m') as month, 
                SUM(cost) as total_cost 
              FROM company_costs 
              WHERE cost_date >= DATE_SUB(NOW(), INTERVAL 12 MONTH)
              GROUP BY DATE_FORMAT(cost_date, '%Y-%m') 
              ORDER BY month";
        
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        
        // 确保返回12个月的数据，包括没有数据的月份
        $statistics = [];
        $currentMonth = date('Y-m');
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months", strtotime($currentMonth)));
            $statistics[$month] = 0.00;
        }
        
        foreach ($result as $row) {
            $statistics[$row['month']] = floatval($row['total_cost']);
        }
        
        // 转换为数组格式便于前端使用
        $formattedStatistics = [];
        foreach ($statistics as $month => $totalCost) {
            $formattedStatistics[] = [
                'month' => $month,
                'total_cost' => $totalCost
            ];
        }
        
        return $formattedStatistics;
    }
    
    // 获取本月各类型费用统计
    public function getCurrentMonthStatistics() {
        $sql = "SELECT 
                cost_type, 
                SUM(cost) as total_cost 
              FROM company_costs 
              WHERE cost_date BETWEEN DATE_FORMAT(NOW(), '%Y-%m-01') AND LAST_DAY(NOW())
              GROUP BY cost_type 
              ORDER BY total_cost DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // 获取上月各类型费用统计
    public function getPreviousMonthStatistics() {
        $sql = "SELECT 
                cost_type, 
                SUM(cost) as total_cost 
              FROM company_costs 
              WHERE cost_date BETWEEN DATE_FORMAT(DATE_SUB(NOW(), INTERVAL 1 MONTH), '%Y-%m-01') AND LAST_DAY(DATE_SUB(NOW(), INTERVAL 1 MONTH))
              GROUP BY cost_type 
              ORDER BY total_cost DESC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}