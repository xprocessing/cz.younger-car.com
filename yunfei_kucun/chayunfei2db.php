<?php
// 根据订单的邮编、重量、尺寸、城市，查询中邮 EMS 和 运德物流运费
// 支持 GET 参数：city, postcode, weight, length, width, height, receiver_country_code, global_order_no
// 示例测试 URL:
// https://cz.younger-car.com/chayunfei.php?postcode=92113-3931&weight=7.1928&length=74.0&width=27.0&height=18.0&city=San%20Diego&receiver_country_code=US&global_order_no=TEST123456

// ========== 响应头配置 ==========
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json; charset=utf-8"); // 改为JSON输出

// ========== 依赖引入 ==========
require_once 'config.php'; // 必须包含：EMS_TOKEN, EMS_KEY, WD_APP_ID, WD_APP_TOKEN

// ========== 常量定义 ==========
$channelsDict = [
    'AMGD' => '美东Amazon Ground',
    'FEDHDE' => '美东FedEx HomeDelivery',
    'FEDHDEMP' => '美东FedEx HD Multiple子母单',
    'UPSGDE' => '美东UPS Ground',
    'NJUSPSGA' => '美东USPS Ground（磅内磅上）',
    'USPSPME' => '美东USPS Priority Mail',
    'SPEEDXNJ' => '美东SpeedX(磅内磅上)',
    'FDXSPE' => '美东FedEx SmartPost',
    'UNIUNINJ' => '美东UniUni(磅内磅上)',
    'GOFONJ' => '美东GOFO Express',
    'GFYUNNJ' => '美东GOFO YunExpress',
    'AMGDCA' => '美西Amazon Ground',
    'UPSGW' => '美西UPS Ground',
    'CAUSPSGA' => '美西USPS Ground（磅内磅上）',
    'USPSPMW' => '美西USPS Priority Mail',
    'USPSGACASG' => '美西USPS Ground（磅内磅上）签名',
    'FEDHDW' => '美西FedEx HomeDelivery',
    'FEDHDWMP' => '美西FedEx HD Multiple子母单',
    'UPSGWFBA' => '美西UPS Ground FBA',
    'SPEEDXCA' => '美西SpeedX(磅内磅上)',
    'CAGLS' => '美西GLS',
    'FEDSPW' => '美西FedEx SmartPost',
    'UNIUNICA' => '美西UniUni(磅内磅上)',
    'GOFOCA' => '美西GOFO Express',
    'GFYUNCA' => '美西GOFO YunExpress',
    'FEDIGCA' => '美西FedEx International Ground-美发加',
    'FEDEX-GROUND-EA' => '美东FedEx重货',
    'SS-FEDEX-G-E' => '美东FEDEX大货渠道',
    'FEDEX-SM' => '美国FEDEX经济小包',
    'FEDEX-LP' => '美国FEDEX经济大包',
    'AMAZON-GROUND' => '美国AMAZON重货渠道',
    'USPS-FIRST-CLASS' => '美国USPS小包',
    'USPS-PRIORITY' => '美国USPS大包',
    'DHL-US-SP' => '美国DHL本地小包',
    'DHL-US-BP' => '美国DHL本地大包',
    'YUN-GROUND' => '美国YUN重货渠道',
    'CE-PARCEL' => '美国CE普货渠道',
    'CE-GROUND-EA' => '美东CE重货渠道',
    'UPS-GROUND-EA' => '美东UPS重货',
    'UPS-GROUND-MULT' => '美国UPS一票多箱',
    'UPS-SUREPOST' => 'UPS_SUREPOST',
    'UPS-2ND-DAY' => 'UPS两日达',
    'INT-PRI-SP' => '国际普快小包',
    'INT-PRI-LP' => '国际普快大包',
    'IPA-INT-ECONOMIC' => '国际经济小包',
    'US-G2G-INT' => '美加G2G国际渠道',
    'US-UPS-INT' => '美国UPS国际渠道',
    'FEDEX-GROUND' => '美国FEDEX重货渠道',
    'SS-FEDEX-G-W' => '美西FEDEX大货渠道',
    'CE-GROUND' => '美西CE重货渠道',
    'UPS-GROUND' => '美国UPS重货渠道',
    'OT-GROUND' => '美国OnTrac重货渠道',
    'UK-EVRI' => '英国EVRI',
    'UK-EVRI-L' => '英国EVRI超大渠道',
    'DX-EXPRESS' => '英国DX快递渠道',
    'DPD-UK' => '英国DPD单箱',
    'DPD-MULTI' => '英国DPD多箱',
    'YODEL48H-S' => 'Yodel_48h小包',
    'YODEL48H-L' => 'Yodel_48h大包',
    'XDP-EP_A-C' => 'XDP-EP(A,B,C区)'
];

