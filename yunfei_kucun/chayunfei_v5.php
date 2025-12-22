<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once 'key.php'; // 必须包含：EMS_TOKEN, EMS_KEY, WD_APP_ID, WD_APP_TOKEN

$city      = trim(isset($_GET['city']) ? $_GET['city'] : '');
$postcode  = trim(isset($_GET['postcode']) ? $_GET['postcode'] : '');
$weight    = max(0.001, floatval(isset($_GET['weight']) ? $_GET['weight'] : 0));
$length    = max(1, round(floatval(isset($_GET['length']) ? $_GET['length'] : 0), 1));
$width     = max(1, round(floatval(isset($_GET['width'])  ? $_GET['width']  : 0), 1));
$height    = max(1, round(floatval(isset($_GET['height']) ? $_GET['height'] : 0), 1));

// 中邮配置
$warehouse = "USWE,USEA";
$channel   = "FEDEX-GROUND-EA,SS-FEDEX-G-E,FEDEX-SM,FEDEX-LP,AMAZON-GROUND,USPS-FIRST-CLASS,USPS-PRIORITY,DHL-US-SP,DHL-US-BP,YUN-GROUND,CE-PARCEL,CE-GROUND-EA,UPS-GROUND-EA,UPS-GROUND-MULT,UPS-SUREPOST,UPS-2ND-DAY,INT-PRI-SP,INT-PRI-LP,IPA-INT-ECONOMIC,US-G2G-INT,US-UPS-INT";

$warehouseList = array_filter(array_unique(array_map('trim', explode(',', $warehouse))));
$channelList   = array_filter(array_unique(array_map('trim', explode(',', $channel))));

// ========== SOAP 调用 ==========
function callEmsSoap($service, $paramsJson)
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

    $start = microtime(true);
    $ch = curl_init();
    curl_setopt_array($ch, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_TIMEOUT        => 20,
        CURLOPT_POSTFIELDS     => $xml,
        CURLOPT_HTTPHEADER     => array('Content-Type: text/xml; charset=utf-8'),
        CURLOPT_SSL_VERIFYPEER => false,
    ));
    $resp = curl_exec($ch);
    $err  = curl_error($ch);
    curl_close($ch);

    $time = round((microtime(true) - $start) * 1000, 2);

    if ($err) {
        return array('data' => json_encode(array('ask' => 'Fail', 'message' => 'cURL Error: ' . $err)), 'time' => $time);
    }
    preg_match('#<response>(.*?)</response>#s', $resp, $m);
    $responseData = isset($m[1]) ? $m[1] : json_encode(array('ask' => 'Fail', 'message' => 'No <response> tag'));

    return array('data' => $responseData, 'time' => $time);
}

// ========== 中邮批量查询 ==========
$baseParams = array(
    "country_code" => "US",
    "postcode"     => (string)$postcode,
    "type"         => 1,
    "weight"       => round($weight, 3),
    "length"       => $length,
    "width"        => $width,
    "height"       => $height,
    "pieces"       => 1
);

$finalResult = array();
foreach ($warehouseList as $wh) {
    foreach ($channelList as $ch) {
        $params = $baseParams;
        $params["warehouse_code"]  = $wh;
        $params["shipping_method"] = strtoupper($ch);

        $paramsJson   = json_encode($params, JSON_UNESCAPED_UNICODE);
        $soapResponse = callEmsSoap('getCalculateFee', $paramsJson);
        $raw          = $soapResponse['data'];
        $timeSpent    = $soapResponse['time'];

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            $data = array('ask' => 'Fail', 'message' => 'JSON parse error');
        }

        $finalResult[$wh][$ch] = $data;
        $finalResult[$wh][$ch]['time'] = $timeSpent;
    }
}

