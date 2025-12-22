<?php
// 根据订单的邮编、重量、尺寸、城市，查询中邮 EMS 和 运德物流运费
// 支持 GET 参数：city, postcode, weight, length, width, height
// 示例测试 URL:
// https://cz.younger-car.com/chayunfei.php?postcode=92113-3931&weight=7.1928&length=74.0&width=27.0&height=18.0&city=San%20Diego

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: text/html; charset=utf-8");

require_once 'key.php'; // 必须包含：EMS_TOKEN, EMS_KEY, WD_APP_ID, WD_APP_TOKEN

$scriptStartTime = microtime(true);

// ========== 输入参数处理 ==========
$global_order_no = trim($_GET['global_order_no'] ?? '');
echo "<h1>🌐 订单号: " . htmlspecialchars($global_order_no ?: 'N/A') . "</h1>";
$city      = trim($_GET['city'] ?? '');
$postcode  = trim($_GET['postcode'] ?? '');
$weight    = max(0.001, floatval($_GET['weight'] ?? 0));
$length    = max(1, round(floatval($_GET['length'] ?? 0), 1));
$width     = max(1, round(floatval($_GET['width'] ?? 0), 1));
$height    = max(1, round(floatval($_GET['height'] ?? 0), 1));

if (empty($postcode) || empty($city)) {
    die('<h2>错误：缺少必要参数 city 或 postcode</h2>');
}

// ========== 基础参数 ==========
$baseParams = [
    "country_code" => "US",
    "postcode"     => (string)$postcode,
    "type"         => 1,
    "weight"       => round($weight, 3),
    "length"       => $length,
    "width"        => $width,
    "height"       => $height,
    "pieces"       => 1
];

// ========== 中邮仓库与渠道 ==========
$warehouse = "USWE,USEA";
$channel   = "FEDEX-GROUND-EA,SS-FEDEX-G-E,FEDEX-SM,FEDEX-LP,AMAZON-GROUND,USPS-FIRST-CLASS,USPS-PRIORITY,DHL-US-SP,DHL-US-BP,YUN-GROUND,CE-PARCEL,CE-GROUND-EA,UPS-GROUND-EA,UPS-GROUND-MULT,UPS-SUREPOST,UPS-2ND-DAY";

$warehouseList = array_filter(array_unique(array_map('trim', explode(',', $warehouse))));
$channelList   = array_filter(array_unique(array_map('trim', explode(',', $channel))));

// ========== 中邮并发请求函数 ==========
function concurrentEmsRequests(array $requests): array {
    if (empty($requests)) return [];

    $mh = curl_multi_init();
    $handles = [];
    $results = [];

    $url = "http://cpws.ems.com.cn/default/svc/web-service";
    $token = EMS_TOKEN;
    $key = EMS_KEY;

    foreach ($requests as $keyId => $params) {
        $paramsJson = json_encode($params, JSON_UNESCAPED_UNICODE);
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.example.org/Ec/">
    <SOAP-ENV:Body>
        <ns1:callService>
            <paramsJson>{$paramsJson}</paramsJson>
            <appToken>{$token}</appToken>
            <appKey>{$key}</appKey>
            <service>getCalculateFee</service>
        </ns1:callService>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_POSTFIELDS => $xml,
            CURLOPT_HTTPHEADER => ['Content-Type: text/xml; charset=utf-8'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TCP_KEEPALIVE => 1,
        ]);

        curl_multi_add_handle($mh, $ch);
        $handles[$keyId] = $ch;
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        if ($running > 0) {
            curl_multi_select($mh, 0.05);
        }
    } while ($running > 0);

    foreach ($handles as $keyId => $ch) {
        $resp = curl_multi_getcontent($ch);
        $err = curl_error($ch);

        if ($err) {
            $results[$keyId] = json_encode(['ask' => 'Fail', 'message' => 'cURL Error: ' . $err]);
        } else {
            preg_match('#<response>(.*?)</response>#s', $resp, $m);
            $results[$keyId] = $m[1] ?? json_encode(['ask' => 'Fail', 'message' => 'No response tag']);
        }

        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);
    return $results;
}

