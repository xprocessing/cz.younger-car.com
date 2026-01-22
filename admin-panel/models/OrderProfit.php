<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

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
        
        // CNY成本字段：保留原始字符串值，不做转换
        // wms_outbound_cost_amount, wms_shipping_price_amount 直接展示数据库原始值
        
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
        $sql = "SELECT op.*, s.platform_name, s.store_name, COALESCE(p.pic_url, '') as product_image
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                LEFT JOIN products p ON op.local_sku = p.sku
                WHERE op.id = ?";
        $stmt = $this->db->query($sql, [$id]);
        $data = $stmt->fetch();
        return $this->convertCurrencyFields($data);
    }
    
    // 根据订单号获取订单利润
    public function getByOrderNo($globalOrderNo) {
        $sql = "SELECT op.*, s.platform_name, s.store_name, COALESCE(p.pic_url, '') as product_image
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                LEFT JOIN products p ON op.local_sku = p.sku
                WHERE op.global_order_no = ?";
        $stmt = $this->db->query($sql, [$globalOrderNo]);
        $data = $stmt->fetch();
        return $this->convertCurrencyFields($data);
    }
    
    // 获取所有订单利润
    public function getAll($limit = null, $offset = 0) {
        $sql = "SELECT op.*, s.platform_name, s.store_name, COALESCE(p.pic_url, '') as product_image
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                LEFT JOIN products p ON op.local_sku = p.sku
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
        $sql = "SELECT op.*, s.platform_name, s.store_name, COALESCE(p.pic_url, '') as product_image
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                LEFT JOIN products p ON op.local_sku = p.sku
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
        $sql = "SELECT op.*, s.platform_name, s.store_name, COALESCE(p.pic_url, '') as product_image
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                LEFT JOIN products p ON op.local_sku = p.sku
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
        $sql = "SELECT op.*, s.platform_name, s.store_name, COALESCE(p.pic_url, '') as product_image
                FROM order_profit op 
                LEFT JOIN store s ON op.store_id = s.store_id 
                LEFT JOIN products p ON op.local_sku = p.sku
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
                LEFT JOIN products p ON op.local_sku = p.sku
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
        $sql = "SELECT op.*, COALESCE(p.pic_url, '') as product_image
                FROM order_profit op
                LEFT JOIN products p ON op.local_sku = p.sku
                WHERE 1=1";
        
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
            'total_amount' => 0, // 总订单金额
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
                
                // 计算总订单金额（订单金额统一为美元）
                if (isset($row['order_total_amount']) && !empty($row['order_total_amount'])) {
                    $stats['total_amount'] += parseCurrencyAmount($row['order_total_amount']);
                }
                
                // 计算WMS成本（需要转换为数值）
                $stats['wms_cost'] += parseCurrencyAmount($convertedRow['wms_outbound_cost_amount'] ?? '0');
                $stats['wms_shipping'] += parseCurrencyAmount($convertedRow['wms_shipping_price_amount'] ?? '0');
                
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
        $sql = "SELECT op.*, s.platform_name, COALESCE(p.pic_url, '') as product_image
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                LEFT JOIN products p ON op.local_sku = p.sku
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
                        'avg_profit_rate' => 0,
                        'total_amount' => 0,
                        'total_wms_cost' => 0,
                        'total_wms_shipping' => 0
                    ];
                }
                
                // 解析数值并累加
                $profit = parseCurrencyAmount($row['profit_amount'] ?? '0');
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                $amount = parseCurrencyAmount($row['order_total_amount'] ?? '0');
                $wmsCost = parseCurrencyAmount($row['wms_outbound_cost_amount'] ?? '0');
                $wmsShipping = parseCurrencyAmount($row['wms_shipping_price_amount'] ?? '0');
                
                $platformStats[$platformName]['order_count']++;
                $platformStats[$platformName]['total_profit'] += $profit;
                $platformStats[$platformName]['total_profit_rate'] += $profitRate;
                $platformStats[$platformName]['total_amount'] += $amount;
                $platformStats[$platformName]['total_wms_cost'] += $wmsCost;
                $platformStats[$platformName]['total_wms_shipping'] += $wmsShipping;
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
                        'avg_profit_rate' => 0,
                        'total_amount' => 0,
                        'total_wms_cost' => 0,
                        'total_wms_shipping' => 0
                    ];
                }
                
                // 解析数值并累加
                $profit = parseCurrencyAmount($row['profit_amount'] ?? '0');
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                $amount = parseCurrencyAmount($row['order_total_amount'] ?? '0');
                $wmsCost = parseCurrencyAmount($row['wms_outbound_cost_amount'] ?? '0');
                $wmsShipping = parseCurrencyAmount($row['wms_shipping_price_amount'] ?? '0');
                
                $storeStats[$storeId]['order_count']++;
                $storeStats[$storeId]['total_profit'] += $profit;
                $storeStats[$storeId]['total_profit_rate'] += $profitRate;
                $storeStats[$storeId]['total_amount'] += $amount;
                $storeStats[$storeId]['total_wms_cost'] += $wmsCost;
                $storeStats[$storeId]['total_wms_shipping'] += $wmsShipping;
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
        
        // 初始化11个区间的计数
        $distribution = [
            'negative' => 0,       // 亏损 (<0%)
            'range_0_10' => 0,      // 0-10%
            'range_10_20' => 0,     // 10-20%
            'range_20_30' => 0,     // 20-30%
            'range_30_40' => 0,     // 30-40%
            'range_40_50' => 0,     // 40-50%
            'range_50_60' => 0,     // 50-60%
            'range_60_70' => 0,     // 60-70%
            'range_70_80' => 0,     // 70-80%
            'range_80_90' => 0,     // 80-90%
            'range_90_plus' => 0     // >90%
        ];
        
        if (is_array($data) && count($data) > 0) {
            foreach ($data as $row) {
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                
                if ($profitRate < 0) {
                    $distribution['negative']++;
                } elseif ($profitRate >= 0 && $profitRate < 10) {
                    $distribution['range_0_10']++;
                } elseif ($profitRate >= 10 && $profitRate < 20) {
                    $distribution['range_10_20']++;
                } elseif ($profitRate >= 20 && $profitRate < 30) {
                    $distribution['range_20_30']++;
                } elseif ($profitRate >= 30 && $profitRate < 40) {
                    $distribution['range_30_40']++;
                } elseif ($profitRate >= 40 && $profitRate < 50) {
                    $distribution['range_40_50']++;
                } elseif ($profitRate >= 50 && $profitRate < 60) {
                    $distribution['range_50_60']++;
                } elseif ($profitRate >= 60 && $profitRate < 70) {
                    $distribution['range_60_70']++;
                } elseif ($profitRate >= 70 && $profitRate < 80) {
                    $distribution['range_70_80']++;
                } elseif ($profitRate >= 80 && $profitRate < 90) {
                    $distribution['range_80_90']++;
                } else {
                    $distribution['range_90_plus']++;
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
                        'avg_profit_rate' => 0,
                        'total_amount' => 0,
                        'total_wms_cost' => 0,
                        'total_wms_shipping' => 0
                    ];
                }
                
                // 解析数值并累加
                $profit = parseCurrencyAmount($row['profit_amount'] ?? '0');
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                $amount = parseCurrencyAmount($row['order_total_amount'] ?? '0');
                $wmsCost = parseCurrencyAmount($row['wms_outbound_cost_amount'] ?? '0');
                $wmsShipping = parseCurrencyAmount($row['wms_shipping_price_amount'] ?? '0');
                
                $brandStats[$brandName]['order_count']++;
                $brandStats[$brandName]['total_profit'] += $profit;
                $brandStats[$brandName]['total_profit_rate'] += $profitRate;
                $brandStats[$brandName]['total_amount'] += $amount;
                $brandStats[$brandName]['total_wms_cost'] += $wmsCost;
                $brandStats[$brandName]['total_wms_shipping'] += $wmsShipping;
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
                        'avg_profit_rate' => 0,
                        'total_amount' => 0,
                        'total_wms_cost' => 0,
                        'total_wms_shipping' => 0
                    ];
                }
                
                // 解析数值并累加
                $profit = parseCurrencyAmount($row['profit_amount'] ?? '0');
                $profitRate = parseCurrencyAmount($row['profit_rate'] ?? '0');
                $amount = parseCurrencyAmount($row['order_total_amount'] ?? '0');
                $wmsCost = parseCurrencyAmount($row['wms_outbound_cost_amount'] ?? '0');
                $wmsShipping = parseCurrencyAmount($row['wms_shipping_price_amount'] ?? '0');
                
                $skuStats[$sku]['order_count']++;
                $skuStats[$sku]['total_profit'] += $profit;
                $skuStats[$sku]['total_profit_rate'] += $profitRate;
                $skuStats[$sku]['total_amount'] += $amount;
                $skuStats[$sku]['total_wms_cost'] += $wmsCost;
                $skuStats[$sku]['total_wms_shipping'] += $wmsShipping;
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
    
    public function getDailySalesStats($last30DaysStart, $endDate) {
        $sql = "SELECT 
                    DATE(global_purchase_time) as sale_date,
                    COUNT(*) as order_count
                FROM order_profit
                WHERE global_purchase_time >= ? 
                AND global_purchase_time <= ?
                GROUP BY DATE(global_purchase_time)
                ORDER BY sale_date ASC";
        
        $stmt = $this->db->query($sql, [$last30DaysStart, $endDate]);
        return $stmt->fetchAll();
    }
    
    // 获取各平台销售额占比数据
    public function getPlatformSalesPercentage($startDate = null, $endDate = null) {
        $sql = "SELECT s.platform_name, SUM(CAST(REPLACE(REPLACE(REPLACE(op.order_total_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2))) as total_sales
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                WHERE 1=1";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND DATE(op.global_purchase_time) >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND DATE(op.global_purchase_time) <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY s.platform_name
                  ORDER BY total_sales DESC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取各平台订单总量占比数据
    public function getPlatformOrderCountPercentage($startDate = null, $endDate = null) {
        $sql = "SELECT s.platform_name, COUNT(*) as order_count
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                WHERE 1=1";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND DATE(op.global_purchase_time) >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND DATE(op.global_purchase_time) <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY s.platform_name
                  ORDER BY order_count DESC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取各平台毛利润占比数据
    public function getPlatformProfitPercentage($startDate = null, $endDate = null) {
        $sql = "SELECT s.platform_name, SUM(CAST(REPLACE(REPLACE(REPLACE(op.profit_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2))) as total_profit
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                WHERE 1=1";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND DATE(op.global_purchase_time) >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND DATE(op.global_purchase_time) <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY s.platform_name
                  ORDER BY total_profit DESC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取各平台广告费占比数据
    public function getPlatformCostPercentage($startDate = null, $endDate = null) {
        $sql = "SELECT s.platform_name, SUM(c.cost) as total_cost
                FROM shop_costs c
                LEFT JOIN store s ON c.store_name COLLATE utf8mb4_unicode_ci = s.store_name COLLATE utf8mb4_unicode_ci
                WHERE 1=1";
        
        $params = [];
        
        if ($startDate) {
            $sql .= " AND c.cost_date >= ?";
            $params[] = $startDate;
        }
        
        if ($endDate) {
            $sql .= " AND c.cost_date <= ?";
            $params[] = $endDate;
        }
        
        $sql .= " GROUP BY s.platform_name
                  ORDER BY total_cost DESC";
        
        $stmt = $this->db->query($sql, $params);
        return $stmt->fetchAll();
    }
    
    // 获取最近60天各平台每日销售额数据
    public function getDailyPlatformSales($days = 60) {
        // 计算日期范围
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime("-$days days"));
        
        $sql = "SELECT 
                    DATE(op.global_purchase_time) as sale_date,
                    s.platform_name,
                    SUM(CAST(REPLACE(REPLACE(REPLACE(op.order_total_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2))) as total_sales
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                WHERE DATE(op.global_purchase_time) BETWEEN ? AND ?
                GROUP BY DATE(op.global_purchase_time), s.platform_name
                ORDER BY sale_date ASC, s.platform_name ASC";
        
        $params = [$startDate, $endDate];
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetchAll();
        
        // 处理数据格式，便于前端绘制折线图
        $dailySales = [];
        $platforms = [];
        
        // 收集所有平台和日期
        foreach ($result as $row) {
            $saleDate = $row['sale_date'];
            $platformName = $row['platform_name'] ?? '未知平台';
            
            // 确保日期数组存在
            if (!isset($dailySales[$saleDate])) {
                $dailySales[$saleDate] = [];
            }
            
            // 记录销售额
            $dailySales[$saleDate][$platformName] = floatval($row['total_sales']);
            
            // 收集平台名称
            if (!in_array($platformName, $platforms)) {
                $platforms[] = $platformName;
            }
        }
        
        // 生成连续的日期序列
        $continuousDates = [];
        $currentDate = $startDate;
        while (strtotime($currentDate) <= strtotime($endDate)) {
            $continuousDates[] = $currentDate;
            $currentDate = date('Y-m-d', strtotime($currentDate . ' +1 day'));
        }
        
        // 构建最终的返回数据结构
        $formattedData = [
            'dates' => $continuousDates,
            'platforms' => $platforms,
            'sales' => []
        ];
        
        // 填充每个日期每个平台的销售额数据
        foreach ($continuousDates as $date) {
            $dateData = [];
            foreach ($platforms as $platform) {
                $dateData[] = isset($dailySales[$date][$platform]) ? $dailySales[$date][$platform] : 0;
            }
            $formattedData['sales'][] = $dateData;
        }
        
        return $formattedData;
    }
    
    // 获取各个平台的月度销售额统计数据
    public function getPlatformMonthlySalesStats() {
        // 获取本月、上月、上上月的年份和月份
        $currentYearMonth = date('Y-m');
        $lastYearMonth = date('Y-m', strtotime('-1 month'));
        $lastLastYearMonth = date('Y-m', strtotime('-2 month'));
        
        // 计算每个月的开始和结束日期
        $currentMonthStart = $currentYearMonth . '-01';
        $currentMonthEnd = date('Y-m-t', strtotime($currentYearMonth));
        
        $lastMonthStart = $lastYearMonth . '-01';
        $lastMonthEnd = date('Y-m-t', strtotime($lastYearMonth));
        
        $lastLastMonthStart = $lastLastYearMonth . '-01';
        $lastLastMonthEnd = date('Y-m-t', strtotime($lastLastYearMonth));
        
        // 构建SQL查询
        $sql = "SELECT 
                    s.platform_name,
                    
                    -- 本月销售额
                    SUM(CASE 
                        WHEN DATE_FORMAT(op.global_purchase_time, '%Y-%m') = ? 
                        THEN CAST(REPLACE(REPLACE(REPLACE(op.order_total_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2)) 
                        ELSE 0 
                    END) as current_month_sales,
                    
                    -- 上月销售额
                    SUM(CASE 
                        WHEN DATE_FORMAT(op.global_purchase_time, '%Y-%m') = ? 
                        THEN CAST(REPLACE(REPLACE(REPLACE(op.order_total_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2)) 
                        ELSE 0 
                    END) as last_month_sales,
                    
                    -- 上上月销售额
                    SUM(CASE 
                        WHEN DATE_FORMAT(op.global_purchase_time, '%Y-%m') = ? 
                        THEN CAST(REPLACE(REPLACE(REPLACE(op.order_total_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2)) 
                        ELSE 0 
                    END) as last_last_month_sales
                
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                WHERE 
                    DATE(op.global_purchase_time) BETWEEN ? AND ?
                GROUP BY s.platform_name
                ORDER BY s.platform_name ASC";
        
        $params = [$currentYearMonth, $lastYearMonth, $lastLastYearMonth, $lastLastMonthStart, $currentMonthEnd];
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetchAll();
        
        // 处理结果，计算增长率
        $stats = [];
        foreach ($result as $row) {
            $platform = $row['platform_name'] ?? '未知平台';
            $currentMonthSales = floatval($row['current_month_sales']);
            $lastMonthSales = floatval($row['last_month_sales']);
            $lastLastMonthSales = floatval($row['last_last_month_sales']);
            
            // 计算增长率
            $growthRate = 0;
            if ($lastLastMonthSales > 0) {
                $growthRate = (($lastMonthSales - $lastLastMonthSales) / $lastLastMonthSales) * 100;
            }
            
            $stats[] = [
                'platform_name' => $platform,
                'current_month_sales' => $currentMonthSales,
                'last_month_sales' => $lastMonthSales,
                'last_last_month_sales' => $lastLastMonthSales,
                'growth_rate' => $growthRate
            ];
        }
        
        return $stats;
    }
    
    // 获取各个平台的月度订单量统计数据
    public function getPlatformMonthlyOrderStats() {
        // 获取本月、上月、上上月的年份和月份
        $currentYearMonth = date('Y-m');
        $lastYearMonth = date('Y-m', strtotime('-1 month'));
        $lastLastYearMonth = date('Y-m', strtotime('-2 month'));
        
        // 计算每个月的开始和结束日期
        $currentMonthStart = $currentYearMonth . '-01';
        $currentMonthEnd = date('Y-m-t', strtotime($currentYearMonth));
        
        $lastMonthStart = $lastYearMonth . '-01';
        $lastMonthEnd = date('Y-m-t', strtotime($lastYearMonth));
        
        $lastLastMonthStart = $lastLastYearMonth . '-01';
        $lastLastMonthEnd = date('Y-m-t', strtotime($lastLastYearMonth));
        
        // 构建SQL查询
        $sql = "SELECT 
                    s.platform_name,
                    
                    -- 本月订单量
                    COUNT(CASE 
                        WHEN DATE_FORMAT(op.global_purchase_time, '%Y-%m') = ? 
                        THEN op.id 
                        ELSE NULL 
                    END) as current_month_orders,
                    
                    -- 上月订单量
                    COUNT(CASE 
                        WHEN DATE_FORMAT(op.global_purchase_time, '%Y-%m') = ? 
                        THEN op.id 
                        ELSE NULL 
                    END) as last_month_orders,
                    
                    -- 上上月订单量
                    COUNT(CASE 
                        WHEN DATE_FORMAT(op.global_purchase_time, '%Y-%m') = ? 
                        THEN op.id 
                        ELSE NULL 
                    END) as last_last_month_orders
                
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                WHERE 
                    DATE(op.global_purchase_time) BETWEEN ? AND ?
                GROUP BY s.platform_name
                ORDER BY s.platform_name ASC";
        
        $params = [$currentYearMonth, $lastYearMonth, $lastLastYearMonth, $lastLastMonthStart, $currentMonthEnd];
        $stmt = $this->db->query($sql, $params);
        $result = $stmt->fetchAll();
        
        // 处理结果，计算增长率
        $stats = [];
        foreach ($result as $row) {
            $platform = $row['platform_name'] ?? '未知平台';
            $currentMonthOrders = intval($row['current_month_orders']);
            $lastMonthOrders = intval($row['last_month_orders']);
            $lastLastMonthOrders = intval($row['last_last_month_orders']);
            
            // 计算增长率
            $growthRate = 0;
            if ($lastLastMonthOrders > 0) {
                $growthRate = (($lastMonthOrders - $lastLastMonthOrders) / $lastLastMonthOrders) * 100;
            }
            
            $stats[] = [
                'platform_name' => $platform,
                'current_month_orders' => $currentMonthOrders,
                'last_month_orders' => $lastMonthOrders,
                'last_last_month_orders' => $lastLastMonthOrders,
                'growth_rate' => $growthRate
            ];
        }
        
        return $stats;
    }
}