// ========== 运德并发查询 ==========
function concurrentWedoRequests($channelGroups, $commonParams, $apiUrl, $userAccount, $testToken)
{
    $start = microtime(true);
    $mh = curl_multi_init();
    $handles = array();

    foreach ($channelGroups as $groupKey => $codes) {
        $contentParams = $commonParams;
        $contentParams['channelCode'] = implode(',', $codes);
        $contentJson = json_encode($contentParams, JSON_UNESCAPED_UNICODE);

        $requestData = array(
            'userAccount' => $userAccount,
            'content'     => $contentJson
        );
        ksort($requestData);
        $requestData['sign'] = strtoupper(md5(implode('', $requestData) . $testToken));

        $ch = curl_init($apiUrl);
        curl_setopt_array($ch, array(
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $requestData,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT        => 60
        ));
        curl_multi_add_handle($mh, $ch);
        $handles[$groupKey] = $ch;
    }

    $running = null;
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    $results = array();
    foreach ($handles as $key => $ch) {
        $resp = curl_multi_getcontent($ch);
        $results[$key] = curl_errno($ch) ? array('error' => curl_error($ch)) : json_decode($resp, true);
        curl_multi_remove_handle($mh, $ch);
        curl_close($ch);
    }
    curl_multi_close($mh);

    $merged = array();
    foreach ($results as $r) {
        if (isset($r['data']) && is_array($r['data'])) {
            $merged = array_merge($merged, $r['data']);
        }
    }

    $totalTime = round((microtime(true) - $start) * 1000, 2);
    return array('data' => $merged, 'total_time' => $totalTime);
}

$allChannelCodes = "AMGD,FEDHDE,FEDHDEMP,UPSGDE,NJUSPSGA,USPSPME,SPEEDXNJ,FDXSPE,UNIUNINJ,GOFONJ,GFYUNNJ,AMGDCA,UPSGW,CAUSPSGA,USPSPMW,USPSGACASG,FEDHDW,FEDHDWMP,UPSGWFBA,SPEEDXCA,CAGLS,FEDSPW,UNIUNICA,GOFOCA,GFYUNCA,FEDIGCA";
$channelArray    = array_filter(array_unique(array_map('trim', explode(',', $allChannelCodes))));
$channelGroups   = array_chunk($channelArray, 5);

$wedoCommonParams = array(
    'country'           => 'US',
    'city'              => $city,
    'postcode'          => $postcode,
    'weight'            => round($weight, 3),
    'length'            => $length,
    'width'             => $width,
    'height'            => $height,
    'signatureService'  => 0
);

$wedoApiUrl      = "http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery";
$wedoUserAccount = WD_APP_ID;
$wedoTestToken   = WD_APP_TOKEN;

$wedoResponse = concurrentWedoRequests($channelGroups, $wedoCommonParams, $wedoApiUrl, $wedoUserAccount, $wedoTestToken);
$wedoResults  = isset($wedoResponse['data']) ? $wedoResponse['data'] : array();
$wedoTotalTime = isset($wedoResponse['total_time']) ? $wedoResponse['total_time'] : 0;