// ========== 构建中邮请求列表 ==========
$emsRequests = [];
$requestKeys = [];

foreach ($warehouseList as $wh) {
    foreach ($channelList as $ch) {
        $params = $baseParams;
        $params["warehouse_code"] = $wh;
        $params["shipping_method"] = strtoupper($ch);
        $keyId = "$wh||$ch";
        $emsRequests[$keyId] = $params;
        $requestKeys[] = $keyId;
    }
}

// ⏱️ 开始计时：中邮
$emsStartTime = microtime(true);

// 并发执行（分批，每批最多 12 个）
$finalResult = [];
$maxBatch = 12;

for ($i = 0; $i < count($requestKeys); $i += $maxBatch) {
    $batchKeys = array_slice($requestKeys, $i, $maxBatch);
    $batchReqs = array_intersect_key($emsRequests, array_flip($batchKeys));
    $batchRes = concurrentEmsRequests($batchReqs);

    foreach ($batchKeys as $keyId) {
        [$wh, $ch] = explode('||', $keyId, 2);
        $data = json_decode($batchRes[$keyId], true);
        $finalResult[$wh][$ch] = $data ?: ['ask' => 'Fail', 'message' => 'Invalid JSON'];
    }
}

$emsTotalTime = round((microtime(true) - $emsStartTime) * 1000, 2);

