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
    $sql = "SELECT * FROM order_profit WHERE 1=1 LIMIT 10";


    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    $results = $stmt->fetchAll();
    echo json_encode([
        'data' => $results,
        'msg' => '执行成功'
    ], JSON_UNESCAPED_UNICODE);

   
?>