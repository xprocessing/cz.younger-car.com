<?php
// 临时脚本用于检查数据库结构
require_once 'config/config.php';
require_once 'includes/database.php';

try {
    $db = Database::getInstance();
    $conn = $db->getConnection();
    
    echo "=== Store表结构 ===\n";
    $stmt = $conn->query("DESCRIBE store");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "{$row['Field']}: {$row['Type']} (Null: {$row['Null']}, Key: {$row['Key']})
";
    }
    
    echo "\n=== Store表数据 (前10条) ===\n";
    $stmt = $conn->query("SELECT * FROM store LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo print_r($row, true) . "\n";
    }
    
    echo "\n=== Order Profit表与Store表关联测试 ===\n";
    // 测试平台筛选查询
    $testPlatformStmt = $conn->query("SELECT s.platform_name, COUNT(op.id) as order_count 
                                      FROM order_profit op 
                                      LEFT JOIN store s ON op.store_id = s.store_id 
                                      GROUP BY s.platform_name");
    while ($row = $testPlatformStmt->fetch(PDO::FETCH_ASSOC)) {
        echo "平台: {$row['platform_name']}, 订单数: {$row['order_count']}\n";
    }
    
} catch (PDOException $e) {
    echo "数据库错误: " . $e->getMessage() . "\n";
}

unlink(__FILE__); // 执行完毕后删除脚本
?>