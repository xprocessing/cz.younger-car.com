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
    //当前时间戳，按秒//待审核订单
    $currentTimestamp = time();
    $nDaysAgo=$_GET['nDaysAgo'] ?? 2;   
    $nDaysAgoTimestamp = $currentTimestamp - ($nDaysAgo * 24 * 60 * 60);

   
    // 调用POST接口示例
    $orderParams = [
        'offset' => 0,
        'length' => 300,
        'order_status' => 4,//待发货订单
        'date_type' => 'global_purchase_time',
       'start_time' => $nDaysAgoTimestamp,
        'end_time' => $currentTimestamp,
    ];
    $orders = $apiClient->post('/pb/mp/order/v2/list', $orderParams);
    //print_r("订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}


//测试链接 https://cz.younger-car.com/xlingxing/php/get_orders.php?nDaysAgo=2
?>