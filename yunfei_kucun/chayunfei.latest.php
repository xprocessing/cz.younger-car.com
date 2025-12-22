<?php
// 根据订单的邮编、重量、尺寸、城市，查询中邮 EMS 和 运德物流运费
// 支持 GET 参数：city, postcode, weight, length, width, height
// 示例测试 URL:
// https://cz.younger-car.com/chayunfei.php?postcode=92113-3931&weight=7.1928&length=74.0&width=27.0&height=18.0&city=San%20Diego

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: text/html; charset=utf-8");

require_once 'key.php'; // 必须包含：EMS_TOKEN, EMS_KEY, WD_APP_ID, WD_APP_TOKEN
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
// ========== 全局计时（优化：明确计时用途） ==========
$totalExecutionStartTime = microtime(true);

// ========== 输入参数处理（优化：变量名加业务前缀，避免模糊） ==========
$orderNumber = trim($_GET['global_order_no'] ?? '');
echo "<h1>🌐 订单号: " . htmlspecialchars($orderNumber ?: 'N/A') . "</h1>";

$recipientCity = trim($_GET['city'] ?? '');
$recipientPostcode = trim($_GET['postcode'] ?? '');
$packageWeight = max(0.001, floatval($_GET['weight'] ?? 0)); // 单位：kg（假设）
$packageLength = max(1, round(floatval($_GET['length'] ?? 0), 1)); // 单位：cm（假设）
$packageWidth = max(1, round(floatval($_GET['width'] ?? 0), 1));
$packageHeight = max(1, round(floatval($_GET['height'] ?? 0), 1));
$receiver_country_code =trim($_GET['receiver_country_code'] ?? '');
// 参数校验（优化：提示更具体）
if (empty($recipientPostcode) || empty($recipientCity)) {
    die('<h2>错误：缺少必要参数【收件城市（city）】或【收件邮编（postcode）】</h2>');
}

// ========== 基础请求参数（优化：数组键名语义化，加注释说明） ==========
if($receiver_country_code=='UK'){
    $receiver_country_code='GB';
}
$baseRequestParams = [
    //"country_code" => "US", // 目标国家代码（固定美国）
    "country_code" => $receiver_country_code,
    "postcode"     => (string)$recipientPostcode, // 收件邮编
    "type"         => 1, // 商品类型（1：普通货物，根据接口文档确认）
    "weight"       => round($packageWeight, 3), // 包裹重量（保留3位小数）
    "length"       => $packageLength, // 包裹长
    "width"        => $packageWidth, // 包裹宽
    "height"       => $packageHeight, // 包裹高
    "pieces"       => 1 // 包裹件数（固定1件）
];

// ========== 中邮物流配置（优化：变量名加ems前缀，明确归属） ==========
$emsWarehouseCodes = "USWE,USEA,UK"; // 中邮仓库编码（USWE：美国西部仓，USEA：美国东部仓）
$emsChannelCodes = "FEDEX-GROUND-EA,SS-FEDEX-G-E,FEDEX-SM,FEDEX-LP,AMAZON-GROUND,USPS-FIRST-CLASS,USPS-PRIORITY,DHL-US-SP,DHL-US-BP,YUN-GROUND,CE-PARCEL,CE-GROUND-EA,UPS-GROUND-EA,UPS-GROUND-MULT,UPS-SUREPOST,UPS-2ND-DAY,DX-EXPRESS,DPD-UK,DPD-MULTI,YODEL48H-S,YODEL48H-L,XDP-EP_A-C,UK-EVRI,UK-EVRI-L"; // 中邮渠道编码

// 解析为去重数组（优化：变量名更清晰）
$emsWarehouseList = array_filter(array_unique(array_map('trim', explode(',', $emsWarehouseCodes))));
$emsChannelList = array_filter(array_unique(array_map('trim', explode(',', $emsChannelCodes))));

