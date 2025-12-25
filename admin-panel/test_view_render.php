<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/OrderProfit.php';
require_once __DIR__ . '/helpers/functions.php';

session_start();

// 检查登录状态
if (!isLoggedIn()) {
    echo "<h1>未登录，已重定向到登录页</h1>";
    echo "<p>如果看到这个页面，说明 isLoggedIn() 函数工作正常</p>";
    exit;
}

// 模拟控制器逻辑
$startDate = $_GET['start_date'] ?? date('Y-m-01');
$endDate = $_GET['end_date'] ?? date('Y-m-d');
$storeId = $_GET['store_id'] ?? '';

// 计算最近30天的起始日期
$last30DaysStart = date('Y-m-d', strtotime('-30 days'));

echo "<h1>检查步骤</h1>";

$orderProfitModel = new OrderProfit();

echo "<h2>步骤1: 检查登录状态</h2>";
echo "<p>✓ 已登录</p>";

echo "<h2>步骤2: 调用模型方法</h2>";
$stats = $orderProfitModel->getProfitStats($startDate, $endDate, $storeId);
echo "<p>✓ getProfitStats() 调用成功</p>";

$platformStats = $orderProfitModel->getPlatformStats($last30DaysStart, $endDate);
echo "<p>✓ getPlatformStats() 调用成功，返回 " . count($platformStats) . " 条记录</p>";

$storeStats = $orderProfitModel->getStoreStats($last30DaysStart, $endDate);
echo "<p>✓ getStoreStats() 调用成功，返回 " . count($storeStats) . " 条记录</p>";

$storeList = $orderProfitModel->getStoreList();
echo "<p>✓ getStoreList() 调用成功</p>";

echo "<h2>步骤3: 检查变量作用域</h2>";
echo "<p>platformStats 变量存在: " . (isset($platformStats) ? '是' : '否') . "</p>";
echo "<p>storeStats 变量存在: " . (isset($storeStats) ? '是' : '否') . "</p>";

echo "<h2>步骤4: 测试视图渲染</h2>";

// 模拟视图渲染（不包含 header 和 footer）
echo "<div class='card mt-4'>";
echo "<div class='card-header'>最近30天按平台统计（测试）</div>";
echo "<div class='card-body'>";
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
echo "</div></div>";

echo "<h2>步骤5: 检查原始视图文件</h2>";
$viewFile = VIEWS_DIR . '/order_profit/stats.php';
echo "<p>视图文件路径: " . $viewFile . "</p>";
echo "<p>视图文件存在: " . (file_exists($viewFile) ? '是' : '否') . "</p>";

if (file_exists($viewFile)) {
    echo "<p>视图文件可读: " . (is_readable($viewFile) ? '是' : '否') . "</p>";
}
?>
