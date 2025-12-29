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
    //当前时间戳，按 Y-m-d H:i:s 格式
    $end_date = date('Y-m-d H:i:s');
    //前天的时间 ，按 Y-m-d H:i:s 格式
    $n_days=$_GET['n_days'] ?? 2; // 默认值为2天
    $start_date = date('Y-m-d H:i:s', strtotime("-$n_days days"));
    //格式化为日期时间字符串

   
    // 调用POST接口示例
    $orderParams = [
        'offset' => 0,
        'length' => 200,       
        'date_type' => 2, //1 订购时间【站点时间】 2 订单修改时间【北京时间】 3 平台更新时间【UTC时间】
        'start_date' => $start_date, //查询时间，左闭右开，格式：Y-m-d 或 Y-m-d H:i:s
        'end_date' => $end_date,
        'fulfillment_channel' => 1 // 1表示FBA订单，2表示FBM订单
    ];
    $orders = $apiClient->post('/erp/sc/data/mws/orders', $orderParams);
    //print_r("订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}

//https://cz.younger-car.com/xlingxing/php/get_orders_amazon2db.php?n_days=1
//$orders 数据中的data为数组，data[0].amazon_order_id为订单号
//用,拼接订单号，批量查询订单详情，获取利润数据。
$order_ids = implode(',', array_column($orders['data'], 'amazon_order_id'));
echo $order_ids;
try {
    // 调用POST接口获取订单详情
    $detailParams = [
        'order_id' => $order_ids
    ];
    $orderDetails = $apiClient->post('/erp/sc/data/mws/orderDetail', $detailParams);
    //json格式化输出订单详情
    echo json_encode($orderDetails, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}




?>