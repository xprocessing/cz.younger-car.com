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


//提取获取的数据 data.data数组中每一数据 对应的  item_list 数组中每一个数据的wid,sku,quantity_receive 组成一个新的数组。
$newArray = [];
foreach ($orders['data'] as $order) {
    foreach ($order['item_list'] as $item) {
        $newArray[] = [
            'wid' => $item['wid'],
            'sku' => $item['sku'],
            'quantity_receive' => $item['quantity_receive'],
        ];
    }
}
// 打印新数组
print_r($newArray);

//连接数据库，用$newArray 的数据 更新inventory_details数据表中wid,sku对应的quantity_receive字段

// 引入数据库配置文件
require_once __DIR__ . '/../../admin-panel/config/config.php';

try {
    // 创建PDO实例
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );

    // 准备SQL：仅更新已存在的wid+sku对应的quantity_receive
    $sql = "UPDATE inventory_details 
            SET quantity_receive = :quantity_receive
            WHERE wid = :wid AND sku = :sku";
    $stmt = $pdo->prepare($sql);

    // 开启事务
    $pdo->beginTransaction();
// 跳过 quantity_receive 为 0 或空字符串的记录
    foreach ($newArray as $row) {
        if (empty($row['quantity_receive']) && $row['quantity_receive'] !== '0') {
            continue;
        }
        $stmt->execute([
            ':wid' => $row['wid'],
            ':sku' => $row['sku'],
            ':quantity_receive' => $row['quantity_receive'],
        ]);
    }

    // 提交事务
    $pdo->commit();

    echo json_encode(['status' => 'success', 'message' => '库存入库数量已更新' . count($newArray) . '条'], JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    // 回滚事务
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }
    echo json_encode(['status' => 'error', 'message' => '数据库错误：' . $e->getMessage()], JSON_UNESCAPED_UNICODE);
}



//测试链接https://cz.younger-car.com/xlingxing/php/get_purchaseOrderList2db.php?nDaysAgo=300


?>