// ========== 输出样式 ==========
echo '<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; width: 100%; margin: 15px 0; }
th, td { border: 1px solid #999; padding: 6px 10px; text-align: left; }
th { background-color: #f2f2f2; }
.fail { color: #d8000c; }
.success { color: #008000; }
</style>';

// ========== 中邮结果表格 ==========
echo "<h2>📦 中邮 EMS 物流费用（按 totalFee 升序 | 耗时: {$emsTotalTime} ms）</h2>";

$emsSortableData = [];
foreach ($finalResult as $warehouse => $channels) {
    foreach ($channels as $channel => $info) {
        $totalFee = isset($info['data']['totalFee']) ? floatval($info['data']['totalFee']) : PHP_INT_MAX;
        $emsSortableData[] = [
            'warehouse' => $warehouse,
            'channel' => $channel,
            'totalFee' => $totalFee,
            'ask' => $info['ask'] ?? '-',
            'errCode' => $info['Error']['errCode'] ?? '-',
            'errMsg' => $info['Error']['errMessage'] ?? '-'
        ];
    }
}

usort($emsSortableData, fn($a, $b) => $a['totalFee'] <=> $b['totalFee']);

echo '<table>';
echo '<thead><tr>
<th>仓库</th><th>渠道</th><th>费用 (CNY)</th><th>状态</th><th>错误码</th><th>错误信息</th>
</tr></thead><tbody>';

foreach ($emsSortableData as $item) {
    $fee = $item['totalFee'] === PHP_INT_MAX ? '-' : number_format($item['totalFee'], 2);
    $statusClass = (isset($item['ask']) && strtoupper($item['ask']) === 'SUCCESS') ? ' class="success"' : ' class="fail"';
    echo "<tr>
        <td><strong>{$item['warehouse']}</strong></td>
        <td>{$item['channel']}</td>
        <td>{$fee}</td>
        <td{$statusClass}>{$item['ask']}</td>
        <td>{$item['errCode']}</td>
        <td>{$item['errMsg']}</td>
    </tr>";
}
echo '</tbody></table>';

// ========== 运德并发函数 ==========
function concurrentWedoRequests($channelGroups, $commonParams, $apiUrl, $userAccount, $testToken) {
    $startTime = microtime(true);
    $mh = curl_multi_init();
    $handles = [];
    $results = [];

    foreach ($channelGroups as $groupKey => $channelCodes) {
        $contentParams = $commonParams;
        $contentParams['channelCode'] = implode(',', $channelCodes);
        $contentJson = json_encode($contentParams, JSON_UNESCAPED_UNICODE);
        if (!$contentJson) continue;

        $requestData = [
            'userAccount' => $userAccount,
            'content' => $contentJson
        ];
        ksort($requestData);
        $signStr = implode('', $requestData) . $testToken;
        $requestData['sign'] = strtoupper(md5($signStr));

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 5
        ]);
        curl_multi_add_handle($mh, $ch);
        $handles[$groupKey] = $ch;
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        if ($running > 0) curl_multi_select($mh, 0.05);
    } while ($running > 0);

    foreach ($handles as $groupKey => $ch) {
        $response = curl_multi_getcontent($ch);
        $results[$groupKey] = json_decode($response, true) ?: ['error' => 'Invalid JSON'];
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    curl_multi_close($mh);

    $merged = [];
    foreach ($results as $r) {
        if (!empty($r['data']) && is_array($r['data'])) {
            $merged = array_merge($merged, $r['data']);
        }
    }

    $totalTime = round((microtime(true) - $startTime) * 1000, 2);
    return ['data' => $merged, 'time' => $totalTime];
}

// ========== 运德请求 ==========
$allChannelCodes = "AMGD,FEDHDE,FEDHDEMP,UPSGDE,NJUSPSGA,USPSPME,SPEEDXNJ,FDXSPE,UNIUNINJ,GOFONJ,GFYUNNJ,AMGDCA,UPSGW,CAUSPSGA,USPSPMW,USPSGACASG,FEDHDW,FEDHDWMP,UPSGWFBA,SPEEDXCA,CAGLS,FEDSPW,UNIUNICA,GOFOCA,GFYUNCA,FEDIGCA";
$channelArray = array_filter(array_unique(array_map('trim', explode(',', $allChannelCodes))));
$channelGroups = array_chunk($channelArray, 5);

$wedoCommonParams = [
    'country' => 'US',
    'city' => $city,
    'postcode' => $postcode,
    'weight' => round($weight, 3),
    'length' => $length,
    'width' => $width,
    'height' => $height,
    'signatureService' => 0
];

$wedoResponse = concurrentWedoRequests(
    $channelGroups,
    $wedoCommonParams,
    "http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery",
    WD_APP_ID,
    WD_APP_TOKEN
);

$wedoResults = $wedoResponse['data'];
$wedoTotalTime = $wedoResponse['time'];

// ========== 运德结果表格 ==========
echo "<h2>🚚 运德物流费用（按 shipFee 升序 | 耗时: {$wedoTotalTime} ms）（汇率 1 USD ≈ 7.0 CNY）</h2>";

$wedoSortableData = [];
foreach ($wedoResults as $method => $details) {
    $shipFeeRaw = $details['shipFee'] ?? null;
    if (is_numeric($shipFeeRaw) || (is_string($shipFeeRaw) && is_numeric(trim($shipFeeRaw)))) {
        $shipFee = floatval($shipFeeRaw);
    } else {
        $shipFee = PHP_INT_MAX;
    }
    $wedoSortableData[] = [
        'channel' => $method,
        'shipFee' => $shipFee,
        'currency' => $details['currency'] ?? 'USD'
    ];
}

usort($wedoSortableData, fn($a, $b) => $a['shipFee'] <=> $b['shipFee']);

echo '<table><thead><tr>
<th>#</th><th>渠道</th><th>费用 (CNY)</th><th>原币种</th>
</tr></thead><tbody>';

$idx = 1;
foreach ($wedoSortableData as $item) {
    if ($item['shipFee'] === PHP_INT_MAX) continue;
    $cny = number_format($item['shipFee'] * 7.0, 2);
    echo "<tr><td>{$idx}</td><td>{$item['channel']}</td><td>{$cny}</td><td>{$item['currency']}</td></tr>";
    $idx++;
}

// 显示无效项（无费用）
foreach ($wedoSortableData as $item) {
    if ($item['shipFee'] !== PHP_INT_MAX) continue;
    echo "<tr><td>-</td><td>{$item['channel']}</td><td>-</td><td>{$item['currency']}</td></tr>";
}

if (empty($wedoSortableData)) {
    echo '<tr><td colspan="4">无返回数据</td></tr>';
}
echo '</tbody></table>';

// ========== 总耗时 ==========
$totalTime = round((microtime(true) - $scriptStartTime) * 1000, 2);
echo "<p><strong>⏱️ 总执行时间: {$totalTime} 毫秒</strong></p>";
?>