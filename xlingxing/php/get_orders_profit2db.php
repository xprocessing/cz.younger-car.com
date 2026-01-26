<?php
// ========== 响应头配置（必须在所有输出前） ==========
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

// ========== 初始化响应数据 ==========
$response = [
    'code' => 200,
    'msg' => '操作成功',
    'data' => [],
    'logs' => []
];

try {
    // ========== 引入配置文件（提前引入，避免后续依赖） ==========
    $configPath = __DIR__ . '/../../config.php';
    if (!file_exists($configPath)) {
        throw new Exception("配置文件不存在：{$configPath}");
    }
    require_once $configPath;

    // ========== 引入领星API客户端 ==========
    $apiPath = __DIR__ . '/lx_api.php';
    if (!file_exists($apiPath)) {
        throw new Exception("领星API文件不存在：{$apiPath}");
    }
    require_once $apiPath;

    // ========== 领星API调用逻辑 ==========
    $nDaysAgo=$_GET['nDaysAgo'] ?? 1;
    $apiClient = new LingXingApiClient();
    $currentTimestamp = time();
    $oneDaysAgoTimestamp = $currentTimestamp - ($nDaysAgo * 24 * 60 * 60);
    $nDaysAgoTimestamp = $currentTimestamp - (($nDaysAgo+2) * 24 * 60 * 60);

    // 构造请求参数
    $orderParams = [
        'offset' => 0,
        'length' => 200,
        'order_status' => 6,
        'date_type' => 'global_purchase_time',
        'start_time' => $nDaysAgoTimestamp,
        'end_time' => $oneDaysAgoTimestamp
    ];

    // 调用API获取订单数据
    $apiResult = $apiClient->post('/pb/mp/order/v2/list', $orderParams);
    $orders = $apiResult['data']['list'] ?? [];
    //echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
   
    $response['data']['api_orders_count'] = count($orders);

    if (empty($orders)) {
        $response['msg'] = '未获取到订单数据';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ========== 数据库操作逻辑 ==========
    // 初始化PDO连接
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

    // 使用INSERT ... ON DUPLICATE KEY UPDATE优化（需为global_order_no创建唯一索引）
    $sql = "INSERT INTO order_profit (
        store_id,
        global_order_no,
        wid,
        warehouse_name,
        receiver_country,
        global_purchase_time,
        local_sku,
        order_total_amount,       
        profit_amount,
        profit_rate,
        wms_outbound_cost_amount,
        wms_shipping_price_amount,
        transaction_fee_amount,
        cg_price_amount,
        update_time
    ) VALUES (
        :store_id,
        :global_order_no,
        :wid,
        :warehouse_name,    
        :receiver_country,
        :global_purchase_time,
        :local_sku,
        :order_total_amount,        
        :profit_amount,
        :profit_rate,
        :wms_outbound_cost_amount,
        :wms_shipping_price_amount,
        :transaction_fee_amount,
        :cg_price_amount,
        :update_time
    ) ON DUPLICATE KEY UPDATE
        store_id = VALUES(store_id),
        wid = VALUES(wid),
        warehouse_name = VALUES(warehouse_name),
        receiver_country = VALUES(receiver_country),
        global_purchase_time = VALUES(global_purchase_time),
        local_sku = VALUES(local_sku),
        order_total_amount = VALUES(order_total_amount),        
        profit_amount = VALUES(profit_amount),
        profit_rate = VALUES(profit_rate),
        wms_outbound_cost_amount = VALUES(wms_outbound_cost_amount),
        wms_shipping_price_amount = VALUES(wms_shipping_price_amount),
        transaction_fee_amount = VALUES(transaction_fee_amount),
        cg_price_amount = VALUES(cg_price_amount),
        update_time = VALUES(update_time)";

    $stmt = $pdo->prepare($sql);
    $syncCount = 0;

    // 遍历订单数据并处理
    foreach ($orders as $order) {
        // 安全取值：避免数组索引未定义错误
        $addressInfo = $order['address_info'] ?? [];
        $itemInfo = $order['item_info'][0] ?? [];
        $transactionInfo = $order['transaction_info'][0] ?? [];

        // 处理时间格式：将时间戳转为数据库datetime格式
        $purchaseTime = $order['global_purchase_time'] ?? 0;
        $purchaseTime = $purchaseTime ? date('Y-m-d H:i:s', $purchaseTime) : null;

        // 计算利润利率（避免除以0），order_total_amount和profit_amount字符串前面均有货币符号，需去掉货币符号，转换为浮点数
        $orderTotal = floatval(preg_replace('/[^0-9.]/', '', $transactionInfo['order_total_amount'] ?? 0));
        $profit = floatval(preg_replace('/[^0-9.-]/', '', $transactionInfo['profit_amount'] ?? 0));  
        $profitRate = $orderTotal > 0 ? round(($profit / $orderTotal) * 100, 2) : 0;

        //去掉字符串中的-号
        $wms_outbound_cost_amount=$transactionInfo['wms_outbound_cost_amount'];
        $wms_outbound_cost_amount=str_replace('-','',$wms_outbound_cost_amount);
        $wms_shipping_price_amount=$transactionInfo['wms_shipping_price_amount'];
        $wms_shipping_price_amount=str_replace('-','',$wms_shipping_price_amount);
        $transaction_fee_amount=$transactionInfo['transaction_fee_amount'];        
        $cg_price_amount=$transactionInfo['cg_price_amount'];
        


        // 构造数据数组
        $data = [
            ':store_id' => $order['store_id'] ?? '',
            ':global_order_no' => $order['global_order_no'] ?? '',
            ':receiver_country' => $addressInfo['receiver_country_code'] ?? '',
            ':global_purchase_time' => $purchaseTime,
            ':wid' => $order['wid'] ?? '',
            ':warehouse_name' => $order['warehouse_name'] ?? '',
            ':local_sku' => $itemInfo['local_sku'] ?? '',
            ':order_total_amount' => $transactionInfo['order_total_amount'] ?? 0,           
            ':profit_amount' => $transactionInfo['profit_amount'] ?? 0,
            ':profit_rate' => $profitRate,
            ':wms_outbound_cost_amount' => $wms_outbound_cost_amount ?? 0,
            ':wms_shipping_price_amount' => $wms_shipping_price_amount ?? 0,
            ':transaction_fee_amount' => $transaction_fee_amount ?? 0,
            ':cg_price_amount' => $cg_price_amount ?? 0,
            ':update_time' => date('Y-m-d H:i:s')
        ];

        // 执行SQL
        $stmt->execute($data);
        $syncCount++;
        $response['logs'][] = "订单【{$data[':global_order_no']}】同步成功";
    }

    $response['data']['synced_count'] = $syncCount;
    $pdo = null; // 关闭连接

} catch (Exception $e) {
    // 统一异常处理
    $response['code'] = 500;
    $response['msg'] = '操作失败：' . $e->getMessage();
    $response['logs'] = [];
    // 记录错误日志（生产环境建议开启）
    error_log("[订单同步错误] " . date('Y-m-d H:i:s') . "：" . $e->getMessage() . " 行号：" . $e->getLine());
}

// 输出最终JSON响应
echo json_encode($response, JSON_UNESCAPED_UNICODE);

//请求参考链接：https://cz.younger-car.com/xlingxing/php/get_orders_profit2db.php&nDaysAgo=1
?>