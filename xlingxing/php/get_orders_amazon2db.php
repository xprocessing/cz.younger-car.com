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

    //1. 获取订单列表
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
    //2. 获取订单详情
    $orderDetails = $apiClient->post('/erp/sc/data/mws/orderDetail', $detailParams);
    //json格式化输出订单详情
    echo json_encode($orderDetails, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}


//数据库准备
 // ========== 引入配置文件（提前引入，避免后续依赖） ==========
$configPath = __DIR__ . '/../../admin-panel/config/config.php';
    if (!file_exists($configPath)) {
        throw new Exception("配置文件不存在：{$configPath}");
    }
    require_once $configPath;
    
$pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false // 禁用模拟预处理，提升安全性
        ]
    );

 $sql = "INSERT INTO order_profit (
        store_id,
        global_order_no,
        warehouse_name,
        receiver_country,
        global_purchase_time,
        local_sku,
        order_total_amount,       
        profit_amount,
        profit_rate,
        wms_outbound_cost_amount,
        wms_shipping_price_amount,
        update_time
    ) VALUES (
        :store_id,
        :global_order_no,
        :warehouse_name,    
        :receiver_country,
        :global_purchase_time,
        :local_sku,
        :order_total_amount,        
        :profit_amount,
        :profit_rate,
        :wms_outbound_cost_amount,
        :wms_shipping_price_amount,
        :update_time
    ) ON DUPLICATE KEY UPDATE
        store_id = VALUES(store_id),
        warehouse_name = VALUES(warehouse_name),
        receiver_country = VALUES(receiver_country),
        global_purchase_time = VALUES(global_purchase_time),
        local_sku = VALUES(local_sku),
        order_total_amount = VALUES(order_total_amount),        
        profit_amount = VALUES(profit_amount),
        profit_rate = VALUES(profit_rate),
        wms_outbound_cost_amount = VALUES(wms_outbound_cost_amount),
        wms_shipping_price_amount = VALUES(wms_shipping_price_amount),
        update_time = VALUES(update_time)";

    $stmt = $pdo->prepare($sql);
    // 构造数据数组
    //3.整理$orders和$orderDetails 数据作为新的数组


// 构造数据数组
$data = [];
foreach ($orders['data'] as $order) {
    foreach ($orderDetails['data'] as $detail) {
        if ($order['amazon_order_id'] === $detail['amazon_order_id']) {
               //出库成本，采购费用+佣金
            $wms_outbound_cost_amount =$detail['item_list'][0]['cg_price']+$detail['item_list'][0]['commission_amount'];
            //利润率 $detail['item_list'][0]['profit']/$order['order_total_amount'],格式12%
            $profit_rate = round($detail['item_list'][0]['profit']/$order['order_total_amount']*100,2) . '%';

            


            $data[] = [
                'store_id' => $order['sid'],
                'global_order_no' => $order['amazon_order_id'],
                'warehouse_name' => $order['fulfillment_channel'],
                'receiver_country' => $detail['country'],
                'global_purchase_time' => $order['purchase_date_local'],
                'local_sku' => $order['item_list'][0]['local_sku'],
                'order_total_amount' => $detail['icon']+$order['order_total_amount'],
                'profit_amount' => $detail['icon']+$detail['item_list'][0]['profit'],
                'profit_rate' => $profit_rate,
                'wms_outbound_cost_amount' => $wms_outbound_cost_amount,
                'wms_shipping_price_amount' => $detail['item_list'][0]['fba_shipment_amount'],
                'update_time' => date('Y-m-d H:i:s')
            ];
            echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        }
    }
}
    // 执行SQL
   // $stmt->execute($data);




?>