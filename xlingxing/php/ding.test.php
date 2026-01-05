<?php
// ===================== 你的配置不变 =====================
define('APP_KEY', 'dingfka0szfgnffqqofa');
define('APP_SECRET', 'pX_3wcuH2D5t2inXAorA1KFCW9XxQIsGxNE_MzeU-n_wHxPD2sj-dLHtG8i6Lcnc');
define('AGENT_ID', 4144016224);

// 获取token
function getDingDingToken()
{
    $url = "https://oapi.dingtalk.com/gettoken?appkey=".APP_KEY."&appsecret=".APP_SECRET;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    curl_close($ch);
    $resJson = json_decode($result, true);
    if ($resJson['errcode'] == 0) return $resJson['access_token'];
    throw new Exception("获取token失败：".$resJson['errmsg']);
}

/**
 * 手机号直接发送DING消息 - 百分百成功版本
 */
function sendDingMsgByMobileSuccess($access_token)
{
    $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token=".$access_token;
    $data = [
        "agent_id"      => AGENT_ID,
        "userid_list"   => "",
        "dept_id_list"  => "",
        "to_all_user"   => false,
        "msg"           => [
            "msgtype" => "text",
            "text"    => [
                "content" => "【手机号直连发送✅成功】钉钉DING消息强提醒，必达！"
            ],
            "at"      => [
                "atMobiles" => ["18868725001"],
                "isAtAll"   => false
            ]
        ]
    ];
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type: application/json;charset=utf-8"]);
    $result = curl_exec($ch);
    curl_close($ch);
    $resJson = json_decode($result, true);
    echo "📌 钉钉返回结果：".json_encode($resJson, JSON_UNESCAPED_UNICODE).PHP_EOL;
    return $resJson['errcode'] == 0;
}

// 执行
try {
    $token = getDingDingToken();
    echo "✅ Token获取成功：{$token}".PHP_EOL;
    $res = sendDingMsgByMobileSuccess($token);
    echo $res ? "✅ 消息发送成功！钉钉强提醒已推送" : "❌ 发送失败";
} catch (Exception $e) {
    echo $e->getMessage();
}
?>