// ========== 全局变量初始化 ==========
$totalExecutionStartTime = microtime(true);
$result = [
    'meta' => [
        'order_number' => 'N/A',
        'request_params' => [],
        'total_execution_time_ms' => 0,
        'error' => null // 全局错误信息
    ],
    'ems' => [
        'request_time_ms' => 0,
        'results' => [], // 按仓库+渠道分组的结果
        'sorted_results' => [] // 按运费升序排序的结果
    ],
    'wedo' => [
        'request_time_ms' => 0,
        'results' => [], // 原始渠道结果
        'sorted_results' => [] // 按运费升序排序的结果
    ]
];

// ========== 输入参数处理 ==========
$orderNumber = trim($_GET['global_order_no'] ?? '');
$recipientCity = trim($_GET['city'] ?? '');
$recipientPostcode = trim($_GET['postcode'] ?? '');
$packageWeight = max(0.001, floatval($_GET['weight'] ?? 0)); // 单位：kg
$packageLength = max(1, round(floatval($_GET['length'] ?? 0), 1)); // 单位：cm
$packageWidth = max(1, round(floatval($_GET['width'] ?? 0), 1));
$packageHeight = max(1, round(floatval($_GET['height'] ?? 0), 1));
$receiverCountryCode = trim($_GET['receiver_country_code'] ?? 'US');

// 统一国家代码（UK -> GB）
if ($receiverCountryCode === 'UK') {
    $receiverCountryCode = 'GB';
}

// 参数校验
if (empty($recipientPostcode) || empty($recipientCity)) {
    $result['meta']['error'] = [
        'code' => 'MISSING_PARAMS',
        'message' => '缺少必要参数：收件城市（city）或收件邮编（postcode）'
    ];
    echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

// 填充元数据中的请求参数
$result['meta']['order_number'] = $orderNumber ?: 'N/A';
$result['meta']['request_params'] = [
    'city' => $recipientCity,
    'postcode' => $recipientPostcode,
    'weight_kg' => $packageWeight,
    'dimensions_cm' => [
        'length' => $packageLength,
        'width' => $packageWidth,
        'height' => $packageHeight
    ],
    'receiver_country_code' => $receiverCountryCode
];

// ========== 中邮EMS请求函数 ==========
function sendEmsConcurrentRequests(array $requestParamsList): array {
    if (empty($requestParamsList)) return [];

    $multiHandle = curl_multi_init();
    $curlHandles = [];
    $responseResults = [];

    $emsApiUrl = "http://cpws.ems.com.cn/default/svc/web-service";
    $emsAppToken = EMS_TOKEN;
    $emsAppKey = EMS_KEY;

    foreach ($requestParamsList as $requestKey => $params) {
        $paramsJson = json_encode($params, JSON_UNESCAPED_UNICODE);
        $soapRequestXml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<SOAP-ENV:Envelope xmlns:SOAP-ENV="http://schemas.xmlsoap.org/soap/envelope/" xmlns:ns1="http://www.example.org/Ec/">
    <SOAP-ENV:Body>
        <ns1:callService>
            <paramsJson>{$paramsJson}</paramsJson>
            <appToken>{$emsAppToken}</appToken>
            <appKey>{$emsAppKey}</appKey>
            <service>getCalculateFee</service>
        </ns1:callService>
    </SOAP-ENV:Body>
</SOAP-ENV:Envelope>
XML;

        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, [
            CURLOPT_URL => $emsApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 12,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_POSTFIELDS => $soapRequestXml,
            CURLOPT_HTTPHEADER => ['Content-Type: text/xml; charset=utf-8'],
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TCP_KEEPALIVE => 1,
        ]);

        curl_multi_add_handle($multiHandle, $curlHandle);
        $curlHandles[$requestKey] = $curlHandle;
    }

    $isRunning = null;
    do {
        curl_multi_exec($multiHandle, $isRunning);
        if ($isRunning > 0) {
            curl_multi_select($multiHandle, 0.05);
        }
    } while ($isRunning > 0);

    foreach ($curlHandles as $requestKey => $curlHandle) {
        $responseContent = curl_multi_getcontent($curlHandle);
        $curlError = curl_error($curlHandle);

        if ($curlError) {
            $responseResults[$requestKey] = json_encode([
                'ask' => 'Fail',
                'message' => "中邮请求cURL错误：{$curlError}"
            ]);
        } else {
            preg_match('#<response>(.*?)</response>#s', $responseContent, $responseMatches);
            $responseResults[$requestKey] = $responseMatches[1] ?? json_encode([
                'ask' => 'Fail',
                'message' => '中邮响应缺少<response>标签'
            ]);
        }

        curl_multi_remove_handle($multiHandle, $curlHandle);
        curl_close($curlHandle);
    }

    curl_multi_close($multiHandle);
    return $responseResults;
}

