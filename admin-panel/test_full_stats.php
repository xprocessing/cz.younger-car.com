<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/OrderProfit.php';
require_once __DIR__ . '/helpers/functions.php';

session_start();

// 检查登录状态
if (!isLoggedIn()) {
    redirect(APP_URL . '/login.php');
}

// 模拟控制器逻辑
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$storeId = $_GET['store_id'] ?? '';

// 计算最近30天的起始日期
$last30DaysStart = date('Y-m-d', strtotime('-30 days'));

$orderProfitModel = new OrderProfit();
$stats = $orderProfitModel->getProfitStats($startDate, $endDate, $storeId);
// 获取最近30天的平台和店铺统计数据
$platformStats = $orderProfitModel->getPlatformStats($last30DaysStart, $endDate);
$storeStats = $orderProfitModel->getStoreStats($last30DaysStart, $endDate);

$storeList = $orderProfitModel->getStoreList();
$title = '利润统计（测试页面）';

// 显示调试信息
echo "<!DOCTYPE html>
<html lang='zh-CN'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>{$title}</title>
    <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'>
</head>
<body>
    <div class='container mt-4'>
        <h1>{$title}</h1>
        
        <div class='alert alert-info'>
            <strong>查询日期范围:</strong> {$last30DaysStart} 到 {$endDate}
        </div>
        
        <div class='row mb-4'>
            <div class='col-md-6'>
                <div class='card'>
                    <div class='card-header bg-primary text-white'>调试信息</div>
                    <div class='card-body'>
                        <p><strong>platformStats:</strong></p>
                        <ul>
                            <li>类型: " . gettype($platformStats) . "</li>
                            <li>是否为空: " . (empty($platformStats) ? '是' : '否') . "</li>
                            <li>元素数量: " . count($platformStats) . "</li>
                        </ul>
                        
                        <p><strong>storeStats:</strong></p>
                        <ul>
                            <li>类型: " . gettype($storeStats) . "</li>
                            <li>是否为空: " . (empty($storeStats) ? '是' : '否') . "</li>
                            <li>元素数量: " . count($storeStats) . "</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class='col-md-6'>
                <div class='card'>
                    <div class='card-header bg-success text-white'>变量导出</div>
                    <div class='card-body'>
                        <pre style='max-height: 300px; overflow: auto;'>";
print_r($platformStats);
echo "                        </pre>
                    </div>
                </div>
            </div>
        </div>";

// 渲染平台统计表格
echo "<div class='card mt-4'>
    <div class='card-header'>
        <h5 class='mb-0'>最近30天按平台统计</h5>
    </div>
    <div class='card-body'>
        <div class='table-responsive'>
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th>平台名称</th>
                        <th>订单数量</th>
                        <th>总利润</th>
                        <th>平均利润率</th>
                    </tr>
                </thead>
                <tbody>";
if (!empty($platformStats)) {
    foreach ($platformStats as $platform) {
        echo "<tr>
            <td>" . htmlspecialchars($platform['platform_name']) . "</td>
            <td>" . number_format($platform['order_count']) . "</td>
            <td><strong>¥" . number_format($platform['total_profit'], 2) . "</strong></td>
            <td><strong class='{$platform['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger'}'>
                " . number_format($platform['avg_profit_rate'], 2) . "%
            </strong></td>
        </tr>";
    }
} else {
    echo "<tr>
        <td colspan='4' class='text-center'>暂无数据</td>
    </tr>";
}
echo "                </tbody>
            </table>
        </div>
    </div>
</div>";

// 渲染店铺统计表格
echo "<div class='card mt-4'>
    <div class='card-header'>
        <h5 class='mb-0'>最近30天按店铺统计</h5>
    </div>
    <div class='card-body'>
        <div class='table-responsive'>
            <table class='table table-hover'>
                <thead>
                    <tr>
                        <th>平台名称</th>
                        <th>店铺名称</th>
                        <th>订单数量</th>
                        <th>总利润</th>
                        <th>平均利润率</th>
                    </tr>
                </thead>
                <tbody>";
if (!empty($storeStats)) {
    foreach ($storeStats as $store) {
        echo "<tr>
            <td>" . htmlspecialchars($store['platform_name']) . "</td>
            <td>" . htmlspecialchars($store['store_name']) . "</td>
            <td>" . number_format($store['order_count']) . "</td>
            <td><strong>¥" . number_format($store['total_profit'], 2) . "</strong></td>
            <td><strong class='{$store['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger'}'>
                " . number_format($store['avg_profit_rate'], 2) . "%
            </strong></td>
        </tr>";
    }
} else {
    echo "<tr>
        <td colspan='5' class='text-center'>暂无数据</td>
    </tr>";
}
echo "                </tbody>
            </table>
        </div>
    </div>
</div>";

echo "    </div>
</body>
</html>";
?>
