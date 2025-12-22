import asyncio

from openapi import OpenApiBase
import json
# 加载配置并选择环境（比如生产环境）
with open('config.json', 'r', encoding='utf-8') as f:
    config = json.load(f)
env_config = config['LingXing']  # 切换环境只需改为 'dev'

# 提取配置参数
base_url = env_config['base_url']
app_id = env_config['app_id']
app_secret = env_config['app_secret']
api_path = "/pb/mp/shop/v2/getSellerList"  # 选择订单接口 Path

async def main():
    op_api = OpenApiBase(base_url, app_id, app_secret)
    token_resp = await op_api.generate_access_token()
    # 可以自行将AccessToken保存到缓存中
    print(token_resp.access_token)
    # RefreshToken用于续费AccessToken，只能使用一次
    print(token_resp.refresh_token)
    # AccessToken的有效期，TTL
    print(token_resp.expires_in)

    # # 刷新AccessToken
    # token_resp = await op_api.refresh_token(token_resp.refresh_token)

    # 组装请求参数
    req_body = {
    "offset": 0,
    "length": 200,
    "platform_code": [10008,10011],
    "is_sync": 1,
    "status": 1
}

    
    # 发起OpenAPI的请求
    resp = await op_api.request(token_resp.access_token, api_path, "POST",
                                req_body=req_body)
    print(resp.dict())
    print(resp.code)
    print(resp.data)
    print(resp.error_details)
    print(resp.request_id)
    print(resp.response_time)


if __name__ == '__main__':
    loop = asyncio.get_event_loop()
    loop.run_until_complete(main())