<?php
// 调试脚本：验证inventory_details_fba模块功能
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/database.php';
require_once __DIR__ . '/models/InventoryDetailsFba.php';

// 数据库连接测试
echo "=== 数据库连接测试 ===\n";
try {
    $db = Database::getInstance();
    echo "✅ 数据库连接成功\n";
    
    // 测试表是否存在
    $stmt = $db->query("SHOW TABLES LIKE 'inventory_details_fba'");
    if ($stmt->rowCount() > 0) {
        echo "✅ 表 'inventory_details_fba' 存在\n";
        
        // 测试数据数量
        $stmt = $db->query("SELECT COUNT(*) as count FROM inventory_details_fba");
        $count = $stmt->fetch()['count'];
        echo "✅ 表中共有 {$count} 条记录\n";
        
        // 如果有数据，显示前几条记录
        if ($count > 0) {
            $stmt = $db->query("SELECT * FROM inventory_details_fba LIMIT 2");
            $records = $stmt->fetchAll();
            echo "✅ 前2条记录：\n";
            foreach ($records as $index => $record) {
                echo "--- 记录 ".($index+1)." ---\n";
                echo "仓库名: {$record['name']}\n";
                echo "SKU: {$record['sku']}\n";
                echo "ASIN: {$record['asin']}\n";
                echo "商品名称: {$record['product_name']}\n";
                echo "总数: {$record['total']}\n";
                echo "FBA可售: {$record['afn_fulfillable_quantity']}\n";
            }
        }
    } else {
        echo "❌ 表 'inventory_details_fba' 不存在\n";
    }
} catch (Exception $e) {
    echo "❌ 数据库操作失败：" . $e->getMessage() . "\n";
}

echo "\n=== 模型功能测试 ===\n";
try {
    $model = new InventoryDetailsFba();
    
    // 测试getAll方法（无过滤条件）
    $allRecords = $model->getAll();
    echo "✅ getAll() 方法返回 {$count} 条记录\n";
    
    // 测试getAll方法（带过滤条件）
    if ($count > 0) {
        $firstRecord = $allRecords[0];
        $filteredRecords = $model->getAll(['sku' => substr($firstRecord['sku'], 0, 3)]);
        echo "✅ 使用SKU过滤（" . substr($firstRecord['sku'], 0, 3) . "）返回 " . count($filteredRecords) . " 条记录\n";
    }
    
    // 测试getAllWarehouseNames方法
    $warehouseNames = $model->getAllWarehouseNames();
    echo "✅ getAllWarehouseNames() 方法返回 " . count($warehouseNames) . " 个仓库名\n";
    if (count($warehouseNames) > 0) {
        echo "✅ 仓库名列表：" . implode(', ', $warehouseNames) . "\n";
    }
    
} catch (Exception $e) {
    echo "❌ 模型操作失败：" . $e->getMessage() . "\n";
}

echo "\n=== 调试完成 ===\n";
