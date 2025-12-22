1、 前言

1.1、如何与 WedoWarehouse(运德海外仓) 对接

本文介绍如何与 WedoWarehouse（运德仓储） 进行对接，如何发送请求，以及返回的数据格式如何。

1.2、申请开发者令牌

在开始之前，您需要联系运德客服，以获取用户账号和token，并开通授权。联系邮箱：api@wedoexpress.com。

1.3、如何发送请求

WedoWarehouse采用的是类似 RESTFUL 风格的 API 设计，数据发送前后都会进行数据签名验证。因此你需要对 HTTP 协议有一定了解，并能够运用你熟悉的语言发送 HTTP 请求。

1.4、如何获取数据签名

构造签名串算法：

1、将要请求的内容，按照参数名称从低到高排序，然后将个参数内容拼接起来组成签名因子。注意:sign 字段不参与签名。例如：请求的数据格式如下:userAccount=test&content={“test”:”test”},按照参数名称从低到高排序后则变成:content={“test”:”test”}&userAccount=test,则签名因子为：{“test”:”test”}test

2、根据第一步得到的签名因子，再将系统分配的授权 token 拼在签名因子后面则变 成:{“test”:”test”}test27AF7B595DA48CE004790B22DF0E0AED，再用 md5 进行签名后再将字母全部转换成大写记得到签名串

上述签名结果：DC02A58745EAFC5EC00B89FADE590438，则最终要传输的数据内容为：

content={“test”:test”}&sign=DC02A58745EAFC5EC00B89FADE590438&userAccount=test

1.5、对接请求地址及流程

1、正式环境地址：http://fg.wedoexpress.com

2、你在 WedoWarehouse提供的正式环境做接口调试开发后，需要发邮件给 api@wedoexpress.com 申请接入正式环境，收到邮件后，我们会在三个工作日内核实你对接数据的准确性并邮件回复开通生产环境权限。

1.6、数据保密及安全提醒

请你妥善保管好 WedoWarehouse分配给你的授权信息，不要随意泄漏给第三方人员或单位，WedoWarehouse的工作人员不会找你们索取授权信息的。如有发现你的授权信息有泄漏的风险，请第一时间发邮件联系 api@wedoexpress.com 处理。

# 签名代码
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

# 运费试算

2.15 运费试算

资源地址：http://fg.wedoexpress.com/api.php?mod=apiManage&act=getShipFeeQuery

资源描述：运费试算

HTTP 方法：POST

数据交换格式：form-data

接口参数规范说明：

参数名	类型	是否必须	描述	示例值
userAccount	string	是	用户标识	
sign	string	是	数据签名串	48C9D00039EDA8A5DFBD19F9643D4F44
content	json	是	订单详细内容json 格式	详细内容参见【content 参数规范说明】
content 参数规范说明：

参数名	类型	是否必须	长度	描述	示例值
channelCode	string	是	200	渠道简码（多个用英文逗号间隔)	USZXT,WDFEDEX
country	string	是	20	国家简码	US
city	string	是	20	收件人城市	LOS ANGELES
postcode	string	是	20	收件人邮编	90001
weight	string	是	20	重量(kg)	0.079
length	string	是	20	长(cm)	26
width	string	是	20	宽(cm)	20
height	string	是	20	高(cm)	2
signatureService	int	否	1	签名服务(0:None;1:Adult（成人签名）;2:Direct Signature（直接签名）)	0
content 字段内容开发样例：

{"channelCode":"USZXT,WDFEDEX","country":"US","city":"LOS ANGELES","postcode":"90001","weight":"0.079","length":26,"width":20,"height":2}

返回结果说明：

参数名	类型	描述	示例值
errCode	string	错误码	200
errMsg	string	错误描述信息	获取数据成功
data	json	返回请求详情	{"errCode":200,"errMsg":"\u6267\u884c\u6210\u529f","status":true,"data":{"USZXT":{"mainRecordCode":"IF-B000BAD1-DE45-84B3-427F-2D44D85486F1","recordCode":"08235873-392B-AFA8-3A77-EED2765DE559","shipFee":"29.8210","currency":"CNY"}}}
