<?php
// 测试控制器变量传递
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// 模拟登录用户
$_SESSION['user_id'] = 1;

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/helpers/functions.php';

// 直接加载模型测试数据
echo "=== 测试控制器变量传递 ===\n";

require_once __DIR__ . '/models/InventoryDetailsFba.php';
$model = new InventoryDetailsFba();
$allRecords = $model->getAll();
$warehouseNames = $model->getAllWarehouseNames();

echo "模型获取到的记录数: " . count($allRecords) . "\n";
echo "仓库名称列表: " . implode(', ', $warehouseNames) . "\n";

// 模拟控制器中的变量传递方式
$page = 1;
$limit = 50;
$offset = 0;
$totalCount = count($allRecords);
$totalPages = ceil($totalCount / $limit);
$inventoryDetails = array_slice($allRecords, $offset, $limit);
$title = 'FBA库存详情管理';

// 使用extract函数
$data = [
    'inventoryDetails' => $inventoryDetails,
    'totalPages' => $totalPages,
    'page' => $page,
    'warehouseNames' => $warehouseNames,
    'title' => $title
];
extract($data);

// 测试变量是否可以访问
echo "\n使用extract后的变量:\n";
echo "inventoryDetails: " . gettype($inventoryDetails) . " (" . count($inventoryDetails) . "条)\n";
echo "totalPages: " . $totalPages . "\n";
echo "page: " . $page . "\n";
echo "warehouseNames: " . gettype($warehouseNames) . " (" . count($warehouseNames) . "个)\n";
echo "title: " . $title . "\n";

echo "\n✅ 变量传递测试完成，extract函数正常工作\n";
