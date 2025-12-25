<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/OrderProfit.php';
require_once __DIR__ . '/helpers/functions.php';

$last30DaysStart = date('Y-m-d', strtotime('-30 days'));
$today = date('Y-m-d');

echo "<!DOCTYPE html>
<html lang='zh-CN'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>调试 Stats 控制器</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-4'>
        <h1>调试 Stats 控制器</h1>
        
        <div class='alert alert-info'>
            <strong>查询日期范围:</strong> {$last30DaysStart} 到 {$today}
        </div>";

try {
    $orderProfitModel = new OrderProfit();
    
    echo "<div class='card mb-3'>";
    echo "<div class='card-header'>步骤1: 调用 getPlatformStats</div>";
    echo "<div class='card-body'>";
    
    $platformStats = $orderProfitModel->getPlatformStats($last30DaysStart, $today);
    
    echo "<p><strong>返回结果类型:</strong> " . gettype($platformStats) . "</p>";
    echo "<p><strong>是否为空:</strong> " . (empty($platformStats) ? '是' : '否') . "</p>";
    echo "<p><strong>元素数量:</strong> " . count($platformStats) . "</p>";
    
    if (empty($platformStats)) {
        echo "<div class='alert alert-warning'>platformStats 为空数组，页面会显示'暂无数据'</div>";
    } else {
        echo "<table class='table'>";
        echo "<thead><tr><th>平台名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr></thead>";
        echo "<tbody>";
        foreach ($platformStats as $platform) {
            echo "<tr>";
            echo "<td>{$platform['platform_name']}</td>";
            echo "<td>{$platform['order_count']}</td>";
            echo "<td>¥" . number_format($platform['total_profit'], 2) . "</td>";
            echo "<td>" . number_format($platform['avg_profit_rate'], 2) . "%</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "<div class='alert alert-success'>platformStats 有数据，应该可以正常显示</div>";
    }
    echo "</div></div>";
    
    echo "<div class='card mb-3'>";
    echo "<div class='card-header'>步骤2: 调用 getStoreStats</div>";
    echo "<div class='card-body'>";
    
    $storeStats = $orderProfitModel->getStoreStats($last30DaysStart, $today);
    
    echo "<p><strong>返回结果类型:</strong> " . gettype($storeStats) . "</p>";
    echo "<p><strong>是否为空:</strong> " . (empty($storeStats) ? '是' : '否') . "</p>";
    echo "<p><strong>元素数量:</strong> " . count($storeStats) . "</p>";
    
    if (empty($storeStats)) {
        echo "<div class='alert alert-warning'>storeStats 为空数组，页面会显示'暂无数据'</div>";
    } else {
        echo "<table class='table'>";
        echo "<thead><tr><th>平台名称</th><th>店铺名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr></thead>";
        echo "<tbody>";
        foreach ($storeStats as $store) {
            echo "<tr>";
            echo "<td>{$store['platform_name']}</td>";
            echo "<td>{$store['store_name']}</td>";
            echo "<td>{$store['order_count']}</td>";
            echo "<td>¥" . number_format($store['total_profit'], 2) . "</td>";
            echo "<td>" . number_format($store['avg_profit_rate'], 2) . "%</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
        echo "<div class='alert alert-success'>storeStats 有数据，应该可以正常显示</div>";
    }
    echo "</div></div>";
    
    echo "<div class='card mb-3'>";
    echo "<div class='card-header'>步骤3: 模拟视图渲染</div>";
    echo "<div class='card-body'>";
    
    echo "<h6>平台统计表格渲染:</h6>";
    echo "<table class='table table-hover'>";
    echo "<thead><tr><th>平台名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr></thead>";
    echo "<tbody>";
    if (!empty($platformStats)) {
        foreach ($platformStats as $platform) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($platform['platform_name']) . "</td>";
            echo "<td>" . number_format($platform['order_count']) . "</td>";
            echo "<td><strong>¥" . number_format($platform['total_profit'], 2) . "</strong></td>";
            $class = $platform['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger';
            echo "<td><strong class='{$class}'>" . number_format($platform['avg_profit_rate'], 2) . "%</strong></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4' class='text-center'>暂无数据</td></tr>";
    }
    echo "</tbody></table>";
    
    echo "<h6 class='mt-3'>店铺统计表格渲染:</h6>";
    echo "<table class='table table-hover'>";
    echo "<thead><tr><th>平台名称</th><th>店铺名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr></thead>";
    echo "<tbody>";
    if (!empty($storeStats)) {
        foreach ($storeStats as $store) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($store['platform_name']) . "</td>";
            echo "<td>" . htmlspecialchars($store['store_name']) . "</td>";
            echo "<td>" . number_format($store['order_count']) . "</td>";
            echo "<td><strong>¥" . number_format($store['total_profit'], 2) . "</strong></td>";
            $class = $store['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger';
            echo "<td><strong class='{$class}'>" . number_format($store['avg_profit_rate'], 2) . "%</strong></td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='5' class='text-center'>暂无数据</td></tr>";
    }
    echo "</tbody></table>";
    
    echo "</div></div>";
    
} catch (Exception $e) {
    echo "<div class='alert alert-danger'>";
    echo "<strong>错误:</strong> " . $e->getMessage();
    echo "<br><strong>文件:</strong> " . $e->getFile();
    echo "<br><strong>行号:</strong> " . $e->getLine();
    echo "</div>";
}

echo "    </div>
</body>
</html>";
?>
