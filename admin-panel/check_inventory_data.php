<?php
// 检查inventory_details表的当前数据状态
require_once 'config/config.php';

try {
    // Create a new PDO instance
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS
    );
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $conn = $pdo;

echo "=== 检查inventory_details表数据状态 ===\n";

// 1. 检查表是否存在
tableCheck: $sql = "SHOW TABLES LIKE 'inventory_details'";
$stmt = $conn->prepare($sql);
$stmt->execute();
$tableExists = $stmt->fetchColumn() !== false;

echo "表inventory_details存在: " . ($tableExists ? "是" : "否") . "\n";

if (!$tableExists) {
    echo "表不存在，无法继续检查\n";
    exit;
}

// 2. 检查行数
$sql = "SELECT COUNT(*) FROM inventory_details";
$stmt = $conn->prepare($sql);
$stmt->execute();
$rowCount = $stmt->fetchColumn();

echo "表中总行数: $rowCount\n";

// 3. 检查product_onway字段的情况
$sql = "SELECT SUM(product_onway) AS total_onway, COUNT(*) AS non_zero_count 
        FROM inventory_details 
        WHERE product_onway > 0";
$stmt = $conn->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo "product_onway字段总和: " . $result['total_onway'] . "\n";
echo "product_onway>0的记录数: " . $result['non_zero_count'] . "\n";

// 4. 查看前5条记录的详情
if ($rowCount > 0) {
    echo "\n=== 前5条记录详情 ===\n";
    $sql = "SELECT id, sku, product_onway, product_valid_num, quantity_receive 
            FROM inventory_details 
            LIMIT 5";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    foreach ($rows as $row) {
        echo "ID: {$row['id']}, SKU: {$row['sku']}, 调拨在途: {$row['product_onway']}, 可用量: {$row['product_valid_num']}, 待到货: {$row['quantity_receive']}\n";
    }
}

// 5. 检查order_profit表的情况
echo "\n=== 检查order_profit表相关情况 ===\n";
$sql = "SELECT COUNT(*) FROM order_profit WHERE global_purchase_time >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
$stmt = $conn->prepare($sql);
$stmt->execute();
$orderCount = $stmt->fetchColumn();

echo "近30天order_profit表记录数: $orderCount\n";

// 6. 直接执行getInventoryAlert()方法中的SQL查询
echo "\n=== 直接执行getInventoryAlert()的SQL查询 ===\n";
$thirtyDaysAgo = date('Y-m-d 00:00:00', strtotime('-30 days'));

$sql = "SELECT combined_data.sku,
               SUM(combined_data.product_valid_num) as product_valid_num,
               SUM(combined_data.quantity_receive) as quantity_receive,
               SUM(combined_data.product_onway) as product_onway,
               COALESCE(MAX(op.outbound_30days), 0) as outbound_30days
        FROM (
            SELECT i.sku COLLATE utf8mb4_unicode_ci as sku,
                   i.product_valid_num,
                   i.quantity_receive,
                   i.product_onway
            FROM inventory_details i
            WHERE i.wid != 5693
            UNION ALL
            SELECT op.local_sku COLLATE utf8mb4_unicode_ci as sku,
                   0 as product_valid_num,
                   0 as quantity_receive,
                   0 as product_onway
            FROM order_profit op
            WHERE op.global_purchase_time >= ?
        ) AS combined_data
        LEFT JOIN (
            SELECT local_sku COLLATE utf8mb4_unicode_ci as local_sku,
                   COUNT(*) as outbound_30days
            FROM order_profit
            WHERE global_purchase_time >= ?
            GROUP BY local_sku
        ) op ON combined_data.sku = op.local_sku
        GROUP BY combined_data.sku
        HAVING product_valid_num > 0 OR quantity_receive > 0 OR product_onway > 0 OR outbound_30days > 0
        ORDER BY outbound_30days DESC, combined_data.sku ASC";

$stmt = $pdo->prepare($sql);
$stmt->execute([$thirtyDaysAgo, $thirtyDaysAgo]);
$data = $stmt->fetchAll(PDO::FETCH_ASSOC);

$count = 0;
foreach ($data as $item) {
    if ($count >= 5) break;
    echo "SKU: {$item['sku']}, 可用量: {$item['product_valid_num']}, 待到货: {$item['quantity_receive']}, 调拨在途: {$item['product_onway']}, 30天出库: {$item['outbound_30days']}\n";
    $count++;
}

echo "\n=== 检查完成 ===\n";

} catch (PDOException $e) {
    echo "数据库错误: " . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
}
?>