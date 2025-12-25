<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/helpers/functions.php';

$db = Database::getInstance();
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>表结构检查</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h1>表结构检查</h1>
    
    <?php
    // 1. 检查 order_profit 表结构
    echo "<h3 class='mt-4'>order_profit 表结构</h3>";
    $sql = "DESCRIBE order_profit";
    $stmt = $db->query($sql);
    $columns = $stmt->fetchAll();
    
    echo "<table class='table table-striped table-bordered'>";
    echo "<thead><tr><th>字段名</th><th>类型</th><th>是否为空</th><th>键</th><th>默认值</th></tr></thead>";
    echo "<tbody>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td><strong>{$col['Field']}</strong></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    
    // 2. 检查 store 表结构
    echo "<h3 class='mt-4'>store 表结构</h3>";
    $sql = "DESCRIBE store";
    $stmt = $db->query($sql);
    $columns = $stmt->fetchAll();
    
    echo "<table class='table table-striped table-bordered'>";
    echo "<thead><tr><th>字段名</th><th>类型</th><th>是否为空</th><th>键</th><th>默认值</th></tr></thead>";
    echo "<tbody>";
    foreach ($columns as $col) {
        echo "<tr>";
        echo "<td><strong>{$col['Field']}</strong></td>";
        echo "<td>{$col['Type']}</td>";
        echo "<td>{$col['Null']}</td>";
        echo "<td>{$col['Key']}</td>";
        echo "<td>" . ($col['Default'] ?? 'NULL') . "</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    
    // 3. 查看前5条原始数据
    echo "<h3 class='mt-4'>order_profit 前5条原始数据</h3>";
    $sql = "SELECT * FROM order_profit ORDER BY id DESC LIMIT 5";
    $stmt = $db->query($sql);
    $data = $stmt->fetchAll();
    
    echo "<table class='table table-striped table-bordered table-sm'>";
    echo "<thead><tr><th>id</th><th>store_id</th><th>global_order_no</th><th>global_purchase_time</th><th>profit_amount</th><th>profit_rate</th></tr></thead>";
    echo "<tbody>";
    foreach ($data as $row) {
        echo "<tr>";
        echo "<td>{$row['id']}</td>";
        echo "<td>" . htmlspecialchars($row['store_id'] ?? 'NULL') . "</td>";
        echo "<td>" . htmlspecialchars($row['global_order_no']) . "</td>";
        echo "<td>{$row['global_purchase_time']}</td>";
        echo "<td>{$row['profit_amount']}</td>";
        echo "<td>{$row['profit_rate']}</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    
    // 4. 检查 global_purchase_time 的分布
    echo "<h3 class='mt-4'>global_purchase_time 日期分布</h3>";
    $sql = "SELECT 
                DATE(global_purchase_time) as date, 
                COUNT(*) as count
            FROM order_profit
            GROUP BY DATE(global_purchase_time)
            ORDER BY date DESC
            LIMIT 10";
    $stmt = $db->query($sql);
    $dateData = $stmt->fetchAll();
    
    echo "<table class='table table-striped table-bordered'>";
    echo "<thead><tr><th>日期</th><th>数量</th></tr></thead>";
    echo "<tbody>";
    foreach ($dateData as $row) {
        echo "<tr>";
        echo "<td>{$row['date']}</td>";
        echo "<td>{$row['count']}</td>";
        echo "</tr>";
    }
    echo "</tbody></table>";
    
    // 5. 检查 store_id 是否匹配
    echo "<h3 class='mt-4'>order_profit 和 store 关联情况</h3>";
    $sql = "SELECT 
                COUNT(CASE WHEN s.store_id IS NULL THEN 1 END) as unmatched,
                COUNT(CASE WHEN s.store_id IS NOT NULL THEN 1 END) as matched
            FROM order_profit op
            LEFT JOIN store s ON op.store_id = s.store_id";
    $stmt = $db->query($sql);
    $result = $stmt->fetch();
    
    echo "<div class='alert alert-info'>";
    echo "匹配成功: <strong>{$result['matched']}</strong> 条<br>";
    echo "匹配失败 (store_id 在 store 表中不存在): <strong>{$result['unmatched']}</strong> 条<br>";
    echo "</div>";
    
    // 6. 查看未匹配的记录
    if ($result['unmatched'] > 0) {
        echo "<h3 class='mt-4'>未匹配的 store_id (最多10条)</h3>";
        $sql = "SELECT op.store_id, COUNT(*) as count
                FROM order_profit op
                LEFT JOIN store s ON op.store_id = s.store_id
                WHERE s.store_id IS NULL
                GROUP BY op.store_id
                LIMIT 10";
        $stmt = $db->query($sql);
        $unmatched = $stmt->fetchAll();
        
        echo "<table class='table table-striped table-bordered'>";
        echo "<thead><tr><th>store_id</th><th>数量</th></tr></thead>";
        echo "<tbody>";
        foreach ($unmatched as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['store_id']) . "</td>";
            echo "<td>{$row['count']}</td>";
            echo "</tr>";
        }
        echo "</tbody></table>";
    }
    ?>
</div>
</body>
</html>
