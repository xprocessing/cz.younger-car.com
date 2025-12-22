<?php
/**
 * 功能：获取用户授权信息接口样例
 * 版本：1.0
 * 日期：2015/08/05
 * 作者：蒋和超
 */
//备注：此接口只有系统级授权用户才有权限调用，普通用户没有权限
require_once './wedo_common.php';
$testUserAccount 			= 'pjV391564';//系统级测试用户
$testToken		 			= 'EE75EB8A74E5ED94C8A03BA4F18EBBB0';//系统级测试token
$sandBoxUrl 				= "http://api.wedoexpress.com/p/authUserInfo/";
$content					= array('userName'=>'testClient','mobile'=>'13635363484');
$data 						= array('userAccount'=>$testUserAccount,'content'=>json_encode($content));
$signature					= createSignature($data);
$data['signature']			= $signature;
$data						= http_build_query($data);
$response 					= curlPostData($sandBoxUrl,$data);
print_r($response);
exit;
?>

