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
    // ========== 引入配置文件 ==========
    $configPath = __DIR__ . '/../../admin-panel/config/config.php';
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

    // ========== 处理请求参数 ==========
    $sku = $_GET['sku'] ?? '';
    if (empty($sku)) {
        throw new Exception("缺少必要参数：sku");
    }

    // ========== 调用领星API获取库存数据 ==========
    $apiClient = new LingXingApiClient();
    $params = [
        'offset' => 0,
        'length' => 100,
        'sku' => $sku
    ];

    $apiResult = $apiClient->post('/erp/sc/routing/data/local_inventory/inventoryDetails', $params);
    $inventoryItems = $apiResult['data'] ?? [];

    $response['data']['api_inventory_count'] = count($inventoryItems);

    if (empty($inventoryItems)) {
        $response['msg'] = '未获取到库存数据';
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
            PDO::ATTR_EMULATE_PREPARES => false
        ]
    );

    // 使用INSERT ... ON DUPLICATE KEY UPDATE（基于uk_wid_sku唯一索引）
    $sql = "INSERT INTO inventory_details (
        wid,
        product_id,
        sku,
        seller_id,
        fnsku,
        product_total,
        product_valid_num,
        product_bad_num,
        product_qc_num,
        product_lock_num,
        good_lock_num,
        bad_lock_num,
        stock_cost_total,
        quantity_receive,
        stock_cost,
        product_onway,
        transit_head_cost,
        average_age,
        third_inventory,
        stock_age_list,
        available_inventory_box_qty,
        purchase_price,
        price,
        head_stock_price,
        stock_price
    ) VALUES (
        :wid,
        :product_id,
        :sku,
        :seller_id,
        :fnsku,
        :product_total,
        :product_valid_num,
        :product_bad_num,
        :product_qc_num,
        :product_lock_num,
        :good_lock_num,
        :bad_lock_num,
        :stock_cost_total,
        :quantity_receive,
        :stock_cost,
        :product_onway,
        :transit_head_cost,
        :average_age,
        :third_inventory,
        :stock_age_list,
        :available_inventory_box_qty,
        :purchase_price,
        :price,
        :head_stock_price,
        :stock_price
    ) ON DUPLICATE KEY UPDATE
        product_id = VALUES(product_id),
        seller_id = VALUES(seller_id),
        fnsku = VALUES(fnsku),
        product_total = VALUES(product_total),
        product_valid_num = VALUES(product_valid_num),
        product_bad_num = VALUES(product_bad_num),
        product_qc_num = VALUES(product_qc_num),
        product_lock_num = VALUES(product_lock_num),
        good_lock_num = VALUES(good_lock_num),
        bad_lock_num = VALUES(bad_lock_num),
        stock_cost_total = VALUES(stock_cost_total),
        quantity_receive = VALUES(quantity_receive),
        stock_cost = VALUES(stock_cost),
        product_onway = VALUES(product_onway),
        transit_head_cost = VALUES(transit_head_cost),
        average_age = VALUES(average_age),
        third_inventory = VALUES(third_inventory),
        stock_age_list = VALUES(stock_age_list),
        available_inventory_box_qty = VALUES(available_inventory_box_qty),
        purchase_price = VALUES(purchase_price),
        price = VALUES(price),
        head_stock_price = VALUES(head_stock_price),
        stock_price = VALUES(stock_price)";

    $stmt = $pdo->prepare($sql);
    $syncCount = 0;

    // 遍历库存数据并处理
    foreach ($inventoryItems as $item) {
        // 构造数据数组（安全取值并处理类型转换）
        $data = [
            ':wid' => intval($item['wid'] ?? 0),
            ':product_id' => intval($item['product_id'] ?? 0),
            ':sku' => $item['sku'] ?? '',
            ':seller_id' => $item['seller_id'] ?? '',
            ':fnsku' => $item['fnsku'] ?? '',
            ':product_total' => intval($item['product_total'] ?? 0),
            ':product_valid_num' => intval($item['product_valid_num'] ?? 0),
            ':product_bad_num' => intval($item['product_bad_num'] ?? 0),
            ':product_qc_num' => intval($item['product_qc_num'] ?? 0),
            ':product_lock_num' => intval($item['product_lock_num'] ?? 0),
            ':good_lock_num' => intval($item['good_lock_num'] ?? 0),
            ':bad_lock_num' => intval($item['bad_lock_num'] ?? 0),
            ':stock_cost_total' => floatval($item['stock_cost_total'] ?? 0),
            ':quantity_receive' => $item['quantity_receive'] ?? '0',
            ':stock_cost' => floatval($item['stock_cost'] ?? 0),
            ':product_onway' => intval($item['product_onway'] ?? 0),
            ':transit_head_cost' => floatval($item['transit_head_cost'] ?? 0),
            ':average_age' => intval($item['average_age'] ?? 0),
            ':third_inventory' => json_encode($item['third_inventory'] ?? []),
            ':stock_age_list' => json_encode($item['stock_age_list'] ?? []),
            ':available_inventory_box_qty' => floatval($item['available_inventory_box_qty'] ?? 0),
            ':purchase_price' => floatval($item['purchase_price'] ?? 0),
            ':price' => floatval($item['price'] ?? 0),
            ':head_stock_price' => floatval($item['head_stock_price'] ?? 0),
            ':stock_price' => floatval($item['stock_price'] ?? 0)
        ];

        // 执行SQL
        $stmt->execute($data);
        $syncCount++;
        $response['logs'][] = "库存【{$data[':sku']} - 仓库ID:{$data[':wid']}】同步成功";
    }

    $response['data']['synced_count'] = $syncCount;
    $pdo = null; // 关闭连接

} catch (Exception $e) {
    // 统一异常处理
    $response['code'] = 500;
    $response['msg'] = '操作失败：' . $e->getMessage();
    $response['logs'] = [];
    error_log("[库存同步错误] " . date('Y-m-d H:i:s') . "：" . $e->getMessage() . " 行号：" . $e->getLine());
}

// 输出最终JSON响应
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// 请求参考链接：https://cz.younger-car.com/xlingxing/php/get_inventoryDetails2db.php?sku=NI-C63-FL-GB
?>