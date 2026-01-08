<?php
require_once APP_ROOT . '/includes/database.php';

class CarData {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取车型数据
    public function getById($id) {
        $sql = "SELECT * FROM car_data WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 获取所有车型数据，支持过滤
    public function getAll($filters = [], $limit = null, $offset = 0) {
        $sql = "SELECT * FROM car_data";
        $params = [];
        
        // 添加过滤条件
        if (!empty($filters)) {
            $whereClause = [];
            
            if (isset($filters['keyword']) && !empty($filters['keyword'])) {
                $whereClause[] = "(make LIKE ? OR make_cn LIKE ? OR model LIKE ? OR trim LIKE ? OR market LIKE ?)";
                $searchTerm = '%' . $filters['keyword'] . '%';
                for ($i = 0; $i < 5; $i++) {
                    $params[] = $searchTerm;
                }
            }
            
            if (isset($filters['make']) && !empty($filters['make'])) {
                $whereClause[] = "make = ?";
                $params[] = $filters['make'];
            }
            
            if (isset($filters['make_cn']) && !empty($filters['make_cn'])) {
                $whereClause[] = "make_cn = ?";
                $params[] = $filters['make_cn'];
            }
            
            if (isset($filters['model']) && !empty($filters['model'])) {
                $whereClause[] = "model = ?";
                $params[] = $filters['model'];
            }
            
            if (isset($filters['year']) && !empty($filters['year'])) {
                $whereClause[] = "year = ?";
                $params[] = $filters['year'];
            }
            
            if (isset($filters['market']) && !empty($filters['market'])) {
                $whereClause[] = "market = ?";
                $params[] = $filters['market'];
            }
            
            if (!empty($whereClause)) {
                $sql .= " WHERE " . implode(" AND ", $whereClause);
            }
        }
        
        $sql .= " ORDER BY id DESC";
        
        if ($limit !== null) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取车型数据总数，支持过滤
    public function getCount($filters = []) {
        $sql = "SELECT COUNT(*) as count FROM car_data";
        $params = [];
        
        // 添加过滤条件
        if (!empty($filters)) {
            $whereClause = [];
            
            if (isset($filters['keyword']) && !empty($filters['keyword'])) {
                $whereClause[] = "(make LIKE ? OR make_cn LIKE ? OR model LIKE ? OR trim LIKE ? OR market LIKE ?)";
                $searchTerm = '%' . $filters['keyword'] . '%';
                for ($i = 0; $i < 5; $i++) {
                    $params[] = $searchTerm;
                }
            }
            
            if (isset($filters['make']) && !empty($filters['make'])) {
                $whereClause[] = "make = ?";
                $params[] = $filters['make'];
            }
            
            if (isset($filters['make_cn']) && !empty($filters['make_cn'])) {
                $whereClause[] = "make_cn = ?";
                $params[] = $filters['make_cn'];
            }
            
            if (isset($filters['model']) && !empty($filters['model'])) {
                $whereClause[] = "model = ?";
                $params[] = $filters['model'];
            }
            
            if (isset($filters['year']) && !empty($filters['year'])) {
                $whereClause[] = "year = ?";
                $params[] = $filters['year'];
            }
            
            if (isset($filters['market']) && !empty($filters['market'])) {
                $whereClause[] = "market = ?";
                $params[] = $filters['market'];
            }
            
            if (!empty($whereClause)) {
                $sql .= " WHERE " . implode(" AND ", $whereClause);
            }
        }
        
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    // 创建车型数据
    public function create($data) {
        $sql = "INSERT INTO car_data (make, make_cn, model, year, trim, trim_description, market) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['make'] ?? null,
            $data['make_cn'] ?? null,
            $data['model'] ?? null,
            $data['year'] ?? null,
            $data['trim'] ?? null,
            $data['trim_description'] ?? null,
            $data['market'] ?? null
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // 更新车型数据
    public function update($id, $data) {
        $sql = "UPDATE car_data SET make = ?, make_cn = ?, model = ?, year = ?, trim = ?, trim_description = ?, market = ? WHERE id = ?";
        $params = [
            $data['make'] ?? null,
            $data['make_cn'] ?? null,
            $data['model'] ?? null,
            $data['year'] ?? null,
            $data['trim'] ?? null,
            $data['trim_description'] ?? null,
            $data['market'] ?? null,
            $id
        ];
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
    
    // 删除车型数据
    public function delete($id) {
        $sql = "DELETE FROM car_data WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }
    
    // 批量导入车型数据
    public function import($dataArray) {
        if (empty($dataArray)) {
            return 0;
        }
        
        $sql = "INSERT IGNORE INTO car_data (make, make_cn, model, year, trim, trim_description, market) VALUES ";
        $params = [];
        $values = [];
        
        foreach ($dataArray as $data) {
            $values[] = "(?, ?, ?, ?, ?, ?, ?)";
            $params[] = $data['make'] ?? null;
            $params[] = $data['make_cn'] ?? null;
            $params[] = $data['model'] ?? null;
            $params[] = $data['year'] ?? null;
            $params[] = $data['trim'] ?? null;
            $params[] = $data['trim_description'] ?? null;
            $params[] = $data['market'] ?? null;
        }
        
        $sql .= implode(", ", $values);
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // 获取所有品牌列表
    public function getMakeList() {
        $sql = "SELECT DISTINCT make FROM car_data WHERE make IS NOT NULL AND make != '' ORDER BY make";
        $stmt = $this->db->query($sql);
        $makes = $stmt->fetchAll();
        return array_column($makes, 'make');
    }
    
    // 获取所有中文品牌列表
    public function getMakeCnList() {
        $sql = "SELECT DISTINCT make_cn FROM car_data WHERE make_cn IS NOT NULL AND make_cn != '' ORDER BY make_cn";
        $stmt = $this->db->query($sql);
        $makesCn = $stmt->fetchAll();
        return array_column($makesCn, 'make_cn');
    }
    
    // 获取所有市场列表
    public function getMarketList() {
        $sql = "SELECT DISTINCT market FROM car_data WHERE market IS NOT NULL AND market != '' ORDER BY market";
        $stmt = $this->db->query($sql);
        $markets = $stmt->fetchAll();
        return array_column($markets, 'market');
    }
    
    // 获取所有年份列表
    public function getYearList() {
        $sql = "SELECT DISTINCT year FROM car_data WHERE year IS NOT NULL ORDER BY year DESC";
        $stmt = $this->db->query($sql);
        $years = $stmt->fetchAll();
        return array_column($years, 'year');
    }
    
    // 获取所有车型列表
    public function getModelList() {
        $sql = "SELECT DISTINCT model FROM car_data WHERE model IS NOT NULL AND model != '' ORDER BY model";
        $stmt = $this->db->query($sql);
        $models = $stmt->fetchAll();
        return array_column($models, 'model');
    }
}
?>