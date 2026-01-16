<?php
// 响应头配置
header("Access-Control-Allow-Origin: *");
header("Content-Type: text/html; charset=utf-8");

// 检查是否提供了订单号参数
if (empty($_GET['global_order_no'])) {
    die("<h3>请提供订单号参数：?global_order_no=订单号</h3>");
}

$globalOrderNo = trim($_GET['global_order_no']);

// 数据库配置引入
require_once '../config.php';

// 初始化变量
$emsData = [];
$wedoData = [];
$error = '';

try {
    // 连接数据库
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME,
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // 查询数据
    $stmt = $pdo->prepare("SELECT shisuanyunfei FROM yunfei WHERE global_order_no = :order_no LIMIT 1");
    $stmt->bindParam(':order_no', $globalOrderNo);
    $stmt->execute();
    $result = $stmt->fetch();

    if (!$result) {
        $error = "未找到订单号为 {$globalOrderNo} 的运费记录,请匹配sku后5分钟再看。系统每隔5分钟同步一次运费数据。如果持续没有，应该是sku需要更新规格数据";
    } else {
        // 解析JSON数据
        $yunfeiData = json_decode($result['shisuanyunfei'], true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            $error = "数据解析错误：" . json_last_error_msg();
        } else {
            // 处理EMS数据（筛选出有运费的记录）
            if (!empty($yunfeiData['ems']['results'])) {
                foreach ($yunfeiData['ems']['results'] as $item) {
                    if (isset($item['total_fee_cny']) && $item['total_fee_cny'] !== null && $item['total_fee_cny'] > 0) {
                        $emsData[] = $item;
                    }
                }
                // EMS运费从低到高排序
                usort($emsData, function($a, $b) {
                    return $a['total_fee_cny'] - $b['total_fee_cny'];
                });
            }

            // 处理运德物流数据（筛选出有运费的记录）
            if (!empty($yunfeiData['wedo']['results'])) {
                foreach ($yunfeiData['wedo']['results'] as $item) {
                    if (isset($item['ship_fee_original']) && $item['ship_fee_original'] !== null && $item['ship_fee_original'] > 0) {
                        $wedoData[] = $item;
                    }
                }
                // 运德物流运费（人民币）从低到高排序
                usort($wedoData, function($a, $b) {
                    return $a['ship_fee_cny'] - $b['ship_fee_cny'];
                });
            }
        }
    }
} catch (PDOException $e) {
    $error = "数据库错误：" . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>运费查询结果 - <?php echo htmlspecialchars($globalOrderNo); ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 1200px; margin: 0 auto; }
        h1 { color: #333; }
        .error { color: #dc3545; padding: 10px; background: #f8d7da; border-radius: 4px; }
        .section { margin: 30px 0; }
        h2 { color: #666; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 6px 6px; text-align: left; border: 1px solid #ddd; }
        th { background-color: #f8f9fa; font-weight: bold; }
        tr:hover { background-color: #f8f9fa; }
        .no-data { color: #6c757d; padding: 20px; text-align: center; }
        .sort-tip { font-size: 14px; color: #6c757d; margin-bottom: 8px; }
    </style>
</head>
<body>
    <div class="container">
        <h1>订单运费试算查询：<?php echo htmlspecialchars($globalOrderNo); ?> &nbsp;&nbsp;&nbsp;<a href="czyunfei.html" target="_blank" >🚀手工运费试算</a></h1>
        
        <?php if ($error): ?>
            <div class="error"><?php echo $error; ?></div>
        <?php else: ?>
            <!-- 中邮EMS结果 -->
            <div class="section">
                <h2>中邮EMS 运费试算信息</h2>
                <div class="sort-tip">排序规则：运费金额从低到高</div>
                <?php if (empty($emsData)): ?>
                    <div class="no-data">无有效运费数据</div>
                <?php else: ?>
                    <table>
                        <tr>
                            <th>仓库代码</th>
                            <th>渠道代码</th>
                            <th>渠道名称</th>
                            <th>运费 (CNY)</th>
                           
                        </tr>
                        <?php foreach ($emsData as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['warehouse_code']); ?></td>
                                <td><?php echo htmlspecialchars($item['channel_code']); ?></td>
                                <td><?php echo htmlspecialchars($item['channel_name']); ?></td>
                                <td><?php echo number_format($item['total_fee_cny'], 2); ?></td>
                               
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>

            <!-- 运德物流结果 -->
            <div class="section">
                <h2>运德物流 运费试算信息</h2>                
                <?php if (empty($wedoData)): ?>
                    <div class="no-data">无有效运费数据</div>
                <?php else: ?>
                    <table>
                        <tr>
                            <th>渠道代码</th>
                            <th>渠道名称</th>
                            <th>原始运费 (<?php echo htmlspecialchars($wedoData[0]['currency'] ?? 'USD'); ?>)</th>
                            <th>运费 (CNY)</th>
                        </tr>
                        <?php foreach ($wedoData as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['channel_code']); ?></td>
                                <td><?php echo htmlspecialchars($item['channel_name']); ?></td>
                                <td><?php echo number_format($item['ship_fee_original'], 2); ?></td>
                                <td><?php echo number_format($item['ship_fee_cny'], 2); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </table>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>