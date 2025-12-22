<?php
/**
 * 功能：获取开放渠道可达国家接口样例
 * 版本：1.0
 * 日期：2015/06/20
 * 作者：蒋和超
 */
require_once './wedo_common.php';
$testUserAccount 			= 'testAccount1';//用户级测试账号
$testToken		 			= 'EE75EB8A74E5ED94C8A03BA4F18EBBB0';//用户级测试token
$sandBoxUrl 				= "http://sandbox.api.wedoexpress.com/p/getChannelReachedCountry/";
$content					= array("channelCode"=>'CNPPYE');
$data 						= array('userAccount'=>$testUserAccount,"content"=>json_encode($content));
$signature					= createSignature($data);
$data['signature']			= $signature;
$data						= http_build_query($data);
$response					= curlPostData($sandBoxUrl, $data);
print_r($response);
exit;
?>

