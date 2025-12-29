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
        $sql = "SELECT op.*, s.platform_name, s.store_name 
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                WHERE op.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $data = $stmt->fetch();
        return $this->convertCurrencyFields($data);
    }
    
    // 根据订单号获取订单利润
    public function getByOrderNo($globalOrderNo) {
        $sql = "SELECT op.*, s.platform_name, s.store_name 
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                WHERE op.global_order_no = ?";
        $stmt = $this->db->query($sql, [$globalOrderNo]);
        $data = $stmt->fetch();
        return $this->convertCurrencyFields($data);
    }
    
    // 获取所有订单利润
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT op.*, s.platform_name, s.store_name 
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                ORDER BY op.id DESC";
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
        $sql = "SELECT op.*, s.platform_name, s.store_name 
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                WHERE op.global_order_no LIKE ? 
                OR op.store_id LIKE ? 
                OR op.local_sku LIKE ? 
                OR op.receiver_country LIKE ? 
                OR op.warehouse_name LIKE ? 
                ORDER BY op.id DESC";
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
        $sql = "SELECT op.*, s.platform_name, s.store_name 
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                WHERE op.store_id = ? 
                ORDER BY op.id DESC";
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
    public function searchWithFilters($keyword = '', $platformName = '', $storeId = '', $warehouseName = '', $startDate = '', $endDate = '', $rateMin = '', $rateMax = '', $limit = null, $offset = 0) {
        $sql = "SELECT op.*, s.platform_name, s.store_name 
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                WHERE 1=1";
        $params = [];
        
        // 关键词搜索
        if ($keyword) {
            $sql .= " AND (op.global_order_no LIKE ? OR op.store_id LIKE ? OR op.local_sku LIKE ? OR op.receiver_country LIKE ? OR op.warehouse_name LIKE ?)";
            $params = array_merge($params, ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"]);
        }
        
        // 平台名称筛选
        if ($platformName) {
            $sql .= " AND s.platform_name = ?";
            $params[] = $platformName;
        }
        
        // 店铺筛选
        if ($storeId) {
            $sql .= " AND op.store_id = ?";
            $params[] = $storeId;
        }
        
        // 发货仓库筛选
        if ($warehouseName) {
            $sql .= " AND op.warehouse_name = ?";
            $params[] = $warehouseName;
        }
        
        // 下单时间起始筛选
        if ($startDate) {
            $sql .= " AND DATE(op.global_purchase_time) >= ?";
            $params[] = $startDate;
        }
        
        // 下单时间结束筛选
        if ($endDate) {
            $sql .= " AND DATE(op.global_purchase_time) <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " ORDER BY op.id DESC";
        
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
    public function getSearchWithFiltersCount($keyword = '', $platformName = '', $storeId = '', $warehouseName = '', $startDate = '', $endDate = '', $rateMin = '', $rateMax = '') {
        $sql = "SELECT op.* FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                WHERE 1=1";
        $params = [];
        
        // 关键词搜索
        if ($keyword) {
            $sql .= " AND (op.global_order_no LIKE ? OR op.store_id LIKE ? OR op.local_sku LIKE ? OR op.receiver_country LIKE ? OR op.warehouse_name LIKE ?)";
            $params = array_merge($params, ["%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%", "%$keyword%"]);
        }
        
        // 平台名称筛选
        if ($platformName) {
            $sql .= " AND s.platform_name = ?";
            $params[] = $platformName;
        }
        
        // 店铺筛选
        if ($storeId) {
            $sql .= " AND op.store_id = ?";
            $params[] = $storeId;
        }
        
        // 发货仓库筛选
        if ($warehouseName) {
            $sql .= " AND op.warehouse_name = ?";
            $params[] = $warehouseName;
        }
        
        // 下单时间起始筛选
        if ($startDate) {
            $sql .= " AND DATE(op.global_purchase_time) >= ?";
            $params[] = $startDate;
        }
        
        // 下单时间结束筛选
        if ($endDate) {
            $sql .= " AND DATE(op.global_purchase_time) <= ?";
            $params[] = $endDate;
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
            $sql .= " AND DATE(global_purchase_time) >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND DATE(global_purchase_time) <= ?";
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
        $sql = "SELECT store_id, platform_name, store_name FROM store ORDER BY platform_name, store_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 获取发货仓库列表
    public function getWarehouseList() {
        $sql = "SELECT DISTINCT warehouse_name FROM order_profit WHERE warehouse_name IS NOT NULL AND warehouse_name != '' ORDER BY warehouse_name";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // 根据store_id获取店铺详细信息
    public function getStoreInfo($storeId) {
        $sql = "SELECT platform_name, store_name FROM store WHERE store_id = ?";
        $stmt = $this->db->query($sql, [$storeId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
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
    
    // 获取所有符合筛选条件的数据（用于导出）
    public function getAllWithFilters($keyword = '', $platformName = '', $storeId = '', $warehouseName = '', $startDate = '', $endDate = '', $rateMin = '', $rateMax = '') {
        // 限制最多100条数据
        return $this->searchWithFilters($keyword, $platformName, $storeId, $warehouseName, $startDate, $endDate, $rateMin, $rateMax, 100, 0);
    }
    
    // 按平台统计最近30天的利润和利润率
    public function getPlatformStats($last30DaysStart, $endDate) {
        $sql = "SELECT op.*, s.platform_name
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                WHERE DATE(op.global_purchase_time) >= ? AND DATE(op.global_purchase_time) <= ?
                ORDER BY s.platform_name";
        
        $params = [$last30DaysStart, $endDate];
        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();
        
        // 按平台分组统计
        $platformStats = [];
        
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $row) {
                $platformName = $row['platform_name'] ?? '未知平台';
                
                // 确保该平台的统计数组存在
                if (!isset($platformStats[$platformName])) {
                    $platformStats[$platformName] = [
                        'platform_name' => $platformName,
                        'order_count' => 0,
                        'total_profit' => 0,
                        'total_profit_rate' => 0,
                        'avg_profit_rate' => 0
                    ];
                }
                
                // 解析数值并累加
                $profit = parseCurrencyAmount($row['profit_amount'] ?? '0');
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                
                $platformStats[$platformName]['order_count']++;
                $platformStats[$platformName]['total_profit'] += $profit;
                $platformStats[$platformName]['total_profit_rate'] += $profitRate;
            }
            
            // 计算平均利润率
            foreach ($platformStats as &$stat) {
                $stat['avg_profit_rate'] = $stat['order_count'] > 0 ? ($stat['total_profit_rate'] / $stat['order_count']) : 0;
            }
        }
        
        return array_values($platformStats);
    }
    
    // 按店铺统计最近30天的利润和利润率
    public function getStoreStats($last30DaysStart, $endDate) {
        $sql = "SELECT op.*, s.platform_name, s.store_name
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                WHERE DATE(op.global_purchase_time) >= ? AND DATE(op.global_purchase_time) <= ?
                ORDER BY s.platform_name, s.store_name";
        
        $params = [$last30DaysStart, $endDate];
        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();
        
        // 按店铺分组统计
        $storeStats = [];
        
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $row) {
                $storeId = $row['store_id'] ?? '未知店铺';
                $platformName = $row['platform_name'] ?? '未知平台';
                $storeName = $row['store_name'] ?? '未知店铺名称';
                
                // 确保该店铺的统计数组存在
                if (!isset($storeStats[$storeId])) {
                    $storeStats[$storeId] = [
                        'store_id' => $storeId,
                        'platform_name' => $platformName,
                        'store_name' => $storeName,
                        'order_count' => 0,
                        'total_profit' => 0,
                        'total_profit_rate' => 0,
                        'avg_profit_rate' => 0
                    ];
                }
                
                // 解析数值并累加
                $profit = parseCurrencyAmount($row['profit_amount'] ?? '0');
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                
                $storeStats[$storeId]['order_count']++;
                $storeStats[$storeId]['total_profit'] += $profit;
                $storeStats[$storeId]['total_profit_rate'] += $profitRate;
            }
            
            // 计算平均利润率
            foreach ($storeStats as &$stat) {
                $stat['avg_profit_rate'] = $stat['order_count'] > 0 ? ($stat['total_profit_rate'] / $stat['order_count']) : 0;
            }
            
            // 先按平台名称排序，再按总利润从高到低排序
            usort($storeStats, function($a, $b) {
                if ($a['platform_name'] === $b['platform_name']) {
                    return $b['total_profit'] - $a['total_profit'];
                }
                return strcmp($a['platform_name'], $b['platform_name']);
            });
        }
        
        return array_values($storeStats);
    }
    
    // 获取利润率分布数据
    public function getProfitRateDistribution($startDate, $endDate) {
        $sql = "SELECT profit_rate
                FROM order_profit
                WHERE DATE(global_purchase_time) >= ? AND DATE(global_purchase_time) <= ?";
        
        $params = [$startDate, $endDate];
        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();
        
        // 初始化四个区间的计数
        $distribution = [
            'negative' => 0,      // 亏损 (<0%)
            'low' => 0,           // 低利润 (0-5%)
            'normal' => 0,        // 正常利润 (5-15%)
            'high' => 0           // 高利润 (>15%)
        ];
        
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $row) {
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                
                if ($profitRate < 0) {
                    $distribution['negative']++;
                } elseif ($profitRate >= 0 && $profitRate < 5) {
                    $distribution['low']++;
                } elseif ($profitRate >= 5 && $profitRate <= 15) {
                    $distribution['normal']++;
                } else {
                    $distribution['high']++;
                }
            }
        }
        
        return $distribution;
    }
    
    // 按品牌统计最近30天的利润和利润率
    public function getBrandStats($last30DaysStart, $endDate) {
        $sql = "SELECT op.*, p.brand_name
                FROM order_profit op
                LEFT JOIN products p ON op.local_sku = p.sku
                WHERE DATE(op.global_purchase_time) >= ? AND DATE(op.global_purchase_time) <= ?";
        
        $params = [$last30DaysStart, $endDate];
        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();
        
        // 按品牌分组统计
        $brandStats = [];
        
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $row) {
                $brandName = $row['brand_name'] ?? '未知品牌';
                
                // 确保该品牌的统计数组存在
                if (!isset($brandStats[$brandName])) {
                    $brandStats[$brandName] = [
                        'brand_name' => $brandName,
                        'order_count' => 0,
                        'total_profit' => 0,
                        'total_profit_rate' => 0,
                        'avg_profit_rate' => 0
                    ];
                }
                
                // 解析数值并累加
                $profit = parseCurrencyAmount($row['profit_amount'] ?? '0');
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                
                $brandStats[$brandName]['order_count']++;
                $brandStats[$brandName]['total_profit'] += $profit;
                $brandStats[$brandName]['total_profit_rate'] += $profitRate;
            }
            
            // 计算平均利润率
            foreach ($brandStats as &$stat) {
                $stat['avg_profit_rate'] = $stat['order_count'] > 0 ? ($stat['total_profit_rate'] / $stat['order_count']) : 0;
            }
            
            // 按总利润从高到低排序
            usort($brandStats, function($a, $b) {
                return $b['total_profit'] - $a['total_profit'];
            });
        }
        
        return array_values($brandStats);
    }
    
    // 按SKU统计最近30天的销量、利润和利润率，按销量排序取前100名
    public function getSkuStats($last30DaysStart, $endDate) {
        $sql = "SELECT op.*
                FROM order_profit op
                WHERE DATE(op.global_purchase_time) >= ? AND DATE(op.global_purchase_time) <= ?";
        
        $params = [$last30DaysStart, $endDate];
        $stmt = $this->db->query($sql, $params);
        $data = $stmt->fetchAll();
        
        // 按SKU分组统计
        $skuStats = [];
        
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $row) {
                $sku = $row['local_sku'] ?? '未知SKU';
                
                // 确保该SKU的统计数组存在
                if (!isset($skuStats[$sku])) {
                    $skuStats[$sku] = [
                        'sku' => $sku,
                        'order_count' => 0,
                        'total_profit' => 0,
                        'total_profit_rate' => 0,
                        'avg_profit_rate' => 0
                    ];
                }
                
                // 解析数值并累加
                $profit = parseCurrencyAmount($row['profit_amount'] ?? '0');
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                
                $skuStats[$sku]['order_count']++;
                $skuStats[$sku]['total_profit'] += $profit;
                $skuStats[$sku]['total_profit_rate'] += $profitRate;
            }
            
            // 计算平均利润率
            foreach ($skuStats as &$stat) {
                $stat['avg_profit_rate'] = $stat['order_count'] > 0 ? ($stat['total_profit_rate'] / $stat['order_count']) : 0;
            }
            
            // 按总销量从高到低排序，取前100名
            usort($skuStats, function($a, $b) {
                return $b['order_count'] - $a['order_count'];
            });
            
            // 只返回前100名
            $skuStats = array_slice($skuStats, 0, 100);
        }
        
        return $skuStats;
    }
}