<?php
// 定义 APP_ROOT 常量
define('APP_ROOT', __DIR__);

// 包含配置文件
require_once APP_ROOT . '/config/config.php';
require_once APP_ROOT . '/includes/database.php';

// 创建数据库连接
$db = Database::getInstance();

// 获取数据库中所有表
echo "=== All Tables in Database ===\n";
$sql = "SHOW TABLES";
$stmt = $db->query($sql);
$tables = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($tables as $table) {
    echo "\n--- Table: $table ---\n";
    
    // 检查表结构
    $descSql = "DESCRIBE $table";
    $descStmt = $db->query($descSql);
    $columns = $descStmt->fetchAll();
    
    // 查找可能包含onway或调拨相关字段
    $onwayColumns = [];
    foreach ($columns as $column) {
        $field = strtolower($column['Field']);
        if (strpos($field, 'onway') !== false || 
            strpos($field, '调拨') !== false ||
            strpos($field, 'transfer') !== false ||
            strpos($field, '途') !== false ||
            strpos($field, 'transit') !== false) {
            $onwayColumns[] = $column['Field'] . " (" . $column['Type'] . ")";
        }
    }
    
    if (!empty($onwayColumns)) {
        echo "Found potential onway/transfer columns: " . implode(', ', $onwayColumns) . "\n";
        
        // 查看该表的前5条记录，显示相关字段
        $selectSql = "SELECT * FROM $table LIMIT 5";
        $selectStmt = $db->query($selectSql);
        $rows = $selectStmt->fetchAll();
        
        if (!empty($rows)) {
            echo "Sample data:\n";
            foreach ($rows as $row) {
                $rowData = [];
                foreach ($columns as $column) {
                    $field = $column['Field'];
                    $value = $row[$field];
                    if (strpos(strtolower($field), 'onway') !== false || 
                        strpos(strtolower($field), '调拨') !== false ||
                        strpos(strtolower($field), 'transfer') !== false ||
                        strpos(strtolower($field), '途') !== false ||
                        strpos(strtolower($field), 'transit') !== false) {
                        $rowData[] = $field . ": '" . $value . "'";
                    }
                }
                echo "  - " . implode(', ', $rowData) . "\n";
            }
        }
    } else {
        echo "No onway/transfer related columns found\n";
    }
}

echo "\n=== Test completed ===\n";
