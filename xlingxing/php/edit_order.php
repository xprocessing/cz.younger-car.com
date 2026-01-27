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

    $type_id = $_GET['type_id'];
    $wid = $_GET['wid'];
    $global_order_no = $_GET['global_order_no'];



    // 调用POST接口示例
    $orderParams = [
        'order_list' => [
            [
                'global_order_no' => $global_order_no,
                'logistics' => [
                    'logistics_type_id' => $type_id,
                    'sys_wid' => $wid
                ]
            ]
        ]
    ];
    $orders = $apiClient->post('/pb/mp/order/editOrder', $orderParams);
    //print_r("订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}

//测试链接 https://cz.younger-car.com/xlingxing/php/edit_order.php?type_id=203571748136745984&wid=5832&global_order_no=103662673459556100
