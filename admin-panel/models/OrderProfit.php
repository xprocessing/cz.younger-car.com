<?php
require_once APP_ROOT . '/includes/database.php';
require_once APP_ROOT . '/helpers/functions.php';

class OrderProfit {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 转换字段为数值（用于筛选和计算）
    private function convertCurrencyFields($data) {
        if (!is_array($data)) {
            return $data;
        }
        
        // CNY成本字段：需要转换为数值（没有货币符号）
        $cnyFields = [
            'wms_outbound_cost_amount', 
            'wms_shipping_price_amount'
        ];
        
        foreach ($cnyFields as $field) {
            if (isset($data[$field])) {
                $data[$field] = (float)($data[$field] ?? 0);
            }
        }
        
        // 带不同国家货币符号的字段：不转换，直接展示原始值
        // order_total_amount, profit_amount 保持原始字符串格式
        
        // 利润率字段：转换为数值用于筛选比较，同时保留原始值用于显示
        if (isset($data['profit_rate'])) {
            $originalRate = $data['profit_rate'];
            $data['profit_rate'] = parseCurrencyAmount($originalRate); // 用于筛选
            $data['profit_rate_original'] = $originalRate; // 用于显示
        }
        
        return $data;
    }
    
    // 根据ID获取订单利润
    public function getById($id) {
        $sql = "SELECT * FROM order_profit WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $data = $stmt->fetch();
        return $this->convertCurrencyFields($data);
    }
    
