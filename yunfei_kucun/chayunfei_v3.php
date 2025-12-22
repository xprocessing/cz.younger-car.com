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
$channel   = "FEDEX-GROUND-EA,SS-FEDEX-G-E";
//$channel   = "FEDEX-GROUND-EA,SS-FEDEX-G-E,FEDEX-SM,FEDEX-LP,AMAZON-GROUND,USPS-FIRST-CLASS,USPS-PRIORITY,DHL-US-SP,DHL-US-BP,YUN-GROUND,CE-PARCEL,CE-GROUND-EA,UPS-GROUND-EA,UPS-GROUND-MULT,UPS-SUREPOST,UPS-2ND-DAY,INT-PRI-SP,INT-PRI-LP,IPA-INT-ECONOMIC,US-G2G-INT,US-UPS-INT";

// 解析 warehouse 和 channel，支持逗号分隔
$warehouseList = array_filter(array_unique(array_map('trim', explode(',', $warehouse))));
$channelList   = array_filter(array_unique(array_map('trim', explode(',', $channel))));

// ========== SOAP 调用封装 (增加了耗时返回) ==========
/**
 * 调用中邮EMS的SOAP服务
 * @param string $service 服务名称
 * @param string $paramsJson 参数JSON字符串
 * @return array 包含['data']和['time']的数组
 */
function callEmsSoap(string $service, string $paramsJson): array
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
    
    $startTime = microtime(true); // 【新增】记录开始时间

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

    $endTime = microtime(true); // 【新增】记录结束时间
    $timeSpent = round(($endTime - $startTime) * 1000, 2); // 【新增】计算耗时(毫秒)

    if ($err) {
        return [
            'data' => json_encode(['ask' => 'Fail', 'message' => 'cURL Error: ' . $err]),
            'time' => $timeSpent
        ];
    }

    preg_match('#<response>(.*?)</response>#s', $resp, $m);
    $responseData = $m[1] ?? json_encode(['ask' => 'Fail', 'message' => 'No <response> tag']);
    
    return [
        'data' => $responseData,
        'time' => $timeSpent
    ];
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

// ========== 批量查询：每个 warehouse × 每个 channel (增加耗时记录) ==========
$finalResult = [];
$anySuccess = false;
$totalRequests = count($warehouseList) * count($channelList);

foreach ($warehouseList as $wh) {
    foreach ($channelList as $ch) {
        $params = $baseParams;
        $params["warehouse_code"] = $wh;
        $params["shipping_method"] = strtoupper($ch);

        $paramsJson = json_encode($params, JSON_UNESCAPED_UNICODE);
        $soapResponse = callEmsSoap('getCalculateFee', $paramsJson); // 【修改】接收包含耗时的返回值
        $raw = $soapResponse['data'];
        $timeSpent = $soapResponse['time']; // 【新增】获取耗时
        
        $data = json_decode($raw, true);

        // 统一返回结构 (【修改】增加time字段)
        $finalResult[$wh][$ch] = $data ?? [
            'ask' => 'Fail',
            'message' => 'JSON parse error or empty response'
        ];
        $finalResult[$wh][$ch]['time'] = $timeSpent; // 【新增】将耗时存入结果

        if (isset($data['ask']) && strtoupper($data['ask']) === 'SUCCESS') {
            $anySuccess = true;
        }
    }
}

// ========== 最终输出 (【修改】表格增加耗时列) ==========
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

