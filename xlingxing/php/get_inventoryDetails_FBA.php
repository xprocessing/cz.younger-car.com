<?php
// ========== 响应头配置 ==========
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8"); // 改为JSON输出
//引入lx_api.php

require_once __DIR__ . '/lx_api.php';
try {
    // 初始化API客户端
    $apiClient = new LingXingApiClient();

    $offset = $_GET['offset'] ?? 0;
   
    // 调用POST接口示例
    $params = [
        'offset' => $offset,
        'length' => 100
       
        
    ];
    $result = $apiClient->post('/basicOpen/openapi/storage/fbaWarehouseDetail', $params);
    //print_r("订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}

// 测试https://cz.younger-car.com/xlingxing/php/get_inventoryDetails_FBA.php?offset=0
// 测试https://cz.younger-car.com/xlingxing/php/get_inventoryDetails_FBA.php?offset=100

//将$result中的data数据存入数据库


?>