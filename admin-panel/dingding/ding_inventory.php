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

// 2. 从inventory_details表获取库龄超过180天sku，且库存可用量大于10。增加仓库名称字段，通过wid对应warehouses表中name，按可用库存从高到低排序
$sql2 = "SELECT i.sku, i.average_age, i.product_valid_num, w.name as warehouse_name
         FROM inventory_details i
         JOIN warehouses w ON i.wid = w.wid
         WHERE i.average_age > 180 AND i.product_valid_num > 10
         ORDER BY i.product_valid_num DESC";
$stmt2 = $db->prepare($sql2);
$stmt2->execute();
$oldStockSkus = $stmt2->fetchAll();

if (!empty($oldStockSkus)) {
    $content .= "### 库龄超过180天的SKU\n";
    $content .= "| SKU | 平均库龄(天) | 可用库存 | 仓库名称 |\n";
    $content .= "| --- | --- | --- | --- |\n";
    foreach ($oldStockSkus as $sku) {
        $content .= sprintf("| %s | %d | %d | %s |\n", 
            $sku['sku'], 
            $sku['average_age'], 
            $sku['product_valid_num'],
            $sku['warehouse_name']
        );
    }
    $content .= "\n";
    $content.="更多库存信息，请访问：https://cz.younger-car.com/admin-panel/inventory_details.php?action=inventory_alert";
}

// 3. 从order_profit和inventory_details表，根据sku的近30天销量，判断库存是否不足

echo $content;
// 如果没有数据，添加提示信息
if (empty($content)) {
    $content = "暂无异常数据需要推送\n";
    //结束程序
    exit;
}

$mobileList = ["18868725001","18868268995","13868380570","15645962735"];
//$mobileList = ["18868725001"];
//$content = "缺货预警：有缺货sku...[点击查看](https://cz.younger-car.com/admin-panel/inventory_details.php?action=inventory_alert)";

// 3. 调用推送方法，获取标准化执行结果
$executeResult = $dingTalkPusher->push($mobileList, $content);

// 4. 打印执行结果（可根据业务需求进一步处理，如记录日志、入库等）
echo PHP_EOL . "==================== 最终执行结果 ====================" . PHP_EOL;
var_dump($executeResult);
?>