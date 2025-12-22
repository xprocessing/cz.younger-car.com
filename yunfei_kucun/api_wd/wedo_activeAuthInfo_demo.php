<?php
/**
 * 功能：激活用户授权信息开发样例
 * 版本：1.0
 * 日期：2015/06/20
 * 作者：蒋和超
 */
require_once './wedo_common.php';
$testUserAccount 			= 'pjV391564';//用户级测试账号
$testToken		 			= '88C5D8B292E5B6153803682554FBA4F8';//用户级测试token
$sandBoxUrl 				= "http://api.wedoexpress.com/p/activeAuthInfo/";
$data 						= array('userAccount'=>$testUserAccount);
$signature					= createSignature($data);
$data['signature']			= $signature;
$data						= http_build_query($data);
$response					= curlPostData($sandBoxUrl, $data);
print_r($signature);
print_r($response);
exit;
?>

