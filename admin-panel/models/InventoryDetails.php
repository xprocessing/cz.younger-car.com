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
        $allowedSortFields = ['id', 'wid', 'sku', 'product_valid_num', 'quantity_receive', 'average_age', 'purchase_price', 'head_stock_price', 'stock_price'];
        $allowedSortOrders = ['ASC', 'DESC'];
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'DESC';
        }
        
        $sql = "SELECT i.id, i.wid, i.sku, i.product_valid_num, i.quantity_receive, i.average_age, 
                       i.purchase_price, i.head_stock_price, i.stock_price, w.name as warehouse_name 
                FROM inventory_details i 
                LEFT JOIN warehouses w ON i.wid = w.wid 
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
                purchase_price, head_stock_price, stock_price) 
               VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['wid'],
            $data['sku'],
            $data['product_valid_num'],
            $data['quantity_receive'],
            $data['average_age'],
            $data['purchase_price'],
            $data['head_stock_price'],
            $data['stock_price']
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
                stock_price = ? 
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
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM inventory_details WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function search($keyword, $limit = null, $offset = 0, $sortField = 'id', $sortOrder = 'DESC') {
        $allowedSortFields = ['id', 'wid', 'sku', 'product_valid_num', 'quantity_receive', 'average_age', 'purchase_price', 'head_stock_price', 'stock_price'];
        $allowedSortOrders = ['ASC', 'DESC'];
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'DESC';
        }
        
        $sql = "SELECT i.id, i.wid, i.sku, i.product_valid_num, i.quantity_receive, i.average_age, 
                       i.purchase_price, i.head_stock_price, i.stock_price, w.name as warehouse_name 
                FROM inventory_details i 
                LEFT JOIN warehouses w ON i.wid = w.wid 
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
        $allowedSortFields = ['id', 'wid', 'sku', 'product_valid_num', 'quantity_receive', 'average_age', 'purchase_price', 'head_stock_price', 'stock_price'];
        $allowedSortOrders = ['ASC', 'DESC'];
        
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'id';
        }
        
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $sortOrder = 'DESC';
        }
        
        $sql = "SELECT i.id, i.wid, i.sku, i.product_valid_num, i.quantity_receive, i.average_age, 
                       i.purchase_price, i.head_stock_price, i.stock_price, w.name as warehouse_name 
                FROM inventory_details i 
                LEFT JOIN warehouses w ON i.wid = w.wid 
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
                       w.name as warehouse_name 
                FROM inventory_details i 
                LEFT JOIN warehouses w ON i.wid = w.wid 
                WHERE i.average_age > ? 
                AND i.product_valid_num > 0
                ORDER BY i.average_age DESC, i.sku ASC";
        
        $stmt = $this->db->query($sql, [$thresholdDays]);
        return $stmt->fetchAll();
    }
    
    public function getInventoryAlert() {
        $sql = "SELECT i.id,
                       i.wid,
                       i.sku,
                       i.product_valid_num,
                       i.quantity_receive,
                       i.product_onway,
                       i.average_age,
                       w.name as warehouse_name,
                       COALESCE(op.outbound_30days, 0) as outbound_30days
                FROM inventory_details i
                LEFT JOIN warehouses w ON i.wid = w.wid
                LEFT JOIN (
                    SELECT wid, 
                           local_sku, 
                           COUNT(*) as outbound_30days
                    FROM order_profit
                    WHERE global_purchase_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
                    GROUP BY wid, local_sku
                ) op ON i.wid = op.wid AND i.sku = op.local_sku
                ORDER BY i.average_age DESC, i.sku ASC";
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
}
