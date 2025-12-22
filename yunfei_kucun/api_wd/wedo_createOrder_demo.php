<?php
/**
 * 功能：创建订单开发样例
 * 版本：1.0
 * 日期：2015/06/20
 * 作者：蒋和超
 */
require_once './wedo_common.php';
$testUserAccount = 'testAccount1';//用户级测试账号
$testToken		 = 'EE75EB8A74E5ED94C8A03BA4F18EBBB0';//用户级测试token
$sandBoxUrl 	 = "http://sandbox.api.wedoexpress.com/p/createOrder/";
$content 		 = array();
//订单主体信息
$content['goods']			= array();
$content['refrenceId']		= 'SV13480';
$content['channelCode']		= 'CNPGHN';
$content['weight'] 			= 1.2;
$content['totalValue'] 		= 0.5;
$content['currency'] 		= "USD";
$content['packageType'] 	= 1;
$content['returnWay'] 		= 1;
$content['pickWay']			= 1;
$content['whCode']			= 'WDSZAC';
$content['isSpecial']		= 1;
$content['volWeight']		= array("L"=>"10","W"=>"12","H"=>"13","U"=>"CM");
$content['notes']	 		= "这是测试订单";

//发件人信息
$sender['name']				= "test";
$sender['postcode']			= '518111';
$sender['phone']			= '0755-8642532';
$sender['mobile']			= '13638363484';
$sender['email']			= 'test@test.com';
$sender['countryCode']		= 'CN';
$sender['countryName']		= 'China';
$sender['company']			= 'wedo';
$sender['address']			= 'test address';
$sender['province']			= 'Guangdong';
$sender['city']				= 'Shenzhen';
//揽收人信息
$picker['name']				= 'test';
$picker['postcode']			= '518111';
$picker['phone']			= '0755-8642532';
$picker['mobile']			= '13638363484';
$picker['email']			= 'test@test.com';
$picker['countryCode']		= 'CN';
$picker['countryName']		= 'China';
$picker['company']			= 'wedo';
$picker['address']			= 'test address';
$picker['province']			= 'Guangdong';
$picker['city']				= 'Shenzhen';
//收件人信息
$receiver['name']			= 'Jake Sudrajat'.$i;
$receiver['postcode']		= '45653';
$receiver['phone']			= '0478101987';
$receiver['mobile']			= '13638363484';
$receiver['email']			= 'test@test.com';
$receiver['countryCode']	= 'US';
$receiver['countryName']	= 'United States';
$receiver['company']		= 'Jake Sudrajat';
$receiver['address1']		= '10 Blackbutt';
$receiver['address2']		= 'test address2';
$receiver['province']		= 'Queensland';
$receiver['city']			= 'Tewantin';
//商品信息
$good['cnName']				= '测试商品';
$good['enName']				= 'test good';
$good['description']		= 'test good';
$good['weight']				= 0.2;
$good['unit']				= 'pcs';
$good['sku']				= "S_00123";
$good['count']				= 1;
$good['declaredValue']		= 0.2;
$good['declareCurrency']	= 'USD';
$good['origin']				= 'CN';
$good['hsCode']				= "256355";
$content['sender']			= $sender;
$content['receiver']		= $receiver;
$content['pickup']			= $picker;
$content['goods'][]			= $good;
$content['goods'][]			= $good;

$data 						= array('content'=>json_encode($content),'userAccount'=>$testUserAccount);
$signature					= createSignature($data);
$data['signature']			= $signature;
$data						= http_build_query($data);
$response					= curlPostData($sandBoxUrl, $data);
print_r($response);
echo "\n";
exit;
?>

