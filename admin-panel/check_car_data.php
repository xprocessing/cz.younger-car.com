<?php
// 定义APP_ROOT常量
define('APP_ROOT', __DIR__);
require_once 'includes/database.php';

$db = Database::getInstance();
$stmt = $db->query('SELECT COUNT(*) as count FROM car_data');
$result = $stmt->fetch();
echo 'car_data表中的数据总数：' . $result['count'] . '\n';

// 尝试获取一些示例数据
$stmt = $db->query('SELECT * FROM car_data LIMIT 10');
$rows = $stmt->fetchAll();
echo '\n前10条数据示例：\n';
foreach ($rows as $row) {
    echo 'ID: ' . $row['id'] . ', 品牌: ' . $row['make'] . ' (' . $row['make_cn'] . '), 车型: ' . $row['model'] . '\n';
}
