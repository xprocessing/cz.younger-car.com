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
    $provider_type = $_GET['provider_type'] ?? 0;
    //物流商类型： 0 API物流 1 自定义物流 2 海外仓物流 4 平台物流

   
    // 调用POST接口示例
    $logisticsParams = [
        'provider_type' => $provider_type,
        'page' => 1,
        'length' => 300       
    ];
    $orders = $apiClient->post('/erp/sc/routing/wms/WmsLogistics/listUsedLogisticsType', $logisticsParams);
    //print_r("物流数据：" . PHP_EOL);
    //json格式化输出
    echo json_encode($orders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (\Exception $e) {
    echo "错误：" . $e->getMessage() . PHP_EOL;
}

//测试地址 cz.younger-car.com/xlingxing/php/get_listUsedLogisticsType.php?provider_type=2

?>