<?php
// 跨域配置（和ems_api5.1.php保持一致，可选但建议加）
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// 引入配置文件（必须包含 EMS_TOKEN 和 EMS_KEY 常量）
require_once __DIR__ . '/../../config.php';

/**
 * 调用 EMS OMS API 通用函数 (使用 cURL 发送 SOAP 请求)
 *
 * @param string $service 要调用的服务方法名
 * @param string $paramsJson 作为 JSON 字符串传递的参数
 * @return array 返回的 API 响应数据
 */
function callEmsSoap(string $service, string $paramsJson): array {  
    // 生产环境 URL
    $url = "http://cpws.ems.com.cn/default/svc/web-service";
    // 测试环境 URL (如果需要)
    // $url = "http://sbx-zy-oms.eminxing.com/default/svc/web-service";

    $token = EMS_TOKEN;
    $key   = EMS_KEY;

    // 构造 SOAP XML 请求体（移除变量插值的多余空格）
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

    // 初始化 cURL（移除变量前多余空格）
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 30, // 设置超时时间
        CURLOPT_POSTFIELDS => $xml,
        CURLOPT_HTTPHEADER => [
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen($xml) // 明确设置长度可能有助于某些服务器
        ],
        CURLOPT_SSL_VERIFYPEER => false, // 生产环境建议开启 SSL 验证
        CURLOPT_SSL_VERIFYHOST => false, // 生产环境建议开启 SSL 主机验证
    ]);

    // 执行请求
    $resp = curl_exec($ch);
    $err = curl_error($ch);       
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE); // 获取 HTTP 状态码
    curl_close($ch);

    // 检查 cURL 错误
    if ($err) {
        return ['ask' => 'Fail', 'message' => 'cURL Error: ' . $err];
    }

    // 检查 HTTP 状态码
    if ($httpCode !== 200) {
        return ['ask' => 'Fail', 'message' => 'HTTP Error: ' . $httpCode . ' Response Body: ' . $resp];
    }

    // 尝试从响应中提取 <response> 标签内的内容
    if (!preg_match('#<response>(.*?)</response>#s', $resp, $matches)) {
        return ['ask' => 'Fail', 'message' => 'No <response> tag found in SOAP response', 'raw_response' => $resp];        
    }

    $responseContent = $matches[1];

    // 解码 JSON 响应
    $decoded = json_decode($responseContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return [
            'ask' => 'Fail', 
            'message' => 'JSON Decode Error: ' . json_last_error_msg(), 
            'raw_response' => $resp, 
            'response_content' => $responseContent
        ];
    }

    return $decoded;
}

// --- getProductList 业务逻辑 ---
// 1. 接收请求参数（支持 GET 传参，便于灵活调用）
$pageSize = intval($_GET['pageSize'] ?? 10);
$page = intval($_GET['page'] ?? 1);
$productSku = trim($_GET['sku'] ?? '');
$productSkuArr = !empty($_GET['sku_arr']) ? explode(',', trim($_GET['sku_arr'])) : [];

// 2. 校验必填参数
if ($pageSize <= 0 || $page <= 0) {
    $result = [
        'success' => false,
        'message' => 'pageSize 和 page 必须为大于 0 的整数',
        'data' => []
    ];
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 3. 准备 API 参数
$params = [
    "pageSize" => $pageSize,
    "page" => $page
];
// 可选参数：按 SKU 过滤
if (!empty($productSku)) {
    $params["product_sku"] = $productSku;
}
if (!empty($productSkuArr)) {
    $params["product_sku_arr"] = array_filter($productSkuArr); // 过滤空值
}

// 4. 转换参数为 JSON 字符串
$paramsJson = json_encode($params, JSON_UNESCAPED_UNICODE);

// 5. 调用 API
$response = callEmsSoap('getProductList', $paramsJson);

// 6. 统一输出格式（JSON 格式，便于解析）
$result = [
    'success' => strtoupper($response['ask'] ?? '') === 'SUCCESS',
    'request' => $params,
    'data' => $response
];

echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);


# 基础调用（第1页，每页10条）
//http://cz.younger-car.com/yunfei_kucun/api_ems/get_product.php?page=1&pageSize=10

# 按单个SKU查询
// http://cz.younger-car.com/yunfei_kucun/api_ems/get_product.php?page=1&pageSize=10&sku=NI-C63-FL-GB

# 按多个SKU查询（逗号分隔）
//http://cz.younger-car.com/yunfei_kucun/api_ems/get_product.php?page=1&pageSize=10&sku_arr=NI-C63-FL-GB,NI-C63-FL-GB2
?>