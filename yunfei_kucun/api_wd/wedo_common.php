<?php 
/**
 * 说明：运德接口测试开发样例公共信息
 */
error_reporting(-1);
header("Content-type: text/html; charset=utf-8");
date_default_timezone_set('Asia/Shanghai');

//发送curl post请求
function curlPostData($url,$data,$header=array()){
	$hander = curl_init();
	curl_setopt($hander,CURLOPT_URL,$url);
	curl_setopt($hander,CURLOPT_HEADER,0);
// 	curl_setopt($hander,CURLOPT_VERBOSE,1); //测试
	curl_setopt($hander,CURLOPT_HTTPHEADER,$header);
	curl_setopt($hander,CURLOPT_FOLLOWLOCATION,1);
	curl_setopt($hander,CURLOPT_SSL_VERIFYPEER,0);
	curl_setopt($hander,CURLOPT_POST, 1);
	curl_setopt($hander,CURLOPT_POSTFIELDS, $data);
	curl_setopt($hander,CURLOPT_RETURNTRANSFER,true);
	curl_setopt($hander,CURLOPT_TIMEOUT,60);
	$cnt=0;
	while($cnt < 3 && ($result=curl_exec($hander))===FALSE) $cnt++;
	curl_close($hander);
	return $result;
}

//创建签名信息
function createSignature($data){
	global  $testUserAccount,$testToken;
	ksort($data);
	$str = '';
	foreach ($data as $k=>$v){
		$str.=$v;
	}
	$signature = strtoupper(md5($str.$testToken));	

	return $signature;
}
?>