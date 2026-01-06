<?php
// 设置错误报告
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 定义应用根目录
define('APP_ROOT', __DIR__);

try {
    // 加载数据库类
    require_once APP_ROOT . '/includes/database.php';
    
    // 检查数据库连接
    echo "<h2>数据库连接测试</h2>";
    
    global $db;
    if (isset($db) && $db instanceof Database) {
        echo "<p style='color: green;'>✅ 数据库连接成功</p>";
        
        // 检查表是否存在
        $checkTableSql = "SHOW TABLES LIKE 'inventory_details_fba'";
        $stmt = $db->query($checkTableSql);
        
        if ($stmt->rowCount() > 0) {
            echo "<p style='color: green;'>✅ 表 'inventory_details_fba' 存在</p>";
            
            // 检查表中是否有数据
            $countSql = "SELECT COUNT(*) AS total FROM inventory_details_fba";
            $stmt = $db->query($countSql);
            $result = $stmt->fetch();
            
            echo "<p><strong>表中数据行数:</strong> {$result['total']}</p>";
            
            if ($result['total'] > 0) {
                // 获取前5条数据查看
                $dataSql = "SELECT * FROM inventory_details_fba LIMIT 5";
                $stmt = $db->query($dataSql);
                $data = $stmt->fetchAll();
                
                echo "<h3>前5条数据:</h3>";
                echo "<table border='1' cellpadding='5' cellspacing='0'>";
                echo "<tr>";
                // 输出表头
                foreach ($data[0] as $key => $value) {
                    echo "<th>{$key}</th>";
                }
                echo "</tr>";
                
                // 输出数据
                foreach ($data as $row) {
                    echo "<tr>";
                    foreach ($row as $value) {
                        echo "<td>{$value}</td>";
                    }
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "<p style='color: red;'>❌ 表中没有数据</p>";
            }
        } else {
            echo "<p style='color: red;'>❌ 表 'inventory_details_fba' 不存在</p>";
        }
    } else {
        echo "<p style='color: red;'>❌ 数据库连接失败</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ 错误: " . $e->getMessage() . "</p>";
    echo "<p><strong>错误位置:</strong> " . $e->getFile() . " 第 " . $e->getLine() . " 行</p>";
}
?>