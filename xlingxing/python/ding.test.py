import requests
import json

# 替换成你的3个核心参数
APP_KEY = "dingfka0szfgnffqqofa"
APP_SECRET = "pX_3wcuH2D5t2inXAorA1KFCW9XxQIsGxNE_MzeU-n_wHxPD2sj-dLHtG8i6Lcnc"
AGENT_ID = 4144016224  # 纯数字

def get_dingding_token():
    """第一步：获取access_token"""
    url = f"https://oapi.dingtalk.com/gettoken?appkey={APP_KEY}&appsecret={APP_SECRET}"
    res = requests.get(url)
    res_json = res.json()
    if res_json["errcode"] == 0:
        return res_json["access_token"]
    else:
        raise Exception(f"获取token失败：{res_json['errmsg']}")

def send_ding_msg_by_mobile(access_token):
    """第二步：按手机号发送DING消息"""
    url = f"https://oapi.dingtalk.com/topapi/message/corpconversation/asyncsend_v2?access_token={access_token}"
    headers = {"Content-Type": "application/json;charset=utf-8"}
    # 构造消息体
    data = {
        "agent_id": AGENT_ID,
        "userid_list": "",
        "dept_id_list": "",
        "to_all_user": False,
        "msg": {
            "msgtype": "text",
            "text": {
                "content": "【Python发送】钉钉DING消息测试，这是一条重要提醒！"
            },
            "at": {
                "atMobiles": ["18868725001", "18069755001"],
                "isAtAll": False
            }
        }
    }
    res = requests.post(url, headers=headers, data=json.dumps(data))
    res_json = res.json()
    return res_json["errcode"] == 0

if __name__ == "__main__":
    # 执行发送逻辑
    token = get_dingding_token()
    print(f"获取的access_token：{token}")
    result = send_ding_msg_by_mobile(token)
    print(f"消息发送结果：{'成功' if result else '失败'}")