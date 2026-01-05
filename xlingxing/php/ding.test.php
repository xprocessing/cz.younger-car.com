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
 * 2. ✅ 修复：根据手机号获取【钉钉标准userId(userid_str)】
 * 纯数字的是unionId无效，字符串的userid_str才是能发消息的标准ID
 */
function getRealUserIdByMobile($access_token, $mobile)
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
        $unionId = $resJson['userid']; // 纯数字 无效ID
        $realUserId = $resJson['userid_str']; // 字符串 标准有效ID
        echo "✅ 手机号【{$mobile}】→ unionId(无效)：{$unionId} → 标准userId(有效)：{$realUserId}".PHP_EOL;
        return $realUserId;
    } else {
        throw new Exception("手机号【{$mobile}】查询userId失败：{$resJson['errmsg']} 错误码：{$resJson['errcode']}");
    }
}

/**
 * 3. ✅ 双修复：按标准userId发送DING消息
 * ① dept_id_list 传null 解决errcode:41
 * ② 使用标准userId 解决用户不存在
 */
function sendDingMsgByUserId_Success($access_token, $userIdArr, $atUserIdArr = [])
{
    $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token=".$access_token;
    $userIdStr = implode(',', $userIdArr);
    // ============ 核心修复点 START ============
    $data = [
        "agent_id"      => AGENT_ID,
        "userid_list"   => $userIdStr,  // 有用户ID则传拼接字符串
        "dept_id_list"  => null,        // 无部门则传null，禁止传"" 【修复41错误】
        "to_all_user"   => false,
        "msg"           => [
            "msgtype" => "text",
            "text"    => [
                "content" => "✅【标准userId发送成功】钉钉DING消息强提醒，精准推送必达！"
            ],
            "at"      => [
                "atUserIds" => $atUserIdArr,
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

    // 你的手机号列表
    $mobileList = ["18868725001"];
    $realUserIdList = [];
    foreach ($mobileList as $mobile) {
        $realUserIdList[] = getRealUserIdByMobile($accessToken, $mobile);
    }
    echo PHP_EOL;

    // 发送消息+@对应用户
    $sendResult = sendDingMsgByUserId_Success($accessToken, $realUserIdList, $realUserIdList);
    if ($sendResult) {
        echo "✅ ✅ ✅ 消息发送成功！标准userId精准推送完成！✅ ✅ ✅";
    } else {
        echo "❌ 消息发送失败！";
    }

} catch (Exception $e) {
    echo "❌ 程序执行异常：" . $e->getMessage() . PHP_EOL;
}
?>