// ====================== 输出页面 ======================
echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>运费查询工具</title>
<style>
body{font-family:Arial,sans-serif;background:#f9f9f9;padding:20px;margin:0;}
h2{color:#333;margin:40px 0 20px;}
table{border-collapse:collapse;width:100%;background:#fff;margin:20px 0;box-shadow:0 1px 3px rgba(0,0,0,0.1);}
th,td{border:1px solid #ddd;padding:10px 12px;text-align:left;}
th{background:#f5f5f5;font-weight:bold;}
.gold{background:#fffbe6;border:2px solid #d4a017;color:#d48000;font-weight:bold;border-radius:6px;padding:6px 12px;}
.silver{background:#fff8e7;color:#e67e22;font-weight:bold;border-radius:4px;padding:4px 8px;}
.gray{color:#999;}
tr:hover{background:#f9f9f9;}
</style></head><body>';

// ========== 1. 中邮 EMS 排序输出 ==========
echo '<h2>中邮EMS 物流费用（按 totalFee 从低到高排序）</h2>';
if (!is_array($finalResult) || empty($finalResult)) {
    echo '<p style="color:#d8000c;">中邮EMS 接口全部超时或失败</p>';
} else {
    $rows = array();
    foreach ($finalResult as $wh => $channels) {
        if (!is_array($channels)) continue;
        foreach ($channels as $ch => $info) {
            if (!is_array($info)) continue;
            $fee = isset($info['data']['totalFee']) ? $info['data']['totalFee'] : null;
            $feeNum = (is_numeric($fee) && $fee > 0) ? (float)$fee : PHP_FLOAT_MAX;

            $rows[] = array(
                'wh'   => $wh,
                'ch'   => $ch,
                'info' => $info,
                'fee'  => $feeNum
            );
        }
    }

    // 手动排序（兼容老PHP）
    usort($rows, function($a, $b) {
        if ($a['fee'] == $b['fee']) return 0;
        return ($a['fee'] < $b['fee']) ? -1 : 1;
    });

    echo '<table><thead><tr>
        <th>仓库</th><th>渠道</th><th>SHIPPING</th><th>totalFee</th>
        <th>结果</th><th>错误码</th><th>错误信息</th><th>耗时(ms)</th>
        </tr></thead><tbody>';

    foreach ($rows as $i => $r) {
        $info = $r['info'];
        $ask  = isset($info['ask']) ? $info['ask'] : 'Fail';
        $totalFee = isset($info['data']['totalFee']) ? $info['data']['totalFee'] : '-';
        $shipping = isset($info['data']['SHIPPING']) ? $info['data']['SHIPPING'] : '-';
        $errCode  = isset($info['Error']['errCode']) ? $info['Error']['errCode'] : '-';
        $errMsg   = isset($info['Error']['errMessage']) ? $info['Error']['errMessage'] : '-';
        $time     = isset($info['time']) ? $info['time'] : '-';

        $feeClass = ($r['fee'] >= PHP_FLOAT_MAX) ? 'gray' : ($i == 0 ? 'gold' : ($i <= 2 ? 'silver' : ''));

        echo "<tr>
            <td><strong>{$r['wh']}</strong></td>
            <td>{$r['ch']}</td>
            <td>{$shipping}</td>
            <td class=\"$feeClass\">{$totalFee}</td>
            <td>" . (strtoupper($ask) == 'SUCCESS' ? $ask : "<span style='color:#d8000c'>$ask</span>") . "</td>
            <td>{$errCode}</td>
            <td>{$errMsg}</td>
            <td>{$time}</td>
        </tr>";
    }
    echo '</tbody></table>';
}

// ========== 2. 运德物流排序输出 ==========
$totalTimeText = $wedoTotalTime ? $wedoTotalTime . ' ms' : '超时/失败';
echo '<h2>运德物流 费用查询（按 shipFee 精确从低到高排序，总耗时：'.$totalTimeText.'）</h2>';

if (!is_array($wedoResults) || empty($wedoResults)) {
    echo '<p style="color:#d8000c;">运德接口无响应或全部失败</p>';
} else {
    $rows = array();
    foreach ($wedoResults as $method => $detail) {
        if (!is_array($detail)) continue;
        $raw = isset($detail['shipFee']) ? $detail['shipFee'] : '';

        if (preg_match('/[\d\.]+/', $raw, $m)) {
            $feeNum = (float)$m[0];
        } else {
            $feeNum = PHP_FLOAT_MAX;
        }

        $rows[] = array(
            'method'  => $method,
            'detail'  => $detail,
            'fee_num' => $feeNum,
            'fee_txt' => $raw ? $raw : '-'
        );
    }

    usort($rows, function($a, $b) {
        if ($a['fee_num'] == $b['fee_num']) return 0;
        return ($a['fee_num'] < $b['fee_num']) ? -1 : 1;
    });

    echo '<table><thead><tr>
        <th width="280">运德渠道</th>
        <th width="160">费用 CNY</th>
        <th>货币单位</th>
        </tr></thead><tbody>';

    foreach ($rows as $i => $r) {
        $feeClass = ($r['fee_num'] >= PHP_FLOAT_MAX) ? 'gray' : ($i == 0 ? 'gold' : ($i <= 2 ? 'silver' : ''));

        echo "<tr>
            <td><strong>{$r['method']}</strong></td>
            <td class=\"$feeClass\">{$r['fee_txt']}</td>
            <td>" . (isset($r['detail']['currency']) ? $r['detail']['currency'] : 'CNY') . "</td>
        </tr>";
    }
    echo '</tbody></table>';
}

echo '</body></html>';
?>