// ========== 中邮并发请求函数（优化：参数/返回值命名语义化，加类型提示） ==========
/**
 * 并发发送中邮物流运费查询请求
 * @param array $requestParamsList 批量请求参数（键：仓库+渠道唯一标识，值：单条请求参数）
 * @return array 批量响应结果（键：仓库+渠道唯一标识，值：接口响应数据）
 */
function sendEmsConcurrentRequests(array $requestParamsList): array {
    if (empty($requestParamsList)) return [];

    $multiHandle = curl_multi_init();
    $curlHandles = [];
    $responseResults = [];

    // 中邮接口配置（优化：常量名加EMS前缀，避免全局污染）
    $emsApiUrl = "http://cpws.ems.com.cn/default/svc/web-service";
    $emsAppToken = EMS_TOKEN;
    $emsAppKey = EMS_KEY;

    foreach ($requestParamsList as $requestKey => $params) {
        // 构建SOAP请求XML（优化：变量名明确XML用途）
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

        // 初始化curl（优化：变量名加curl前缀，清晰区分）
        $curlHandle = curl_init();
        curl_setopt_array($curlHandle, [
            CURLOPT_URL => $emsApiUrl,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_TIMEOUT => 12, // 超时时间（12秒）
            CURLOPT_CONNECTTIMEOUT => 5, // 连接超时（5秒）
            CURLOPT_POSTFIELDS => $soapRequestXml,
            CURLOPT_HTTPHEADER => ['Content-Type: text/xml; charset=utf-8'],
            CURLOPT_SSL_VERIFYPEER => false, // 禁用SSL证书校验（生产环境建议开启）
            CURLOPT_TCP_KEEPALIVE => 1, // 开启TCP保活
        ]);

        curl_multi_add_handle($multiHandle, $curlHandle);
        $curlHandles[$requestKey] = $curlHandle;
    }

    // 执行并发请求（优化：变量名明确运行状态）
    $isRunning = null;
    do {
        curl_multi_exec($multiHandle, $isRunning);
        if ($isRunning > 0) {
            curl_multi_select($multiHandle, 0.05); // 等待0.05秒，避免CPU空转
        }
    } while ($isRunning > 0);

    // 处理响应结果（优化：错误信息更具体）
    foreach ($curlHandles as $requestKey => $curlHandle) {
        $responseContent = curl_multi_getcontent($curlHandle);
        $curlError = curl_error($curlHandle);

        if ($curlError) {
            $responseResults[$requestKey] = json_encode([
                'ask' => 'Fail',
                'message' => "中邮请求cURL错误：{$curlError}"
            ]);
        } else {
            // 提取response标签内容（优化：正则匹配加注释）
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

// ========== 构建中邮请求列表（优化：变量名明确请求归属） ==========
$emsRequestParamsList = [];
$emsRequestKeys = [];

foreach ($emsWarehouseList as $warehouseCode) {
    foreach ($emsChannelList as $channelCode) {
        $requestParams = $baseRequestParams;
        $requestParams["warehouse_code"] = $warehouseCode; // 仓库编码
        $requestParams["shipping_method"] = strtoupper($channelCode); // 物流渠道（大写统一格式）
        
        $requestKey = "$warehouseCode||$channelCode"; // 唯一标识（仓库+渠道）
        $emsRequestParamsList[$requestKey] = $requestParams;
        $emsRequestKeys[] = $requestKey;
    }
}

// ⏱️ 中邮请求计时（优化：计时变量名明确归属）
$emsRequestStartTime = microtime(true);

// 并发执行（分批，每批最多12个，避免接口限流）（优化：变量名明确分批逻辑）
$emsFinalResults = [];
$emsMaxBatchSize = 12;

for ($i = 0; $i < count($emsRequestKeys); $i += $emsMaxBatchSize) {
    $batchRequestKeys = array_slice($emsRequestKeys, $i, $emsMaxBatchSize);
    $batchRequestParams = array_intersect_key($emsRequestParamsList, array_flip($batchRequestKeys));
    $batchResponseResults = sendEmsConcurrentRequests($batchRequestParams);

    // 解析批量响应（优化：变量名明确解析逻辑）
    foreach ($batchRequestKeys as $requestKey) {
        list($warehouseCode, $channelCode) = explode('||', $requestKey, 2);
        $responseData = json_decode($batchResponseResults[$requestKey], true);
        $emsFinalResults[$warehouseCode][$channelCode] = $responseData ?: [
            'ask' => 'Fail',
            'message' => '中邮响应JSON解析失败'
        ];
    }
}

// 计算中邮请求耗时（优化：变量名明确耗时归属）
$emsRequestTotalTime = round((microtime(true) - $emsRequestStartTime) * 1000, 2);

// ========== 输出样式（无变量优化，保持原有样式） ==========
echo '<style>
body { font-family: Arial, sans-serif; margin: 20px; }
table { border-collapse: collapse; width: 100%; margin: 15px 0; }
th, td { border: 1px solid #999; padding: 6px 10px; text-align: left; }
th { background-color: #f2f2f2; }
.fail { color: #d8000c; }
.success { color: #008000; }
</style>';

// ========== 中邮结果表格（优化：变量名明确数据归属，注释清晰） ==========
echo "<h2>📦 中邮 EMS 物流费用（按 totalFee 升序 | 耗时: {$emsRequestTotalTime} ms）</h2>";

// 整理可排序数据（优化：数组键名语义化）
$emsSortableData = [];
foreach ($emsFinalResults as $warehouseCode => $channelResults) {
    foreach ($channelResults as $channelCode => $responseInfo) {
        // 提取总费用（无费用时设为最大值，确保排在最后）
        $totalFee = isset($responseInfo['data']['totalFee']) ? floatval($responseInfo['data']['totalFee']) : PHP_INT_MAX;
        
        $emsSortableData[] = [
            'warehouse_code' => $warehouseCode,
            'channel_code' => $channelCode,
            'total_fee' => $totalFee,
            'request_status' => $responseInfo['ask'] ?? '-',
            'error_code' => $responseInfo['Error']['errCode'] ?? '-',
            'error_message' => $responseInfo['Error']['errMessage'] ?? '-'
        ];
    }
}

// 按总费用升序排序（优化：排序逻辑注释明确）
usort($emsSortableData, fn($a, $b) => $a['total_fee'] <=> $b['total_fee']);

// 渲染表格（优化：表格列名更清晰）
echo '<table>';
echo '<thead><tr>
<th>仓库编码</th><th>物流渠道代码</th><th>物流渠道</th><th>总费用 (CNY)</th><th>请求状态</th><th>错误码</th><th>错误信息</th>
</tr></thead><tbody>';

foreach ($emsSortableData as $item) {
    $feeDisplay = $item['total_fee'] === PHP_INT_MAX ? '-' : number_format($item['total_fee'], 2);
    $statusClass = (isset($item['request_status']) && strtoupper($item['request_status']) === 'SUCCESS') ? 'success' : 'fail';
    
    echo "<tr>
        <td><strong>{$item['warehouse_code']}</strong></td>
        <td>{$item['channel_code']}</td>
        <td>{$channelsDict[$item['channel_code']]}</td>
        <td>{$feeDisplay}</td>
        <td class=\"{$statusClass}\">{$item['request_status']}</td>
        <td>{$item['error_code']}</td>
        <td>{$item['error_message']}</td>
    </tr>";
}
echo '</tbody></table>';

// ========== 运德物流并发函数（优化：参数/返回值命名语义化，加类型提示和注释） ==========
/**
 * 并发发送运德物流运费查询请求
 * @param array $channelCodeGroups 渠道编码分组（每组最多5个，避免接口限流）
 * @param array $commonRequestParams 公共请求参数（重量、尺寸、收件信息等）
 * @param string $apiUrl 运德接口URL
 * @param string $appId 运德APP ID
 * @param string $appToken 运德APP Token
 * @return array 合并后的响应数据（含总耗时）：['data' => 运费结果数组, 'time' => 耗时毫秒]
 */
function sendWedoConcurrentRequests(array $channelCodeGroups, array $commonRequestParams, string $apiUrl, string $appId, string $appToken): array {
    $requestStartTime = microtime(true);
    $multiHandle = curl_multi_init();
    $curlHandles = [];
    $responseResults = [];

    foreach ($channelCodeGroups as $groupKey => $channelCodes) {
        // 构建单组请求参数（优化：变量名明确参数用途）
        $groupRequestParams = $commonRequestParams;
        $groupRequestParams['channelCode'] = implode(',', $channelCodes); // 批量渠道编码
        
        // 序列化参数（优化：JSON编码注释明确）
        $requestContentJson = json_encode($groupRequestParams, JSON_UNESCAPED_UNICODE);
        if (!$requestContentJson) {
            continue; // JSON编码失败跳过该组
        }

        // 构建签名参数（优化：签名逻辑注释清晰）
        $signParams = [
            'userAccount' => $appId,
            'content' => $requestContentJson
        ];
        ksort($signParams); // 按键名排序（接口要求）
        $signString = implode('', $signParams) . $appToken; // 拼接签名字符串
        $sign = strtoupper(md5($signString)); // MD5加密并转大写

        // 最终请求参数（优化：变量名明确归属）
        $finalRequestParams = [
            'userAccount' => $appId,
            'content' => $requestContentJson,
            'sign' => $sign
        ];

        // 初始化curl（优化：变量名清晰）
        $curlHandle = curl_init($apiUrl);
        curl_setopt_array($curlHandle, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $finalRequestParams,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false, // 禁用SSL证书校验（生产环境建议开启）
            CURLOPT_TIMEOUT => 20, // 超时时间（20秒）
            CURLOPT_CONNECTTIMEOUT => 5 // 连接超时（5秒）
        ]);
        curl_multi_add_handle($multiHandle, $curlHandle);
        $curlHandles[$groupKey] = $curlHandle;
    }

    // 执行并发请求（优化：变量名明确运行状态）
    $isRunning = null;
    do {
        curl_multi_exec($multiHandle, $isRunning);
        if ($isRunning > 0) {
            curl_multi_select($multiHandle, 0.05); // 等待0.05秒，避免CPU空转
        }
    } while ($isRunning > 0);

    // 处理响应结果（优化：错误信息更具体）
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

    // 合并所有组的结果（优化：合并逻辑注释明确）
    $mergedResponseData = [];
    foreach ($responseResults as $groupResponse) {
        if (!empty($groupResponse['data']) && is_array($groupResponse['data'])) {
            $mergedResponseData = array_merge($mergedResponseData, $groupResponse['data']);
        }
    }

    // 计算总耗时（优化：变量名明确耗时归属）
    $requestTotalTime = round((microtime(true) - $requestStartTime) * 1000, 2);
    return [
        'data' => $mergedResponseData,
        'time' => $requestTotalTime
    ];
}

// ========== 运德物流请求（优化：变量名加wedo前缀，明确归属，注释清晰） ==========
$wedoAllChannelCodes = "AMGD,FEDHDE,FEDHDEMP,UPSGDE,NJUSPSGA,USPSPME,SPEEDXNJ,FDXSPE,UNIUNINJ,GOFONJ,GFYUNNJ,AMGDCA,UPSGW,CAUSPSGA,USPSPMW,USPSGACASG,FEDHDW,FEDHDWMP,UPSGWFBA,SPEEDXCA,CAGLS,FEDSPW,UNIUNICA,GOFOCA,GFYUNCA,FEDIGCA";
$wedoChannelList = array_filter(array_unique(array_map('trim', explode(',', $wedoAllChannelCodes))));
$wedoChannelGroups = array_chunk($wedoChannelList, 5); // 每组5个渠道（接口限制批量数量）

// 运德公共请求参数（优化：数组键名语义化，加注释）
$wedoCommonRequestParams = [
    'country' => 'US', // 目标国家代码
    'city' => $recipientCity, // 收件城市
    'postcode' => $recipientPostcode, // 收件邮编
    'weight' => round($packageWeight, 3), // 包裹重量（保留3位小数）
    'length' => $packageLength, // 包裹长
    'width' => $packageWidth, // 包裹宽
    'height' => $packageHeight, // 包裹高
    'signatureService' => 0 // 是否需要签名服务（0：不需要，1：需要）
];

// 发送运德并发请求（优化：变量名明确响应归属）
$wedoApiUrl = "http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery";
$wedoResponse = sendWedoConcurrentRequests(
    $wedoChannelGroups,
    $wedoCommonRequestParams,
    $wedoApiUrl,
    WD_APP_ID,
    WD_APP_TOKEN
);

$wedoFeeResults = $wedoResponse['data'];
$wedoRequestTotalTime = $wedoResponse['time'];

// ========== 运德结果表格（优化：变量名语义化，注释清晰） ==========
echo "<h2>🚚 运德物流费用（按 shipFee 升序 | 耗时: {$wedoRequestTotalTime} ms）（汇率 1 USD ≈ 7.0 CNY）</h2>";

// 整理可排序数据（优化：数组键名明确含义）
$wedoSortableData = [];
foreach ($wedoFeeResults as $channelCode => $feeDetails) {
    // 提取运费（处理非数字情况）
    $shipFeeRaw = $feeDetails['shipFee'] ?? null;
    if (is_numeric($shipFeeRaw) || (is_string($shipFeeRaw) && is_numeric(trim($shipFeeRaw)))) {
        $shipFee = floatval($shipFeeRaw);
    } else {
        $shipFee = PHP_INT_MAX; // 无效运费设为最大值，排在最后
    }
    
    $wedoSortableData[] = [
        'channel_code' => $channelCode,
        'ship_fee' => $shipFee,
        'currency' => $feeDetails['currency'] ?? 'USD' // 默认为美元（根据接口文档确认）
    ];
}

// 按运费升序排序（优化：排序逻辑注释明确）
usort($wedoSortableData, fn($a, $b) => $a['ship_fee'] <=> $b['ship_fee']);

// 渲染表格（优化：表格列名更清晰）
echo '<table><thead><tr>
<th>#</th><th>物流渠道代码</th><th>物流渠道</th><th>费用 (CNY)</th><th>原币种</th>
</tr></thead><tbody>';

$displayIndex = 1;
foreach ($wedoSortableData as $item) {
    if ($item['ship_fee'] === PHP_INT_MAX) continue; // 跳过无效运费项
    
    $cnyFee = number_format($item['ship_fee'] * 7.0, 2); // 美元转人民币（汇率7.0）
    echo "<tr>
        <td>{$displayIndex}</td>
        <td>{$item['channel_code']}</td>
        <td>{$channelsDict[$item['channel_code']]}</td>
        <td>{$cnyFee}</td>
        <td>{$item['currency']}</td>
    </tr>";
    $displayIndex++;
}

// 显示无效运费项（优化：单独处理，清晰区分）
foreach ($wedoSortableData as $item) {
    if ($item['ship_fee'] !== PHP_INT_MAX) continue;
    
    echo "<tr>
        <td>-</td>
        <td>{$item['channel_code']}</td>
        <td>{$channelsDict[$item['channel_code']]}</td>
        <td>-</td>
        <td>{$item['currency']}</td>
    </tr>";
}

// 无数据时显示提示（优化：提示更友好）
if (empty($wedoSortableData)) {
    echo '<tr><td colspan="4">运德物流暂无返回数据</td></tr>';
}
echo '</tbody></table>';

// ========== 总耗时统计（优化：变量名明确统计对象） ==========
$totalExecutionTime = round((microtime(true) - $totalExecutionStartTime) * 1000, 2);
echo "<p><strong>⏱️ 总执行时间: {$totalExecutionTime} 毫秒</strong></p>";
?>