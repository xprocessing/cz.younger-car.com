<?php
/**
 * 功能：批量获取面单信息接口样例
 * 版本：1.0
 * 日期：2015/08/05
 * 作者：蒋和超
 */
require_once './wedo_common.php';
$testUserAccount 			= 'pjV391564';//用户级测试账号
$testToken		 			= '88C5D8B292E5B6153803682554FBA4F8';//用户级测试token
$sandBoxUrl 				= "http://sandbox.api.wedoexpress.com/p/getBatchLabel/";
$content['LN127293169CN']	= 1;
$content['LN127294513CN']	= 1;
$content['RM630190137CN']	= 1;
$content['RM112448686CN']	= 1;
$content['RL110993706CN']	= 1;
$data 						= array('userAccount'=>$testUserAccount,'content'=>json_encode($content));
$signature					= createSignature($data);
$data['signature']			= $signature;
$data						= http_build_query($data);
$response					= curlPostData($sandBoxUrl, $data);
print_r($response);
exit;
?>

