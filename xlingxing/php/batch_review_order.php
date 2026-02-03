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
    $global_order_no = $_GET['global_order_no'];
    
   
    // 调用POST接口示例
    $orderParams = [
        'global_order_no' =>  [$global_order_no] //array类型
       
    ];
    $orders = $apiClient->post('/basicOpen/openapi/multiplatform/order/review', $orderParams);
    //print_r("订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}


//测试链接 https://cz.younger-car.com/xlingxing/php/batch_review_order.php?global_order_no=103662673459556100   

//更新数据表 order_status 为 已审核
$updateParams = [
    'global_order_no' =>  [$global_order_no], //array类型
    'order_status' => '已审核'
];
///更新数据库

?>