<?php
/**
 * 功能：获取开放渠道接口样例
 * 版本：1.0
 * 日期：2015/06/20
 * 作者：蒋和超
 */
require_once './wedo_common.php';
$testUserAccount 			= 'pjV391564';//用户级测试账号
$testToken		 			= '88C5D8B292E5B6153803682554FBA4F8';//用户级测试token
$sandBoxUrl 				= "http://sandbox.api.wedoexpress.com/p/getAllChannelInfo/";
$data 						= array('userAccount'=>$testUserAccount);
$signature					= createSignature($data);
$data['signature']			= $signature;
$data						= http_build_query($data);
$response					= curlPostData($sandBoxUrl, $data);
print_r($response);
exit;
?>

