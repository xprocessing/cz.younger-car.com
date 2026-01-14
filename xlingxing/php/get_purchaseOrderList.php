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
    $nDaysAgo = $_GET['nDaysAgo'] ?? 2;
    // 当前时间戳，按秒
    $currentTimestamp = time();
    $start_date = date('Y-m-d', $currentTimestamp - ($nDaysAgo * 24 * 60 * 60));
    $end_date = date('Y-m-d', $currentTimestamp);
    // 打印调试信息
    echo "nDaysAgo: $nDaysAgo" . PHP_EOL;
    echo "start_date: $start_date" . PHP_EOL;
    echo "end_date: $end_date" . PHP_EOL;

   
    // 调用POST接口示例
    $orderParams = [
        'offset' => 0,
        'length' => 200,
        'order_status' => 6,       
       'start_date' => $start_date,
        'end_date' => $end_date
    ];
    $orders = $apiClient->post('/erp/sc/routing/data/local_inventory/purchaseOrderList', $orderParams);
    //print_r("订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}



?>