<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class Logistics {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getById($type_id) {
        $sql = "SELECT * FROM logistics WHERE type_id = ?";
        $stmt = $this->db->query($sql, [$type_id]);
        return $stmt->fetch();
    }
    
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM logistics ORDER BY id DESC";
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
        $sql = "SELECT COUNT(*) FROM logistics";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
    
    public function create($data) {
        $sql = "INSERT INTO logistics (type_id, is_used, name, code, logistics_provider_id, order_type, channel_type, relate_olt_id, fee_template_id, billing_type, volume_param, warehouse_type, logistics_provider_name, provider_is_used, is_platform_provider, supplier_code, wp_code, type, wid, is_combine_channel, tms_provider_id, tms_provider_type, supplier_id, is_support_domestic_provider, is_need_marking) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['type_id'],
            $data['is_used'] ?? 0,
            $data['name'],
            $data['code'],
            $data['logistics_provider_id'],
            $data['order_type'] ?? 0,
            $data['channel_type'] ?? 0,
            $data['relate_olt_id'] ?? '0',
            $data['fee_template_id'] ?? 0,
            $data['billing_type'] ?? 0,
            $data['volume_param'] ?? 0,
            $data['warehouse_type'] ?? 0,
            $data['logistics_provider_name'],
            $data['provider_is_used'] ?? 0,
            $data['is_platform_provider'] ?? 0,
            $data['supplier_code'],
            $data['wp_code'],
            $data['type'] ?? 0,
            $data['wid'] ?? 0,
            $data['is_combine_channel'] ?? 0,
            $data['tms_provider_id'] ?? 0,
            $data['tms_provider_type'] ?? 0,
            $data['supplier_id'] ?? 0,
            $data['is_support_domestic_provider'] ?? 0,
            $data['is_need_marking'] ?? 0
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function update($type_id, $data) {
        $sql = "UPDATE logistics SET 
                is_used = ?, 
                name = ?, 
                code = ?, 
                logistics_provider_id = ?, 
                order_type = ?, 
                channel_type = ?, 
                relate_olt_id = ?, 
                fee_template_id = ?, 
                billing_type = ?, 
                volume_param = ?, 
                warehouse_type = ?, 
                logistics_provider_name = ?, 
                provider_is_used = ?, 
                is_platform_provider = ?, 
                supplier_code = ?, 
                wp_code = ?, 
                type = ?, 
                wid = ?, 
                is_combine_channel = ?, 
                tms_provider_id = ?, 
                tms_provider_type = ?, 
                supplier_id = ?, 
                is_support_domestic_provider = ?, 
                is_need_marking = ? 
                WHERE type_id = ?";
        
        $params = [
            $data['is_used'] ?? 0,
            $data['name'],
            $data['code'],
            $data['logistics_provider_id'],
            $data['order_type'] ?? 0,
            $data['channel_type'] ?? 0,
            $data['relate_olt_id'] ?? '0',
            $data['fee_template_id'] ?? 0,
            $data['billing_type'] ?? 0,
            $data['volume_param'] ?? 0,
            $data['warehouse_type'] ?? 0,
            $data['logistics_provider_name'],
            $data['provider_is_used'] ?? 0,
            $data['is_platform_provider'] ?? 0,
            $data['supplier_code'],
            $data['wp_code'],
            $data['type'] ?? 0,
            $data['wid'] ?? 0,
            $data['is_combine_channel'] ?? 0,
            $data['tms_provider_id'] ?? 0,
            $data['tms_provider_type'] ?? 0,
            $data['supplier_id'] ?? 0,
            $data['is_support_domestic_provider'] ?? 0,
            $data['is_need_marking'] ?? 0,
            $type_id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($type_id) {
        $sql = "DELETE FROM logistics WHERE type_id = ?";
        return $this->db->query($sql, [$type_id]);
    }
    
    public function search($keyword, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM logistics 
                WHERE name LIKE ? OR code LIKE ? OR logistics_provider_name LIKE ?
                ORDER BY id DESC";
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
        $sql = "SELECT COUNT(*) FROM logistics 
                WHERE name LIKE ? OR code LIKE ? OR logistics_provider_name LIKE ?";
        $stmt = $this->db->query($sql, ["%$keyword%", "%$keyword%", "%$keyword%"]);
        return $stmt->fetchColumn();
    }
}