// ========== 中邮EMS请求逻辑 ==========
$emsWarehouseCodes = "USWE,USEA,UK";//仓库代码
$emsChannelCodes = "FEDEX-GROUND-EA,SS-FEDEX-G-E,FEDEX-SM,FEDEX-LP,AMAZON-GROUND,USPS-FIRST-CLASS,USPS-PRIORITY,DHL-US-SP,DHL-US-BP,YUN-GROUND,CE-PARCEL,CE-GROUND-EA,UPS-GROUND-EA,UPS-GROUND-MULT,UPS-SUREPOST,UPS-2ND-DAY,DX-EXPRESS,DPD-UK,DPD-MULTI,YODEL48H-S,YODEL48H-L,XDP-EP_A-C,UK-EVRI,UK-EVRI-L";

$emsWarehouseList = array_filter(array_unique(array_map('trim', explode(',', $emsWarehouseCodes))));
$emsChannelList = array_filter(array_unique(array_map('trim', explode(',', $emsChannelCodes))));

// 构建请求参数
$baseRequestParams = [
    "country_code" => $receiverCountryCode,
    "postcode"     => (string)$recipientPostcode,
    "type"         => 1,
    "weight"       => round($packageWeight, 3),
    "length"       => $packageLength,
    "width"        => $packageWidth,
    "height"       => $packageHeight,
    "pieces"       => 1
];

$emsRequestParamsList = [];
$emsRequestKeys = [];
foreach ($emsWarehouseList as $warehouseCode) {
    foreach ($emsChannelList as $channelCode) {
        $requestParams = $baseRequestParams;
        $requestParams["warehouse_code"] = $warehouseCode;
        $requestParams["shipping_method"] = strtoupper($channelCode);
        
        $requestKey = "$warehouseCode||$channelCode";
        $emsRequestParamsList[$requestKey] = $requestParams;
        $emsRequestKeys[] = $requestKey;
    }
}

// 执行并发请求
$emsRequestStartTime = microtime(true);
$emsFinalResults = [];
$emsMaxBatchSize = 12;

for ($i = 0; $i < count($emsRequestKeys); $i += $emsMaxBatchSize) {
    $batchRequestKeys = array_slice($emsRequestKeys, $i, $emsMaxBatchSize);
    $batchRequestParams = array_intersect_key($emsRequestParamsList, array_flip($batchRequestKeys));
    $batchResponseResults = sendEmsConcurrentRequests($batchRequestParams);

    foreach ($batchRequestKeys as $requestKey) {
        list($warehouseCode, $channelCode) = explode('||', $requestKey, 2);
        $responseData = json_decode($batchResponseResults[$requestKey], true);
        $responseData = $responseData ?: [
            'ask' => 'Fail',
            'message' => '中邮响应JSON解析失败'
        ];

        // 提取核心数据
        $totalFee = isset($responseData['data']['totalFee']) ? floatval($responseData['data']['totalFee']) : null;
        $emsFinalResults[$requestKey] = [
            'warehouse_code' => $warehouseCode,
            'channel_code' => $channelCode,
            'channel_name' => $channelsDict[$channelCode] ?? '未知渠道',
            'total_fee_cny' => $totalFee,
            'status' => $responseData['ask'] ?? 'Unknown',
            'error' => [
                'code' => $responseData['Error']['errCode'] ?? null,
                'message' => $responseData['Error']['errMessage'] ?? ($responseData['message'] ?? null)
            ]
        ];
    }
}

