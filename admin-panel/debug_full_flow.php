<?php
// 调试脚本：模拟完整的请求流程
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 先启动会话
session_start();

// 模拟登录用户
$_SESSION['user_id'] = 1;

// 模拟GET请求参数
$_GET['action'] = 'index';

// 调试会话信息
echo "=== 会话信息调试 ===\n";
echo "登录状态: " . (isset($_SESSION['user_id']) ? '已登录' : '未登录') . "\n";

// 加载必要的文件
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/helpers/functions.php';

// 调试权限检查
echo "\n=== 权限检查调试 ===\n";

// 首先尝试直接检查权限
$hasPermission = hasPermission('inventory_details_fba.view');
echo "直接权限检查结果: " . ($hasPermission ? '有权限' : '无权限') . "\n";

// 如果没有权限，我们可以临时绕过权限检查来测试
echo "\n=== 绕过权限检查测试 ===\n";

// 直接调用模型获取数据
require_once __DIR__ . '/models/InventoryDetailsFba.php';
$model = new InventoryDetailsFba();
$inventoryDetails = $model->getAll();

echo "模型查询结果: " . count($inventoryDetails) . " 条记录\n";
if (!empty($inventoryDetails)) {
    echo "第一条记录: " . json_encode($inventoryDetails[0], JSON_UNESCAPED_UNICODE) . "\n";
}

// 调试完整的控制器流程
echo "\n=== 控制器流程调试 ===\n";
require_once __DIR__ . '/controllers/InventoryDetailsFbaController.php';

// 创建控制器实例
$controller = new InventoryDetailsFbaController();

// 临时修改控制器方法，不重定向，只返回数据
$reflection = new ReflectionClass('InventoryDetailsFbaController');
$method = $reflection->getMethod('index');
$method->setAccessible(true);

// 捕获输出
ob_start();
$method->invoke($controller);
$output = ob_get_clean();

// 检查输出是否包含数据
if (strpos($output, '暂无数据') === false) {
    echo "✅ 控制器输出包含数据\n";
    
    // 查找表格行
    $lines = explode('\n', $output);
    $trCount = 0;
    foreach ($lines as $line) {
        if (strpos($line, '<tr>') !== false) {
            $trCount++;
        }
    }
    echo "表格行数: " . $trCount . "\n";
} else {
    echo "❌ 控制器输出显示'暂无数据'\n";
}

// 检查是否有错误信息
if (isset($_SESSION['error'])) {
    echo "❌ 发现错误信息: " . $_SESSION['error'] . "\n";
}

if (isset($_SESSION['success'])) {
    echo "✅ 发现成功信息: " . $_SESSION['success'] . "\n";
}

echo "\n=== 调试完成 ===\n";
