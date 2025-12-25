<?php
require_once 'config/config.php';
require_once 'includes/database.php';

$db = Database::getInstance();

// 检查数据库连接
if (!$db) {
    die("数据库连接失败");
}

// 查询订单利润表的基本信息
try {
    $stmt = $db->query('SELECT COUNT(*) as count, MIN(DATE(global_purchase_time)) as min_date, MAX(DATE(global_purchase_time)) as max_date FROM order_profit');
    $result = $stmt->fetch();
    
    echo "订单利润表统计信息：\n";
    echo "总记录数：" . $result['count'] . "\n";
    echo "最早日期：" . $result['min_date'] . "\n";
    echo "最新日期：" . $result['max_date'] . "\n";
    
    // 查询最近30天的数据
    $last30DaysStart = date('Y-m-d', strtotime('-30 days'));
    $today = date('Y-m-d');
    
    echo "\n最近30天（" . $last30DaysStart . " 至 " . $today . "）的数据：\n";
    $stmt = $db->query('SELECT COUNT(*) as count FROM order_profit WHERE DATE(global_purchase_time) >= ? AND DATE(global_purchase_time) <= ?', [$last30DaysStart, $today]);
    $result = $stmt->fetch();
    echo "记录数：" . $result['count'] . "\n";
    
    // 查询一些样本数据
    $stmt = $db->query('SELECT * FROM order_profit ORDER BY id DESC LIMIT 5');
    $sampleData = $stmt->fetchAll();
    
    echo "\n最新5条记录样本：\n";
    foreach ($sampleData as $row) {
        echo "ID: " . $row['id'] . ", 店铺ID: " . $row['store_id'] . ", 下单时间: " . $row['global_purchase_time'] . ", 利润: " . $row['profit_amount'] . ", 利润率: " . $row['profit_rate'] . "\n";
    }
    
    // 检查店铺表数据
    echo "\n店铺表数据：\n";
    $stmt = $db->query('SELECT COUNT(*) as count FROM store');
    $result = $stmt->fetch();
    echo "店铺总数：" . $result['count'] . "\n";
    
    $stmt = $db->query('SELECT * FROM store ORDER BY platform_name, store_name');
    $stores = $stmt->fetchAll();
    foreach ($stores as $store) {
        echo "店铺ID: " . $store['store_id'] . ", 平台: " . $store['platform_name'] . ", 店铺名称: " . $store['store_name'] . "\n";
    }
    
} catch (Exception $e) {
    echo "查询出错：" . $e->getMessage() . "\n";
}
?>