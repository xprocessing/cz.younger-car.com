<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/OrderProfitController.php';

echo "<h1>测试控制器方法</h1>";

$orderProfitController = new OrderProfitController();

// 测试getPlatformStats
echo "<h2>测试 getPlatformStats</h2>";
$last30DaysStart = date('Y-m-d', strtotime('-30 days'));
$endDate = date('Y-m-d');

echo "<p>日期范围: {$last30DaysStart} 到 {$endDate}</p>";

// 通过反射调用私有方法或者直接访问model
$orderProfitModel = new OrderProfit();

echo "<h3>直接调用model方法</h3>";
$platformStats = $orderProfitModel->getPlatformStats($last30DaysStart, $endDate);
echo "<p>平台统计结果数量: " . count($platformStats) . "</p>";

if (!empty($platformStats)) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>平台名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr>";
    foreach ($platformStats as $platform) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($platform['platform_name']) . "</td>";
        echo "<td>" . number_format($platform['order_count']) . "</td>";
        echo "<td>" . number_format($platform['total_profit'], 2) . "</td>";
        echo "<td>" . number_format($platform['avg_profit_rate'], 2) . "%</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p><strong>暂无平台统计数据</strong></p>";
}

echo "<h3>直接调用model方法 - 店铺统计</h3>";
$storeStats = $orderProfitModel->getStoreStats($last30DaysStart, $endDate);
echo "<p>店铺统计结果数量: " . count($storeStats) . "</p>";

if (!empty($storeStats)) {
    echo "<table border='1' style='border-collapse: collapse;'>";
    echo "<tr><th>平台名称</th><th>店铺名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr>";
    foreach ($storeStats as $store) {
        echo "<tr>";
        echo "<td>" . htmlspecialchars($store['platform_name']) . "</td>";
        echo "<td>" . htmlspecialchars($store['store_name']) . "</td>";
        echo "<td>" . number_format($store['order_count']) . "</td>";
        echo "<td>" . number_format($store['total_profit'], 2) . "</td>";
        echo "<td>" . number_format($store['avg_profit_rate'], 2) . "%</td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p><strong>暂无店铺统计数据</strong></p>";
}

// 测试数据库原始查询
echo "<h3>测试原始SQL查询</h3>";
try {
    $db = Database::getInstance();
    
    $sql = "SELECT op.*, s.platform_name
            FROM order_profit op
            LEFT JOIN store s ON op.store_id = s.store_id
            WHERE DATE(op.global_purchase_time) >= ? AND DATE(op.global_purchase_time) <= ?
            LIMIT 10";
    
    $stmt = $db->query($sql, [$last30DaysStart, $endDate]);
    $data = $stmt->fetchAll();
    
    echo "<p>原始查询结果数量: " . count($data) . "</p>";
    
    if (!empty($data)) {
        echo "<table border='1' style='border-collapse: collapse;'>";
        echo "<tr><th>ID</th><th>订单号</th><th>购买时间</th><th>利润</th><th>平台</th></tr>";
        foreach ($data as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['global_order_no']) . "</td>";
            echo "<td>" . htmlspecialchars($row['global_purchase_time']) . "</td>";
            echo "<td>" . htmlspecialchars($row['profit_amount']) . "</td>";
            echo "<td>" . htmlspecialchars($row['platform_name']) . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<p><strong>原始查询没有返回数据</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>错误: " . $e->getMessage() . "</p>";
}
?>