// 计算耗时并填充结果
$result['ems']['request_time_ms'] = round((microtime(true) - $emsRequestStartTime) * 1000, 2);
$result['ems']['results'] = $emsFinalResults;

// 生成排序后的EMS结果
$emsSortableData = [];
foreach ($emsFinalResults as $item) {
    $emsSortableData[] = $item;
}
usort($emsSortableData, function($a, $b) {
    $feeA = $a['total_fee_cny'] ?? PHP_INT_MAX;
    $feeB = $b['total_fee_cny'] ?? PHP_INT_MAX;
    return $feeA <=> $feeB;
});
$result['ems']['sorted_results'] = $emsSortableData;

// ========== 运德物流请求函数 ==========
function sendWedoConcurrentRequests(array $channelCodeGroups, array $commonRequestParams, string $apiUrl, string $appId, string $appToken): array {
    $requestStartTime = microtime(true);
    $multiHandle = curl_multi_init();
    $curlHandles = [];
    $responseResults = [];

    foreach ($channelCodeGroups as $groupKey => $channelCodes) {
        $groupRequestParams = $commonRequestParams;
        $groupRequestParams['channelCode'] = implode(',', $channelCodes);
        
        $requestContentJson = json_encode($groupRequestParams, JSON_UNESCAPED_UNICODE);
        if (!$requestContentJson) continue;

        // 生成签名
        $signParams = [
            'userAccount' => $appId,
            'content' => $requestContentJson
        ];
        ksort($signParams);
        $signString = implode('', $signParams) . $appToken;
        $sign = strtoupper(md5($signString));

        $finalRequestParams = [
            'userAccount' => $appId,
            'content' => $requestContentJson,
            'sign' => $sign
        ];

        $curlHandle = curl_init($apiUrl);
        curl_setopt_array($curlHandle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $finalRequestParams,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_CONNECTTIMEOUT => 5
        ]);
        curl_multi_add_handle($multiHandle, $curlHandle);
        $curlHandles[$groupKey] = $curlHandle;
    }

    $isRunning = null;
    do {
        curl_multi_exec($multiHandle, $isRunning);
        if ($isRunning > 0) {
            curl_multi_select($multiHandle, 0.05);
        }
    } while ($isRunning > 0);

    foreach ($curlHandles as $groupKey => $curlHandle) {
        $responseContent = curl_multi_getcontent($curlHandle);
        $responseData = json_decode($responseContent, true);
        
        $responseResults[$groupKey] = $responseData ?: [
            'error' => "运德响应JSON解析失败，原始响应：{$responseContent}"
        ];
        
        curl_multi_remove_handle($multiHandle, $curlHandle);
        curl_close($curlHandle);
    }
    curl_multi_close($multiHandle);

    // 合并结果
    $mergedResponseData = [];
    foreach ($responseResults as $groupResponse) {
        if (!empty($groupResponse['data']) && is_array($groupResponse['data'])) {
            $mergedResponseData = array_merge($mergedResponseData, $groupResponse['data']);
        }
    }

    $requestTotalTime = round((microtime(true) - $requestStartTime) * 1000, 2);
    return [
        'data' => $mergedResponseData,
        'time' => $requestTotalTime
    ];
}

// ========== 运德物流请求逻辑 ==========
$wedoAllChannelCodes = "AMGD,FEDHDE,FEDHDEMP,UPSGDE,NJUSPSGA,USPSPME,SPEEDXNJ,FDXSPE,UNIUNINJ,GOFONJ,GFYUNNJ,AMGDCA,UPSGW,CAUSPSGA,USPSPMW,USPSGACASG,FEDHDW,FEDHDWMP,UPSGWFBA,SPEEDXCA,CAGLS,FEDSPW,UNIUNICA,GOFOCA,GFYUNCA,FEDIGCA";
$wedoChannelList = array_filter(array_unique(array_map('trim', explode(',', $wedoAllChannelCodes))));
$wedoChannelGroups = array_chunk($wedoChannelList, 5);

