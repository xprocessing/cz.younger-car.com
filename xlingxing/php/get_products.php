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
    //当前时间戳，按秒
    $currentTimestamp = time();
    //前天的时间戳，按秒
    $twoDaysAgoTimestamp = $currentTimestamp - (5 * 24 * 60 * 60);
    //格式化为日期时间字符串
    $offset =$_GET['offset']??0;
    $length =$_GET['length']??100;

   
    // 调用POST接口示例
    $orderParams = [
        'offset' => $offset,
        'length' => $length,
        'update_time_end'=>$currentTimestamp      
       
    ];
    $orders = $apiClient->post('/erp/sc/routing/data/local_inventory/productList', $orderParams);
    //print_r("订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}

//访问链接示例：https://cz.younger-car.com/xlingxing/php/get_products.php?offset=0&length=100
//https://cz.younger-car.com/xlingxing/php/get_products2db.php?length=100&offset=201
//https://cz.younger-car.com/xlingxing/php/get_products2db.php?length=100&offset=301

?>