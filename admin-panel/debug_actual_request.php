<?php
// 模拟实际请求的调试脚本
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/InventoryDetailsFbaController.php';

// 设置$_GET变量
$_GET['page'] = 1;
$_GET['search'] = '';
$_GET['name'] = '';
$_GET['sku'] = '';

// 模拟会话和登录状态
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['username'] = 'test_user';
$_SESSION['role_id'] = 1;

// 执行控制器的index方法
ob_start();
try {
    $controller = new InventoryDetailsFbaController();
    $controller->index();
    $output = ob_get_clean();
    
    // 分析输出结果
    echo "=== 实际请求模拟结果 ===\n";
    echo "输出长度: " . strlen($output) . " 字符\n";
    
    // 检查是否包含调试信息
    if (strpos($output, '调试信息') !== false) {
        echo "✅ 页面包含调试信息\n";
        
        // 提取调试信息部分
        preg_match('/<div style="background: #ffffcc;.*?<\/div>/s', $output, $matches);
        if (!empty($matches)) {
            echo "调试信息内容:\n";
            // 移除HTML标签
            $debugContent = strip_tags($matches[0]);
            echo $debugContent . "\n";
        }
    } else {
        echo "❌ 页面不包含调试信息\n";
    }
    
    // 检查是否包含表格行
    $tableRows = substr_count($output, '<tr>');
    echo "表格行数: " . $tableRows . "\n";
    
    // 检查是否包含"暂无数据"
    if (strpos($output, '暂无数据') !== false) {
        echo "⚠️  页面显示'暂无数据'\n";
    }
    
    echo "\n=== 模拟完成 ===\n";
    
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ 执行过程中出现异常: " . $e->getMessage() . "\n";
    echo "异常堆栈: " . $e->getTraceAsString() . "\n";
}
?>