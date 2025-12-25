<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/OrderProfitController.php';

echo "<h1>完全模拟控制器流程</h1>";

// 模拟登录状态
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['full_name'] = '测试用户';
$_SESSION['role'] = 'admin';

// 创建控制器实例
$orderProfitController = new OrderProfitController();

// 获取反射类以访问私有属性
$reflection = new ReflectionClass($orderProfitController);
$orderProfitModelProperty = $reflection->getProperty('orderProfitModel');
$orderProfitModelProperty->setAccessible(true);
$orderProfitModel = $orderProfitModelProperty->getValue($orderProfitController);

// 模拟控制器的stats方法逻辑
$startDate = date('Y-m-01');
$endDate = date('Y-m-d');
$storeId = '';

$last30DaysStart = date('Y-m-d', strtotime('-30 days'));

echo "<h3>参数信息:</h3>";
echo "<ul>";
echo "<li>startDate: {$startDate}</li>";
echo "<li>endDate: {$endDate}</li>";
echo "<li>last30DaysStart: {$last30DaysStart}</li>";
echo "<li>storeId: {$storeId}</li>";
echo "</ul>";

$stats = $orderProfitModel->getProfitStats($startDate, $endDate, $storeId);
$platformStats = $orderProfitModel->getPlatformStats($last30DaysStart, $endDate);
$storeStats = $orderProfitModel->getStoreStats($last30DaysStart, $endDate);

$storeList = $orderProfitModel->getStoreList();

echo "<h3>变量检查:</h3>";
echo "<ul>";
echo "<li>\$stats 类型: " . gettype($stats) . "</li>";
echo "<li>\$platformStats 类型: " . gettype($platformStats) . "</li>";
echo "<li>\$platformStats 数量: " . count($platformStats) . "</li>";
echo "<li>\$storeStats 类型: " . gettype($storeStats) . "</li>";
echo "<li>\$storeStats 数量: " . count($storeStats) . "</li>";
echo "<li>\$storeList 数量: " . count($storeList) . "</li>";
echo "</ul>";

echo "<h3>platformStats 内容:</h3>";
if (!empty($platformStats)) {
    echo "<pre>" . print_r($platformStats, true) . "</pre>";
} else {
    echo "<p style='color: red;'>platformStats 为空!</p>";
}

echo "<h3>storeStats 内容:</h3>";
if (!empty($storeStats)) {
    echo "<pre>" . print_r($storeStats, true) . "</pre>";
} else {
    echo "<p style='color: red;'>storeStats 为空!</p>";
}

// 检查是否是真正的数组
echo "<h3>详细检查:</h3>";
echo "<p>is_array(\$platformStats): " . (is_array($platformStats) ? 'true' : 'false') . "</p>";
echo "<p>!empty(\$platformStats): " . (!empty($platformStats) ? 'true' : 'false') . "</p>";
echo "<p>count(\$platformStats): " . count($platformStats) . "</p>";

if (is_array($platformStats)) {
    echo "<p>platformStats keys: " . implode(', ', array_keys($platformStats)) . "</p>";
}

// 现在测试视图渲染
echo "<hr>";
echo "<h2>测试视图渲染</h2>";

// 设置视图需要的变量
$title = '利润统计';

// 手动包含视图文件（但不包括header和footer）
echo "<div style='background: #f0f0f0; padding: 20px;'>";
echo "<h3>视图内容（不含header/footer）:</h3>";
echo "</div>";

// 直接输出stats.php的内容
ob_start();
include VIEWS_DIR . '/order_profit/stats.php';
$renderedContent = ob_get_clean();

echo "<div style='border: 2px solid #ccc; padding: 20px;'>";
echo $renderedContent;
echo "</div>";

// 检查视图中是否有"暂无数据"
if (strpos($renderedContent, '暂无数据') !== false) {
    echo "<p style='color: red;'><strong>视图中包含 '暂无数据'</strong></p>";
} else {
    echo "<p style='color: green;'><strong>视图中不包含 '暂无数据'</strong></p>";
}
?>
