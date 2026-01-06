<?php
// 设置错误报告
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 定义应用根目录
define('APP_ROOT', __DIR__);

try {
    // 加载模型
    require_once APP_ROOT . '/models/InventoryDetailsFba.php';
    
    echo "<h2>模型InventoryDetailsFba测试</h2>";
    
    // 创建模型实例
    $model = new InventoryDetailsFba();
    echo "<p style='color: green;'>✅ 模型实例创建成功</p>";
    
    // 测试getAll()方法 - 无参数
    echo "<h3>测试getAll() - 无参数</h3>";
    $allData = $model->getAll();
    echo "<p><strong>返回数据数量:</strong> " . count($allData) . "</p>";
    
    if (count($allData) > 0) {
        echo "<p style='color: green;'>✅ getAll()无参数调用成功，返回了数据</p>";
        
        // 显示前2条数据
        echo "<h4>前2条数据:</h4>";
        echo "<pre>" . htmlspecialchars(print_r(array_slice($allData, 0, 2), true)) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ getAll()无参数调用返回空数据</p>";
    }
    
    // 测试getAll()方法 - 带搜索参数
    echo "<h3>测试getAll() - 带搜索参数</h3>";
    $searchData = $model->getAll(['search' => '测试']);
    echo "<p><strong>搜索'测试'返回数据数量:</strong> " . count($searchData) . "</p>";
    
    // 测试getAll()方法 - 带仓库名称参数
    echo "<h3>测试getAll() - 带仓库名称参数</h3>";
    $warehouseData = $model->getAll(['name' => '美国FBA仓']);
    echo "<p><strong>仓库'美国FBA仓'返回数据数量:</strong> " . count($warehouseData) . "</p>";
    
    // 测试getAll()方法 - 带SKU参数
    echo "<h3>测试getAll() - 带SKU参数</h3>";
    $skuData = $model->getAll(['sku' => 'SKU-001']);
    echo "<p><strong>SKU包含'SKU-001'返回数据数量:</strong> " . count($skuData) . "</p>";
    
    // 测试getAllWarehouseNames()方法
    echo "<h3>测试getAllWarehouseNames()方法</h3>";
    $warehouseNames = $model->getAllWarehouseNames();
    echo "<p><strong>仓库名称数量:</strong> " . count($warehouseNames) . "</p>";
    
    if (count($warehouseNames) > 0) {
        echo "<p style='color: green;'>✅ getAllWarehouseNames()调用成功，返回了仓库名称</p>";
        echo "<p><strong>仓库名称列表:</strong> " . htmlspecialchars(implode(', ', $warehouseNames)) . "</p>";
    } else {
        echo "<p style='color: red;'>❌ getAllWarehouseNames()调用返回空数据</p>";
    }
    
    // 测试getByNameAndSku()方法
    echo "<h3>测试getByNameAndSku()方法</h3>";
    $singleData = $model->getByNameAndSku('美国FBA仓', 'SKU-001');
    
    if ($singleData) {
        echo "<p style='color: green;'>✅ getByNameAndSku()调用成功，返回了单条数据</p>";
        echo "<pre>" . htmlspecialchars(print_r($singleData, true)) . "</pre>";
    } else {
        echo "<p style='color: red;'>❌ getByNameAndSku()调用返回空数据</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ 错误: " . $e->getMessage() . "</p>";
    echo "<p><strong>错误位置:</strong> " . $e->getFile() . " 第 " . $e->getLine() . " 行</p>";
    echo "<p><strong>错误栈:</strong></p>";
    echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";
}
?>