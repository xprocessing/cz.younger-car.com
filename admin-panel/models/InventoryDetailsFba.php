<?php
require_once APP_ROOT . '/includes/database.php';

class InventoryDetailsFba {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据name和sku获取记录
    public function getByNameAndSku($name, $sku) {
        $sql = "SELECT * FROM inventory_details_fba WHERE name = ? AND sku = ?";
        $stmt = $this->db->query($sql, [$name, $sku]);
        return $stmt->fetch();
    }
    
    // 获取所有记录
    public function getAll($filters = []) {
        $sql = "SELECT * FROM inventory_details_fba";
        $params = [];
        
        // 添加过滤条件
        if (!empty($filters)) {
            $whereClause = [];
            
            if (isset($filters['search'])) {
                $whereClause[] = "(name LIKE ? OR sku LIKE ? OR asin LIKE ? OR product_name LIKE ?)";
                $searchTerm = '%' . $filters['search'] . '%';
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
                $params[] = $searchTerm;
            }
            
            if (isset($filters['name'])) {
                $whereClause[] = "name = ?";
                $params[] = $filters['name'];
            }
            
            if (isset($filters['sku'])) {
                $whereClause[] = "sku LIKE ?";
                $params[] = '%' . $filters['sku'] . '%';
            }
            
            if (!empty($whereClause)) {
                $sql .= " WHERE " . implode(" AND ", $whereClause);
            }
        }
        
        $sql .= " ORDER BY name, sku";
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 删除记录
    public function delete($name, $sku) {
        $sql = "DELETE FROM inventory_details_fba WHERE name = ? AND sku = ?";
        return $this->db->query($sql, [$name, $sku])->rowCount() > 0;
    }
    
    // 获取所有仓库名
    public function getAllWarehouseNames() {
        $sql = "SELECT DISTINCT name FROM inventory_details_fba ORDER BY name";
        $stmt = $this->db->query($sql);
        $names = $stmt->fetchAll();
        return array_column($names, 'name');
    }
}
?>