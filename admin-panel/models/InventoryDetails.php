<?php
require_once APP_ROOT . '/includes/database.php';

class InventoryDetails {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getById($id) {
        $sql = "SELECT i.*, w.name as warehouse_name 
                FROM inventory_details i 
                LEFT JOIN warehouses w ON i.wid = w.wid 
                WHERE i.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function getAll($limit = null, $offset = 0, $sortField = 'id', $sortOrder = 'DESC') {
        $allowedSortFields = ['id', 'wid', 'sku', 'product_valid_num', 'quantity_receive', 'average_age', 'purchase_price', 'head_stock_price', 'stock_price', 'product_onway', 'product_name'];
        $allowedSortOrders = ['ASC', 'DESC'];
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'DESC';
        }
        
        $sql = "SELECT i.id, i.wid, i.sku, i.product_valid_num, i.quantity_receive, i.average_age, 
                       i.purchase_price, i.head_stock_price, i.stock_price, i.product_onway, w.name as warehouse_name, 
                       p.product_name, p.pic_url 
                FROM inventory_details i 
                LEFT JOIN warehouses w ON i.wid = w.wid 
                LEFT JOIN products p ON i.sku = p.sku 
                ORDER BY i.$sortField $sortOrder";
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
        $sql = "SELECT COUNT(*) FROM inventory_details";
        $stmt = $this->db->query($sql);
        return $stmt->fetchColumn();
    }
    
