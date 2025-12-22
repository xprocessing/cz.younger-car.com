<?php
/**
 * 运德海外仓运费试算接口 - 简化版Demo（增加请求耗时统计）
 */
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

// -------------------------- 1. 配置信息 (请替换为你的真实信息) --------------------------
$userAccount = "pjV391564";
$testToken = "88C5D8B292E5B6153803682554FBA4F8";
$apiUrl = "http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery";

// -------------------------- 2. 待请求的业务参数 (根据实际需求修改) --------------------------
$contentParams = [
    'channelCode' => 'AMGD,UPSGDE',
    'country' => 'US',
    'city' => 'LOS ANGELES',
    'postcode' => '90001',
    'weight' => '0.079',
    'length' => 26,
    'width' => 20,
    'height' => 2,
    'signatureService' => 0
];

// -------------------------- 3. 核心逻辑：生成签名并发送请求 --------------------------
try {
    // 记录开始时间（精确到微秒）
    $startTime = microtime(true);

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

    // 记录结束时间（精确到微秒）
    $endTime = microtime(true);

    // 计算耗时（秒数，保留6位小数）
    $costTime = number_format($endTime - $startTime, 6);

    // 解析并输出结果
    $result = json_decode($response, true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("响应解析失败，原始响应: " . $response);
    }

    // -------------------------- 4. 格式化输出结果 --------------------------
    echo "=== 请求成功 ===\n";
    echo "请求耗时: {$costTime} 秒\n"; // 输出耗时
    echo "响应数据:\n" . json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

    if ($result['errCode'] == 200 && !empty($result['data']['data'])) {
        echo "=== 运费详情 ===\n";
        foreach ($result['data']['data'] as $channel => $feeInfo) {
            echo "渠道: $channel\n";
            echo "  - 运费: {$feeInfo['shipFee']} {$feeInfo['currency']}\n";
            echo "  - 记录号: {$feeInfo['recordCode']}\n\n";
        }
    } else {
        echo "=== 业务错误 ===\n";
        echo "错误码: " . $result['errCode'] . "\n";
        echo "错误信息: " . $result['errMsg'] . "\n";
    }

} catch (Exception $e) {
    echo "=== 请求异常 ===\n";
    echo "错误信息: " . $e->getMessage() . "\n";
}