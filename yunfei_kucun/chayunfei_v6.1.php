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
$channel   = "FEDEX-GROUND-EA,SS-FEDEX-G-E,FEDEX-SM,FEDEX-LP,AMAZON-GROUND,USPS-FIRST-CLASS,USPS-PRIORITY,DHL-US-SP,DHL-US-BP,YUN-GROUND,CE-PARCEL,CE-GROUND-EA,UPS-GROUND-EA,UPS-GROUND-MULT,UPS-SUREPOST,UPS-2ND-DAY";

// 解析 warehouse 和 channel，支持逗号分隔
$warehouseList = array_filter(array_unique(array_map('trim', explode(',', $warehouse))));
$channelList   = array_filter(array_unique(array_map('trim', explode(',', $channel))));

// ========== SOAP 调用封装 (单个请求) ==========
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
    
    $startTime = microtime(true);

    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_POSTFIELDS => $xml,
        CURLOPT_HTTPHEADER => ['Content-Type: text/xml; charset=utf-8'],
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $resp = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);

    $endTime = microtime(true);
    $timeSpent = round(($endTime - $startTime) * 1000, 2);

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

// ========== 中邮并发请求封装 ==========
function concurrentEmsRequests(array $requests, int $maxConcurrency = 10): array
{
    $startTime = microtime(true);
    $results = [];
    
    // 分批处理，控制最大并发数
    $batches = array_chunk($requests, $maxConcurrency);
    
    foreach ($batches as $batch) {
        $mh = curl_multi_init();
        $handles = [];
        
        // 准备当前批次的请求
        foreach ($batch as $key => $request) {
            $url = "http://cpws.ems.com.cn/default/svc/web-service";
            $xml = $request['xml'];
            
            $ch = curl_init();
            curl_setopt_array($ch, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_TIMEOUT => 20,
                CURLOPT_POSTFIELDS => $xml,
                CURLOPT_HTTPHEADER => ['Content-Type: text/xml; charset=utf-8'],
                CURLOPT_SSL_VERIFYPEER => false,
            ]);
            
            curl_multi_add_handle($mh, $ch);
            $handles[$key] = $ch;
        }
        
        // 执行并发请求
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);
        
        // 收集结果
        foreach ($handles as $key => $ch) {
            $resp = curl_exec($ch);
            $err = curl_error($ch);
            
            $endTime = microtime(true);
            $timeSpent = round(($endTime - $startTime) * 1000, 2);
            
            if ($err) {
                $results[$key] = [
                    'data' => json_encode(['ask' => 'Fail', 'message' => 'cURL Error: ' . $err]),
                    'time' => $timeSpent
                ];
            } else {
                preg_match('#<response>(.*?)</response>#s', $resp, $m);
                $responseData = $m[1] ?? json_encode(['ask' => 'Fail', 'message' => 'No <response> tag']);
                $results[$key] = [
                    'data' => $responseData,
                    'time' => $timeSpent
                ];
            }
            
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        
        curl_multi_close($mh);
    }
    
    $endTime = microtime(true);
    $totalTimeSpent = round(($endTime - $startTime) * 1000, 2);
    
    return [
        'results' => $results,
        'total_time' => $totalTimeSpent
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

// ========== 中邮批量查询：并发处理 ==========
$finalResult = [];
$anySuccess = false;
$totalRequests = count($warehouseList) * count($channelList);

// 准备所有请求
$emsRequests = [];
$requestKeys = [];
$keyIndex = 0;

foreach ($warehouseList as $wh) {
    foreach ($channelList as $ch) {
        $params = $baseParams;
        $params["warehouse_code"] = $wh;
        $params["shipping_method"] = strtoupper($ch);
        
        $paramsJson = json_encode($params, JSON_UNESCAPED_UNICODE);
        $token = EMS_TOKEN;
        $key = EMS_KEY;
        
        // 构建SOAP XML
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
        
        $emsRequests[$keyIndex] = [
            'xml' => $xml,
            'warehouse' => $wh,
            'channel' => $ch
        ];
        $requestKeys[$keyIndex] = "$wh|$ch";
        $keyIndex++;
    }
}

// 执行并发请求（最大10个并发）
$emsConcurrentResult = concurrentEmsRequests($emsRequests, 10);
$emsResults = $emsConcurrentResult['results'];
$emsTotalTime = $emsConcurrentResult['total_time'];

// 整理结果
foreach ($emsResults as $key => $result) {
    list($wh, $ch) = explode('|', $requestKeys[$key]);
    $raw = $result['data'];
    $timeSpent = $result['time'];
    
    $data = json_decode($raw, true);
    
    if (!isset($finalResult[$wh])) {
        $finalResult[$wh] = [];
    }
    $finalResult[$wh][$ch] = $data ?? [
        'ask' => 'Fail',
        'message' => 'JSON parse error or empty response'
    ];
    $finalResult[$wh][$ch]['time'] = $timeSpent;
    
    if (isset($data['ask']) && strtoupper($data['ask']) === 'SUCCESS') {
        $anySuccess = true;
    }
}

// ========== 样式与基础输出 ==========
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

// -------------------------- 中邮EMS结果表格（按totalFee升序） --------------------------
echo '<h2>中邮EMS 物流费用查询（并发总耗时: ' . $emsTotalTime . ' ms）（按totalFee升序）</h2>';

$emsSortableData = [];
foreach ($finalResult as $warehouse => $channels) {
    foreach ($channels as $channel => $info) {
        $totalFee = isset($info['data']['totalFee']) ? floatval($info['data']['totalFee']) : PHP_INT_MAX;
        $emsSortableData[] = [
            'warehouse' => $warehouse,
            'channel' => $channel,
            'totalFee' => $totalFee,
            'SHIPPING' => $info['data']['SHIPPING'] ?? '-',
            'ask' => $info['ask'] ?? '-',
            'errCode' => $info['Error']['errCode'] ?? '-',
            'errMsg' => $info['Error']['errMessage'] ?? '-',
            'timeSpent' => $info['time'] ?? '-'
        ];
    }
}

// 中邮排序（优化浮点数比较）
usort($emsSortableData, function($a, $b) {
    return $a['totalFee'] <=> $b['totalFee']; // 用太空船运算符，支持浮点数精确比较
});

echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>中邮仓库 (Warehouse)</th>';
echo '<th>渠道 (Channel)</th>';
echo '<th>费用CNY (totalFee)</th>';
echo '<th>结果 (Result)</th>';
echo '<th>错误码 (Error Code)</th>';
echo '<th>错误信息 (Error Message)</th>';
echo '<th>耗时 (ms)</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

foreach ($emsSortableData as $item) {
    $askClass = ($item['ask'] === 'Failure') ? ' class="fail"' : '';
    $totalFeeDisplay = $item['totalFee'] === PHP_INT_MAX ? '-' : number_format($item['totalFee'], 2);
    
    echo '<tr>';
    echo "<td><strong>{$item['warehouse']}</strong></td>";
    echo "<td>{$item['channel']}</td>";
    echo "<td>{$totalFeeDisplay}</td>";
    echo "<td{$askClass}>{$item['ask']}</td>";
    echo "<td>{$item['errCode']}</td>";
    echo "<td>{$item['errMsg']}</td>";
    echo "<td>{$item['timeSpent']}</td>";
    echo '</tr>';
}

echo '</tbody>';
echo '</table>';

// -------------------------- 运德物流查询（并发版，优化shipFee排序） --------------------------
function concurrentWedoRequests($channelGroups, $commonParams, $apiUrl, $userAccount, $testToken) {
    $startTime = microtime(true);

    $mh = curl_multi_init();
    $handles = [];
    $results = [];

    foreach ($channelGroups as $groupKey => $channelCodes) {
        $contentParams = $commonParams;
        $contentParams['channelCode'] = implode(',', $channelCodes);
        
        $contentJson = json_encode($contentParams, JSON_UNESCAPED_UNICODE);
        if ($contentJson === false) {
            $results[$groupKey] = ['error' => "JSON编码失败: " . json_last_error_msg()];
            continue;
        }

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
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_TIMEOUT => 60
        ]);

        curl_multi_add_handle($mh, $ch);
        $handles[$groupKey] = $ch;
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

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

    $mergedData = [];
    foreach ($results as $result) {
        if (isset($result['data']) && is_array($result['data'])) {
            $mergedData = array_merge($mergedData, $result['data']);
        }
    }
    
    $endTime = microtime(true);
    $totalTimeSpent = round(($endTime - $startTime) * 1000, 2);

    return [
        'data' => $mergedData,
        'total_time' => $totalTimeSpent
    ];
}