    public function create($data) {
        $sql = "INSERT INTO inventory_details (wid, sku, product_valid_num, quantity_receive, average_age, 
                purchase_price, head_stock_price, stock_price, product_onway) 
               VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['wid'],
            $data['sku'],
            $data['product_valid_num'],
            $data['quantity_receive'],
            $data['average_age'],
            $data['purchase_price'],
            $data['head_stock_price'],
            $data['stock_price'],
            $data['product_onway'] ?? 0
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function update($id, $data) {
        $sql = "UPDATE inventory_details SET 
                wid = ?, 
                sku = ?, 
                product_valid_num = ?, 
                quantity_receive = ?, 
                average_age = ?, 
                purchase_price = ?, 
                head_stock_price = ?, 
                stock_price = ?, 
                product_onway = ? 
                WHERE id = ?";
        
        $params = [
            $data['wid'],
            $data['sku'],
            $data['product_valid_num'],
            $data['quantity_receive'],
            $data['average_age'],
            $data['purchase_price'],
            $data['head_stock_price'],
            $data['stock_price'],
            $data['product_onway'] ?? 0,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM inventory_details WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function search($keyword, $limit = null, $offset = 0, $sortField = 'id', $sortOrder = 'DESC') {
        $allowedSortFields = ['id', 'wid', 'sku', 'product_valid_num', 'quantity_receive', 'product_onway', 'average_age', 'purchase_price', 'head_stock_price', 'stock_price', 'product_name'];
        $allowedSortOrders = ['ASC', 'DESC'];
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'DESC';
        }
        
        $sql = "SELECT i.id, i.wid, i.sku, i.product_valid_num, i.quantity_receive, i.average_age, 
                       i.purchase_price, i.head_stock_price, i.stock_price, i.product_onway, w.name as warehouse_name, 
                       p.product_name, p.pic_url 
                FROM inventory_details i 
                LEFT JOIN warehouses w ON i.wid = w.wid 
                LEFT JOIN products p ON i.sku = p.sku 
                WHERE i.sku LIKE ? 
                ORDER BY i.$sortField $sortOrder";
        $params = ["%$keyword%"];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getSearchCount($keyword) {
        $sql = "SELECT COUNT(*) FROM inventory_details WHERE sku LIKE ?";
        $stmt = $this->db->query($sql, ["%$keyword%"]);
        return $stmt->fetchColumn();
    }
    
    public function getByWid($wid, $limit = null, $offset = 0, $sortField = 'id', $sortOrder = 'DESC') {
        $allowedSortFields = ['id', 'wid', 'sku', 'product_valid_num', 'quantity_receive', 'product_onway', 'average_age', 'purchase_price', 'head_stock_price', 'stock_price', 'product_name'];
        $allowedSortOrders = ['ASC', 'DESC'];
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'DESC';
        }
        
        $sql = "SELECT i.id, i.wid, i.sku, i.product_valid_num, i.quantity_receive, i.average_age, 
                       i.purchase_price, i.head_stock_price, i.stock_price, i.product_onway, w.name as warehouse_name, 
                       p.product_name, p.pic_url 
                FROM inventory_details i 
                LEFT JOIN warehouses w ON i.wid = w.wid 
                LEFT JOIN products p ON i.sku = p.sku 
                WHERE i.wid = ?
                ORDER BY i.$sortField $sortOrder";
        $params = [$wid];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getCountByWid($wid) {
        $sql = "SELECT COUNT(*) FROM inventory_details WHERE wid = ?";
        $stmt = $this->db->query($sql, [$wid]);
        return $stmt->fetchColumn();
    }
    
    public function getOveragedInventory($thresholdDays = 180) {
        $sql = "SELECT i.sku, 
                       i.wid, 
                       i.product_valid_num, 
                       i.average_age, 
                       w.name as warehouse_name,
                       COALESCE(p.product_name, '') as product_name,
                       COALESCE(p.pic_url, '') as product_image 
                FROM inventory_details i 
                LEFT JOIN warehouses w ON i.wid = w.wid 
                LEFT JOIN products p ON i.sku = p.sku
                WHERE i.average_age > ? 
                AND i.product_valid_num > 0
                ORDER BY i.average_age DESC, i.sku ASC";
        
        $stmt = $this->db->query($sql, [$thresholdDays]);
        return $stmt->fetchAll();
    }
    
    public function getInventoryAlert() {
        $thirtyDaysAgo = date('Y-m-d 00:00:00', strtotime('-30 days'));
        
        $sql = "SELECT combined_data.sku,
                       SUM(combined_data.product_valid_num) as product_valid_num,
                       SUM(combined_data.quantity_receive) as quantity_receive,
                       SUM(combined_data.product_onway) as product_onway,
                       COALESCE(MAX(op.outbound_30days), 0) as outbound_30days,
                       COALESCE(p.product_name, '') as product_name,
                       COALESCE(p.pic_url, '') as product_image
                FROM (
                    -- 从inventory_details获取基础数据
                    SELECT i.sku COLLATE utf8mb4_unicode_ci as sku,
                           i.product_valid_num,
                           i.quantity_receive,
                           i.product_onway
                    FROM inventory_details i
                    WHERE i.wid != 5693
                    UNION ALL
                    -- 从order_profit获取最近30天有出库记录的SKU
                    SELECT op.local_sku COLLATE utf8mb4_unicode_ci as sku,
                           0 as product_valid_num,
                           0 as quantity_receive,
                           0 as product_onway
                    FROM order_profit op
                    WHERE op.global_purchase_time >= ?
                ) AS combined_data
                LEFT JOIN (
                    SELECT local_sku COLLATE utf8mb4_unicode_ci as local_sku,
                           COUNT(*) as outbound_30days
                    FROM order_profit
                    WHERE global_purchase_time >= ?
                    GROUP BY local_sku
                ) op ON combined_data.sku = op.local_sku
                LEFT JOIN products p ON combined_data.sku = p.sku
                GROUP BY combined_data.sku
                HAVING product_valid_num > 0 OR quantity_receive > 0 OR product_onway > 0 OR outbound_30days > 0
                ORDER BY outbound_30days DESC, combined_data.sku ASC";
        
        $stmt = $this->db->query($sql, [$thirtyDaysAgo, $thirtyDaysAgo]);
        return $stmt->fetchAll();
    }
}
