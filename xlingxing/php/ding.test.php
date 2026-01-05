<?php
// ===================== 你的核心配置参数 (无需修改 直接复用) =====================
define('APP_KEY', 'dingfka0szfgnffqqofa');
define('APP_SECRET', 'pX_3wcuH2D5t2inXAorA1KFCW9XxQIsGxNE_MzeU-n_wHxPD2sj-dLHtG8i6Lcnc');
define('AGENT_ID', 4144016224);

/**
 * 1. 通用方法：获取钉钉全局凭证 access_token
 * @return string 有效token
 * @throws Exception 获取失败抛出异常
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
 * 2. 核心方法：根据【单个手机号】查询对应的员工userId
 * @param string $access_token 有效凭证
 * @param string $mobile 员工钉钉绑定手机号
 * @return string 员工唯一userId
 * @throws Exception 查询失败抛出异常
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
 * 3. 核心方法：根据【userId列表】发送钉钉DING消息（精准推送，强提醒）
 * @param string $access_token 有效凭证
 * @param array $userIdArr 需要发送的userId数组 ['id1','id2']
 * @param array $atUserIdArr 需要@的userId数组 (可选，不传则不@人)
 * @param string $msgContent 消息内容
 * @return bool true=发送成功 false=发送失败
 */
function sendDingMsgByUserId($access_token, $userIdArr, $atUserIdArr = [], $msgContent = "【重要提醒】钉钉消息通知！")
{
    $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token=".$access_token;
    // 拼接userId字符串，多个用英文逗号分隔，钉钉接口要求格式
    $userIdStr = implode(',', $userIdArr);
    
    // 构造钉钉标准消息体
    $data = [
        "agent_id"      => AGENT_ID,
        "userid_list"   => $userIdStr,  // 必填：要发送的userId列表
        "dept_id_list"  => "",
        "to_all_user"   => false,
        "msg"           => [
            "msgtype" => "text",
            "text"    => [
                "content" => $msgContent
            ],
            "at"      => [
                "atUserIds" => $atUserIdArr, // @指定userId的员工
                "isAtAll"   => false         // 是否@所有人
            ]
        ]
    ];

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    // 中文不转义，钉钉消息正常显示中文
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data, JSON_UNESCAPED_UNICODE));
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json;charset=utf-8"
    ]);

    $result = curl_exec($ch);
    curl_close($ch);

    $resJson = json_decode($result, true);
    return $resJson['errcode'] == 0;
}

// ===================== 主执行逻辑 (完整闭环：手机号 → userId → 发送消息) =====================
try {
    // 步骤1：获取access_token
    $accessToken = getDingDingToken();
    echo "✅ 获取access_token成功：" . $accessToken . PHP_EOL . PHP_EOL;

    // 步骤2：配置需要发送消息的【手机号列表】(可修改/增减手机号)
    $mobileList = ["18868725001"];
    $userIdList = []; // 存储转换后的userId列表

    // 步骤3：循环遍历手机号，批量获取对应的userId
    foreach ($mobileList as $mobile) {
        $userId = getUserIdByMobile($accessToken, $mobile);
        $userIdList[] = $userId;
        echo "✅ 手机号【{$mobile}】→ 对应userId：{$userId}" . PHP_EOL;
    }
    echo PHP_EOL;

    // 步骤4：用userId发送DING消息 + @对应的人
    $msgContent = "【PHP完整版-精准推送】根据手机号获取userId后发送的钉钉DING消息，强提醒必达！";
    $sendResult = sendDingMsgByUserId($accessToken, $userIdList, $userIdList, $msgContent);
    
    // 步骤5：输出结果
    if ($sendResult) {
        echo "✅ 消息发送成功！发送对象userId列表：" . implode(',', $userIdList);
    } else {
        echo "❌ 消息发送失败！";
    }

} catch (Exception $e) {
    // 异常捕获，友好提示错误信息
    echo "❌ 程序执行异常：" . $e->getMessage() . PHP_EOL;
}
?>