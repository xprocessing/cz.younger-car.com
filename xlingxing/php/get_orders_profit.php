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
        'length' => 20,
        'order_status' => 6,
        'date_type' => 'global_purchase_time',
        'start_time' => $nDaysAgoTimestamp,
        'end_time' => $oneDaysAgoTimestamp 
    ];
    $orders = $apiClient->post('/pb/mp/order/v2/list', $orderParams);
    //print_r("已发货订单数据：" . PHP_EOL);
    //json格式化输出
    //echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}


include '../../admin-panel/config/config.php';
//将订单列表每条，数据插入到数据库中，若存在则更新


try {
    // 初始化PDO连接
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", // 建议指定字符集
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    $orders = $orders['data']['list'] ?? [];
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    // 遍历每个订单
    foreach ($orders as $order) {
        // 字段映射：根据orders.json结构调整键名对应关系
        $data = [
            'store_id' => $order['store_id'] ?? '',
            'global_order_no' => $order['global_order_no'] ?? '',
            'receiver_country' => $order['receiver_country'] ?? '',
            'global_purchase_time' => $order['global_purchase_time'] ?? '',
            'local_sku' => $order['local_sku'] ?? '',
            'order_total_amount' => $order['order_total_amount'] ?? '',
            'outbound_cost_amount' => $order['outbound_cost_amount'] ?? '',
            'profit_amount' => $order['profit_amount'] ?? '',
            'profit_rate' => $order['profit_rate'] ?? '',
            'wms_outbound_cost_amount' => $order['wms_outbound_cost_amount'] ?? '',
            'wms_shipping_price_amount' => $order['wms_shipping_price_amount'] ?? '',
            'update_time' => date('Y-m-d H:i:s') // 当前时间
        ];

        // 检查订单是否已存在
        $checkStmt = $pdo->prepare("SELECT id FROM order_profit WHERE global_order_no = :global_order_no");
        $checkStmt->execute([':global_order_no' => $data['global_order_no']]);
        $existingId = $checkStmt->fetchColumn();

        if ($existingId) {
            // 订单存在，执行更新操作
            $updateSql = "UPDATE order_profit SET
                store_id = :store_id,
                receiver_country = :receiver_country,
                global_purchase_time = :global_purchase_time,
                local_sku = :local_sku,
                order_total_amount = :order_total_amount,
                outbound_cost_amount = :outbound_cost_amount,
                profit_amount = :profit_amount,
                profit_rate = :profit_rate,
                wms_outbound_cost_amount = :wms_outbound_cost_amount,
                wms_shipping_price_amount = :wms_shipping_price_amount,
                update_time = :update_time
                WHERE global_order_no = :global_order_no";
            
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute($data);
            echo "更新订单 {$data['global_order_no']} 成功\n";
        } else {
            // 订单不存在，执行插入操作
            $insertSql = "INSERT INTO order_profit (
                store_id,
                global_order_no,
                receiver_country,
                global_purchase_time,
                local_sku,
                order_total_amount,
                outbound_cost_amount,
                profit_amount,
                profit_rate,
                wms_outbound_cost_amount,
                wms_shipping_price_amount,
                update_time
            ) VALUES (
                :store_id,
                :global_order_no,
                :receiver_country,
                :global_purchase_time,
                :local_sku,
                :order_total_amount,
                :outbound_cost_amount,
                :profit_amount,
                :profit_rate,
                :wms_outbound_cost_amount,
                :wms_shipping_price_amount,
                :update_time
            )";
            
            $insertStmt = $pdo->prepare($insertSql);
            $insertStmt->execute($data);
            echo "插入新订单 {$data['global_order_no']} 成功\n";
        }
    }

    // 关闭连接
    $pdo = null;

} catch (PDOException $e) {
    // 错误处理（可选：记录日志/输出调试信息）
    // error_log("运费数据操作失败：" . $e->getMessage());
    // die("数据库错误：" . $e->getMessage()); // 调试时启用，生产环境注释
} finally {
    // 可选：关闭连接（PHP会自动回收，高并发场景建议手动关闭）
    // $pdo = null;
}




?>