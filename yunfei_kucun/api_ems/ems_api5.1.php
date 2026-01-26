<?php
// ems_api_precise_multi_warehouse.php
// 支持多仓库 + 多渠道精确运费查询接口
// 示例：
// 单仓库单渠道： /ems_api_precise_multi_warehouse.php?postcode=90210&weight=1.5&warehouse=USEA&channel=FEDEX-LP
// 多仓库多渠道： /ems_api_precise_multi_warehouse.php?postcode=90210&weight=1.5&warehouse=USEA,USWE&channel=USPS-PRIORITY,AMAZON-GROUND

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");


require_once 'key.php'; // 必须包含 EMS_TOKEN 和 EMS_KEY 两个常量

// ========== 参数接收 ==========
$postcode  = trim($_GET['postcode']  ?? '');
$weight    = max(0.001, floatval($_GET['weight'] ?? 0));     // 最低 0.001kg
$warehouse = strtoupper(trim($_GET['warehouse'] ?? ''));
$channel   = trim($_GET['channel'] ?? '');

// 可选尺寸（单位 cm），默认最小 1cm
$length = max(1, round(floatval($_GET['length'] ?? 0), 1));
$width  = max(1, round(floatval($_GET['width']  ?? 0), 1));
$height = max(1, round(floatval($_GET['height'] ?? 0), 1));

// ========== 参数校验 ==========
if ($postcode === '' || $weight <= 0 || $warehouse === '' || $channel === '') {
    echo json_encode([
        'success' => false,
        'message' => 'postcode、weight、warehouse、channel 均为必传参数'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 解析 warehouse 和 channel，支持逗号分隔
$warehouseList = array_filter(array_unique(array_map('trim', explode(',', $warehouse))));
$channelList   = array_filter(array_unique(array_map('trim', explode(',', $channel))));

if (empty($warehouseList) || empty($channelList)) {
    echo json_encode([
        'success' => false,
        'message' => 'warehouse 或 channel 参数格式错误'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ========== SOAP 调用封装 ==========
function callEmsSoap(string $service, string $paramsJson): string
{
    $url   = "http://cpws.ems.com.cn/default/svc/web-service";
    $token = EMS_TOKEN;
    $key   = EMS_KEY;

    $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.example.org/Ec/">
  <SOAP-ENV:Body>
    <ns1:callService>
      <paramsJson>{$paramsJson}</paramsJson>
      <appToken>{$token}</appToken>
      <appKey>{$key}</appKey>
      <service>{$service}</service>
    </ns1:callService>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_TIMEOUT        => 20,                    // 多组合时适当放宽
        CURLOPT_POSTFIELDS     => $xml,
        CURLOPT_HTTPHEADER     => ['Content-Type: text/xml; charset=utf-8'],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return json_encode(['ask' => 'Fail', 'message' => 'cURL Error: ' . $err]);
    }

    preg_match('#<response>(.*?)</response>#s', $resp, $m);
    return $m[1] ?? json_encode(['ask' => 'Fail', 'message' => 'No <response> tag']);
}

// ========== 统一请求参数基础部分 ==========
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

// ========== 批量查询：每个 warehouse × 每个 channel ==========
$finalResult   = [];
$anySuccess    = false;
$totalRequests = count($warehouseList) * count($channelList);

foreach ($warehouseList as $wh) {
    foreach ($channelList as $ch) {
        $params                 = $baseParams;
        $params["warehouse_code"]   = $wh;
        $params["shipping_method"]  = strtoupper($ch);

        $paramsJson = json_encode($params, JSON_UNESCAPED_UNICODE);
        $raw        = callEmsSoap('getCalculateFee', $paramsJson);
        $data       = json_decode($raw, true);

        // 统一返回结构
        $finalResult[$wh][$ch] = $data ?? [
            'ask'     => 'Fail',
            'message' => 'JSON parse error or empty response'
        ];

        if (isset($data['ask']) && strtoupper($data['ask']) === 'SUCCESS') {
            $anySuccess = true;
        }
    }
}

// ========== 最终输出 ==========
$response = [
    'success' => $anySuccess,
    'request' => [
        'postcode'   => $postcode,
        'weight'     => $weight,
        'warehouses' => $warehouseList,
        'channels'   => $channelList,
        'total_combinations' => $totalRequests
    ],
    'data' => $finalResult
];

//echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

//将response 转为json 格式
//$result = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$obj = $response;   // 转成关联数组
$data = $obj['data'];               // 取出我们关心的部分

// 可选：加点基础样式，让表格更好看
echo '<style>
    table { border-collapse: collapse; width: 100%; font-family: Arial, sans-serif; }
    th, td { border: 1px solid #999; padding: 8px 12px; text-align: left; }
    th { background-color: #f2f2f2; }
    .fail { color: #d8000c; }
</style>';

echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>仓库 (Warehouse)</th>';
echo '<th>渠道 (Channel)</th>';
echo '<th>费用CNY (SHIPPING)</th>';
echo '<th>费用CNY (totalFee)</th>';
echo '<th>结果 (Result)</th>';
echo '<th>错误码 (Error Code)</th>';
echo '<th>错误信息 (Error Message)</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($data as $warehouse => $channels) {
    foreach ($channels as $channel => $info) {
        $ask      = $info['ask'] ?? '-';
        $errCode  = $info['Error']['errCode'] ?? '-';
        $errMsg   = $info['Error']['errMessage'] ?? '-';
        $totalFee = $info['data']['totalFee'];
        $SHIPPING = $info['data']['SHIPPING'];

        // Failure 标红
        $askClass = ($ask === 'Failure') ? ' class="fail"' : '';

        echo '<tr>';
        echo "<td><strong>$warehouse</strong></td>";
        echo "<td>$channel</td>";
         echo "<td>$SHIPPING</td>";
        echo "<td>$totalFee</td>";       
        echo "<td$askClass>$ask</td>";
        echo "<td>$errCode</td>";
        echo "<td>$errMsg</td>";
        echo '</tr>';
    }
}

echo '</tbody>';
echo '</table>';

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

//增加一个运德运费查询


//测试多仓库链接
//http://cz.younger-car.com/yunfei_kucun/api_ems/ems_api5.1.php?postcode=90210&weight=1.5&warehouse=USEA&channel=FEDEX-LP