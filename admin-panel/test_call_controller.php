<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/OrderProfitController.php';

echo "<h1>直接调用控制器 stats() 方法</h1>";
echo "<p>模拟 URL: order_profit.php?action=stats</p>";

// 模拟登录状态
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['full_name'] = '测试用户';
$_SESSION['role'] = 'admin';

// 创建控制器实例
$orderProfitController = new OrderProfitController();

// 使用反射调用 stats 方法
try {
    ob_start();
    $orderProfitController->stats();
    $output = ob_get_clean();
    
    echo "<hr>";
    echo "<h2>控制器输出内容:</h2>";
    echo "<div style='border: 2px solid #ccc; padding: 20px;'>";
    echo $output;
    echo "</div>";
    
    // 检查输出中是否有"暂无数据"
    if (strpos($output, '暂无数据') !== false) {
        echo "<hr>";
        echo "<p style='color: red;'><strong>⚠️ 控制器输出中包含 '暂无数据'！</strong></p>";
        
        // 找出所有包含"暂无数据"的位置
        $pos = 0;
        $count = 0;
        while (($pos = strpos($output, '暂无数据', $pos)) !== false) {
            $count++;
            $context = substr($output, max(0, $pos - 50), 100);
            echo "<p>第 {$count} 处位置 (around line " . substr_count(substr($output, 0, $pos), "\n") . "):</p>";
            echo "<pre style='background: #ffffcc;'>" . htmlspecialchars($context) . "</pre>";
            $pos += strlen('暂无数据');
        }
    } else {
        echo "<hr>";
        echo "<p style='color: green;'><strong>✓ 控制器输出中不包含 '暂无数据'</strong></p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>错误: " . $e->getMessage() . "</p>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}
?>
