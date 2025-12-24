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
        'length' => 10,
        'order_status' => 6,
        'date_type' => 'global_purchase_time',
        'start_time' => $nDaysAgoTimestamp,
        'end_time' => $oneDaysAgoTimestamp 
    ];
    $orders = $apiClient->post('/pb/mp/order/v2/list', $orderParams);
    //print_r("已发货订单数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
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

    // 核心SQL：INSERT + ON DUPLICATE KEY UPDATE（依赖global_order_no的唯一索引）
    $sql = "INSERT INTO yunfei (global_order_no, shisuanyunfei) 
            VALUES (:global_order_no, :shisuanyunfei) 
            ON DUPLICATE KEY UPDATE 
                shisuanyunfei = VALUES(shisuanyunfei)"; // VALUES() 引用插入时的参数值

    // 预处理语句
    $stmt = $pdo->prepare($sql);
    
    // 绑定参数（支持字符串/数字等类型，PDO自动处理）
    $stmt->bindParam(':global_order_no', $global_order_no);
    $stmt->bindParam(':shisuanyunfei', $shisuanyunfei);
    
    // 执行语句
    $stmt->execute();

    // 可选：获取受影响的行数（插入=1，更新=2）
    // $affectedRows = $stmt->rowCount();

} catch (PDOException $e) {
    // 错误处理（可选：记录日志/输出调试信息）
    // error_log("运费数据操作失败：" . $e->getMessage());
    // die("数据库错误：" . $e->getMessage()); // 调试时启用，生产环境注释
} finally {
    // 可选：关闭连接（PHP会自动回收，高并发场景建议手动关闭）
    // $pdo = null;
}




?>