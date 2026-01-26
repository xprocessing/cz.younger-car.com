<?php
require_once __DIR__ . '/../../config.php';  // 引入 EMS_TOKEN 和 EMS_KEY

/**
 * 调用 getProductList 接口获取产品列表
 *
 * @param array  $ params 请求参数
 * @return array 解析后的 JSON 响应
 */
function callGetProductList(array  $params): array {
     $url = "http://cpws.ems.com.cn/default/svc/web-service"; // 正式环境地址

    // 构造 paramsJson
     $paramsJson = json_encode( $params, JSON_UNESCAPED_UNICODE);

    // 构造 SOAP XML
     $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.example.org/Ec/">
  <SOAP-ENV:Body>
    <ns1:callService>
      <paramsJson>{ $ paramsJson}</paramsJson>
      <appToken>{ $ _SERVER['EMS_TOKEN'] ?? EMS_TOKEN}</appToken>
      <appKey>{ $ _SERVER['EMS_KEY'] ?? EMS_KEY}</appKey>
      <service>getProductList</service>
    </ns1:callService>
  </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

    // 初始化 cURL
     $ch = curl_init();
    curl_setopt_array( $ch, [
        CURLOPT_URL            =>  $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     =>  $xml,
        CURLOPT_HTTPHEADER     => ['Content-Type: text/xml; charset=utf-8'],
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

     $response = curl_exec( $ch);
     $error    = curl_error( $ch);
    curl_close( $ch);

    if ( $error) {
        return ['ask' => 'Fail', 'message' => 'cURL Error: ' .  $error];
    }

    // 提取 <response> 标签内容
    if (preg_match('#<response>(.*?)</response>#s',  $response,  $matches)) {
         $jsonStr =  $matches[1];
         $data    = json_decode( $jsonStr, true);
        return  $data ?? ['ask' => 'Fail', 'message' => 'Invalid JSON response'];
    } else {
        return ['ask' => 'Fail', 'message' => 'No <response> tag found in SOAP response'];
    }
}

// ====== 使用示例 ======

// 查询第1页，每页10条，指定两个 SKU
 $requestParams = [
    "pageSize"         => 10,
    "page"             => 1,
    // "product_sku"   => "SKU123",       // 单个 SKU 查询（可选）
    "product_sku_arr"  => ["NI-DH296B", "NI-674871"] // 多个 SKU（数组）
];

 $result = callGetProductList( $requestParams);

// 输出结果
header('Content-Type: application/json; charset=utf-8');
echo json_encode( $result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

//测试链接 https://cz.younger-car.com/yunfei_kucun/api_ems/getProductList.php