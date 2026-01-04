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

   $type = $_GET['type'];
   
    // 调用POST接口示例
    $orderParams = [
        'offset' => 0,
        'length' => 200,
        'type'=>$type
        
    ];
    $orders = $apiClient->post('/erp/sc/data/local_inventory/warehouse', $orderParams);
    //print_r("订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}

//测试https://cz.younger-car.com/xlingxing/php/get_warehouse.php?type=1
// 仓库类型：1 本地仓【默认值】 ，3 海外仓 ，4 亚马逊平台仓，6 AWD仓


?>