// 运德渠道列表
$allChannelCodes = "AMGD,FEDHDE,UPSGDE,NJUSPSGA,USPSPME,SPEEDXNJ,FDXSPE,UNIUNINJ,GOFONJ,GFYUNNJ,AMGDCA,UPSGW,CAUSPSGA,USPSPMW,FEDHDW,SPEEDXCA,CAGLS,FEDSPW,UNIUNICA,GOFOCA,GFYUNCA";
$channelArray = array_filter(array_unique(array_map('trim', explode(',', $allChannelCodes))));
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

// 执行并发请求
$wedoResponse = concurrentWedoRequests($channelGroups, $wedoCommonParams, $wedoApiUrl, $wedoUserAccount, $wedoTestToken);
$wedoResults = $wedoResponse['data'];
$wedoTotalTime = $wedoResponse['total_time'];

// -------------------------- 运德物流结果表格（优化shipFee排序） --------------------------
echo '<h2>运德物流 费用查询 (并发总耗时: ' . $wedoTotalTime . ' ms)（按shipFee升序）</h2>';

$wedoSortableData = [];
foreach ($wedoResults as $method => $details) {
    // 【重点修复1】严格处理shipFee值，确保为浮点数
    $shipFee = 0;
    if (isset($details['shipFee'])) {
        // 处理字符串格式的数字、空字符串、null等情况
        $shipFeeRaw = $details['shipFee'];
        if (is_string($shipFeeRaw)) {
            $shipFeeRaw = trim($shipFeeRaw);
            // 空字符串或非数字字符串设为极大值
            $shipFee = is_numeric($shipFeeRaw) ? floatval($shipFeeRaw) : PHP_INT_MAX;
        } elseif (is_numeric($shipFeeRaw)) {
            $shipFee = floatval($shipFeeRaw);
        } else {
            $shipFee = PHP_INT_MAX;
        }
    } else {
        $shipFee = PHP_INT_MAX;
    }

    $wedoSortableData[] = [
        'channel' => $method,
        'shipFee' => $shipFee,
        'currency' => $details['currency'] ?? 'CNY'
    ];
}

