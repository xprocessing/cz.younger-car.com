<?php
// 定义 APP_ROOT 常量
define('APP_ROOT', __DIR__);

// 包含配置文件
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/includes/database.php';

// 创建数据库连接
$db = Database::getInstance();

// 检查inventory_details表的基本信息
echo "=== Inventory Details Table Overview ===\n";

// 1. 检查表中总记录数
$sql1 = "SELECT COUNT(*) as total_records FROM inventory_details";
$stmt1 = $db->query($sql1);
$totalRecords = $stmt1->fetchColumn();
echo "Total records in inventory_details: " . $totalRecords . "\n";

// 2. 检查包含product_onway字段的记录数（包括0值）
$sql2 = "SELECT COUNT(*) as onway_records FROM inventory_details WHERE product_onway IS NOT NULL";
$stmt2 = $db->query($sql2);
$onwayRecords = $stmt2->fetchColumn();
echo "Records with product_onway field (including 0): " . $onwayRecords . "\n";

// 3. 检查product_onway字段的平均值、最大值、最小值
$sql3 = "SELECT AVG(product_onway) as avg_onway, MAX(product_onway) as max_onway, MIN(product_onway) as min_onway FROM inventory_details";
$stmt3 = $db->query($sql3);
$onwayStats = $stmt3->fetch();
echo "product_onway statistics: " . 
     "avg: " . $onwayStats['avg_onway'] . ", " . 
     "max: " . $onwayStats['max_onway'] . ", " . 
     "min: " . $onwayStats['min_onway'] . "\n";

// 4. 查看前20条记录的完整数据
echo "\n=== First 20 records from inventory_details ===\n";
$sql4 = "SELECT id, wid, sku, product_valid_num, quantity_receive, product_onway, average_age 
         FROM inventory_details 
         ORDER BY id DESC 
         LIMIT 20";
$stmt4 = $db->query($sql4);
$result4 = $stmt4->fetchAll();

foreach ($result4 as $row) {
    echo "ID: " . $row['id'] . ", " .
         "WID: " . $row['wid'] . ", " .
         "SKU: " . $row['sku'] . ", " .
         "Valid Num: " . $row['product_valid_num'] . ", " .
         "Receive: " . $row['quantity_receive'] . ", " .
         "Onway: " . $row['product_onway'] . ", " .
         "Avg Age: " . $row['average_age'] . "\n";
}

// 5. 检查是否有其他表可能包含调拨在途数据
echo "\n=== Check other tables for onway data ===\n";

// 检查数据库中所有表
$sql5 = "SHOW TABLES";
$stmt5 = $db->query($sql5);
$tables = $stmt5->fetchAll(PDO::FETCH_COLUMN);

// 查找可能包含onway或调拨相关数据的表
$onwayTables = [];
foreach ($tables as $table) {
    // 检查表名是否包含onway或调拨相关关键词
    if (strpos(strtolower($table), 'onway') !== false || 
        strpos(strtolower($table), '调拨') !== false ||
        strpos(strtolower($table), 'transfer') !== false) {
        $onwayTables[] = $table;
    }
}

echo "Tables potentially related to onway/transfer: " . implode(', ', $onwayTables) . "\n";

// 6. 检查order_profit表是否有相关字段
echo "\n=== Check order_profit table structure ===\n";
if (in_array('order_profit', $tables)) {
    $sql6 = "DESCRIBE order_profit";
    $stmt6 = $db->query($sql6);
    $orderProfitStructure = $stmt6->fetchAll();
    echo "order_profit table columns:\n";
    foreach ($orderProfitStructure as $column) {
        echo "- " . $column['Field'] . " (" . $column['Type'] . ")\n";
    }
}

// 7. 检查是否有其他可能的数据源
echo "\n=== Check for other potential data sources ===\n";
// 查看是否有API调用或其他数据导入逻辑
$apiFiles = glob(APP_ROOT . '/**/*.php');
$apiCount = 0;
foreach ($apiFiles as $file) {
    $content = file_get_contents($file);
    if (strpos($content, 'api') !== false || strpos($content, 'curl') !== false || strpos($content, 'import') !== false) {
        $apiCount++;
        // 只显示前5个文件
        if ($apiCount <= 5) {
            echo "- " . $file . "\n";
        }
    }
}
if ($apiCount > 5) {
    echo "... and " . ($apiCount - 5) . " more files\n";
}

echo "\n=== Test completed ===\n";
