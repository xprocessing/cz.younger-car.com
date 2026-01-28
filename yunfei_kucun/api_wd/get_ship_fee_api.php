<?php

/**
 * 运德海外仓运费试算接口 Demo
 * 包含签名生成、POST 请求发送、响应解析
 */
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

/*
   'channelCode' => 'AMGDCA,CAUSPSGA',  // 渠道简码（多个用逗号分隔）
        'country' => 'US',                 // 国家简码
        'city' => 'LOS ANGELES',           // 收件人城市
        'postcode' => '90001',             // 收件人邮编
        'weight' => '0.079',               // 重量（kg）
        'length' => 26,                    // 长（cm）
        'width' => 20,                     // 宽（cm）
        'height' => 2,                     // 高（cm）

*/
$channels = $_GET['channels'];
$country = $_GET['country'];
$city = $_GET['city'];
$postcode = $_GET['postcode'];
$weight = $_GET['weight'];
$length = $_GET['length'];
$width = $_GET['width'];
$height = $_GET['height'];



require_once __DIR__ . '/../../config.php';
// -------------------------- 配置信息 --------------------------
// 替换为你的用户账号（联系运德客服获取）
$userAccount = WD_APP_ID;
// 替换为你的授权 token（联系运德客服获取）
$testToken = WD_APP_TOKEN;
// 运费试算接口地址
$apiUrl = "http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery";
// -------------------------- 配置信息结束 --------------------------

/**
 * 发送 POST 请求（form-data 格式）
 * @param string $url 接口地址
 * @param array $data 请求参数（关联数组）
 * @return string 接口响应内容
 */
function curlPostData($url, $data)
{
    $ch = curl_init();

    // 设置请求地址
    curl_setopt($ch, CURLOPT_URL, $url);
    // 禁止输出 header
    curl_setopt($ch, CURLOPT_HEADER, 0);
    // 允许重定向
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    // 禁用 SSL 证书校验（正式环境建议开启）
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    // 设置为 POST 请求
    curl_setopt($ch, CURLOPT_POST, 1);
    // 设置 POST 数据（自动编码为 form-data 格式）
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    // 要求返回响应内容
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 设置超时时间（60秒）
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    // 发送请求（最多重试3次）
    $cnt = 0;
    $result = false;
    while ($cnt < 3 && $result === false) {
        $result = curl_exec($ch);
        $cnt++;
    }

    // 捕获 curl 错误
    if (curl_errno($ch)) {
        $errorMsg = "CURL 请求失败：" . curl_error($ch);
        curl_close($ch);
        throw new Exception($errorMsg);
    }

    curl_close($ch);
    return $result;
}

/**
 * 生成签名串
 * @param array $data 待签名的参数（关联数组）
 * @return string 大写 MD5 签名
 */
function createSignature($data)
{
    global $testToken;

    // 1. 按参数名升序排序
    ksort($data);

    // 2. 拼接所有参数的值（sign 字段不参与）
    $signStr = '';
    foreach ($data as $key => $value) {
        if ($key !== 'sign') {
            $signStr .= $value;
        }
    }

    // 3. 拼接 token 后进行 MD5 加密，再转大写
    $signature = strtoupper(md5($signStr . $testToken));

    return $signature;
}

/**
 * 运费试算主函数
 * @param array $contentParams content 字段的 JSON 参数
 * @return array 解析后的接口响应
 */
function calculateShipFee($contentParams)
{
    global $userAccount, $apiUrl;

    try {
        // 1. 构造 content 字段（JSON 字符串）
        $contentJson = json_encode($contentParams, JSON_UNESCAPED_UNICODE);
        if ($contentJson === false) {
            throw new Exception("content 参数 JSON 编码失败：" . json_last_error_msg());
        }

        // 2. 构造待签名的参数数组（不含 sign）
        $requestData = [
            'userAccount' => $userAccount,
            'content' => $contentJson
        ];

        // 3. 生成签名
        $sign = createSignature($requestData);

        // 4. 补充 sign 字段，形成最终请求参数
        $requestData['sign'] = $sign;

        // 5. 发送 POST 请求

        // echo "请求参数：" . json_encode($requestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

        $response = curlPostData($apiUrl, $requestData);

        // 6. 解析响应（JSON 转数组）
        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("响应数据解析失败：" . json_last_error_msg() . "，原始响应：" . $response);
        }

        // echo "\n=== 请求成功，响应结果 ===" . "\n";
        return $responseData;
    } catch (Exception $e) {
        echo "\n=== 请求失败 ===" . "\n";
        //echo "错误信息：" . $e->getMessage() . "\n";
        return ['errCode' => '500', 'errMsg' => $e->getMessage()];
    }
}

// -------------------------- 测试代码 --------------------------
try {
    // 构造 content 参数（根据实际需求修改）
    $contentParams = [
        'channelCode' => $channels,  // 渠道简码（多个用逗号分隔）
        'country' => $country,                 // 国家简码
        'city' => $city,           // 收件人城市
        'postcode' => $postcode,             // 收件人邮编
        'weight' => $weight,               // 重量（kg）
        'length' => $length,                    // 长（cm）
        'width' => $width,                     // 宽（cm）
        'height' => $height,                     // 高（cm）
        'signatureService' => 0            // 签名服务（0:无，1:成人签名，2:直接签名）
    ];

    // 调用运费试算函数
    $result = calculateShipFee($contentParams);

    // 打印格式化结果
   // echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    // 提取核心数据（这里是关键逻辑）,如果shipFee为空,则不提取该渠道的运费试算数据
    // 如果currency为空,则设为null
    

    $extractedData = [];
    if (isset($result['data']) && is_array($result['data'])) {
        foreach ($result['data'] as $channel => $channelInfo) {
            // 提取你需要的三个字段
            $extractedData[] = [
                'channel_code' => $channel,        // 渠道名称（如 AMGDCA）
                'totalFee' => $channelInfo['shipFee'] ?? null,  // 总费用（对应 shipFee）,如果为空,则设为null
                'currency' => $channelInfo['currency'] ?? null  // 货币类型（如 USD）,如果为空,则设为null
            ];
        }
    }

    // 输出提取后的结果（格式化展示）
    //echo "提取后的核心数据：\n";
    echo json_encode($extractedData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
} catch (Exception $e) {
    echo "测试代码执行失败：" . $e->getMessage() . "\n";
}


//测试链接 http://cz.younger-car.com/yunfei_kucun/api_wd/get_ship_fee_api.php?channels=AMGDCA,CAUSPSGA&country=US&city=LOS ANGELES&postcode=90001&weight=0.079&length=26&width=20&height=2&signatureService=0