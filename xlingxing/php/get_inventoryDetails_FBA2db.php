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
    $offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;

    // ========== 调用领星API获取FBA库存数据 ==========
    $apiClient = new LingXingApiClient();
    $params = [
        'offset' => $offset,
        'length' => 200,
        'fulfillment_channel_type' => 'FBA'
    ];

    $apiResult = $apiClient->post('/basicOpen/openapi/storage/fbaWarehouseDetail', $params);
    $inventoryItems = $apiResult['data'] ?? [];

    $response['data']['api_inventory_count'] = count($inventoryItems);

    if (empty($inventoryItems)) {
        $response['msg'] = '未获取到FBA库存数据';
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

    // 构造INSERT ... ON DUPLICATE KEY UPDATE语句（基于uk_name_sku唯一索引）
    $sql = "INSERT INTO inventory_details_fba (
        name,
        seller_group_name,
        sid,
        asin,
        product_name,
        small_image_url,
        seller_sku,
        fnsku,
        sku,
        category_text,
        cid,
        product_brand_text,
        bid,
        share_type,
        total,
        total_price,
        available_total,
        available_total_price,
        afn_fulfillable_quantity,
        afn_fulfillable_quantity_price,
        reserved_fc_transfers,
        reserved_fc_transfers_price,
        reserved_fc_processing,
        reserved_fc_processing_price,
        reserved_customerorders,
        reserved_customerorders_price,
        quantity,
        quantity_price,
        afn_unsellable_quantity,
        afn_unsellable_quantity_price,
        afn_inbound_working_quantity,
        afn_inbound_working_quantity_price,
        afn_inbound_shipped_quantity,
        afn_inbound_shipped_quantity_price,
        afn_inbound_receiving_quantity,
        afn_inbound_receiving_quantity_price,
        stock_up_num,
        stock_up_num_price,
        afn_researching_quantity,
        afn_researching_quantity_price,
        total_fulfillable_quantity,
        inv_age_0_to_30_days,
        inv_age_0_to_30_price,
        inv_age_31_to_60_days,
        inv_age_31_to_60_price,
        inv_age_61_to_90_days,
        inv_age_61_to_90_price,
        inv_age_0_to_90_days,
        inv_age_0_to_90_price,
        inv_age_91_to_180_days,
        inv_age_91_to_180_price,
        inv_age_181_to_270_days,
        inv_age_181_to_270_price,
        inv_age_271_to_330_days,
        inv_age_271_to_330_price,
        inv_age_271_to_365_days,
        inv_age_271_to_365_price,
        inv_age_331_to_365_days,
        inv_age_331_to_365_price,
        inv_age_365_plus_days,
        inv_age_365_plus_price,
        recommended_action,
        sell_through,
        estimated_excess_quantity,
        estimated_storage_cost_next_month,
        fba_minimum_inventory_level,
        fba_inventory_level_health_status,
        historical_days_of_supply,
        historical_days_of_supply_price,
        low_inventory_level_fee_applied,
        fulfillment_channel,
        cg_price,
        cg_transport_costs,
        fba_storage_quantity_list
    ) VALUES (
        :name,
        :seller_group_name,
        :sid,
        :asin,
        :product_name,
        :small_image_url,
        :seller_sku,
        :fnsku,
        :sku,
        :category_text,
        :cid,
        :product_brand_text,
        :bid,
        :share_type,
        :total,
        :total_price,
        :available_total,
        :available_total_price,
        :afn_fulfillable_quantity,
        :afn_fulfillable_quantity_price,
        :reserved_fc_transfers,
        :reserved_fc_transfers_price,
        :reserved_fc_processing,
        :reserved_fc_processing_price,
        :reserved_customerorders,
        :reserved_customerorders_price,
        :quantity,
        :quantity_price,
        :afn_unsellable_quantity,
        :afn_unsellable_quantity_price,
        :afn_inbound_working_quantity,
        :afn_inbound_working_quantity_price,
        :afn_inbound_shipped_quantity,
        :afn_inbound_shipped_quantity_price,
        :afn_inbound_receiving_quantity,
        :afn_inbound_receiving_quantity_price,
        :stock_up_num,
        :stock_up_num_price,
        :afn_researching_quantity,
        :afn_researching_quantity_price,
        :total_fulfillable_quantity,
        :inv_age_0_to_30_days,
        :inv_age_0_to_30_price,
        :inv_age_31_to_60_days,
        :inv_age_31_to_60_price,
        :inv_age_61_to_90_days,
        :inv_age_61_to_90_price,
        :inv_age_0_to_90_days,
        :inv_age_0_to_90_price,
        :inv_age_91_to_180_days,
        :inv_age_91_to_180_price,
        :inv_age_181_to_270_days,
        :inv_age_181_to_270_price,
        :inv_age_271_to_330_days,
        :inv_age_271_to_330_price,
        :inv_age_271_to_365_days,
        :inv_age_271_to_365_price,
        :inv_age_331_to_365_days,
        :inv_age_331_to_365_price,
        :inv_age_365_plus_days,
        :inv_age_365_plus_price,
        :recommended_action,
        :sell_through,
        :estimated_excess_quantity,
        :estimated_storage_cost_next_month,
        :fba_minimum_inventory_level,
        :fba_inventory_level_health_status,
        :historical_days_of_supply,
        :historical_days_of_supply_price,
        :low_inventory_level_fee_applied,
        :fulfillment_channel,
        :cg_price,
        :cg_transport_costs,
        :fba_storage_quantity_list
    ) ON DUPLICATE KEY UPDATE
        seller_group_name = VALUES(seller_group_name),
        sid = VALUES(sid),
        asin = VALUES(asin),
        product_name = VALUES(product_name),
        small_image_url = VALUES(small_image_url),
        seller_sku = VALUES(seller_sku),
        fnsku = VALUES(fnsku),
        category_text = VALUES(category_text),
        cid = VALUES(cid),
        product_brand_text = VALUES(product_brand_text),
        bid = VALUES(bid),
        share_type = VALUES(share_type),
        total = VALUES(total),
        total_price = VALUES(total_price),
        available_total = VALUES(available_total),
        available_total_price = VALUES(available_total_price),
        afn_fulfillable_quantity = VALUES(afn_fulfillable_quantity),
        afn_fulfillable_quantity_price = VALUES(afn_fulfillable_quantity_price),
        reserved_fc_transfers = VALUES(reserved_fc_transfers),
        reserved_fc_transfers_price = VALUES(reserved_fc_transfers_price),
        reserved_fc_processing = VALUES(reserved_fc_processing),
        reserved_fc_processing_price = VALUES(reserved_fc_processing_price),
        reserved_customerorders = VALUES(reserved_customerorders),
        reserved_customerorders_price = VALUES(reserved_customerorders_price),
        quantity = VALUES(quantity),
        quantity_price = VALUES(quantity_price),
        afn_unsellable_quantity = VALUES(afn_unsellable_quantity),
        afn_unsellable_quantity_price = VALUES(afn_unsellable_quantity_price),
        afn_inbound_working_quantity = VALUES(afn_inbound_working_quantity),
        afn_inbound_working_quantity_price = VALUES(afn_inbound_working_quantity_price),
        afn_inbound_shipped_quantity = VALUES(afn_inbound_shipped_quantity),
        afn_inbound_shipped_quantity_price = VALUES(afn_inbound_shipped_quantity_price),
        afn_inbound_receiving_quantity = VALUES(afn_inbound_receiving_quantity),
        afn_inbound_receiving_quantity_price = VALUES(afn_inbound_receiving_quantity_price),
        stock_up_num = VALUES(stock_up_num),
        stock_up_num_price = VALUES(stock_up_num_price),
        afn_researching_quantity = VALUES(afn_researching_quantity),
        afn_researching_quantity_price = VALUES(afn_researching_quantity_price),
        total_fulfillable_quantity = VALUES(total_fulfillable_quantity),
        inv_age_0_to_30_days = VALUES(inv_age_0_to_30_days),
        inv_age_0_to_30_price = VALUES(inv_age_0_to_30_price),
        inv_age_31_to_60_days = VALUES(inv_age_31_to_60_days),
        inv_age_31_to_60_price = VALUES(inv_age_31_to_60_price),
        inv_age_61_to_90_days = VALUES(inv_age_61_to_90_days),
        inv_age_61_to_90_price = VALUES(inv_age_61_to_90_price),
        inv_age_0_to_90_days = VALUES(inv_age_0_to_90_days),
        inv_age_0_to_90_price = VALUES(inv_age_0_to_90_price),
        inv_age_91_to_180_days = VALUES(inv_age_91_to_180_days),
        inv_age_91_to_180_price = VALUES(inv_age_91_to_180_price),
        inv_age_181_to_270_days = VALUES(inv_age_181_to_270_days),
        inv_age_181_to_270_price = VALUES(inv_age_181_to_270_price),
        inv_age_271_to_330_days = VALUES(inv_age_271_to_330_days),
        inv_age_271_to_330_price = VALUES(inv_age_271_to_330_price),
        inv_age_271_to_365_days = VALUES(inv_age_271_to_365_days),
        inv_age_271_to_365_price = VALUES(inv_age_271_to_365_price),
        inv_age_331_to_365_days = VALUES(inv_age_331_to_365_days),
        inv_age_331_to_365_price = VALUES(inv_age_331_to_365_price),
        inv_age_365_plus_days = VALUES(inv_age_365_plus_days),
        inv_age_365_plus_price = VALUES(inv_age_365_plus_price),
        recommended_action = VALUES(recommended_action),
        sell_through = VALUES(sell_through),
        estimated_excess_quantity = VALUES(estimated_excess_quantity),
        estimated_storage_cost_next_month = VALUES(estimated_storage_cost_next_month),
        fba_minimum_inventory_level = VALUES(fba_minimum_inventory_level),
        fba_inventory_level_health_status = VALUES(fba_inventory_level_health_status),
        historical_days_of_supply = VALUES(historical_days_of_supply),
        historical_days_of_supply_price = VALUES(historical_days_of_supply_price),
        low_inventory_level_fee_applied = VALUES(low_inventory_level_fee_applied),
        fulfillment_channel = VALUES(fulfillment_channel),
        cg_price = VALUES(cg_price),
        cg_transport_costs = VALUES(cg_transport_costs),
        fba_storage_quantity_list = VALUES(fba_storage_quantity_list)";

    $stmt = $pdo->prepare($sql);
    $syncCount = 0;

    // 遍历FBA库存数据并入库
    foreach ($inventoryItems as $item) {
        // 构造数据数组（安全取值+类型转换，适配表字段类型）
        $data = [
            ':name' => $item['name'] ?? '',
            ':seller_group_name' => $item['seller_group_name'] ?? '',
            ':sid' => intval($item['sid'] ?? 0),
            ':asin' => $item['asin'] ?? '',
            ':product_name' => $item['product_name'] ?? '',
            ':small_image_url' => $item['small_image_url'] ?? '',
            ':seller_sku' => $item['seller_sku'] ?? '',
            ':fnsku' => $item['fnsku'] ?? '',
            ':sku' => $item['sku'] ?? '',
            ':category_text' => $item['category_text'] ?? '',
            ':cid' => intval($item['cid'] ?? 0),
            ':product_brand_text' => $item['product_brand_text'] ?? '',
            ':bid' => intval($item['bid'] ?? 0),
            ':share_type' => intval($item['share_type'] ?? 0),
            ':total' => intval($item['total'] ?? 0),
            ':total_price' => floatval($item['total_price'] ?? 0),
            ':available_total' => intval($item['available_total'] ?? 0),
            ':available_total_price' => $item['available_total_price'] ?? '',
            ':afn_fulfillable_quantity' => intval($item['afn_fulfillable_quantity'] ?? 0),
            ':afn_fulfillable_quantity_price' => $item['afn_fulfillable_quantity_price'] ?? '',
            ':reserved_fc_transfers' => intval($item['reserved_fc_transfers'] ?? 0),
            ':reserved_fc_transfers_price' => $item['reserved_fc_transfers_price'] ?? '',
            ':reserved_fc_processing' => intval($item['reserved_fc_processing'] ?? 0),
            ':reserved_fc_processing_price' => $item['reserved_fc_processing_price'] ?? '',
            ':reserved_customerorders' => intval($item['reserved_customerorders'] ?? 0),
            ':reserved_customerorders_price' => $item['reserved_customerorders_price'] ?? '',
            ':quantity' => intval($item['quantity'] ?? 0),
            ':quantity_price' => $item['quantity_price'] ?? '',
            ':afn_unsellable_quantity' => intval($item['afn_unsellable_quantity'] ?? 0),
            ':afn_unsellable_quantity_price' => $item['afn_unsellable_quantity_price'] ?? '',
            ':afn_inbound_working_quantity' => intval($item['afn_inbound_working_quantity'] ?? 0),
            ':afn_inbound_working_quantity_price' => $item['afn_inbound_working_quantity_price'] ?? '',
            ':afn_inbound_shipped_quantity' => intval($item['afn_inbound_shipped_quantity'] ?? 0),
            ':afn_inbound_shipped_quantity_price' => $item['afn_inbound_shipped_quantity_price'] ?? '',
            ':afn_inbound_receiving_quantity' => intval($item['afn_inbound_receiving_quantity'] ?? 0),
            ':afn_inbound_receiving_quantity_price' => $item['afn_inbound_receiving_quantity_price'] ?? '',
            ':stock_up_num' => intval($item['stock_up_num'] ?? 0),
            ':stock_up_num_price' => $item['stock_up_num_price'] ?? '',
            ':afn_researching_quantity' => intval($item['afn_researching_quantity'] ?? 0),
            ':afn_researching_quantity_price' => $item['afn_researching_quantity_price'] ?? '',
            ':total_fulfillable_quantity' => intval($item['total_fulfillable_quantity'] ?? 0),
            ':inv_age_0_to_30_days' => intval($item['inv_age_0_to_30_days'] ?? 0),
            ':inv_age_0_to_30_price' => $item['inv_age_0_to_30_price'] ?? '',
            ':inv_age_31_to_60_days' => intval($item['inv_age_31_to_60_days'] ?? 0),
            ':inv_age_31_to_60_price' => $item['inv_age_31_to_60_price'] ?? '',
            ':inv_age_61_to_90_days' => intval($item['inv_age_61_to_90_days'] ?? 0),
            ':inv_age_61_to_90_price' => $item['inv_age_61_to_90_price'] ?? '',
            ':inv_age_0_to_90_days' => intval($item['inv_age_0_to_90_days'] ?? 0),
            ':inv_age_0_to_90_price' => $item['inv_age_0_to_90_price'] ?? '',
            ':inv_age_91_to_180_days' => intval($item['inv_age_91_to_180_days'] ?? 0),
            ':inv_age_91_to_180_price' => $item['inv_age_91_to_180_price'] ?? '',
            ':inv_age_181_to_270_days' => intval($item['inv_age_181_to_270_days'] ?? 0),
            ':inv_age_181_to_270_price' => $item['inv_age_181_to_270_price'] ?? '',
            ':inv_age_271_to_330_days' => intval($item['inv_age_271_to_330_days'] ?? 0),
            ':inv_age_271_to_330_price' => $item['inv_age_271_to_330_price'] ?? '',
            ':inv_age_271_to_365_days' => intval($item['inv_age_271_to_365_days'] ?? 0),
            ':inv_age_271_to_365_price' => $item['inv_age_271_to_365_price'] ?? '',
            ':inv_age_331_to_365_days' => intval($item['inv_age_331_to_365_days'] ?? 0),
            ':inv_age_331_to_365_price' => $item['inv_age_331_to_365_price'] ?? '',
            ':inv_age_365_plus_days' => intval($item['inv_age_365_plus_days'] ?? 0),
            ':inv_age_365_plus_price' => $item['inv_age_365_plus_price'] ?? '',
            ':recommended_action' => $item['recommended_action'] ?? '',
            ':sell_through' => floatval($item['sell_through'] ?? 0),
            ':estimated_excess_quantity' => floatval($item['estimated_excess_quantity'] ?? 0),
            ':estimated_storage_cost_next_month' => floatval($item['estimated_storage_cost_next_month'] ?? 0),
            ':fba_minimum_inventory_level' => floatval($item['fba_minimum_inventory_level'] ?? 0),
            ':fba_inventory_level_health_status' => $item['fba_inventory_level_health_status'] ?? '',
            ':historical_days_of_supply' => floatval($item['historical_days_of_supply'] ?? 0),
            ':historical_days_of_supply_price' => $item['historical_days_of_supply_price'] ?? '',
            ':low_inventory_level_fee_applied' => $item['low_inventory_level_fee_applied'] ?? '',
            ':fulfillment_channel' => $item['fulfillment_channel'] ?? '',
            ':cg_price' => $item['cg_price'] ?? '',
            ':cg_transport_costs' => $item['cg_transport_costs'] ?? '',
            ':fba_storage_quantity_list' => json_encode($item['fba_storage_quantity_list'] ?? [], JSON_UNESCAPED_UNICODE)
        ];

        // 执行SQL写入/更新
        $stmt->execute($data);
        $syncCount++;
        $response['logs'][] = "FBA库存【{$data[':sku']} - 仓库:{$data[':name']}】同步成功";
    }

    // 组装响应数据
    $response['data']['synced_count'] = $syncCount;
    $response['data']['request_offset'] = $offset;
    $pdo = null; // 关闭数据库连接

} catch (Exception $e) {
    // 统一异常处理
    $response['code'] = 500;
    $response['msg'] = '操作失败：' . $e->getMessage();
    $response['logs'] = [];
    // 记录错误日志
    error_log("[FBA库存同步错误] " . date('Y-m-d H:i:s') . "：" . $e->getMessage() . " 行号：" . $e->getLine());
}

// 输出最终JSON响应
echo json_encode($response, JSON_UNESCAPED_UNICODE);

// 请求参考链接：https://cz.younger-car.com/xlingxing/php/get_inventoryDetails_FBA2db.php?offset=0
// 测试：https://cz.younger-car.com/xlingxing/php/get_inventoryDetails_FBA2db.php?offset=100
?>