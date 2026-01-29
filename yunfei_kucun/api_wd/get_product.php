<?php

/**
 * 运德海外仓运费试算接口 Demo
 * 包含签名生成、POST 请求发送、响应解析
 */
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

$sku = $_GET['sku'] ?? '';
$platform_name = $_GET['platform_name'];

require_once __DIR__ . '/../../config.php';
// -------------------------- 配置信息 --------------------------
$platform_name = $_GET['platform_name'];
switch ($platform_name) {
    case 'Amazon':
        $userAccount = WD_APP_ID_Amazon;
        $testToken = WD_APP_TOKEN_Amazon;
        break;
    case 'eBay':
        $userAccount = WD_APP_ID_eBay;
        $testToken = WD_APP_TOKEN_eBay;
        break;
    case 'Shopify':
        $userAccount = WD_APP_ID_Shopify;
        $testToken = WD_APP_TOKEN_Shopify;
        break;
    default:
        $userAccount = WD_APP_ID;
        $testToken = WD_APP_TOKEN;
        break;
}

// 替换为你的用户账号（联系运德客服获取）
//$userAccount = WD_APP_ID;
// 替换为你的授权 token（联系运德客服获取）
///$testToken = WD_APP_TOKEN;



// 运费试算接口地址
$apiUrl = "http://fg.wedoexpress.com/api.php?mod=apiManage&act=queryGoodsInfo";
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
 * 获取商品信息 主函数 getGoodsInfo
 * @param array $contentParams content 字段的 JSON 参数
 * @return array 解析后的接口响应
 */
function getGoodsInfo($contentParams)
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
        //echo "请求参数：" . json_encode($requestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

        $response = curlPostData($apiUrl, $requestData);

        // 6. 解析响应（JSON 转数组）
        $responseData = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("响应数据解析失败：" . json_last_error_msg() . "，原始响应：" . $response);
        }

        //echo "\n=== 请求成功，响应结果 ===" . "\n";
        return $responseData;
    } catch (Exception $e) {
        //echo "\n=== 请求失败 ===" . "\n";
        //echo "错误信息：" . $e->getMessage() . "\n";
        return ['errCode' => '500', 'errMsg' => $e->getMessage()];
    }
}

// -------------------------- 测试代码 --------------------------
try {
    // 构造 content 参数（根据实际需求修改）
    $contentParams = [
        'storeCode' => 'WDUSLG',  // 仓库编码（选取填写英文简码即可）
        'page' => '1',                 // 页码，每页50组数据
        'skuType' => 'userSKU',           // userSKU:用户料号 sku:运德料号
        'skuArr' => [$sku],             // 料号数组	[‘sku1’,’sku2’,’sku3’]       
        'signatureService' => 0            // 签名服务（0:无，1:成人签名，2:直接签名）
    ];

    // 调用 获取商品信息 函数
    $result = getGoodsInfo($contentParams);

    // 打印格式化结果
    //echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";

    // 初始化整理后的数组
    $sku_data = [];

    // 1. 先检查数据结构是否完整，避免Undefined index报错
    if (isset($result['data']) && !empty($result['data'])) {
        // 2. 获取data下的第一个SKU数据（因为data是键为SKU的关联数组，取第一个元素）
        $sku_detail = reset($result['data']);

        // 3. 按要求映射字段
        $sku_data = [
            'sku'     => $sku_detail['userSku'],
            'weight'  => $sku_detail['skuWeight'],
            'length'  => $sku_detail['skuLength'],
            'width'   => $sku_detail['skuWidth'],
            'height'  => $sku_detail['skuHeight']
        ];
    }

    // 输出整理后的结果（可选，方便验证）
    echo json_encode($sku_data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo "测试代码执行失败：" . $e->getMessage() . "\n";
}


//测试链接 http://cz.younger-car.com/yunfei_kucun/api_wd/get_product.php?sku=NI-C63-FL-GB&platform_name=
