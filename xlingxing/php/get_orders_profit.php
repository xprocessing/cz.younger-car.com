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
    //24小时之前的订单时间戳，按秒
    $oneDaysAgoTimestamp = $currentTimestamp - (1 * 24 * 60 * 60);
    //N天的时间戳，按秒
    $nDaysAgoTimestamp = $currentTimestamp - (3 * 24 * 60 * 60);
    //格式化为日期时间字符串

   
    // 调用POST接口示例
    $orderParams = [
        'offset' => 0,
        'length' => 100,
        'order_status' => 6,
        'date_type' => 'global_purchase_time',
        'start_time' => $nDaysAgoTimestamp,
        'end_time' => $oneDaysAgoTimestamp 
    ];
    $orders = $apiClient->post('/pb/mp/order/v2/list', $orderParams);
    //print_r("已发货订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}



?>