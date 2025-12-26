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

    // ========== 领星API调用逻辑（与get_products.php保持一致） ==========
    $offset = $_GET['offset'] ?? 0;
    $length = $_GET['length'] ?? 100;
    
    $apiClient = new LingXingApiClient();
    
    // 保持与get_products.php相同的参数（仅offset和length）
    $productParams = [
        'offset' => $offset,
        'length' => $length       
    ];

    // 调用与get_products.php相同的接口
    $apiResult = $apiClient->post('/erp/sc/routing/data/local_inventory/productList', $productParams);
    
    // 关键修改：根据get_products.php的返回结构调整数据提取路径
    // （假设API返回的产品列表直接在根节点的list中，需根据实际返回结构确认）
    $products = $apiResult['list'] ?? []; 
    
    $response['data']['api_products_count'] = count($products);
    $response['logs'][] = "API返回原始数据结构：" . json_encode($apiResult, JSON_UNESCAPED_UNICODE); // 临时日志，用于调试

    if (empty($products)) {
        $response['msg'] = '未获取到产品数据';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }

    // ========== 数据库操作逻辑 ==========
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

    $sql = "INSERT INTO products (
        id, cid, bid, sku, sku_identifier, product_name, pic_url,
        cg_delivery, cg_transport_costs, purchase_remark, cg_price,
        status, open_status, is_combo, create_time, update_time,
        product_developer_uid, cg_opt_uid, cg_opt_username, spu, ps_id,
        attribute, brand_name, category_name, status_text, product_developer,
        supplier_quote, aux_relation_list, custom_fields, global_tags
    ) VALUES (
        :id, :cid, :bid, :sku, :sku_identifier, :product_name, :pic_url,
        :cg_delivery, :cg_transport_costs, :purchase_remark, :cg_price,
        :status, :open_status, :is_combo, :create_time, :update_time,
        :product_developer_uid, :cg_opt_uid, :cg_opt_username, :spu, :ps_id,
        :attribute, :brand_name, :category_name, :status_text, :product_developer,
        :supplier_quote, :aux_relation_list, :custom_fields, :global_tags
    ) ON DUPLICATE KEY UPDATE
        cid = VALUES(cid), bid = VALUES(bid), sku_identifier = VALUES(sku_identifier),
        product_name = VALUES(product_name), pic_url = VALUES(pic_url),
        cg_delivery = VALUES(cg_delivery), cg_transport_costs = VALUES(cg_transport_costs),
        purchase_remark = VALUES(purchase_remark), cg_price = VALUES(cg_price),
        status = VALUES(status), open_status = VALUES(open_status), is_combo = VALUES(is_combo),
        create_time = VALUES(create_time), update_time = VALUES(update_time),
        product_developer_uid = VALUES(product_developer_uid), cg_opt_uid = VALUES(cg_opt_uid),
        cg_opt_username = VALUES(cg_opt_username), spu = VALUES(spu), ps_id = VALUES(ps_id),
        attribute = VALUES(attribute), brand_name = VALUES(brand_name),
        category_name = VALUES(category_name), status_text = VALUES(status_text),
        product_developer = VALUES(product_developer), supplier_quote = VALUES(supplier_quote),
        aux_relation_list = VALUES(aux_relation_list), custom_fields = VALUES(custom_fields),
        global_tags = VALUES(global_tags)";

    $stmt = $pdo->prepare($sql);
    $syncCount = 0;

    foreach ($products as $product) {
        // 时间字段处理（根据API返回的时间格式调整，若为时间戳则用date转换）
        $createTime = isset($product['create_time']) ? 
            (is_numeric($product['create_time']) ? date('Y-m-d H:i:s', $product['create_time']) : $product['create_time']) : null;
        $updateTime = isset($product['update_time']) ? 
            (is_numeric($product['update_time']) ? date('Y-m-d H:i:s', $product['update_time']) : $product['update_time']) : date('Y-m-d H:i:s');

        // JSON字段处理
        $attribute = isset($product['attribute']) ? json_encode($product['attribute'], JSON_UNESCAPED_UNICODE) : null;
        $supplierQuote = isset($product['supplier_quote']) ? json_encode($product['supplier_quote'], JSON_UNESCAPED_UNICODE) : null;
        $auxRelationList = isset($product['aux_relation_list']) ? json_encode($product['aux_relation_list'], JSON_UNESCAPED_UNICODE) : null;
        $customFields = isset($product['custom_fields']) ? json_encode($product['custom_fields'], JSON_UNESCAPED_UNICODE) : null;
        $globalTags = isset($product['global_tags']) ? json_encode($product['global_tags'], JSON_UNESCAPED_UNICODE) : null;

        $data = [
            ':id' => $product['id'] ?? null,
            ':cid' => $product['cid'] ?? null,
            ':bid' => $product['bid'] ?? null,
            ':sku' => $product['sku'] ?? '', // sku为非空唯一键，必须赋值
            ':sku_identifier' => $product['sku_identifier'] ?? null,
            ':product_name' => $product['product_name'] ?? null,
            ':pic_url' => $product['pic_url'] ?? null,
            ':cg_delivery' => $product['cg_delivery'] ?? null,
            ':cg_transport_costs' => $product['cg_transport_costs'] ?? null,
            ':purchase_remark' => $product['purchase_remark'] ?? null,
            ':cg_price' => $product['cg_price'] ?? null,
            ':status' => $product['status'] ?? null,
            ':open_status' => $product['open_status'] ?? null,
            ':is_combo' => $product['is_combo'] ?? null,
            ':create_time' => $createTime,
            ':update_time' => $updateTime,
            ':product_developer_uid' => $product['product_developer_uid'] ?? null,
            ':cg_opt_uid' => $product['cg_opt_uid'] ?? null,
            ':cg_opt_username' => $product['cg_opt_username'] ?? null,
            ':spu' => $product['spu'] ?? null,
            ':ps_id' => $product['ps_id'] ?? null,
            ':attribute' => $attribute,
            ':brand_name' => $product['brand_name'] ?? null,
            ':category_name' => $product['category_name'] ?? null,
            ':status_text' => $product['status_text'] ?? null,
            ':product_developer' => $product['product_developer'] ?? null,
            ':supplier_quote' => $supplierQuote,
            ':aux_relation_list' => $auxRelationList,
            ':custom_fields' => $customFields,
            ':global_tags' => $globalTags
        ];

        $stmt->execute($data);
        $syncCount++;
        $response['logs'][] = "产品【{$data[':sku']}】同步成功";
    }

    $response['data']['synced_count'] = $syncCount;
    $pdo = null;

} catch (Exception $e) {
    $response['code'] = 500;
    $response['msg'] = '操作失败：' . $e->getMessage();
    $response['logs'] = [];
    error_log("[产品同步错误] " . date('Y-m-d H:i:s') . "：" . $e->getMessage() . " 行号：" . $e->getLine());
}

echo json_encode($response, JSON_UNESCAPED_UNICODE);
?>