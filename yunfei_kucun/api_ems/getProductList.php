<?php

// 请将这些常量替换为您实际的 API Token 和 Key
require_once __DIR__ . '/../../config.php'; // 必须包含 EMS_TOKEN 和 EMS_KEY 两个常量

/**
 * 调用 EMS OMS API 通用函数 (使用 cURL 发送 SOAP 请求)
 *
 * @param string  $ service 要调用的服务方法名
 * @param string  $ paramsJson 作为 JSON 字符串传递的参数
 * @return array 返回的 API 响应数据
 */
function callEmsSoap(string  $service, string  $paramsJson): array {  
    // 生产环境 URL
     $url = "http://cpws.ems.com.cn/default/svc/web-service";
    // 测试环境 URL (如果需要)
    //  $ url = "http://sbx-zy-oms.eminxing.com/default/svc/web-service";

     $token = EMS_TOKEN;
     $key   = EMS_KEY;

    // 构造 SOAP XML 请求体
     $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.example.org/Ec/">
 <SOAP-ENV:Body>
  <ns1:callService>
   <paramsJson>{ $paramsJson}</paramsJson>
   <appToken>{ $token}</appToken>
   <appKey>{ $key}</appKey>
   <service>{ $service}</service>   
  </ns1:callService>
 </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

    // 初始化 cURL
     $ch = curl_init();
    curl_setopt_array( $ch, [
        CURLOPT_URL =>  $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 30, // 设置超时时间
        CURLOPT_POSTFIELDS =>  $xml,
        CURLOPT_HTTPHEADER => [
            'Content-Type: text/xml; charset=utf-8',
            'Content-Length: ' . strlen( $xml) // 明确设置长度可能有助于某些服务器
        ],
        CURLOPT_SSL_VERIFYPEER => false, // 注意：生产环境中应验证 SSL 证书
        CURLOPT_SSL_VERIFYHOST => false, // 注意：生产环境中应验证 SSL 主机
    ]);

    // 执行请求
     $resp = curl_exec( $ch);
     $err = curl_error( $ch);       
     $httpCode = curl_getinfo( $ch, CURLINFO_HTTP_CODE); // 获取 HTTP 状态码
    curl_close( $ch);

    // 检查 cURL 错误
    if ( $err) {
        return ['ask' => 'Fail', 'message' => 'cURL Error: ' .  $err];
    }

    // 检查 HTTP 状态码
    if ( $httpCode !== 200) {
        return ['ask' => 'Fail', 'message' => 'HTTP Error: ' .  $httpCode . ' Response Body: ' .  $resp];
    }

    // 尝试从响应中提取 <response> 标签内的内容
    if (!preg_match('#<response>(.*?)</response>#s',  $resp,  $matches)) {
        return ['ask' => 'Fail', 'message' => 'No <response> tag found in SOAP response', 'raw_response' =>  $resp];        
    }

     $responseContent =  $matches[1];

    // 解码 JSON 响应
     $decoded = json_decode( $responseContent, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        return ['ask' => 'Fail', 'message' => 'JSON Decode Error: ' . json_last_error_msg(), 'raw_response' =>  $resp, 'response_content' =>  $responseContent];
    }

    return  $decoded;
}

// --- getProductList 示例 ---

// 准备要传递给 API 的参数
// 根据文档，pageSize 和 page 是必需的
 $params = [
    "pageSize" => 10, // 每页获取 10 条记录
    "page" => 1       // 获取第 1 页
    // "product_sku" => "YOUR_SPECIFIC_SKU", // 可选：按特定 SKU 查询
    // "product_sku_arr" => ["SKU1", "SKU2"], // 可选：按多个 SKU 数组查询
];

// 将参数数组转换为 JSON 字符串
 $paramsJson = json_encode( $params, JSON_UNESCAPED_UNICODE);

// 调用 API
 $response = callEmsSoap('getProductList',  $paramsJson);

// 输出结果
echo "<pre>";
echo "Request Parameters:\n";
print_r( $params);
echo "\nAPI Response:\n";
print_r( $response);    
echo "</pre>";

?>