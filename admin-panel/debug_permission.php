<?php
// 调试脚本：详细检查权限问题
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 模拟登录用户
$_SESSION['user_id'] = 1;

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/helpers/functions.php';

echo "=== 权限问题详细调试 ===\n";

echo "\n1. 当前用户ID: " . $_SESSION['user_id'] . "\n";

// 尝试加载Permission模型
try {
    require_once __DIR__ . '/models/Permission.php';
    $permissionModel = new Permission();
    echo "2. Permission模型加载成功\n";
    
    // 检查用户拥有的所有权限
    echo "\n3. 用户拥有的所有权限:\n";
    $userPermissions = $permissionModel->getUserPermissions($_SESSION['user_id']);
    if (!empty($userPermissions)) {
        foreach ($userPermissions as $permission) {
            echo "   - " . $permission['permission_slug'] . " (" . $permission['permission_name'] . ")\n";
        }
    } else {
        echo "   该用户没有任何权限\n";
    }
    
    // 检查inventory_details_fba.view权限是否存在
    echo "\n4. 检查权限'inventory_details_fba.view'是否存在:\n";
    $permission = $permissionModel->getBySlug('inventory_details_fba.view');
    if ($permission) {
        echo "   ✅ 权限存在\n";
        echo "   权限ID: " . $permission['permission_id'] . "\n";
        echo "   权限名称: " . $permission['permission_name'] . "\n";
    } else {
        echo "   ❌ 权限不存在\n";
        echo "   需要创建该权限\n";
    }
    
    // 检查用户是否有该权限
    echo "\n5. 用户是否有'inventory_details_fba.view'权限:\n";
    $hasPermission = $permissionModel->checkUserPermission($_SESSION['user_id'], 'inventory_details_fba.view');
    echo "   " . ($hasPermission ? '✅ 有权限' : '❌ 无权限') . "\n";
    
} catch (Exception $e) {
    echo "❌ 权限检查失败: " . $e->getMessage() . "\n";
}

echo "\n=== 临时解决方案 ===\n";
echo "如果是权限问题，可以通过以下方式临时解决：\n";
echo "1. 在控制器中临时注释权限检查代码\n";
echo "2. 或者添加权限到数据库中\n";

// 显示临时解决方案代码
echo "\n临时修改控制器代码的示例：\n";
echo "// 注释掉权限检查\n";
echo "// if (!hasPermission('inventory_details_fba.view')) {\n";
echo "//     showError('您没有权限查看FBA库存详情');\n";
echo "//     redirect(APP_URL . '/dashboard.php');\n";
echo "// }\n";
echo "\n=== 调试完成 ===\n";
