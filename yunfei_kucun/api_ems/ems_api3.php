<?php
// ems_api_precise.php
// 支持多渠道精确运费查询接口
// 示例：
// 单渠道： /ems_api_precise.php?postcode=90210&weight=1.5&warehouse=USEA&channel=FEDEX-LP
// 多渠道： /ems_api_precise.php?postcode=90210&weight=1.5&warehouse=USEA&channel=FEDEX-LP,USPS-FCM,YANWEN-SF

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

require_once 'key.php'; // 必须包含 EMS_TOKEN 和 EMS_KEY 两个常量

// ========== 参数接收 ==========
$postcode  = trim($_GET['postcode']  ?? '');
$weight    = max(0.001, floatval($_GET['weight'] ?? 0));     // 最低 0.001kg
$warehouse = strtoupper(trim($_GET['warehouse'] ?? ''));
$channel   = trim($_GET['channel'] ?? '');

// 可选尺寸（单位 cm）
$length = max(1, round(floatval($_GET['length'] ?? 0), 1));
$width  = max(1, round(floatval($_GET['width']  ?? 0), 1));
$height = max(1, round(floatval($_GET['height'] ?? 0), 1));

// ========== 参数校验 ==========
if ($postcode === '' || $weight <= 0 || !$warehouse || !$channel) {
    echo json_encode([
        'success' => false,
        'message' => 'postcode、weight、warehouse、channel 均为必传参数'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// 将 channel 参数拆分为数组，支持逗号分隔
$channelList = array_filter(array_map('trim', explode(',', $channel)));
if (empty($channelList)) {
    echo json_encode([
        'success' => false,
        'message' => 'channel 参数格式错误'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// ========== SOAP 调用封装 ==========
function callEmsSoap(string $service, string $paramsJson): string
{
    $url = "http://cpws.ems.com.cn/default/svc/web-service";
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
        CURLOPT_TIMEOUT        => 15,        // 多渠道时适当增加超时
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
    return $m[1] ?? json_encode(['ask' => 'Fail', 'message' => 'No response body']);
}

// ========== 统一请求参数基础部分 ==========
$baseParams = [
    "warehouse_code"  => $warehouse,
    "country_code"    => "US",
    "postcode"        => (string)$postcode,
    "type"            => 1,
    "weight"          => round($weight, 3),
    "length"          => $length,
    "width"           => $width,
    "height"          => $height,
    "pieces"          => 1
];

// ========== 批量查询每个渠道 ==========
$results = [];
$hasAnySuccess = false;

foreach ($channelList as $ch) {
    $params = $baseParams;
    $params["shipping_method"] = strtoupper($ch);  // EMS 接口通常要求大写

    $paramsJson = json_encode($params, JSON_UNESCAPED_UNICODE);
    $rawResponse = callEmsSoap('getCalculateFee', $paramsJson);
    $data = json_decode($rawResponse, true);

    // 统一结构返回
    $results[$ch] = $data ?? ['ask' => 'Fail', 'message' => 'JSON parse error or empty response'];

    if (isset($data['ask']) && strtoupper($data['ask']) === 'SUCCESS') {
        $hasAnySuccess = true;
    }
}

// ========== 最终输出 ==========
$response = [
    'success' => $hasAnySuccess,
    'request' => [
        'postcode'  => $postcode,
        'weight'    => $weight,
        'warehouse' => $warehouse,
        'channels'  => $channelList
    ],
    'data' => $results
];

echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);