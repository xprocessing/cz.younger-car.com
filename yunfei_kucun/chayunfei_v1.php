<?php
//根据订单的邮编,重量，尺寸  ，运德需要城市
//生成一个方法，通过获取get参数，传入参数 city，postcode，weight，length，width，height
//测试https://cz.younger-car.com/chayunfei.php?postcode=92113-3931&weight=7.1928&length=74.0&width=27.0&height=18.0&city=%20San%20Diego
//速度太慢了。，需要用并发来请求。
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'key.php'; // 必须包含 EMS_TOKEN 和 EMS_KEY 两个常量

$city    = trim($_GET['city']);
$postcode  = trim($_GET['postcode']  ?? '');
$weight    = max(0.001, floatval($_GET['weight'] ?? 0));     // 最低 0.001kg

// 尺寸（单位 cm），默认最小 1cm
$length = max(1, round(floatval($_GET['length'] ?? 0), 1));
$width  = max(1, round(floatval($_GET['width']  ?? 0), 1));
$height = max(1, round(floatval($_GET['height'] ?? 0), 1));

//中邮仓库和渠道参数
$warehouse = "USWE,USEA";
$channel   = "FEDEX-GROUND-EA,SS-FEDEX-G-E,FEDEX-SM";
//$channel   = "FEDEX-GROUND-EA,SS-FEDEX-G-E,FEDEX-SM,FEDEX-LP,AMAZON-GROUND,USPS-FIRST-CLASS,USPS-PRIORITY,DHL-US-SP,DHL-US-BP,YUN-GROUND,CE-PARCEL,CE-GROUND-EA,UPS-GROUND-EA,UPS-GROUND-MULT,UPS-SUREPOST,UPS-2ND-DAY,INT-PRI-SP,INT-PRI-LP,IPA-INT-ECONOMIC,US-G2G-INT,US-UPS-INT";


// 解析 warehouse 和 channel，支持逗号分隔
$warehouseList = array_filter(array_unique(array_map('trim', explode(',', $warehouse))));
$channelList   = array_filter(array_unique(array_map('trim', explode(',', $channel))));

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
CURLOPT_URL => $url,
CURLOPT_RETURNTRANSFER => true,
CURLOPT_POST => true,
CURLOPT_TIMEOUT => 20, // 多组合时适当放宽
CURLOPT_POSTFIELDS => $xml,
CURLOPT_HTTPHEADER => ['Content-Type: text/xml; charset=utf-8'],
CURLOPT_SSL_VERIFYPEER => false,
]);

