<?php
require_once APP_ROOT . '/includes/database.php';

class OrderProfit {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取订单利润
    public function getById($id) {
        $sql = "SELECT * FROM order_profit WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 根据订单号获取订单利润
    public function getByOrderNo($globalOrderNo) {
        $sql = "SELECT * FROM order_profit WHERE global_order_no = ?";
        $stmt = $this->db->query($sql, [$globalOrderNo]);
        return $stmt->fetch();
    }
    
    // 获取所有订单利润
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM order_profit ORDER BY id DESC";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->query($sql, [$limit, $offset]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }
    
    // 获取总数
    public function getCount() {
        $sql = "SELECT COUNT(*) as count FROM order_profit";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    // 创建订单利润
    public function create($data) {
        $sql = "INSERT INTO order_profit (
                    store_id, global_order_no, receiver_country, global_purchase_time, 
                    local_sku, order_total_amount, outbound_cost_amount, profit_amount, 
                    profit_rate, wms_outbound_cost_amount, wms_shipping_price_amount, update_time
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['store_id'] ?? '',
            $data['global_order_no'] ?? '',
            $data['receiver_country'] ?? '',
            $data['global_purchase_time'] ?? '',
            $data['local_sku'] ?? '',
            $data['order_total_amount'] ?? '0.00',
            $data['outbound_cost_amount'] ?? '0.00',
            $data['profit_amount'] ?? '0.00',
            $data['profit_rate'] ?? '0.00',
            $data['wms_outbound_cost_amount'] ?? '0.00',
            $data['wms_shipping_price_amount'] ?? '0.00',
            $data['update_time'] ?? date('Y-m-d H:i:s')
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // 更新订单利润
    public function update($id, $data) {
        $sql = "UPDATE order_profit SET 
                    store_id = ?, 
                    global_order_no = ?, 
                    receiver_country = ?, 
                    global_purchase_time = ?, 
                    local_sku = ?, 
                    order_total_amount = ?, 
                    outbound_cost_amount = ?, 
                    profit_amount = ?, 
                    profit_rate = ?, 
                    wms_outbound_cost_amount = ?, 
                    wms_shipping_price_amount = ?, 
                    update_time = ?
                WHERE id = ?";
        $params = [
            $data['store_id'] ?? '',
            $data['global_order_no'] ?? '',
            $data['receiver_country'] ?? '',
            $data['global_purchase_time'] ?? '',
            $data['local_sku'] ?? '',
            $data['order_total_amount'] ?? '0.00',
            $data['outbound_cost_amount'] ?? '0.00',
            $data['profit_amount'] ?? '0.00',
            $data['profit_rate'] ?? '0.00',
            $data['wms_outbound_cost_amount'] ?? '0.00',
            $data['wms_shipping_price_amount'] ?? '0.00',
            $data['update_time'] ?? date('Y-m-d H:i:s'),
            $id
        ];
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
    
    // 删除订单利润
    public function delete($id) {
        $sql = "DELETE FROM order_profit WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }
    
    // 搜索订单利润
    public function search($keyword, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM order_profit 
                WHERE global_order_no LIKE ? 
                OR store_id LIKE ? 
                OR local_sku LIKE ? 
                OR receiver_country LIKE ? 
                ORDER BY id DESC";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 搜索结果数量
    public function getSearchCount($keyword) {
        $sql = "SELECT COUNT(*) as count FROM order_profit 
                WHERE global_order_no LIKE ? 
                OR store_id LIKE ? 
                OR local_sku LIKE ? 
                OR receiver_country LIKE ?";
        $stmt = $this->db->query($sql, ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    // 根据店铺ID获取订单利润
    public function getByStoreId($storeId, $limit = null, $offset = 0) {
        $sql = "SELECT * FROM order_profit WHERE store_id = ? ORDER BY id DESC";
        $params = [$storeId];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取利润统计
    public function getProfitStats($startDate = null, $endDate = null, $storeId = null) {
        $sql = "SELECT 
                    COUNT(*) as order_count,
                    SUM(CAST(order_total_amount AS DECIMAL(10,2))) as total_amount,
                    SUM(CAST(outbound_cost_amount AS DECIMAL(10,2))) as total_cost,
                    SUM(CAST(profit_amount AS DECIMAL(10,2))) as total_profit,
                    AVG(CAST(profit_rate AS DECIMAL(10,2))) as avg_profit_rate,
                    SUM(CAST(wms_outbound_cost_amount AS DECIMAL(10,2))) as wms_cost,
                    SUM(CAST(wms_shipping_price_amount AS DECIMAL(10,2))) as wms_shipping
                FROM order_profit WHERE 1=1";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND update_time >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND update_time <= ?";
            $params[] = $endDate;
        }
        
        if ($storeId) {
            $sql .= " AND store_id = ?";
            $params[] = $storeId;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetch();
    }
    
    // 获取店铺列表
    public function getStoreList() {
        $sql = "SELECT DISTINCT store_id FROM order_profit WHERE store_id IS NOT NULL AND store_id != '' ORDER BY store_id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
    
    // 批量导入数据
    public function batchInsert($data) {
        if (empty($data)) {
            return 0;
        }
        
        $sql = "INSERT INTO order_profit (
                    store_id, global_order_no, receiver_country, global_purchase_time, 
                    local_sku, order_total_amount, outbound_cost_amount, profit_amount, 
                    profit_rate, wms_outbound_cost_amount, wms_shipping_price_amount, update_time
                ) VALUES ";
        
        $values = [];
        $params = [];
        
        foreach ($data as $item) {
            $values[] = "(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $params = array_merge($params, [
                $item['store_id'] ?? '',
                $item['global_order_no'] ?? '',
                $item['receiver_country'] ?? '',
                $item['global_purchase_time'] ?? '',
                $item['local_sku'] ?? '',
                $item['order_total_amount'] ?? '0.00',
                $item['outbound_cost_amount'] ?? '0.00',
                $item['profit_amount'] ?? '0.00',
                $item['profit_rate'] ?? '0.00',
                $item['wms_outbound_cost_amount'] ?? '0.00',
                $item['wms_shipping_price_amount'] ?? '0.00',
                $item['update_time'] ?? date('Y-m-d H:i:s')
            ]);
        }
        
        $sql .= implode(',', $values);
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
}