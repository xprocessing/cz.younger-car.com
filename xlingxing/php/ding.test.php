<?php
// ===================== 你的核心配置参数 (无需修改 直接复用) =====================
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
 * 2. 根据手机号查询对应的员工userId
 */
function getUserIdByMobile($access_token, $mobile)
{
    $url = "https://oapi.dingtalk.com/user/get_by_mobile?access_token={$access_token}&mobile={$mobile}";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    $result = curl_exec($ch);
    curl_close($ch);

    $resJson = json_decode($result, true);
    if ($resJson['errcode'] == 0) {
        return $resJson['userid'];
    } else {
        throw new Exception("手机号【{$mobile}】查询userId失败：{$resJson['errmsg']} 错误码：{$resJson['errcode']}");
    }
}

/**
 * 3. 根据userId发送钉钉DING消息 - 【核心修改：打印完整报错信息】
 */
function sendDingMsgByUserId($access_token, $userIdArr, $atUserIdArr = [], $msgContent = "【重要提醒】钉钉消息通知！")
{
    $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token=".$access_token;
    $userIdStr = implode(',', $userIdArr);
    
    $data = [
        "agent_id"      => AGENT_ID,
        "userid_list"   => $userIdStr,
        "dept_id_list"  => "",
        "to_all_user"   => false,
        "msg"           => [
            "msgtype" => "text",
            "text"    => [
                "content" => $msgContent
            ],
            "at"      => [
                "atUserIds" => $atUserIdArr,
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
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json;charset=utf-8"
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    $resJson = json_decode($result, true);
    // ============ 核心新增：打印钉钉返回的【完整错误信息】 ============
    echo "📌 钉钉消息接口完整返回结果：" . json_encode($resJson, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    return $resJson['errcode'] == 0;
}

// ===================== 主执行逻辑 =====================
try {
    $accessToken = getDingDingToken();
    echo "✅ 获取access_token成功：" . $accessToken . PHP_EOL . PHP_EOL;

    $mobileList = ["18868725001"];
    $userIdList = [];
    foreach ($mobileList as $mobile) {
        $userId = getUserIdByMobile($accessToken, $mobile);
        $userIdList[] = $userId;
        echo "✅ 手机号【{$mobile}】→ 对应userId：{$userId}" . PHP_EOL;
    }
    echo PHP_EOL;

    $msgContent = "【PHP完整版-精准推送】根据手机号获取userId后发送的钉钉DING消息，强提醒必达！";
    $sendResult = sendDingMsgByUserId($accessToken, $userIdList, $userIdList, $msgContent);
    
    if ($sendResult) {
        echo "✅ 消息发送成功！发送对象userId列表：" . implode(',', $userIdList);
    } else {
        echo "❌ 消息发送失败！";
    }

} catch (Exception $e) {
    echo "❌ 程序执行异常：" . $e->getMessage() . PHP_EOL;
}
?>