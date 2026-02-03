<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class OrderReview {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取单个订单审核记录
    public function getById($id) {
        $sql = "SELECT * FROM order_review WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 根据订单号获取审核记录
    public function getByOrderNo($global_order_no) {
        $sql = "SELECT * FROM order_review WHERE global_order_no = ?";
        $stmt = $this->db->query($sql, [$global_order_no]);
        return $stmt->fetch();
    }
    
    // 获取所有订单审核记录，支持分页
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT orr.*, s.platform_name, s.store_name, w.wp_name, l.code 
                FROM order_review orr 
                LEFT JOIN store s ON orr.store_id = s.store_id COLLATE utf8mb4_unicode_ci 
                LEFT JOIN warehouses w ON orr.wid = w.wid 
                LEFT JOIN logistics l ON orr.logistics_type_id = l.type_id COLLATE utf8mb4_unicode_ci 
                ORDER BY orr.id DESC";
        $params = [];
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取订单审核记录总数
    public function getCount() {
        $sql = "SELECT COUNT(*) as total FROM order_review";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // 创建订单审核记录
    public function create($data) {
        $sql = "INSERT INTO order_review (store_id, global_order_no, local_sku, receiver_country_code, city, postal_code, wd_yunfei, ems_yunfei, wid, logistics_type_id, estimated_yunfei, review_status, review_time, review_remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['store_id'] ?? null,
            $data['global_order_no'],
            $data['local_sku'],
            $data['receiver_country_code'],
            $data['city'],
            $data['postal_code'],
            $data['wd_yunfei'] ?? null,
            $data['ems_yunfei'] ?? null,
            $data['wid'] ?? null,
            $data['logistics_type_id'] ?? null,
            $data['estimated_yunfei'] ?? null,
            $data['review_status'] ?? null,
            $data['review_time'] ?? null,
            $data['review_remark'] ?? null
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 更新订单审核记录
    public function update($id, $data) {
        $sql = "UPDATE order_review SET store_id = ?, global_order_no = ?, local_sku = ?, receiver_country_code = ?, city = ?, postal_code = ?, wd_yunfei = ?, ems_yunfei = ?, wid = ?, logistics_type_id = ?, estimated_yunfei = ?, review_status = ?, review_time = ?, review_remark = ? WHERE id = ?";
        $params = [
            $data['store_id'] ?? null,
            $data['global_order_no'],
            $data['local_sku'],
            $data['receiver_country_code'],
            $data['city'],
            $data['postal_code'],
            $data['wd_yunfei'] ?? null,
            $data['ems_yunfei'] ?? null,
            $data['wid'] ?? null,
            $data['logistics_type_id'] ?? null,
            $data['estimated_yunfei'] ?? null,
            $data['review_status'] ?? null,
            $data['review_time'] ?? null,
            $data['review_remark'] ?? null,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 删除订单审核记录
    public function delete($id) {
        $sql = "DELETE FROM order_review WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    // 根据筛选条件搜索订单审核记录
    public function searchWithFilters($keyword, $reviewStatus, $startDate, $endDate, $limit, $offset) {
        $sql = "SELECT orr.*, s.platform_name, s.store_name, w.wp_name, l.code 
                FROM order_review orr 
                LEFT JOIN store s ON orr.store_id = s.store_id COLLATE utf8mb4_unicode_ci 
                LEFT JOIN warehouses w ON orr.wid = w.wid 
                LEFT JOIN logistics l ON orr.logistics_type_id = l.type_id COLLATE utf8mb4_unicode_ci 
                WHERE 1=1";
        $params = [];
        
        if (!empty($keyword)) {
            $sql .= " AND (orr.global_order_no LIKE ? OR orr.local_sku LIKE ? OR orr.receiver_country_code LIKE ? OR s.platform_name LIKE ? OR s.store_name LIKE ? OR w.wp_name LIKE ? OR l.code LIKE ?)";
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        
        if (!empty($reviewStatus)) {
            $sql .= " AND orr.review_status = ?";
            $params[] = $reviewStatus;
        }
        
        if (!empty($startDate)) {
            $sql .= " AND orr.review_time >= ?";
            $params[] = $startDate;
        }
        
        if (!empty($endDate)) {
            $sql .= " AND orr.review_time <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY orr.id DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取筛选条件下的订单审核记录总数
    public function getSearchWithFiltersCount($keyword, $reviewStatus, $startDate, $endDate) {
        $sql = "SELECT COUNT(*) as total FROM order_review WHERE 1=1";
        $params = [];
        
        if (!empty($keyword)) {
            $sql .= " AND (global_order_no LIKE ? OR local_sku LIKE ? OR receiver_country_code LIKE ?)";
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        
        if (!empty($reviewStatus)) {
            $sql .= " AND review_status = ?";
            $params[] = $reviewStatus;
        }
        
        if (!empty($startDate)) {
            $sql .= " AND review_time >= ?";
            $params[] = $startDate;
        }
        
        if (!empty($endDate)) {
            $sql .= " AND review_time <= ?";
            $params[] = $endDate;
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        return $result['total'];
    }
    
    // 根据筛选条件获取所有订单审核记录（用于导出）
    public function getAllWithFilters($keyword = '', $reviewStatus = '', $startDate = '', $endDate = '') {
        $sql = "SELECT * FROM order_review WHERE 1=1";
        $params = [];
        
        if (!empty($keyword)) {
            $sql .= " AND (global_order_no LIKE ? OR local_sku LIKE ? OR receiver_country_code LIKE ?)";
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
            $params[] = '%' . $keyword . '%';
        }
        
        if (!empty($reviewStatus)) {
            $sql .= " AND review_status = ?";
            $params[] = $reviewStatus;
        }
        
        if (!empty($startDate)) {
            $sql .= " AND review_time >= ?";
            $params[] = $startDate;
        }
        
        if (!empty($endDate)) {
            $sql .= " AND review_time <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY id DESC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 批量插入订单审核记录
    public function batchInsert($data) {
        if (empty($data)) return true;
        
        $sql = "INSERT INTO order_review (store_id, global_order_no, local_sku, receiver_country_code, city, postal_code, wd_yunfei, ems_yunfei, wid, logistics_type_id, estimated_yunfei, review_status, review_time, review_remark) VALUES ";
        $params = [];
        
        foreach ($data as $item) {
            $sql .= "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?),";
            $params[] = $item['store_id'] ?? null;
            $params[] = $item['global_order_no'] ?? '';
            $params[] = $item['local_sku'] ?? '';
            $params[] = $item['receiver_country_code'] ?? '';
            $params[] = $item['city'] ?? '';
            $params[] = $item['postal_code'] ?? '';
            $params[] = $item['wd_yunfei'] ?? null;
            $params[] = $item['ems_yunfei'] ?? null;
            $params[] = $item['wid'] ?? null;
            $params[] = $item['logistics_type_id'] ?? null;
            $params[] = $item['estimated_yunfei'] ?? null;
            $params[] = $item['review_status'] ?? null;
            $params[] = $item['review_time'] ?? null;
            $params[] = $item['review_remark'] ?? null;
        }
        
        $sql = rtrim($sql, ',');
        return $this->db->query($sql, $params);
    }
    
    // 获取审单状态列表
    public function getReviewStatusList() {
        $sql = "SELECT DISTINCT review_status FROM order_review WHERE review_status IS NOT NULL ORDER BY review_status";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        
        $statusList = [];
        foreach ($result as $row) {
            $statusList[] = $row['review_status'];
        }
        
        return $statusList;
    }
    
    // 获取国家列表
    public function getCountryList() {
        $sql = "SELECT DISTINCT receiver_country_code FROM order_review ORDER BY receiver_country_code";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetchAll();
        
        $countryList = [];
        foreach ($result as $row) {
            $countryList[] = $row['receiver_country_code'];
        }
        
        return $countryList;
    }
}
?>