<?php
// 检查inventory_details_fba相关权限是否存在

// 加载配置文件
require_once 'config/config.php';
// 加载Database类
require_once 'models/Database.php';

// 实例化数据库连接
$db = Database::getInstance();
$pdo = $db->getConnection();

echo "检查inventory_details_fba相关权限...\n\n";

// 查询权限表中是否存在inventory_details_fba相关权限
$stmt = $pdo->prepare("SELECT * FROM permissions WHERE resource = 'inventory_details_fba'");
$stmt->execute();
$permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($permissions)) {
    echo "❌ 未找到inventory_details_fba相关权限！\n";
    echo "需要插入以下权限：\n";
    echo "1. inventory_details_fba.view (查看FBA库存明细)\n";
    echo "2. inventory_details_fba.delete (删除FBA库存明细)\n";
} else {
    echo "✅ 找到以下inventory_details_fba相关权限：\n";
    foreach ($permissions as $permission) {
        echo "- {$permission['name']} ({$permission['slug']})：{$permission['description']}\n";
    }
}

echo "\n检查完成！";
?>