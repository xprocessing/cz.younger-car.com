<?php
// ========== 响应头配置（必须在所有输出前） ==========
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

    // ========== 引入配置文件（提前引入，避免后续依赖） ==========
    $configPath = __DIR__ . '/../../admin-panel/config/config.php';
    if (!file_exists($configPath)) {
        throw new Exception("配置文件不存在：{$configPath}");
    }
    require_once $configPath;

   
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
    //$sql = "SELECT * FROM order_profit WHERE 1=1 LIMIT 10";

    $sql ="
UPDATE order_profit 
SET 
  order_total_amount = CASE
    WHEN order_total_amount REGEXP '^-?￡' THEN CONCAT('$', ROUND(TRIM(REPLACE(order_total_amount, '￡', '')) * 1.35, 2))
    WHEN order_total_amount REGEXP '^-?JP¥' THEN CONCAT('$', ROUND(TRIM(REPLACE(order_total_amount, 'JP¥', '')), 2) * 0.0064)
    WHEN order_total_amount REGEXP '^-?CA\\$' THEN CONCAT('$', ROUND(TRIM(REPLACE(order_total_amount, 'CA$', '')) * 0.73, 2))
    WHEN order_total_amount REGEXP '^-?¥' THEN CONCAT('$', ROUND(TRIM(REPLACE(order_total_amount, '¥', '')) * 0.14, 2))
    ELSE order_total_amount
  END,
  wms_outbound_cost_amount = CASE
    WHEN wms_outbound_cost_amount REGEXP '^-?￡' THEN CONCAT('$', ROUND(TRIM(REPLACE(wms_outbound_cost_amount, '￡', '')), 2) * 1.35)
    WHEN wms_outbound_cost_amount REGEXP '^-?JP¥' THEN CONCAT('$', ROUND(TRIM(REPLACE(wms_outbound_cost_amount, 'JP¥', '')), 2) * 0.0064)
    WHEN wms_outbound_cost_amount REGEXP '^-?CA\\$' THEN CONCAT('$', ROUND(TRIM(REPLACE(wms_outbound_cost_amount, 'CA$', '')), 2) * 0.73)
    WHEN wms_outbound_cost_amount REGEXP '^-?¥' THEN CONCAT('$', ROUND(TRIM(REPLACE(wms_outbound_cost_amount, '¥', '')), 2) * 0.14)
    ELSE wms_outbound_cost_amount
  END,
  wms_shipping_price_amount = CASE
    WHEN wms_shipping_price_amount REGEXP '^-?￡' THEN CONCAT('$', ROUND(TRIM(REPLACE(wms_shipping_price_amount, '￡', '')), 2) * 1.35)
    WHEN wms_shipping_price_amount REGEXP '^-?JP¥' THEN CONCAT('$', ROUND(TRIM(REPLACE(wms_shipping_price_amount, 'JP¥', '')), 2) * 0.0064)
    WHEN wms_shipping_price_amount REGEXP '^-?CA\\$' THEN CONCAT('$', ROUND(TRIM(REPLACE(wms_shipping_price_amount, 'CA$', '')), 2) * 0.73)
    WHEN wms_shipping_price_amount REGEXP '^-?¥' THEN CONCAT('$', ROUND(TRIM(REPLACE(wms_shipping_price_amount, '¥', '')), 2) * 0.14)
    ELSE wms_shipping_price_amount
  END,
  profit_amount = CASE
    WHEN profit_amount REGEXP '^-?￡' THEN CONCAT('$', ROUND(TRIM(REPLACE(profit_amount, '￡', '')), 2) * 1.35)
    WHEN profit_amount REGEXP '^-?JP¥' THEN CONCAT('$', ROUND(TRIM(REPLACE(profit_amount, 'JP¥', '')), 2) * 0.0064)
    WHEN profit_amount REGEXP '^-?CA\\$' THEN CONCAT('$', ROUND(TRIM(REPLACE(profit_amount, 'CA$', '')), 2) * 0.73)
    WHEN profit_amount REGEXP '^-?¥' THEN CONCAT('$', ROUND(TRIM(REPLACE(profit_amount, '¥', '')), 2) * 0.14)
    ELSE profit_amount
  END;
";


    $stmt = $pdo->prepare($sql);
    $stmt->execute();
  //返回影响的行数数据
    $affectedRows = $stmt->rowCount();
    echo $affectedRows;
    exit;

  

   
?>