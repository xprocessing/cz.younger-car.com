import asyncio
import json
from openapi import OpenApiBase

class LingXingApiClient:
    def __init__(self, config_path='config.json', env='LingXing'):
        """
        初始化凌星API客户端
        :param config_path: 配置文件路径
        :param env: 环境名称
        """
        with open(config_path, 'r', encoding='utf-8') as f:
            config = json.load(f)
        env_config = config[env]
        
        self.base_url = env_config['base_url']
        self.app_id = env_config['app_id']
        self.app_secret = env_config['app_secret']
        self.op_api = OpenApiBase(self.base_url, self.app_id, self.app_secret)
        self.access_token = None
        self.refresh_token = None
        
    async def get_token(self):
        """获取或刷新access token"""
        if not self.access_token:
            token_resp = await self.op_api.generate_access_token()
            self.access_token = token_resp.access_token
            self.refresh_token = token_resp.refresh_token
        return self.access_token
    
    async def request_api(self, api_path, req_body=None, method="POST"):
        """
        通用API请求方法
        :param api_path: API路径
        :param req_body: 请求体参数
        :param method: 请求方法，默认为POST
        :return: API响应结果
        """
        # 获取token
        token = await self.get_token()
        
        # 发起请求
        resp = await self.op_api.request(token, api_path, method, req_body=req_body)
        
        # 如果token过期，尝试刷新token并重试
        if hasattr(resp, 'code') and resp.code == 'TOKEN_EXPIRED':
            token_resp = await self.op_api.refresh_token(self.refresh_token)
            self.access_token = token_resp.access_token
            self.refresh_token = token_resp.refresh_token
            resp = await self.op_api.request(self.access_token, api_path, method, req_body=req_body)
            
        return resp

# 简化的直接调用函数
async def call_lingxing_api(api_path, req_body=None, method="POST"):
    """
    直接调用凌星API的便捷函数
    :param api_path: API路径
    :param req_body: 请求体参数
    :param method: 请求方法
    :return: API响应结果
    """
    client = LingXingApiClient()
    return await client.request_api(api_path, req_body, method)

# 使用示例
async def main():
    # 使用封装的客户端
    client = LingXingApiClient()
    
    # 调用卖家列表接口
    api_path = "/pb/mp/shop/v2/getSellerList"
    req_body = {
        "offset": 0,
        "length": 200,
        "platform_code": [10008, 10011],
        "is_sync": 1,
        "status": 1
    }
    
    # 直接请求数据
    resp = await client.request_api(api_path, req_body)
    
    print(resp.dict())
    print(f"状态码: {resp.code}")
    print(f"数据: {resp.data}")
    print(f"请求ID: {resp.request_id}")
    
    # 或者使用更简洁的方式
    resp2 = await call_lingxing_api(api_path, req_body)
    print(f"\n简洁调用结果: {resp2.data}")

if __name__ == '__main__':
    # Python 3.7+ 可以使用 asyncio.run()
    # asyncio.run(main())
    
    # 兼容旧版本
    loop = asyncio.get_event_loop()
    loop.run_until_complete(main())