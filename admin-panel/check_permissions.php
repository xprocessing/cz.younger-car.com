<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/Permission.php';

// 初始化数据库连接
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "数据库连接成功\n\n";
    
    // 检查权限是否存在
    echo "检查inventory_details_fba相关权限：\n";
    $permissionsToCheck = [
        'inventory_details_fba.view',
        'inventory_details_fba.delete'
    ];
    
    $missingPermissions = [];
    foreach ($permissionsToCheck as $permissionSlug) {
        $stmt = $pdo->prepare("SELECT * FROM permissions WHERE slug = ?");
        $stmt->execute([$permissionSlug]);
        $permission = $stmt->fetch();
        
        if ($permission) {
            echo "✓ $permissionSlug (ID: {$permission['id']}) 存在\n";
        } else {
            echo "✗ $permissionSlug 不存在\n";
            $missingPermissions[] = $permissionSlug;
        }
    }
    
    // 如果权限不存在，添加它们
    if (!empty($missingPermissions)) {
        echo "\n添加缺失的权限：\n";
        $permissionData = [
            'inventory_details_fba.view' => [
                'name' => '查看FBA库存明细',
                'slug' => 'inventory_details_fba.view',
                'description' => '可以查看FBA库存明细列表',
                'module' => 'inventory_details_fba'
            ],
            'inventory_details_fba.delete' => [
                'name' => '删除FBA库存明细',
                'slug' => 'inventory_details_fba.delete',
                'description' => '可以删除FBA库存明细',
                'module' => 'inventory_details_fba'
            ]
        ];
        
        $insertStmt = $pdo->prepare("INSERT INTO permissions (name, slug, description, module) VALUES (?, ?, ?, ?)");
        foreach ($missingPermissions as $slug) {
            $data = $permissionData[$slug];
            $insertStmt->execute([$data['name'], $data['slug'], $data['description'], $data['module']]);
            echo "✓ 添加了 $slug (ID: {$pdo->lastInsertId()})\n";
        }
    }
    
    // 检查管理员角色（role_id=1）是否拥有这些权限
    echo "\n检查管理员角色（role_id=1）的权限：\n";
    
    // 获取所有inventory_details_fba相关权限
    $stmt = $pdo->prepare("SELECT id, slug FROM permissions WHERE module = 'inventory_details_fba'");
    $stmt->execute();
    $fbaPermissions = $stmt->fetchAll();
    
    $missingRolePermissions = [];
    foreach ($fbaPermissions as $permission) {
        $stmt = $pdo->prepare("SELECT * FROM role_permissions WHERE role_id = 1 AND permission_id = ?");
        $stmt->execute([$permission['id']]);
        $rolePermission = $stmt->fetch();
        
        if ($rolePermission) {
            echo "✓ 管理员拥有 {$permission['slug']} 权限\n";
        } else {
            echo "✗ 管理员缺少 {$permission['slug']} 权限\n";
            $missingRolePermissions[] = $permission['id'];
        }
    }
    
    // 如果角色权限不存在，添加它们
    if (!empty($missingRolePermissions)) {
        echo "\n添加缺失的角色权限：\n";
        $insertStmt = $pdo->prepare("INSERT INTO role_permissions (role_id, permission_id) VALUES (1, ?)");
        foreach ($missingRolePermissions as $permissionId) {
            $insertStmt->execute([$permissionId]);
            echo "✓ 添加了权限ID: $permissionId 到管理员角色\n";
        }
    }
    
    echo "\n权限检查和修复完成！\n";
    
} catch (PDOException $e) {
    echo "数据库连接失败: " . $e->getMessage() . "\n";
    exit;
}
?>