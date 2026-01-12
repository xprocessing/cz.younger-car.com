<?php
// 引入外部配置文件（保持你的原有配置引入逻辑）
require_once '../config/config.php';

class DingTalkMsgPusher
{
    // 类内私有属性，存储执行过程中的核心数据
    private $accessToken = '';
    private $realUserIdList = [];

    /**
     * 核心方法：推送钉钉消息（对外暴露的唯一入口）
     * @param array $mobileList 手机号列表
     * @param string $content 消息内容（支持markdown格式）
     * @return array 标准化执行结果（包含状态、消息、详细数据）
     */
    public function push(array $mobileList, string $content): array
    {
        try {
            // 1. 入参合法性校验，避免无效执行
            if (empty($mobileList)) {
                return $this->formatReturnResult(false, '手机号列表不能为空，无法执行推送');
            }
            if (empty(trim($content))) {
                return $this->formatReturnResult(false, '消息内容不能为空，请填写有效推送内容');
            }

            // 2. 获取钉钉全局凭证access_token
            $this->accessToken = $this->getDingDingToken();

            // 3. 批量根据手机号获取真实用户ID列表
            $this->realUserIdList = $this->getRealUserIdListByMobile($mobileList);

            // 4. 执行钉钉消息发送
            $sendSuccess = $this->sendDingMsgByUserId_Success($content);

            // 5. 根据发送结果返回对应数据
            if ($sendSuccess) {
                return $this->formatReturnResult(
                    true,
                    '消息发送成功！钉钉DING消息推送完成',
                    [
                        'access_token' => $this->accessToken,
                        'user_id_list' => $this->realUserIdList,
                        'mobile_list' => $mobileList,
                        'push_content' => $content
                    ]
                );
            } else {
                return $this->formatReturnResult(false, '消息发送失败（钉钉接口返回非成功状态）');
            }

        } catch (Exception $e) {
            // 捕获所有执行异常，返回标准化错误结果
            return $this->formatReturnResult(
                false,
                '程序执行异常：' . $e->getMessage(),
                [
                    'mobile_list' => $mobileList,
                    'push_content' => $content,
                    'error_code' => $e->getCode()
                ]
            );
        }
    }

    /**
     * 获取钉钉全局凭证access_token
     * @return string 有效的access_token
     * @throws Exception 获取失败抛出异常
     */
    private function getDingDingToken(): string
    {
        $url = "https://oapi.dingtalk.com/gettoken?appkey=" . DINGDING_APP_KEY . "&appsecret=" . DINGDING_APP_SECRET;
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        $result = curl_exec($ch);
        curl_close($ch);

        $resJson = json_decode($result, true);
        if ($resJson['errcode'] == 0) {
            echo "✅ 获取access_token成功：" . $resJson['access_token'] . PHP_EOL . PHP_EOL;
            return $resJson['access_token'];
        } else {
            throw new Exception("获取token失败：" . $resJson['errmsg'] . " 错误码：" . $resJson['errcode']);
        }
    }

    /**
     * 批量根据手机号获取真实用户ID列表
     * @param array $mobileList 手机号列表
     * @return array 真实用户ID列表
     * @throws Exception 单个手机号查询失败抛出异常
     */
    private function getRealUserIdListByMobile(array $mobileList): array
    {
        $realUserIdList = [];
        foreach ($mobileList as $mobile) {
            $url = "https://oapi.dingtalk.com/user/get_by_mobile?access_token={$this->accessToken}&mobile={$mobile}";
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            $result = curl_exec($ch);
            curl_close($ch);

            $resJson = json_decode($result, true);
            if ($resJson['errcode'] == 0) {
                $validUserId = $resJson['userid']; // 纯数字就是有效ID，保留原有核心修复逻辑
                echo "✅ 手机号【{$mobile}】→ 有效可用userId：{$validUserId}" . PHP_EOL;
                $realUserIdList[] = $validUserId;
            } else {
                throw new Exception("手机号【{$mobile}】查询userId失败：{$resJson['errmsg']} 错误码：{$resJson['errcode']}");
            }
        }
        echo PHP_EOL;
        return $realUserIdList;
    }

    /**
     * 按userId发送DING消息 最终可用版（保留原有双保险修复逻辑）
     * @param string $content 消息内容
     * @return bool 发送是否成功
     */
    private function sendDingMsgByUserId_Success(string $content): bool
    {
        $url = "https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token=" . $this->accessToken;
        $userIdStr = implode(',', $this->realUserIdList);
        $data = [
            "agent_id"      => DINGDING_AGENT_ID,
            "userid_list"   => $userIdStr,  // 纯数字ID拼接，有效可用
            "dept_id_list"  => null,        // ✅ 必传null 核心修复41错误，保留原有修复
            "to_all_user"   => false,
            "msg"           => [
                "msgtype" => "markdown",
                "markdown"    => [
                    "title" => "DING消息",
                    "text" => $content
                ],
                "at"      => [
                    "atUserIds" => $this->realUserIdList, // @对应用户ID
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

    /**
     * 统一格式化返回结果，便于调用方解析处理
     * @param bool $status 执行状态（true成功/false失败）
     * @param string $message 提示消息
     * @param array $data 附加详细数据（可选）
     * @return array 标准化返回结果
     */
    private function formatReturnResult(bool $status, string $message, array $data = []): array
    {
        return [
            'status' => $status,
            'message' => $message,
            'data' => $data
        ];
    }
}

// ===================== 类的使用示例 =====================
/* 1. 实例化钉钉消息推送类
$dingTalkPusher = new DingTalkMsgPusher();

// 2. 准备传入参数（可根据业务需求动态修改）
$mobileList = ["18868725001"];
$content = "缺货预警：有缺货sku...[点击查看](https://cz.younger-car.com/admin-panel/inventory_details.php?action=inventory_alert)";

// 3. 调用推送方法，获取标准化执行结果
$executeResult = $dingTalkPusher->push($mobileList, $content);

// 4. 打印执行结果（可根据业务需求进一步处理，如记录日志、入库等）
echo PHP_EOL . "==================== 最终执行结果 ====================" . PHP_EOL;
var_dump($executeResult);
*/
?>