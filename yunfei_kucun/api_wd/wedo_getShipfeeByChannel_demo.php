<?php
/**
 * 功能：计算运费接口样例
 * 版本：1.0
 * 日期：2015/06/20
 * 作者：蒋和超
 */
require_once './wedo_common.php';
$testUserAccount = 'pjV391564';//用户级测试账号
$testToken = '88C5D8B292E5B6153803682554FBA4F8';//用户级测试token
$content['channel'] = 'UPSGW';
$content['country']	= 'US';
$content['weight'] = 1;
$content['width'] 	= 10;
$content['length'] = 10;
$content['height'] 	= 10;
$content['postcode'] = "99504";
/*$content['extend']['lwh'] 		= array("L"=>10, "W"=>5, "H"=>1);
$content['extend']['postcode'] 	= '106235';*/
$sandBoxUrl 			= "http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery";
$data 					= array('content'=>json_encode($content),'userAccount'=>$testUserAccount);
$signature				= createSignature($data);
$data['signature']		= $signature;
$data					= http_build_query($data);
$response				= curlPostData($sandBoxUrl, $data);
print_r($response);
exit;
?>

