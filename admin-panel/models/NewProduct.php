<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class NewProduct {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取新产品
    public function getById($id) {
        $sql = "SELECT * FROM new_products WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 根据需求编号获取新产品
    public function getByRequireNo($requireNo) {
        $sql = "SELECT * FROM new_products WHERE require_no = ?";
        $stmt = $this->db->query($sql, [$requireNo]);
        return $stmt->fetch();
    }
    
    // 获取所有新产品
    public function getAll() {
        $sql = "SELECT * FROM new_products ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // 创建新产品
    public function create($data) {
        $sql = "INSERT INTO new_products (require_no, img_url, require_title, npdId, sku, remark, create_time, current_step, process_list) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['require_no'],
            $data['img_url'] ?? null,
            $data['require_title'] ?? null,
            $data['npdId'] ?? null,
            $data['sku'] ?? null,
            $data['remark'] ?? null,
            $data['create_time'] ?? null,
            $data['current_step'] ?? 0,
            $data['process_list'] ? json_encode($data['process_list']) : null
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // 更新新产品
    public function update($id, $data) {
        $sql = "UPDATE new_products SET 
                require_no = ?, 
                img_url = ?, 
                require_title = ?, 
                npdId = ?, 
                sku = ?, 
                remark = ?, 
                create_time = ?, 
                current_step = ?, 
                process_list = ? 
                WHERE id = ?";
        $params = [
            $data['require_no'],
            $data['img_url'] ?? null,
            $data['require_title'] ?? null,
            $data['npdId'] ?? null,
            $data['sku'] ?? null,
            $data['remark'] ?? null,
            $data['create_time'] ?? null,
            $data['current_step'] ?? 0,
            $data['process_list'] ? json_encode($data['process_list']) : null,
            $id
        ];
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
    
    // 删除新产品
    public function delete($id) {
        $sql = "DELETE FROM new_products WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }
    
    // 搜索新产品
    public function search($keyword) {
        $sql = "SELECT * FROM new_products 
                WHERE require_no LIKE ? 
                OR require_title LIKE ? 
                OR npdId LIKE ? 
                OR sku LIKE ? 
                ORDER BY id DESC";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"];
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 格式化进度明细
    public function formatProcessList($processListJson) {
        if (empty($processListJson)) {
            return [];
        }
        
        $processList = json_decode($processListJson, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['error' => '进度数据解析失败: ' . json_last_error_msg()];
        }
        
        return $processList;
    }
    
    // 更新进度
    public function updateStep($id, $step) {
        $sql = "UPDATE new_products SET current_step = ? WHERE id = ?";
        return $this->db->query($sql, [$step, $id])->rowCount() > 0;
    }
}