// 【重点修复2】使用太空船运算符（PHP 7+支持），确保浮点数精确排序
usort($wedoSortableData, function($a, $b) {
    // 太空船运算符 <=> 会正确处理浮点数比较，返回 -1/0/1
    return $a['shipFee'] <=> $b['shipFee'];
});

// 【重点修复3】分离有效数据和无效数据，确保无效数据排在最后
$validWedoData = [];
$invalidWedoData = [];
foreach ($wedoSortableData as $item) {
    if ($item['shipFee'] !== PHP_INT_MAX) {
        $validWedoData[] = $item;
    } else {
        $invalidWedoData[] = $item;
    }
}
// 合并：有效数据（已排序）+ 无效数据（无费用）
$finalWedoData = array_merge($validWedoData, $invalidWedoData);

// 渲染排序后的表格
echo '<table>';
echo '<thead>';
echo '<tr>';
echo '<th>排序</th>'; // 新增排序序号，直观展示
echo '<th>运德渠道 (Channel)</th>';
echo '<th>费用CNY (shipFee)</th>';
echo '<th>货币单位 (Currency)</th>';
echo '</tr>';
echo '</thead>';
echo '<tbody>';

if (!empty($finalWedoData)) {
    $sortNum = 1;
    foreach ($finalWedoData as $item) {
        $shipFeeDisplay = $item['shipFee'] === PHP_INT_MAX ? '-' : number_format($item['shipFee'], 2);
        // $shipFeeDisplay从美金换算为人民币，假设汇率为7.0
        $shipFeeDisplay = $shipFeeDisplay === '-' ? '-' : number_format(floatval($shipFeeDisplay) * 7.0, 2);
        

        echo '<tr>';
        echo "<td>{$sortNum}</td>"; // 排序序号
        echo "<td>{$item['channel']}</td>";
        echo "<td>{$shipFeeDisplay}</td>";
        echo "<td>{$item['currency']}</td>";
        echo '</tr>';
        $sortNum++;
    }
} else {
    echo '<tr><td colspan="4">没有找到有效的物流数据。</td></tr>';
}

echo '</tbody>';
echo '</table>';
?>