// 可选：加点基础样式，让表格更好看
echo '<style>
table {
    border-collapse: collapse;
    width: 100%;
    font-family: Arial, sans-serif;
    margin-bottom: 20px;
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

// -------------------------- 中邮EMS结果表格 --------------------------
echo '<h2>中邮EMS 物流费用查询</h2>';
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
echo '<th>耗时 (ms)</th>'; // 【新增】耗时列
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($finalResult as $warehouse => $channels) {
    foreach ($channels as $channel => $info) {
        $ask = $info['ask'] ?? '-';
        $errCode = $info['Error']['errCode'] ?? '-';
        $errMsg = $info['Error']['errMessage'] ?? '-';
        $totalFee = $info['data']['totalFee'] ?? '-';
        $SHIPPING = $info['data']['SHIPPING'] ?? '-';
        $timeSpent = $info['time'] ?? '-'; // 【新增】获取耗时

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
        echo "<td>$timeSpent</td>"; // 【新增】显示耗时
        echo '</tr>';
    }
}

echo '</tbody>';
echo '</table>';

// -------------------------- 运德物流查询（并发版，增加总耗时统计） --------------------------

/**
 * 并发请求运德物流费用
 * @param array $channelGroups 渠道分组，每组不超过5个渠道
 * @param array $commonParams 公共参数
 * @param string $apiUrl API地址
 * @param string $userAccount 用户账号
 * @param string $testToken 测试token
 * @return array 合并后的结果, 键为'data'和'total_time'
 */
function concurrentWedoRequests($channelGroups, $commonParams, $apiUrl, $userAccount, $testToken) {
    $startTime = microtime(true); // 【新增】记录整个并发批次的开始时间

    $mh = curl_multi_init();
    $handles = [];
    $results = [];

    foreach ($channelGroups as $groupKey => $channelCodes) {
        // 构造当前组的请求参数
        $contentParams = $commonParams;
        $contentParams['channelCode'] = implode(',', $channelCodes);
        
        // 构造content字段的JSON字符串
        $contentJson = json_encode($contentParams, JSON_UNESCAPED_UNICODE);
        if ($contentJson === false) {
            $results[$groupKey] = ['error' => "JSON编码失败: " . json_last_error_msg()];
            continue;
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

        // 初始化curl句柄
        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $requestData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 60
        ]);

        // 添加到multi句柄
        curl_multi_add_handle($mh, $ch);
        $handles[$groupKey] = $ch;
    }

    // 执行并发请求
    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    // 获取结果
    foreach ($handles as $groupKey => $ch) {
        $response = curl_multi_getcontent($ch);
        if (curl_errno($ch)) {
            $results[$groupKey] = ['error' => "cURL请求失败: " . curl_error($ch)];
        } else {
            $results[$groupKey] = json_decode($response, true);
        }
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }

    curl_multi_close($mh);

    // 合并结果
    $mergedData = [];
    foreach ($results as $result) {
        if (isset($result['data']) && is_array($result['data'])) {
            $mergedData = array_merge($mergedData, $result['data']);
        }
    }
    
    $endTime = microtime(true); // 【新增】记录整个并发批次的结束时间
    $totalTimeSpent = round(($endTime - $startTime) * 1000, 2); // 【新增】计算总耗时

    return [
        'data' => $mergedData,
        'total_time' => $totalTimeSpent
    ];
}

// 运德渠道列表（完整列表）
$allChannelCodes = "AMGD,FEDHDE,FEDHDEMP,UPSGDE,NJUSPSGA,USPSPME,SPEEDXNJ,FDXSPE,UNIUNINJ,GOFONJ,GFYUNNJ,AMGDCA,UPSGW,CAUSPSGA,USPSPMW,USPSGACASG,FEDHDW,FEDHDWMP,UPSGWFBA,SPEEDXCA,CAGLS,FEDSPW,UNIUNICA,GOFOCA,GFYUNCA,FEDIGCA";
$channelArray = array_filter(array_unique(array_map('trim', explode(',', $allChannelCodes))));

// 分组，每组最多5个渠道
$channelGroups = array_chunk($channelArray, 5);

// 运德请求公共参数
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

// 运德API配置
$wedoApiUrl = "http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery";
$wedoUserAccount = WD_APP_ID;
$wedoTestToken = WD_APP_TOKEN;

// 执行并发请求 (【修改】接收包含总耗时的返回值)
$wedoResponse = concurrentWedoRequests($channelGroups, $wedoCommonParams, $wedoApiUrl, $wedoUserAccount, $wedoTestToken);
$wedoResults = $wedoResponse['data'];
$wedoTotalTime = $wedoResponse['total_time'];

// 输出运德结果表格
echo '<h2>运德物流 费用查询 (并发总耗时: ' . $wedoTotalTime . ' ms)</h2>'; // 【新增】显示总耗时
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>运德渠道 (Channel)</th>';
echo '<th>费用CNY (SHIPPING)</th>';
echo '<th>货币单位 (Currency)</th>';
// 注意：并发请求无法轻易统计单个渠道的耗时，只能统计整个批次的总耗时
echo '</tr>';
echo '</thead>';
echo '<tbody>';

if (!empty($wedoResults)) {
    foreach ($wedoResults as $method => $details) {
        $shipFee = $details['shipFee'] ?? '-';
        $currency = $details['currency'] ?? 'CNY';
        echo '<tr>';
        echo "<td>$method</td>";
        echo "<td>$shipFee</td>";
        echo "<td>$currency</td>";
        echo '</tr>';
    }
} else {
    echo '<tr><td colspan="3">没有找到有效的物流数据。</td></tr>';
}

echo '</tbody>';
echo '</table>';
?>