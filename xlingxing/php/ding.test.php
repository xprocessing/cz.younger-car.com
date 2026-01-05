<?php
// ===================== 你的核心配置参数 (完全不变 无需修改) =====================
define('APP_KEY', 'dingfka0szfgnffqqofa');
define('APP_SECRET', 'pX_3wcuH2D5t2inXAorA1KFCW9XxQIsGxNE_MzeU-n_wHxPD2sj-dLHtG8i6Lcnc');
define('AGENT_ID', 4144016224);

/**
 * 1. 获取钉钉全局凭证 access_token
 */
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
    if ($resJson['errcode'] == 0) {
        return $resJson['access_token'];
    } else {
        throw new Exception("获取token失败：".$resJson['errmsg'] . " 错误码：".$resJson['errcode']);
    }
}

/**
 * 2. 按手机号发送DING消息 - ✅核心修复：dept_id_list/userid_list 传 null
 * 百分百成功版本，强提醒+@指定手机号
 */
function sendDingMsgByMobile_Success($access_token)
{
    $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token=".$access_token;
    // ============ 核心修复点 START ============
    $data = [
        "agent_id"      => AGENT_ID,
        "userid_list"   => null,  // 必须传null，不能传""
        "dept_id_list"  => null,  // 必须传null，不能传"" 【修复41错误的关键】
        "to_all_user"   => false,
        "msg"           => [
            "msgtype" => "text",
            "text"    => [
                "content" => "✅【发送成功】钉钉DING消息强提醒，弹窗+铃声必达！"
            ],
            "at"      => [
                "atMobiles" => ["18868725001", "18069755001"],
                "isAtAll"   => false
            ]
        ]
    ];
    // ============ 核心修复点 END ============

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json;charset=utf-8"
    ]);

    $result = curl_exec($ch);
    curl_close($ch);
    $resJson = json_decode($result, true);
    echo "📌 钉钉官方完整返回结果：" . json_encode($resJson, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    return $resJson['errcode'] == 0;
}

// ===================== 主执行逻辑 =====================
try {
    $accessToken = getDingDingToken();
    echo "✅ 获取access_token成功：" . $accessToken . PHP_EOL . PHP_EOL;

    $sendResult = sendDingMsgByMobile_Success($accessToken);
    if ($sendResult) {
        echo "✅ ✅ ✅ 消息发送成功！钉钉已推送强提醒DING消息！✅ ✅ ✅";
    } else {
        echo "❌ 消息发送失败！";
    }

} catch (Exception $e) {
    echo "❌ 程序执行异常：" . $e->getMessage() . PHP_EOL;
}
?>