$resp = curl_exec($ch);
$err = curl_error($ch);
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
    "postcode" => (string)$postcode,
    "type" => 1,
    "weight" => round($weight, 3),
    "length" => $length,
    "width" => $width,
    "height" => $height,
    "pieces" => 1
    ];

    // ========== 批量查询：每个 warehouse × 每个 channel ==========
    $finalResult = [];
    $anySuccess = false;
    $totalRequests = count($warehouseList) * count($channelList);

    foreach ($warehouseList as $wh) {
    foreach ($channelList as $ch) {
    $params = $baseParams;
    $params["warehouse_code"] = $wh;
    $params["shipping_method"] = strtoupper($ch);

    $paramsJson = json_encode($params, JSON_UNESCAPED_UNICODE);
    $raw = callEmsSoap('getCalculateFee', $paramsJson);
    $data = json_decode($raw, true);

    // 统一返回结构
    $finalResult[$wh][$ch] = $data ?? [
    'ask' => 'Fail',
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
    'postcode' => $postcode,
    'weight' => $weight,
    'warehouses' => $warehouseList,
    'channels' => $channelList,
    'total_combinations' => $totalRequests
    ],
    'data' => $finalResult
    ];

    //echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    //将response 转为json 格式
    //$result = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    $obj = $response; // 转成关联数组
    $data = $obj['data']; // 取出我们关心的部分

    // 可选：加点基础样式，让表格更好看
    echo '<style>
    table {
        border-collapse: collapse;
        width: 100%;
        font-family: Arial, sans-serif;
    }

    th,
    td {
        border: 1px solid #999;
        padding: 8px 12px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    .fail {
        color: #d8000c;
    }
    </style>';

    echo '<table>';
        echo '<thead>';
            echo '<tr>';
                echo '<th>中邮仓库 (Warehouse)</th>';
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
            $ask = $info['ask'] ?? '-';
            $errCode = $info['Error']['errCode'] ?? '-';
            $errMsg = $info['Error']['errMessage'] ?? '-';
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

    //echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

    //增加一个运德运费查询

    //运德channelCode参数,一次只能请求5个渠道。
    $channelCode="AMGD,FEDHDE,FEDHDEMP"; //一次只能请求5个渠道。

    //$channelCode="AMGD,FEDHDE,FEDHDEMP,UPSGDE,NJUSPSGA,USPSPME,SPEEDXNJ,FDXSPE,UNIUNINJ,GOFONJ,GFYUNNJ,AMGDCA,UPSGW,CAUSPSGA,USPSPMW,USPSGACASG,FEDHDW,FEDHDWMP,UPSGWFBA,SPEEDXCA,CAGLS,FEDSPW,UNIUNICA,GOFOCA,GFYUNCA,FEDIGCA";


    $userAccount = WD_APP_ID;
    $testToken = WD_APP_TOKEN;
    $apiUrl = "http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery";

    // -------------------------- 2. 待请求的业务参数 (根据实际需求修改) --------------------------
    $contentParams = [
    'channelCode' => $channelCode,
    'country' => 'US',
    'city' => $city,
    'postcode' => $postcode,
    'weight' => round($weight, 3),
    'length' =>$length,
    'width' => $width,
    'height' => $height,
    'signatureService' => 0
    ];

    // -------------------------- 3. 核心逻辑：生成签名并发送请求 --------------------------
    try {
    // 构造content字段的JSON字符串
    $contentJson = json_encode($contentParams, JSON_UNESCAPED_UNICODE);
    if ($contentJson === false) {
    throw new Exception("JSON编码失败: " . json_last_error_msg());
    }

    // 准备待签名的数据
    $requestData = [
    'userAccount' => $userAccount,
    'content' => $contentJson
    ];

    // 生成签名
    ksort($requestData);
    $signStr = implode('', $requestData) . $testToken;
    $requestData['sign'] = strtoupper(md5($signStr));

    // 发送cURL POST请求
    $ch = curl_init($apiUrl);
    curl_setopt_array($ch, [
    CURLOPT_POST => true,
    CURLOPT_POSTFIELDS => $requestData,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_SSL_VERIFYPEER => false, // 正式环境建议开启
    CURLOPT_SSL_VERIFYHOST => false, // 正式环境建议开启
    CURLOPT_TIMEOUT => 60
    ]);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
    throw new Exception("cURL请求失败: " . curl_error($ch));
    }
    curl_close($ch);

    // 解析并输出结果
    //echo $response . "\n";
    $response = json_decode($response, true);


    //输出表格//

    echo '<table>';
        echo '<thead>';
            echo '<tr>';

                echo '<th>运德渠道 (Channel)</th>';
                echo '<th>费用CNY (SHIPPING)</th>';
                echo '<th>费用CNY (totalFee)</th>';

                echo '</tr>';
            echo '</thead>';
        echo '<tbody>';
            if (isset($response['data']) && is_array($response['data'])) {
            // 遍历物流方式
            foreach ($response['data'] as $method => $details) {
            // 输出物流方式名称和对应的运费
            echo '<tr>';
                echo "<td>$method</td>";
                echo "<td>{$details['shipFee']}</td>";
                echo "<td>{$details['currency']}</td>";


                echo '</tr>';
            }
            } else {
            echo "没有找到有效的物流数据。";
            }

            echo '</tbody>';
        echo '</table>';



    } catch (Exception $e) {
    echo "=== 请求异常 ===\n";
    echo "错误信息: " . $e->getMessage() . "\n";
    }
























    ?>