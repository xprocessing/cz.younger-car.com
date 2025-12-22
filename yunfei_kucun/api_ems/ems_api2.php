<?php
// ems_api_precise.php
// 指定仓库 + 指定渠道 精确运费查询接口
// 示例：/ems_api_precise.php?postcode=90210&weight=1.5&warehouse=USEA&channel=FEDEX-LP

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8");

require_once 'key.php'; // 必须包含 EMS_TOKEN 和 EMS_KEY 两个常量

// ========== 参数接收 ==========
$postcode  = trim($_GET['postcode']  ?? '');
$weight    = max(0.001, floatval($_GET['weight'] ?? 0));     // 最低 0.001kg
$warehouse = strtoupper(trim($_GET['warehouse'] ?? ''));     // USEA / USEE 等
$channel   = trim($_GET['channel'] ?? '');                   // 单个渠道代码，如 FEDEX-LP

// 可选尺寸（单位 cm），不传时后端会按默认规则处理
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
        CURLOPT_TIMEOUT        => 12,
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
    return $m[1] ?? '';
}

// ========== 构造请求参数 ==========
$params = json_encode([
    "warehouse_code"  => $warehouse,
    "country_code"    => "US",
    "postcode"        => (string)$postcode,
    "shipping_method" => $channel,
    "type"            => 1,
    "weight"          => round($weight, 3),
    "length"          => $length,
    "width"           => $width,
    "height"          => $height,
    "pieces"          => 1
], JSON_UNESCAPED_UNICODE);

$rawResponse = callEmsSoap('getCalculateFee', $params);
$data        = json_decode($rawResponse, true);

// ========== 结果组装 ==========
$result = $data;

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);