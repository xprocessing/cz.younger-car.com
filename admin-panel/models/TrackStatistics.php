<?php
require_once ADMIN_PANEL_DIR . '/includes/database.php';

class TrackStatistics {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    /**
     * 获取赛道统计数据
     * @return array 赛道统计数据
     */
    public function getTrackStatistics() {
        // 获取上月的开始和结束日期
        $lastMonthStart = date('Y-m-01', strtotime('-1 month'));
        $lastMonthEnd = date('Y-m-t', strtotime('-1 month'));

        // 1. 获取每个赛道的订单数据
        $orderSql = "
            SELECT 
                COALESCE(s.track_name, '未分类赛道') as track_name,
                COUNT(DISTINCT op.id) as order_count,
                SUM(CAST(REPLACE(REPLACE(REPLACE(op.order_total_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2))) as total_order_amount,
                SUM(CAST(REPLACE(REPLACE(REPLACE(op.profit_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2))) as total_profit
            FROM 
                order_profit op
            LEFT JOIN 
                store s ON op.store_id = s.store_id
            WHERE 
                DATE(op.global_purchase_time) BETWEEN ? AND ?
            GROUP BY 
                s.track_name
        ";
        $orderStats = $this->db->query($orderSql, [$lastMonthStart, $lastMonthEnd])->fetchAll();

        // 2. 获取每个赛道的费用数据
        $costSql = "
            SELECT 
                track_name,
                SUM(cost) as total_cost
            FROM 
                track_costs
            WHERE 
                cost_date BETWEEN ? AND ?
            GROUP BY 
                track_name
        ";
        $costStats = $this->db->query($costSql, [$lastMonthStart, $lastMonthEnd])->fetchAll();

        // 3. 获取上月的公司总成本
        $companyCostSql = "
            SELECT 
                SUM(cost) as total_company_cost
            FROM 
                company_costs
            WHERE 
                cost_date BETWEEN ? AND ?
        ";
        $companyCostResult = $this->db->query($companyCostSql, [$lastMonthStart, $lastMonthEnd])->fetch();
        $totalCompanyCost = $companyCostResult ? floatval($companyCostResult['total_company_cost']) : 0;

        // 4. 获取每个赛道的店铺费用
        $shopCostSql = "
            SELECT 
                track_name,
                SUM(cost) as total_shop_cost
            FROM 
                shop_costs
            WHERE 
                cost_date BETWEEN ? AND ?
                AND track_name IS NOT NULL AND track_name != ''
            GROUP BY 
                track_name
        ";
        $shopCostStats = $this->db->query($shopCostSql, [$lastMonthStart, $lastMonthEnd])->fetchAll();

        // 4. 合并数据并计算
        $trackStats = [];
        $trackNames = array_unique(array_merge(
            array_column($orderStats, 'track_name'),
            array_column($costStats, 'track_name'),
            array_column($shopCostStats, 'track_name')
        ));
        // 过滤掉null值
        $trackNames = array_filter($trackNames, function($value) {
            return $value !== null && $value !== '';
        });

        // 赛道数量（固定为3个）
        $trackCount = 3;

        foreach ($trackNames as $trackName) {
            // 查找对应赛道的订单数据
            $orderData = array_filter($orderStats, function($item) use ($trackName) {
                return $item['track_name'] === $trackName;
            });
            $orderData = reset($orderData) ?: ['order_count' => 0, 'total_order_amount' => 0, 'total_profit' => 0];

            // 查找对应赛道的费用数据
            $costData = array_filter($costStats, function($item) use ($trackName) {
                return $item['track_name'] === $trackName;
            });
            $costData = reset($costData) ?: ['total_cost' => 0];

            // 查找对应赛道的店铺费用数据
            $shopCostData = array_filter($shopCostStats, function($item) use ($trackName) {
                return $item['track_name'] === $trackName;
            });
            $shopCostData = reset($shopCostData) ?: ['total_shop_cost' => 0];

            // 计算分摊的公司成本
            $allocatedCompanyCost = $totalCompanyCost / $trackCount;

            // 直接使用数据库返回的数值类型
            $totalOrderAmount = floatval($orderData['total_order_amount']);
            $totalProfit = floatval($orderData['total_profit']);

            // 货币转换：将人民币转换为美元（假设汇率为7.0）
            $exchangeRate = 7.0;
            $costInUSD = floatval($costData['total_cost']) / $exchangeRate;
            $allocatedCompanyCostInUSD = $allocatedCompanyCost / $exchangeRate;
            $shopCostInUSD = floatval($shopCostData['total_shop_cost']) / $exchangeRate;
            
            // 计算净利润
            $netProfit = $totalProfit - $costInUSD - $allocatedCompanyCostInUSD - $shopCostInUSD;

            // 计算净利润率
            $netProfitMargin = $totalOrderAmount > 0 ? ($netProfit / $totalOrderAmount) * 100 : 0;

            $trackStats[] = [
                'track_name' => $trackName,
                'order_count' => intval($orderData['order_count']),
                'total_order_amount' => $totalOrderAmount,
                'total_profit' => $totalProfit,
                'total_cost' => $costInUSD,
                'allocated_company_cost' => $allocatedCompanyCostInUSD,
                'shop_cost' => $shopCostInUSD,
                'net_profit' => $netProfit,
                'net_profit_margin' => $netProfitMargin
            ];
        }

        return $trackStats;
    }

    /**
     * 获取赛道销售数据（用于饼图）
     * @return array 赛道销售数据
     */
    public function getTrackSalesData() {
        $stats = $this->getTrackStatistics();
        $data = [];

        foreach ($stats as $stat) {
            $data[] = [
                'label' => $stat['track_name'],
                'value' => $stat['total_order_amount']
            ];
        }

        return $data;
    }

    /**
     * 获取赛道利润数据（用于饼图）
     * @return array 赛道利润数据
     */
    public function getTrackProfitData() {
        $stats = $this->getTrackStatistics();
        $data = [];

        foreach ($stats as $stat) {
            $data[] = [
                'label' => $stat['track_name'],
                'value' => $stat['net_profit']
            ];
        }

        return $data;
    }
}
?>