    // 根据订单号获取订单利润
    public function getByOrderNo($globalOrderNo) {
        $sql = "SELECT * FROM order_profit WHERE global_order_no = ?";
        $stmt = $this->db->query($sql, [$globalOrderNo]);
        $data = $stmt->fetch();
        return $this->convertCurrencyFields($data);
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
        $data = $stmt->fetchAll();
        
        // 转换每条记录的货币字段
        if (is_array($data)) {
            foreach ($data as &$row) {
                $row = $this->convertCurrencyFields($row);
            }
        }
        
        return $data;
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
                    store_id, global_order_no, warehouse_name, receiver_country, global_purchase_time, 
                    local_sku, order_total_amount, profit_amount, 
                    profit_rate, wms_outbound_cost_amount, wms_shipping_price_amount, update_time
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $params = [
            $data['store_id'] ?? '',
            $data['global_order_no'] ?? '',
            $data['warehouse_name'] ?? '',
            $data['receiver_country'] ?? '',
            $data['global_purchase_time'] ?? '',
            $data['local_sku'] ?? '',
            $data['order_total_amount'] ?? '0.00',
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
                    warehouse_name = ?, 
                    receiver_country = ?, 
                    global_purchase_time = ?, 
                    local_sku = ?, 
                    order_total_amount = ?, 
                    profit_amount = ?, 
                    profit_rate = ?, 
                    wms_outbound_cost_amount = ?, 
                    wms_shipping_price_amount = ?, 
                    update_time = ?
                WHERE id = ?";
        $params = [
            $data['store_id'] ?? '',
            $data['global_order_no'] ?? '',
            $data['warehouse_name'] ?? '',
            $data['receiver_country'] ?? '',
            $data['global_purchase_time'] ?? '',
            $data['local_sku'] ?? '',
            $data['order_total_amount'] ?? '0.00',
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
                OR warehouse_name LIKE ? 
                ORDER BY id DESC";
        $params = ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"];
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $params[] = $limit;
            $params[] = $offset;
        }
        
        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();
        
        // 转换每条记录的货币字段
        if (is_array($data)) {
            foreach ($data as &$row) {
                $row = $this->convertCurrencyFields($row);
            }
        }
        
        return $data;
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
        $data = $stmt->fetchAll();
        
        // 转换每条记录的货币字段
        if (is_array($data)) {
            foreach ($data as &$row) {
                $row = $this->convertCurrencyFields($row);
            }
        }
        
        return $data;
    }
    
    // 支持多条件搜索的订单利润
    public function searchWithFilters($keyword = '', $storeId = '', $rateMin = '', $rateMax = '', $limit = null, $offset = 0) {
        $sql = "SELECT * FROM order_profit WHERE 1=1";
        $params = [];
        
        // 关键词搜索
        if ($keyword) {
            $sql .= " AND (global_order_no LIKE ? OR store_id LIKE ? OR local_sku LIKE ? OR receiver_country LIKE ? OR warehouse_name LIKE ?)";
            $params = array_merge($params, ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"]);
        }
        
        // 店铺筛选
        if ($storeId) {
            $sql .= " AND store_id = ?";
            $params[] = $storeId;
        }
        
        $sql .= " ORDER BY id DESC";
        
        // 先获取所有符合条件的记录，不应用分页
        $stmt = $this->db->query($sql, $params);
        $allData = $stmt->fetchAll();
        
        // 转换每条记录的货币字段
        $convertedData = [];
        if (is_array($allData)) {
            foreach ($allData as $row) {
                $convertedRow = $this->convertCurrencyFields($row);
                $convertedData[] = $convertedRow;
            }
        }
        
        // 利润率区间筛选
        $filteredData = [];
        foreach ($convertedData as $row) {
            $include = true;
            
            if ($rateMin !== '' && $row['profit_rate'] < (float)$rateMin) {
                $include = false;
            }
            
            if ($rateMax !== '' && $row['profit_rate'] > (float)$rateMax) {
                $include = false;
            }
            
            if ($include) {
                $filteredData[] = $row;
            }
        }
        
        // 应用分页
        if ($limit !== null) {
            $filteredData = array_slice($filteredData, $offset, $limit);
        }
        
        return $filteredData;
    }
    
    // 支持多条件搜索的结果数量
    public function getSearchWithFiltersCount($keyword = '', $storeId = '', $rateMin = '', $rateMax = '') {
        $sql = "SELECT * FROM order_profit WHERE 1=1";
        $params = [];
        
        // 关键词搜索
        if ($keyword) {
            $sql .= " AND (global_order_no LIKE ? OR store_id LIKE ? OR local_sku LIKE ? OR receiver_country LIKE ?)";
            $params = array_merge($params, ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"]);
        }
        
        // 店铺筛选
        if ($storeId) {
            $sql .= " AND store_id = ?";
            $params[] = $storeId;
        }
        
        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();
        
        // 转换货币字段并进行利润率区间过滤
        $count = 0;
        if (is_array($data)) {
            foreach ($data as $row) {
                $convertedRow = $this->convertCurrencyFields($row);
                
                // 利润率区间过滤
                $profitRate = $convertedRow['profit_rate'];
                $include = true;
                
                if ($rateMin !== '' && $profitRate < (float)$rateMin) {
                    $include = false;
                }
                
                if ($rateMax !== '' && $profitRate > (float)$rateMax) {
                    $include = false;
                }
                
                if ($include) {
                    $count++;
                }
            }
        }
        
        return $count;
    }
    
    // 获取利润统计
    public function getProfitStats($startDate = null, $endDate = null, $storeId = null) {
        $sql = "SELECT * FROM order_profit WHERE 1=1";
        
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
        $data = $stmt->fetchAll();
        
        // 手动计算统计数据
        $stats = [
            'order_count' => 0,
            'avg_profit_rate' => 0,
            'wms_cost' => 0,
            'wms_shipping' => 0,
            'currency_stats' => [], // 按货币类型分组的统计
            'positive_orders' => 0,
            'negative_orders' => 0
        ];
        
        if (is_array($data) && count($data) > 0) {
            $stats['order_count'] = count($data);
            $totalRate = 0;
            $rateCount = 0;
            $positiveOrders = 0;
            $negativeOrders = 0;
            
            foreach ($data as $row) {
                $convertedRow = $this->convertCurrencyFields($row);
                
                // 计算WMS成本
                $stats['wms_cost'] += $convertedRow['wms_outbound_cost_amount'];
                $stats['wms_shipping'] += $convertedRow['wms_shipping_price_amount'];
                
                // 计算利润率
                if ($convertedRow['profit_rate'] !== 0) {
                    $totalRate += $convertedRow['profit_rate'];
                    $rateCount++;
                }
                
                // 计算正负订单数量
                if ($convertedRow['profit_rate'] > 0) {
                    $positiveOrders++;
                } elseif ($convertedRow['profit_rate'] < 0) {
                    $negativeOrders++;
                }
                
                // 按货币类型分组统计
                if (isset($row['order_total_amount']) && !empty($row['order_total_amount'])) {
                    // 提取货币符号
                    preg_match('/^([^\d-]+)/', $row['order_total_amount'], $matches);
                    $currency = $matches[1] ?? 'Unknown';
                    
                    // 确保该货币类型的统计数组存在
                    if (!isset($stats['currency_stats'][$currency])) {
                        $stats['currency_stats'][$currency] = [
                            'order_count' => 0,
                            'total_amount' => 0,
                            'total_profit' => 0
                        ];
                    }
                    
                    // 解析数值并累加
                    $amount = parseCurrencyAmount($row['order_total_amount']);
                    $profit = parseCurrencyAmount($row['profit_amount'] ?? '0');
                    
                    $stats['currency_stats'][$currency]['order_count']++;
                    $stats['currency_stats'][$currency]['total_amount'] += $amount;
                    $stats['currency_stats'][$currency]['total_profit'] += $profit;
                }
            }
            
            // 计算平均利润率
            $stats['avg_profit_rate'] = $rateCount > 0 ? ($totalRate / $rateCount) : 0;
            $stats['positive_orders'] = $positiveOrders;
            $stats['negative_orders'] = $negativeOrders;
        }
        
        return $stats;
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
                    store_id, global_order_no, receiver_country, warehouse_name, global_purchase_time, 
                    local_sku, order_total_amount, profit_amount, 
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
                $item['warehouse_name'] ?? '',
                $item['global_purchase_time'] ?? '',
                $item['local_sku'] ?? '',
                $item['order_total_amount'] ?? '0.00',
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