$wedoCommonRequestParams = [
    'country' => 'US',
    'city' => $recipientCity,
    'postcode' => $recipientPostcode,
    'weight' => round($packageWeight, 3),
    'length' => $packageLength,
    'width' => $packageWidth,
    'height' => $packageHeight,
    'signatureService' => 0
];

$wedoApiUrl = "http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery";
$wedoResponse = sendWedoConcurrentRequests(
    $wedoChannelGroups,
    $wedoCommonRequestParams,
    $wedoApiUrl,
    WD_APP_ID,
    WD_APP_TOKEN
);

// 处理运德结果
$wedoFinalResults = [];
foreach ($wedoResponse['data'] as $channelCode => $feeDetails) {
    $shipFee = null;
    if (is_numeric($feeDetails['shipFee'] ?? null)) {
        $shipFee = floatval($feeDetails['shipFee']);
    }

    $wedoFinalResults[$channelCode] = [
        'channel_code' => $channelCode,
        'channel_name' => $channelsDict[$channelCode] ?? '未知渠道',
        'ship_fee_original' => $shipFee,
        'ship_fee_cny' => $shipFee ? round($shipFee * 7.0, 2) : null,
        'currency' => $feeDetails['currency'] ?? 'USD',
        'raw_data' => $feeDetails
    ];
}

// 填充运德结果
$result['wedo']['request_time_ms'] = $wedoResponse['time'];
$result['wedo']['results'] = $wedoFinalResults;

// 生成排序后的运德结果
$wedoSortableData = [];
foreach ($wedoFinalResults as $item) {
    $wedoSortableData[] = $item;
}
usort($wedoSortableData, function($a, $b) {
    $feeA = $a['ship_fee_original'] ?? PHP_INT_MAX;
    $feeB = $b['ship_fee_original'] ?? PHP_INT_MAX;
    return $feeA <=> $feeB;
});
$result['wedo']['sorted_results'] = $wedoSortableData;

// ========== 总耗时计算 ==========
$result['meta']['total_execution_time_ms'] = round((microtime(true) - $totalExecutionStartTime) * 1000, 2);

// ========== 输出JSON ==========
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

$shisuanyunfei = json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
$global_order_no = $orderNumber;

//将$shisuanyunfei $global_order_no，存入yunfei数据表
include '../admin-panel/config/config.php';
/*
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("INSERT INTO yunfei (global_order_no, shisuanyunfei) VALUES (:global_order_no, :shisuanyunfei)");
    $stmt->bindParam(':global_order_no', $global_order_no);
    $stmt->bindParam(':shisuanyunfei', $shisuanyunfei);
    $stmt->execute();
} catch (PDOException $e) {
    // 这里可以选择记录错误日志，但不影响主流程
}
*/
// 将 $shisuanyunfei、$global_order_no 存入 yunfei 数据表（存在则更新，不存在则插入）

try {
    // 初始化PDO连接
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4", // 建议指定字符集
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    // 核心SQL：INSERT + ON DUPLICATE KEY UPDATE（依赖global_order_no的唯一索引）
    $sql = "INSERT INTO yunfei (global_order_no, shisuanyunfei) 
            VALUES (:global_order_no, :shisuanyunfei) 
            ON DUPLICATE KEY UPDATE 
                shisuanyunfei = VALUES(shisuanyunfei)"; // VALUES() 引用插入时的参数值

    // 预处理语句
    $stmt = $pdo->prepare($sql);
    
    // 绑定参数（支持字符串/数字等类型，PDO自动处理）
    $stmt->bindParam(':global_order_no', $global_order_no);
    $stmt->bindParam(':shisuanyunfei', $shisuanyunfei);
    
    // 执行语句
    $stmt->execute();

    // 可选：获取受影响的行数（插入=1，更新=2）
    // $affectedRows = $stmt->rowCount();

} catch (PDOException $e) {
    // 错误处理（可选：记录日志/输出调试信息）
    // error_log("运费数据操作失败：" . $e->getMessage());
    // die("数据库错误：" . $e->getMessage()); // 调试时启用，生产环境注释
} finally {
    // 可选：关闭连接（PHP会自动回收，高并发场景建议手动关闭）
    // $pdo = null;
}





?>