<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/models/OrderProfit.php';
require_once __DIR__ . '/helpers/functions.php';

$orderProfitModel = new OrderProfit();

$last30DaysStart = date('Y-m-d', strtotime('-30 days'));
$today = date('Y-m-d');

echo "=== 模拟 OrderProfitController::stats() 方法 ===\n\n";
echo "查询日期范围: {$last30DaysStart} 到 {$today}\n\n";

// 调用 getProfitStats
echo "1. 调用 getProfitStats():\n";
$stats = $orderProfitModel->getProfitStats($last30DaysStart, $today, '');
print_r($stats);

echo "\n2. 调用 getPlatformStats():\n";
$platformStats = $orderProfitModel->getPlatformStats($last30DaysStart, $today);
print_r($platformStats);

echo "\n3. 调用 getStoreStats():\n";
$storeStats = $orderProfitModel->getStoreStats($last30DaysStart, $today);
print_r($storeStats);

echo "\n4. 调用 getStoreList():\n";
$storeList = $orderProfitModel->getStoreList();
print_r($storeList);
