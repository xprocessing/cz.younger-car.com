<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';

$last30DaysStart = date('Y-m-d', strtotime('-30 days'));
$today = date('Y-m-d');

echo "=== 调试数据库查询 ===\n\n";
echo "查询日期范围: {$last30DaysStart} 到 {$today}\n\n";

try {
    $db = Database::getInstance();
    
    // 测试1: 检查 order_profit 表是否有数据
    echo "1. 检查 order_profit 表总记录数:\n";
    $sql = "SELECT COUNT(*) as count FROM order_profit";
    $stmt = $db->query($sql);
    $result = $stmt->fetch();
    echo "   总记录数: " . $result['count'] . "\n\n";
    
    // 测试2: 检查最近30天的数据
    echo "2. 检查最近30天的数据:\n";
    $sql = "SELECT COUNT(*) as count FROM order_profit 
            WHERE DATE(global_purchase_time) >= ? AND DATE(global_purchase_time) <= ?";
    $stmt = $db->query($sql, [$last30DaysStart, $today]);
    $result = $stmt->fetch();
    echo "   最近30天记录数: " . $result['count'] . "\n\n";
    
    // 测试3: 检查 global_purchase_time 字段的值
    echo "3. 查看 global_purchase_time 字段的值:\n";
    $sql = "SELECT id, global_order_no, global_purchase_time, store_id 
            FROM order_profit 
            ORDER BY global_purchase_time DESC 
            LIMIT 5";
    $stmt = $db->query($sql);
    $data = $stmt->fetchAll();
    foreach ($data as $row) {
        echo "   ID: {$row['id']}, 订单号: {$row['global_order_no']}, 时间: {$row['global_purchase_time']}, 店铺ID: {$row['store_id']}\n";
    }
    echo "\n";
    
    // 测试4: 检查 store 表是否有数据
    echo "4. 检查 store 表:\n";
    $sql = "SELECT COUNT(*) as count FROM store";
    $stmt = $db->query($sql);
    $result = $stmt->fetch();
    echo "   store 表总记录数: " . $result['count'] . "\n\n";
    
    // 测试5: 检查 order_profit 和 store 表的关联
    echo "5. 检查 order_profit 和 store 表的关联:\n";
    $sql = "SELECT op.*, s.platform_name, s.store_name 
            FROM order_profit op
            LEFT JOIN store s ON op.store_id = s.store_id
            WHERE DATE(op.global_purchase_time) >= ? AND DATE(op.global_purchase_time) <= ?
            LIMIT 3";
    $stmt = $db->query($sql, [$last30DaysStart, $today]);
    $data = $stmt->fetchAll();
    if (empty($data)) {
        echo "   最近30天没有关联数据\n";
    } else {
        foreach ($data as $row) {
            echo "   订单号: {$row['global_order_no']}, 平台: " . ($row['platform_name'] ?? 'NULL') . ", 店铺: " . ($row['store_name'] ?? 'NULL') . "\n";
        }
    }
    echo "\n";
    
    // 测试6: 检查所有数据(不限制日期)的关联
    echo "6. 检查所有数据的关联:\n";
    $sql = "SELECT COUNT(*) as count 
            FROM order_profit op
            LEFT JOIN store s ON op.store_id = s.store_id
            WHERE s.store_id IS NOT NULL";
    $stmt = $db->query($sql);
    $result = $stmt->fetch();
    echo "   有店铺关联的记录数: " . $result['count'] . "\n\n";
    
    // 测试7: 检查没有店铺关联的记录
    echo "7. 检查没有店铺关联的记录:\n";
    $sql = "SELECT COUNT(*) as count 
            FROM order_profit op
            LEFT JOIN store s ON op.store_id = s.store_id
            WHERE s.store_id IS NULL";
    $stmt = $db->query($sql);
    $result = $stmt->fetch();
    echo "   没有店铺关联的记录数: " . $result['count'] . "\n\n";
    
    // 测试8: 查看 store 表的数据
    echo "8. 查看 store 表的数据:\n";
    $sql = "SELECT store_id, platform_name, store_name FROM store LIMIT 5";
    $stmt = $db->query($sql);
    $data = $stmt->fetchAll();
    foreach ($data as $row) {
        echo "   店铺ID: {$row['store_id']}, 平台: {$row['platform_name']}, 店铺名: {$row['store_name']}\n";
    }
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
?>
