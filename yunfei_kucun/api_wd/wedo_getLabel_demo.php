<?php
/**
 * 功能：获取面单信息接口样例
 * 版本：1.0
 * 日期：2015/08/05
 * 作者：蒋和超
 */
require_once './wedo_common.php';
$testUserAccount 			= 'pjV391564';//用户级测试账号
$testToken		 			= '88C5D8B292E5B6153803682554FBA4F8';//用户级测试token
$sandBoxUrl 				= "http://sandbox.api.wedoexpress.com/p/getLabel/";
$content					= array('labelSize'=>1,'trackNumber'=>'LN124460225CN');
$data 						= array('userAccount'=>$testUserAccount,'content'=>json_encode($content));
$signature					= createSignature($data);
$data['signature']			= $signature;
$data						= http_build_query($data);
$response					= curlPostData($sandBoxUrl, $data);
print_r($response);
exit;
?>

