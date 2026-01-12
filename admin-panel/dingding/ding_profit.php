<?php
// 引入外部配置文件（保持你的原有配置引入逻辑）
require_once 'ding_class.php';
// ===================== 类的使用示例 =====================
// 1. 实例化钉钉消息推送类
$dingTalkPusher = new DingTalkMsgPusher();

// 2. 准备传入参数（可根据业务需求动态修改）
$content="";

// 数据库连接
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

$db = new PDO($dsn, DB_USER, DB_PASS, $options);

// 1. 从order_profit表 获取昨天昨日亏损超过10美元的订单，以及store_id对应store表中的store_name
$yesterday = date('Y-m-d', strtotime('-1 day'));
$sql1 = "SELECT op.global_order_no, op.profit_amount, op.local_sku, op.global_purchase_time, s.store_name
         FROM order_profit op
         LEFT JOIN store s ON op.store_id = s.store_id
         WHERE op.profit_rate < '0' 
         AND DATE(STR_TO_DATE(op.global_purchase_time, '%Y-%m-%d %H:%i:%s')) = :yesterday";
$stmt1 = $db->prepare($sql1);
$stmt1->execute(['yesterday' => $yesterday]);
$lossOrders = $stmt1->fetchAll();

if (!empty($lossOrders)) {
    $content .= "### 昨日亏损的订单\n";
    $content .= "| 订单号 | 亏损金额 | SKU | 下单时间 | 店铺名称 |\n";
    $content .= "| --- | --- | --- | --- | --- |\n";
    foreach ($lossOrders as $order) {
        $content .= sprintf("| %s | %s | %s | %s | %s |\n", 
            $order['global_order_no'], 
            $order['profit_amount'], 
            $order['local_sku'], 
            $order['global_purchase_time'],
            $order['store_name']
        );
    }
    $content .= "\n";
    $content.="[更多订单信息](https://cz.younger-car.com/admin-panel/order_profit.php)";
}

// 3. 从order_profit和inventory_details表，根据sku的近30天销量，判断库存是否不足

echo $content;
// 如果没有数据，添加提示信息
if (empty($content)) {
    $content = "暂无异常数据需要推送\n";
    //结束程序
    exit;
}

$mobileList = ["18868725001","18868268995","13868380570"];
//$mobileList = ["18868725001"];
//$content = "缺货预警：有缺货sku...[点击查看](https://cz.younger-car.com/admin-panel/inventory_details.php?action=inventory_alert)";

// 3. 调用推送方法，获取标准化执行结果
$executeResult = $dingTalkPusher->push($mobileList, $content);

// 4. 打印执行结果（可根据业务需求进一步处理，如记录日志、入库等）
echo PHP_EOL . "==================== 最终执行结果 ====================" . PHP_EOL;
var_dump($executeResult);
?>