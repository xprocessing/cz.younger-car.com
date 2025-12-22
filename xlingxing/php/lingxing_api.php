<?php
/**
 * host为OpenAPI域名，需要带上协议，如 https://openapi.lingxing.com
 * appId则为开发者的appId
 * appSecret为开发者的appSecret
 */
require_once __DIR__ . '/vendor/autoload.php';
//引入config.json配置文件
$configFile = __DIR__ . '/config.json';
if (!file_exists($configFile)) die("配置文件不存在");

$jsonContent = file_get_contents($configFile);
$config = json_decode($jsonContent); // 不设true，返回对象

if (json_last_error() !== JSON_ERROR_NONE) {
    die("JSON解析失败：" . json_last_error_msg());
}

// 访问配置项（对象方式）
$appId = $config->LingXing->app_id;
$appSecret = $config->LingXing->app_secret;
$hostUrl = $config->LingXing->host_url;


$client = new \Ak\OpenAPI\Services\OpenAPIRequestService($hostUrl, $appId, $appSecret);/**
 * 发起请求前需要先生成AccessToken或手动设置AccessToken，否则会抛出 InvalidAccessTokenException
 * AccessToken有时效性，可以自行加入缓存，并判断是否已过期，方便续约或重新生成
 */
$accessTokenDto = $client->generateAccessToken();

/**
 * 获取AccessToken
 */
 $accessTokenDto->getAccessToken();
 
 /**
 * 获取RefreshToken（用于刷新AccessToken），请自行保存好
 */
 $accessTokenDto->getRefreshToken();

/**
 * 获取过期时间戳，请自行保存好，用于判断AccessToken是否已过期
 */
$accessTokenDto->getExpireAt();
 
 /**
  * 刷新AccessToken，AccessToken到期前需续约，这里请自行判断AccessToken的有效期
 */
 $client->refreshToken($accessTokenDto->getRefreshToken());

/**
 * 手动设置AccessToken
 */
 $accessToken = 'get_access_token_from_cache';
 $client->setAccessToken($accessToken);
 

/**
 * GET 请求示例
 * $res 会是一个数组，接口文档返回结果json_decode()后的数组结果
 */
//$res = $client->makeRequest('/erp/sc/data/seller/lists', 'GET');

/**
 * POST 请求示例
 */
$params = ['offset'=> 0,"length"=> 20];
$res = $client->makeRequest('/pb/mp/shop/v2/getSellerList', 'POST', $params);
var_dump($res);