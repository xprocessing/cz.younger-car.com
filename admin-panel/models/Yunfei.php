<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class Yunfei {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取记录
    public function getById($id) {
        $sql = "SELECT * FROM yunfei WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 根据订单号获取记录
    public function getByOrderNo($orderNo) {
        $sql = "SELECT * FROM yunfei WHERE global_order_no = ?";
        $stmt = $this->db->query($sql, [$orderNo]);
        return $stmt->fetch();
    }
    
    // 获取所有记录
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM yunfei ORDER BY id DESC";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->query($sql, [$limit, $offset]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }
    
    // 获取总记录数
    public function getCount() {
        $sql = "SELECT COUNT(*) as count FROM yunfei";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    // 搜索记录
    public function search($keyword, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM yunfei WHERE global_order_no LIKE ? ORDER BY id DESC";
        $params = ["%$keyword%"];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取搜索结果总数
    public function getSearchCount($keyword) {
        $sql = "SELECT COUNT(*) as count FROM yunfei WHERE global_order_no LIKE ?";
        $stmt = $this->db->query($sql, ["%$keyword%"]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    // 创建记录
    public function create($data) {
        $sql = "INSERT INTO yunfei (global_order_no, shisuanyunfei) VALUES (?, ?)";
        $params = [
            $data['global_order_no'],
            $data['shisuanyunfei'] ? json_encode($data['shisuanyunfei']) : null
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // 更新记录
    public function update($id, $data) {
        $sql = "UPDATE yunfei SET global_order_no = ?, shisuanyunfei = ? WHERE id = ?";
        $params = [
            $data['global_order_no'],
            $data['shisuanyunfei'] ? json_encode($data['shisuanyunfei']) : null,
            $id
        ];
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
    
    // 删除记录
    public function delete($id) {
        $sql = "DELETE FROM yunfei WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }
    
    // 格式化JSON数据显示
    public function formatYunfeiData($yunfeiJson) {
        if (!$yunfeiJson) {
            return ['error' => '运费数据为空'];
        }
        
        $data = json_decode($yunfeiJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => 'JSON解析失败: ' . json_last_error_msg() . ' (原始数据: ' . substr($yunfeiJson, 0, 200) . '...)'];
        }
        
        return $data;
    }
}