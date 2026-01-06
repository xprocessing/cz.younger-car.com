<?php
// 定义 APP_ROOT 常量
define('APP_ROOT', __DIR__);

// 包含配置文件
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/includes/database.php';

// 创建数据库连接
$db = Database::getInstance();

// 测试1：直接从 inventory_details 表查询 product_onway 数据
echo "=== Test 1: Direct query from inventory_details ===\n";
$sql1 = "SELECT sku, product_onway, product_valid_num, quantity_receive 
         FROM inventory_details 
         WHERE product_onway > 0 
         ORDER BY product_onway DESC 
         LIMIT 10";
$stmt1 = $db->query($sql1);
$result1 = $stmt1->fetchAll();
echo "Found " . count($result1) . " records with product_onway > 0\n";
foreach ($result1 as $row) {
    echo "SKU: " . $row['sku'] . " - product_onway: " . $row['product_onway'] . 
         ", product_valid_num: " . $row['product_valid_num'] . ", quantity_receive: " . $row['quantity_receive'] . "\n";
}

// 测试2：执行与 getInventoryAlert() 相同的查询
echo "\n=== Test 2: Execute getInventoryAlert() query ===\n";
$thirtyDaysAgo = date('Y-m-d 00:00:00', strtotime('-30 days'));

$sql2 = "SELECT combined_data.sku,
               SUM(combined_data.product_valid_num) as product_valid_num,
               SUM(combined_data.quantity_receive) as quantity_receive,
               SUM(combined_data.product_onway) as product_onway,
               COALESCE(MAX(op.outbound_30days), 0) as outbound_30days
        FROM (
            -- 从inventory_details获取基础数据
            SELECT i.sku COLLATE utf8mb4_unicode_ci as sku,
                   i.product_valid_num,
                   i.quantity_receive,
                   i.product_onway
            FROM inventory_details i
            WHERE i.wid != 5693
            UNION ALL
            -- 从order_profit获取最近30天有出库记录的SKU
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
        HAVING product_onway > 0
        ORDER BY product_onway DESC
        LIMIT 10";

$stmt2 = $db->query($sql2, [$thirtyDaysAgo, $thirtyDaysAgo]);
$result2 = $stmt2->fetchAll();
echo "Found " . count($result2) . " records with aggregated product_onway > 0\n";
foreach ($result2 as $row) {
    echo "SKU: " . $row['sku'] . " - aggregated product_onway: " . $row['product_onway'] . 
         ", product_valid_num: " . $row['product_valid_num'] . ", quantity_receive: " . $row['quantity_receive'] . ", outbound_30days: " . $row['outbound_30days'] . "\n";
}

// 测试3：检查是否有重复的SKU记录
echo "\n=== Test 3: Check for duplicate SKU records ===\n";
$sql3 = "SELECT sku, COUNT(*) as count 
         FROM inventory_details 
         WHERE wid != 5693 
         GROUP BY sku 
         HAVING COUNT(*) > 1 
         ORDER BY COUNT(*) DESC 
         LIMIT 10";
$stmt3 = $db->query($sql3);
$result3 = $stmt3->fetchAll();
echo "Found " . count($result3) . " SKUs with multiple records\n";
foreach ($result3 as $row) {
    echo "SKU: " . $row['sku'] . " - count: " . $row['count'] . "\n";
    
    // 显示该SKU的详细记录
    $sql4 = "SELECT id, wid, product_onway, product_valid_num, quantity_receive 
             FROM inventory_details 
             WHERE sku = ? AND wid != 5693";
    $stmt4 = $db->query($sql4, [$row['sku']]);
    $subResult = $stmt4->fetchAll();
    foreach ($subResult as $subRow) {
        echo "  - ID: " . $subRow['id'] . ", WID: " . $subRow['wid'] . ", product_onway: " . $subRow['product_onway'] . 
             ", product_valid_num: " . $subRow['product_valid_num'] . ", quantity_receive: " . $subRow['quantity_receive'] . "\n";
    }
}

// 测试4：检查 quantity_receive 的数据类型问题
echo "\n=== Test 4: Check quantity_receive data type issue ===\n";
$sql5 = "SELECT sku, quantity_receive 
         FROM inventory_details 
         WHERE quantity_receive NOT REGEXP '^[0-9]+$' 
         LIMIT 10";
$stmt5 = $db->query($sql5);
$result5 = $stmt5->fetchAll();
echo "Found " . count($result5) . " records with non-numeric quantity_receive\n";
foreach ($result5 as $row) {
    echo "SKU: " . $row['sku'] . " - quantity_receive: '" . $row['quantity_receive'] . "' (type: " . gettype($row['quantity_receive']) . ")\n";
}

// 测试5：检查具体SKU的聚合计算
echo "\n=== Test 5: Check aggregation for specific SKU ===\n";
if (!empty($result3)) {
    $testSku = $result3[0]['sku'];
    echo "Testing SKU: " . $testSku . "\n";
    
    // 分别查询原始数据和聚合结果
    $sql6 = "SELECT sku, product_onway, product_valid_num, quantity_receive 
             FROM inventory_details 
             WHERE sku = ? AND wid != 5693";
    $stmt6 = $db->query($sql6, [$testSku]);
    $rawData = $stmt6->fetchAll();
    
    echo "Raw data:\n";
    $totalOnway = 0;
    $totalValid = 0;
    foreach ($rawData as $row) {
        echo "  - product_onway: " . $row['product_onway'] . ", product_valid_num: " . $row['product_valid_num'] . ", quantity_receive: " . $row['quantity_receive'] . "\n";
        $totalOnway += (int)$row['product_onway'];
        $totalValid += (int)$row['product_valid_num'];
    }
    
    // 手动计算聚合结果
    echo "Manual aggregation:\n";
    echo "  - total product_onway: " . $totalOnway . "\n";
    echo "  - total product_valid_num: " . $totalValid . "\n";
    
    // 使用SQL聚合
    $sql7 = "SELECT SUM(product_onway) as total_onway, SUM(product_valid_num) as total_valid 
             FROM inventory_details 
             WHERE sku = ? AND wid != 5693";
    $stmt7 = $db->query($sql7, [$testSku]);
    $aggResult = $stmt7->fetch();
    echo "SQL aggregation:\n";
    echo "  - total product_onway: " . $aggResult['total_onway'] . "\n";
    echo "  - total product_valid_num: " . $aggResult['total_valid'] . "\n";
}

echo "\n=== Test completed ===\n";
