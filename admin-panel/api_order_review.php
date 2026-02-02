<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/includes/database.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => '只支持POST请求']);
    exit;
}

$jsonInput = file_get_contents('php://input');
$data = json_decode($jsonInput, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'message' => 'JSON格式错误: ' . json_last_error_msg()]);
    exit;
}

$store_id = $data['store_id'] ?? '';
$global_order_no = $data['global_order_no'] ?? '';
$local_sku = $data['local_sku'] ?? '';
$receiver_country_code = $data['receiver_country_code'] ?? '';
$city = $data['city'] ?? '';
$postal_code = $data['postal_code'] ?? '';
$wid = $data['wid'] ?? null;
$logistics_type_id = $data['logistics_type_id'] ?? null;
$estimated_yunfei = $data['estimated_yunfei'] ?? null;
$wd_yunfei = isset($data['wd_yunfei']) ? json_encode($data['wd_yunfei']) : null;
$ems_yunfei = isset($data['ems_yunfei']) ? json_encode($data['ems_yunfei']) : null;
$review_remark = $data['review_remark'] ?? null;

if (empty($global_order_no)) {
    echo json_encode(['success' => false, 'message' => '订单号不能为空']);
    exit;
}

if (empty($local_sku)) {
    echo json_encode(['success' => false, 'message' => '本地SKU不能为空']);
    exit;
}

if (empty($receiver_country_code)) {
    echo json_encode(['success' => false, 'message' => '国家不能为空']);
    exit;
}

try {
    $db = Database::getInstance();
    
    $sql = "SELECT id FROM order_review WHERE global_order_no = ?";
    $stmt = $db->query($sql, [$global_order_no]);
    $existing = $stmt->fetch();
    
    if ($existing) {
        echo json_encode(['success' => false, 'message' => '该订单号的审核记录已存在']);
        exit;
    }
    
    $sql = "INSERT INTO order_review (store_id, global_order_no, local_sku, receiver_country_code, city, postal_code, wd_yunfei, ems_yunfei, wid, logistics_type_id, estimated_yunfei, review_remark) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $params = [
        $store_id,
        $global_order_no,
        $local_sku,
        $receiver_country_code,
        $city,
        $postal_code,
        $wd_yunfei,
        $ems_yunfei,
        $wid,
        $logistics_type_id,
        $estimated_yunfei,
        $review_remark
    ];
    
    $stmt = $db->query($sql, $params);
    $insertId = $db->lastInsertId();
    
    if ($insertId) {
        echo json_encode([
            'success' => true,
            'message' => '订单审核记录创建成功',
            'data' => [
                'id' => $insertId,
                'global_order_no' => $global_order_no,
                'local_sku' => $local_sku,
                'store_id' => $store_id
            ]
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => '订单审核记录创建失败']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => '服务器错误: ' . $e->getMessage()]);
}
