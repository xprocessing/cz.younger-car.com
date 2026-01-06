<?php
// 检查管理员角色和权限

// 加载配置文件
require_once __DIR__ . '/config/config.php';
// 加载Database类
require_once __DIR__ . '/includes/database.php';

// 实例化数据库连接
$db = Database::getInstance();
$pdo = $db->getConnection();

echo "检查管理员角色和权限...\n\n";

// 1. 查看roles表结构
echo "1. roles表结构：\n";
$stmt = $pdo->prepare("DESCRIBE roles");
$stmt->execute();
$roles_structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($roles_structure as $column) {
    echo "   - {$column['Field']} ({$column['Type']})\n";
}

// 2. 查看所有角色
echo "\n2. 所有角色：\n";
$stmt = $pdo->prepare("SELECT * FROM roles");
$stmt->execute();
$roles = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($roles as $role) {
    echo "   - ID: {$role['id']}, 名称: {$role['name']}, 描述: {$role['description']}\n";
}

// 3. 查看role_permissions表结构
echo "\n3. role_permissions表结构：\n";
$stmt = $pdo->prepare("DESCRIBE role_permissions");
$stmt->execute();
$rp_structure = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rp_structure as $column) {
    echo "   - {$column['Field']} ({$column['Type']})\n";
}

// 4. 查看管理员角色的权限
echo "\n4. 管理员角色的权限：\n";
$stmt = $pdo->prepare(
    "SELECT r.name as role_name, p.name as permission_name, p.slug as permission_slug 
     FROM role_permissions rp 
     JOIN roles r ON rp.role_id = r.id 
     JOIN permissions p ON rp.permission_id = p.id 
     WHERE r.name = '管理员'"
);
$stmt->execute();
$admin_permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($admin_permissions)) {
    echo "   - 管理员角色没有分配任何权限！\n";
} else {
    echo "   已分配的权限数量：" . count($admin_permissions) . "\n";
    foreach ($admin_permissions as $perm) {
        echo "   - {$perm['permission_name']} ({$perm['permission_slug']})\n";
    }
}

echo "\n5. 总权限数量：\n";
$stmt = $pdo->prepare("SELECT COUNT(*) as total FROM permissions");
$stmt->execute();
$total_perms = $stmt->fetch(PDO::FETCH_ASSOC);
echo "   - 所有权限数量：{$total_perms['total']}\n";

// 6. 找出管理员缺少的权限
echo "\n6. 管理员角色缺少的权限：\n";
$stmt = $pdo->prepare(
    "SELECT p.id, p.name, p.slug 
     FROM permissions p 
     WHERE p.id NOT IN ( 
         SELECT rp.permission_id 
         FROM role_permissions rp 
         JOIN roles r ON rp.role_id = r.id 
         WHERE r.name = '管理员' 
     )"
);
$stmt->execute();
$missing_perms = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($missing_perms)) {
    echo "   - 管理员角色已拥有所有权限！\n";
} else {
    echo "   缺少的权限数量：" . count($missing_perms) . "\n";
    foreach ($missing_perms as $perm) {
        echo "   - {$perm['name']} ({$perm['slug']})\n";
    }
}

echo "\n检查完成！";
?>