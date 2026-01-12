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
 * ✅ 核心修复：新版钉钉【纯数字ID就是有效userId】，无userid_str字段
 * 直接返回接口的userid即可，这个ID能正常发送消息，无任何问题
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
        $validUserId = $resJson['userid']; // 纯数字就是有效ID！！！
        echo "✅ 手机号【{$mobile}】→ 有效可用userId：{$validUserId}".PHP_EOL;
        return $validUserId;
    } else {
        throw new Exception("手机号【{$mobile}】查询userId失败：{$resJson['errmsg']} 错误码：{$resJson['errcode']}");
    }
}

/**
 * ✅ 双保险修复：按userId发送DING消息 最终可用版
 * 1. dept_id_list 必须传null 禁止传空字符串 解决41错误
 * 2. userid_list 传纯数字ID即可 新版钉钉完全支持
 */
function sendDingMsgByUserId_Success($access_token, $userIdArr, $atUserIdArr = [], $content = "")
{
    $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token=".$access_token;
    $userIdStr = implode(',', $userIdArr);
    $data = [
        "agent_id"      => AGENT_ID,
        "userid_list"   => $userIdStr,  // 纯数字ID拼接，有效可用
        "dept_id_list"  => null,        // ✅ 必传null 核心修复41错误
        "to_all_user"   => false,
        "msg"           => [
            "msgtype" => "markdown",
            "markdown"    => [
                "title" => "DING消息",
                "text" => $content
            ],
            "at"      => [
                "atUserIds" => $atUserIdArr, // @对应用户ID
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
    echo "📌 钉钉官方完整返回结果：" . json_encode($resJson, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    return $resJson['errcode'] == 0;
}

// ===================== 主执行逻辑 (完全不变) =====================
try {
    $accessToken = getDingDingToken();
    echo "✅ 获取access_token成功：" . $accessToken . PHP_EOL . PHP_EOL;

    // 你的手机号列表
    $mobileList = ["18868725001","18868268995","13868380570"];
    $realUserIdList = [];
    foreach ($mobileList as $mobile) {
        $realUserIdList[] = getRealUserIdByMobile($accessToken, $mobile);
    }
    echo PHP_EOL;

    // 发送消息+@对应用户
    $content = "缺货预警：sku，链接：[点击查看](https://cz.younger-car.com/admin-panel/inventory_details.php?action=inventory_alert)";
    $sendResult = sendDingMsgByUserId_Success($accessToken, $realUserIdList, $realUserIdList, $content);    
    if ($sendResult) {
        echo "✅ ✅ ✅ 消息发送成功！钉钉DING消息推送完成！✅ ✅ ✅";
    } else {
        echo "❌ 消息发送失败！";
    }

} catch (Exception $e) {
    echo "❌ 程序执行异常：" . $e->getMessage() . PHP_EOL;
}
?>