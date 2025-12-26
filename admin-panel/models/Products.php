<?php
require_once APP_ROOT . '/includes/database.php';
require_once APP_ROOT . '/helpers/functions.php';

class Products {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    public function getById($id) {
        $sql = "SELECT * FROM products WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    public function getBySku($sku) {
        $sql = "SELECT * FROM products WHERE sku = ?";
        $stmt = $this->db->query($sql, [$sku]);
        return $stmt->fetch();
    }
    
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT * FROM products ORDER BY id DESC";
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->query($sql, [$limit, $offset]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }
    
    public function getCount() {
        $sql = "SELECT COUNT(*) as count FROM products";
        $stmt = $this->db->query($sql);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function searchWithFilters($keyword = '', $sku = '', $spu = '', $status = '', $brandName = '', $categoryName = '', $limit = null, $offset = 0) {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        
        if ($keyword) {
            $sql .= " AND (product_name LIKE ? OR sku LIKE ? OR spu LIKE ?)";
            $keywordParam = "%{$keyword}%";
            $params[] = $keywordParam;
            $params[] = $keywordParam;
            $params[] = $keywordParam;
        }
        
        if ($sku) {
            $sql .= " AND sku LIKE ?";
            $params[] = "%{$sku}%";
        }
        
        if ($spu) {
            $sql .= " AND spu LIKE ?";
            $params[] = "%{$spu}%";
        }
        
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        if ($brandName) {
            $sql .= " AND brand_name LIKE ?";
            $params[] = "%{$brandName}%";
        }
        
        if ($categoryName) {
            $sql .= " AND category_name LIKE ?";
            $params[] = "%{$categoryName}%";
        }
        
        $sql .= " ORDER BY id DESC";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function getSearchWithFiltersCount($keyword = '', $sku = '', $spu = '', $status = '', $brandName = '', $categoryName = '') {
        $sql = "SELECT COUNT(*) as count FROM products WHERE 1=1";
        $params = [];
        
        if ($keyword) {
            $sql .= " AND (product_name LIKE ? OR sku LIKE ? OR spu LIKE ?)";
            $keywordParam = "%{$keyword}%";
            $params[] = $keywordParam;
            $params[] = $keywordParam;
            $params[] = $keywordParam;
        }
        
        if ($sku) {
            $sql .= " AND sku LIKE ?";
            $params[] = "%{$sku}%";
        }
        
        if ($spu) {
            $sql .= " AND spu LIKE ?";
            $params[] = "%{$spu}%";
        }
        
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        if ($brandName) {
            $sql .= " AND brand_name LIKE ?";
            $params[] = "%{$brandName}%";
        }
        
        if ($categoryName) {
            $sql .= " AND category_name LIKE ?";
            $params[] = "%{$categoryName}%";
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    public function create($data) {
        $sql = "INSERT INTO products (
                    cid, bid, sku, sku_identifier, product_name, pic_url, 
                    cg_delivery, cg_transport_costs, purchase_remark, cg_price, 
                    status, open_status, is_combo, create_time, update_time, 
                    product_developer_uid, cg_opt_uid, cg_opt_username, spu, ps_id,
                    attribute, brand_name, category_name, status_text, 
                    product_developer, supplier_quote, aux_relation_list, custom_fields, global_tags
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $params = [
            $data['cid'] ?? null,
            $data['bid'] ?? null,
            $data['sku'],
            $data['sku_identifier'] ?? null,
            $data['product_name'] ?? null,
            $data['pic_url'] ?? null,
            $data['cg_delivery'] ?? null,
            $data['cg_transport_costs'] ?? null,
            $data['purchase_remark'] ?? null,
            $data['cg_price'] ?? null,
            $data['status'] ?? null,
            $data['open_status'] ?? null,
            $data['is_combo'] ?? null,
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s'),
            $data['product_developer_uid'] ?? null,
            $data['cg_opt_uid'] ?? null,
            $data['cg_opt_username'] ?? null,
            $data['spu'] ?? null,
            $data['ps_id'] ?? null,
            isset($data['attribute']) ? json_encode($data['attribute']) : null,
            $data['brand_name'] ?? null,
            $data['category_name'] ?? null,
            $data['status_text'] ?? null,
            $data['product_developer'] ?? null,
            isset($data['supplier_quote']) ? json_encode($data['supplier_quote']) : null,
            isset($data['aux_relation_list']) ? json_encode($data['aux_relation_list']) : null,
            isset($data['custom_fields']) ? json_encode($data['custom_fields']) : null,
            isset($data['global_tags']) ? json_encode($data['global_tags']) : null
        ];
        
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    public function update($id, $data) {
        $sql = "UPDATE products SET 
                    cid = ?, bid = ?, sku = ?, sku_identifier = ?, product_name = ?, pic_url = ?,
                    cg_delivery = ?, cg_transport_costs = ?, purchase_remark = ?, cg_price = ?,
                    status = ?, open_status = ?, is_combo = ?, update_time = ?,
                    product_developer_uid = ?, cg_opt_uid = ?, cg_opt_username = ?, spu = ?, ps_id = ?,
                    attribute = ?, brand_name = ?, category_name = ?, status_text = ?,
                    product_developer = ?, supplier_quote = ?, aux_relation_list = ?, custom_fields = ?, global_tags = ?
                WHERE id = ?";
        
        $params = [
            $data['cid'] ?? null,
            $data['bid'] ?? null,
            $data['sku'],
            $data['sku_identifier'] ?? null,
            $data['product_name'] ?? null,
            $data['pic_url'] ?? null,
            $data['cg_delivery'] ?? null,
            $data['cg_transport_costs'] ?? null,
            $data['purchase_remark'] ?? null,
            $data['cg_price'] ?? null,
            $data['status'] ?? null,
            $data['open_status'] ?? null,
            $data['is_combo'] ?? null,
            date('Y-m-d H:i:s'),
            $data['product_developer_uid'] ?? null,
            $data['cg_opt_uid'] ?? null,
            $data['cg_opt_username'] ?? null,
            $data['spu'] ?? null,
            $data['ps_id'] ?? null,
            isset($data['attribute']) ? json_encode($data['attribute']) : null,
            $data['brand_name'] ?? null,
            $data['category_name'] ?? null,
            $data['status_text'] ?? null,
            $data['product_developer'] ?? null,
            isset($data['supplier_quote']) ? json_encode($data['supplier_quote']) : null,
            isset($data['aux_relation_list']) ? json_encode($data['aux_relation_list']) : null,
            isset($data['custom_fields']) ? json_encode($data['custom_fields']) : null,
            isset($data['global_tags']) ? json_encode($data['global_tags']) : null,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    public function delete($id) {
        $sql = "DELETE FROM products WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    public function batchDelete($ids) {
        if (empty($ids)) {
            return false;
        }
        
        $placeholders = str_repeat('?,', count($ids) - 1) . '?';
        $sql = "DELETE FROM products WHERE id IN ({$placeholders})";
        return $this->db->query($sql, $ids);
    }
    
    public function export($keyword = '', $sku = '', $spu = '', $status = '', $brandName = '', $categoryName = '') {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        
        if ($keyword) {
            $sql .= " AND (product_name LIKE ? OR sku LIKE ? OR spu LIKE ?)";
            $keywordParam = "%{$keyword}%";
            $params[] = $keywordParam;
            $params[] = $keywordParam;
            $params[] = $keywordParam;
        }
        
        if ($sku) {
            $sql .= " AND sku LIKE ?";
            $params[] = "%{$sku}%";
        }
        
        if ($spu) {
            $sql .= " AND spu LIKE ?";
            $params[] = "%{$spu}%";
        }
        
        if ($status !== '') {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        if ($brandName) {
            $sql .= " AND brand_name LIKE ?";
            $params[] = "%{$brandName}%";
        }
        
        if ($categoryName) {
            $sql .= " AND category_name LIKE ?";
            $params[] = "%{$categoryName}%";
        }
        
        $sql .= " ORDER BY id DESC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    public function import($data) {
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        foreach ($data as $row) {
            try {
                $existing = $this->getBySku($row['sku']);
                
                if ($existing) {
                    $this->update($existing['id'], $row);
                } else {
                    $this->create($row);
                }
                $successCount++;
            } catch (Exception $e) {
                $errorCount++;
                $errors[] = [
                    'sku' => $row['sku'] ?? 'unknown',
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return [
            'success_count' => $successCount,
            'error_count' => $errorCount,
            'errors' => $errors
        ];
    }
    
    public function getBrandList() {
        $sql = "SELECT DISTINCT brand_name FROM products WHERE brand_name IS NOT NULL AND brand_name != '' ORDER BY brand_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getCategoryList() {
        $sql = "SELECT DISTINCT category_name FROM products WHERE category_name IS NOT NULL AND category_name != '' ORDER BY category_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    public function getSpuList() {
        $sql = "SELECT DISTINCT spu FROM products WHERE spu IS NOT NULL AND spu != '' ORDER BY spu";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
