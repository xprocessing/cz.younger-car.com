<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/helpers/functions.php';

$db = Database::getInstance();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>利润统计数据调试</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>利润统计数据调试</h1>
    
    <?php
    $last30DaysStart = date('Y-m-d', strtotime('-30 days'));
    $today = date('Y-m-d');
    
    echo "<div class='alert alert-info'>";
    echo "<strong>查询日期范围:</strong> {$last30DaysStart} 到 {$today}<br>";
    echo "</div>";
    
    // 1. 检查表中的总记录数
    $sql = "SELECT COUNT(*) as total FROM order_profit";
    $stmt = $db->query($sql);
    $result = $stmt->fetch();
    echo "<div class='alert alert-primary'>order_profit 表总记录数: <strong>{$result['total']}</strong></div>";
    
    // 2. 检查最近30天的记录数
    $sql = "SELECT COUNT(*) as total FROM order_profit WHERE DATE(global_purchase_time) >= ?";
    $stmt = $db->query($sql, [$last30DaysStart]);
    $result = $stmt->fetch();
    echo "<div class='alert alert-warning'>最近30天的记录数: <strong>{$result['total']}</strong></div>";
    
    // 3. 查看最近30天的记录
    $sql = "SELECT op.*, s.platform_name, s.store_name 
            FROM order_profit op
            LEFT JOIN store s ON op.store_id = s.store_id
            WHERE DATE(op.global_purchase_time) >= ?
            ORDER BY op.global_purchase_time DESC
            LIMIT 10";
    $stmt = $db->query($sql, [$last30DaysStart]);
    $data = $stmt->fetchAll();
    
    echo "<h3 class='mt-4'>最近30天的记录 (最多10条)</h3>";
    if (is_array($data) && count($data) > 0) {
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>ID</th><th>订单号</th><th>平台</th><th>店铺</th><th>时间</th><th>利润</th><th>利润率</th></tr></thead>";
        echo "<tbody>";
        foreach ($data as $row) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['global_order_no']}</td>";
            echo "<td>" . htmlspecialchars($row['platform_name'] ?? '未知') . "</td>";
            echo "<td>" . htmlspecialchars($row['store_name'] ?? '未知') . "</td>";
            echo "<td>{$row['global_purchase_time']}</td>";
            echo "<td>{$row['profit_amount']}</td>";
            echo "<td>{$row['profit_rate']}</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-danger'>无数据！</div>";
    }
    
    // 4. 检查 store 表
    $sql = "SELECT * FROM store LIMIT 5";
    $stmt = $db->query($sql);
    $storeData = $stmt->fetchAll();
    
    echo "<h3 class='mt-4'>store 表数据 (最多5条)</h3>";
    if (is_array($storeData) && count($storeData) > 0) {
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>店铺ID</th><th>平台</th><th>店铺名称</th></tr></thead>";
        echo "<tbody>";
        foreach ($storeData as $row) {
            echo "<tr>";
            echo "<td>{$row['store_id']}</td>";
            echo "<td>" . htmlspecialchars($row['platform_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['store_name']) . "</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-danger'>store 表无数据！</div>";
    }
    
    // 5. 模拟 getPlatformStats 查询
    echo "<h3 class='mt-4'>平台统计结果 (模拟 getPlatformStats)</h3>";
    $sql = "SELECT op.*, s.platform_name
            FROM order_profit op
            LEFT JOIN store s ON op.store_id = s.store_id
            WHERE DATE(op.global_purchase_time) >= ? AND DATE(op.global_purchase_time) <= ?
            ORDER BY s.platform_name";
    $params = [$last30DaysStart, $today];
    $stmt = $db->query($sql, $params);
    $platformData = $stmt->fetchAll();
    
    $platformStats = [];
    if (is_array($platformData) && count($platformData) > 0) {
        foreach ($platformData as $row) {
            $platformName = $row['platform_name'] ?? '未知平台';
            if (!isset($platformStats[$platformName])) {
                $platformStats[$platformName] = [
                    'platform_name' => $platformName,
                    'order_count' => 0,
                    'total_profit' => 0,
                    'total_profit_rate' => 0
                ];
            }
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
        
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>平台名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr></thead>";
        echo "<tbody>";
        foreach ($platformStats as $stat) {
            $class = $stat['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($stat['platform_name']) . "</td>";
            echo "<td>{$stat['order_count']}</td>";
            echo "<td>¥" . number_format($stat['total_profit'], 2) . "</td>";
            echo "<td class='{$class}'>" . number_format($stat['avg_profit_rate'], 2) . "%</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-danger'>平台统计无数据！</div>";
    }
    
    // 6. 模拟 getStoreStats 查询
    echo "<h3 class='mt-4'>店铺统计结果 (模拟 getStoreStats)</h3>";
    $sql = "SELECT op.*, s.platform_name, s.store_name
            FROM order_profit op
            LEFT JOIN store s ON op.store_id = s.store_id
            WHERE DATE(op.global_purchase_time) >= ? AND DATE(op.global_purchase_time) <= ?
            ORDER BY s.platform_name, s.store_name";
    $params = [$last30DaysStart, $today];
    $stmt = $db->query($sql, $params);
    $storeData = $stmt->fetchAll();
    
    $storeStats = [];
    if (is_array($storeData) && count($storeData) > 0) {
        foreach ($storeData as $row) {
            $storeId = $row['store_id'] ?? '未知店铺';
            $platformName = $row['platform_name'] ?? '未知平台';
            $storeName = $row['store_name'] ?? '未知店铺名称';
            if (!isset($storeStats[$storeId])) {
                $storeStats[$storeId] = [
                    'store_id' => $storeId,
                    'platform_name' => $platformName,
                    'store_name' => $storeName,
                    'order_count' => 0,
                    'total_profit' => 0,
                    'total_profit_rate' => 0
                ];
            }
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
        
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>平台名称</th><th>店铺名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr></thead>";
        echo "<tbody>";
        foreach ($storeStats as $stat) {
            $class = $stat['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger';
            echo "<tr>";
            echo "<td>" . htmlspecialchars($stat['platform_name']) . "</td>";
            echo "<td>" . htmlspecialchars($stat['store_name']) . "</td>";
            echo "<td>{$stat['order_count']}</td>";
            echo "<td>¥" . number_format($stat['total_profit'], 2) . "</td>";
            echo "<td class='{$class}'>" . number_format($stat['avg_profit_rate'], 2) . "%</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    } else {
        echo "<div class='alert alert-danger'>店铺统计无数据！</div>";
    }
    
    // 7. 检查 parseCurrencyAmount 函数
    echo "<h3 class='mt-4'>parseCurrencyAmount 函数测试</h3>";
    echo "<table class='table table-striped table-bordered'>";
    echo "<thead><tr><th>输入</th><th>输出</th></tr></thead>";
    echo "<tbody>";
    $testCases = ['¥100.00', '$50.00', '€75.50', '-123.45', '10.5%', '0', ''];
    foreach ($testCases as $test) {
        $result = parseCurrencyAmount($test);
        echo "<tr><td>" . htmlspecialchars($test) . "</td><td>{$result}</td></tr>";
    }
    echo "</tbody></table>";
    ?>
</div>
</body>
</html>
