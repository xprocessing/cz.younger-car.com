import asyncio
from lingxing_api import LingXingApiClient

async def main():
    # 创建客户端实例（适合多次调用）
    client = LingXingApiClient()
    path1 = "/pb/mp/order/v2/list"
    body1 = {
        "offset": 0,
        "length": 10,
        "date_type":"update_time",
        "start_time":"2025-12-01 00:00:00",
        "end_time":"2025-12-30 23:59:59",
        "order_status":5
    }
    resp1 = await client.request_api(path1, body1)
    # 可以添加打印响应的代码
    print(resp1.data)
    #将结果保存为json文件
    with open("order_list1.json", "w", encoding="utf-8") as f:
        import json
        json.dump(resp1.data, f, ensure_ascii=False, indent=4)   
    ##将结果保存为csv文件
    import pandas as pd
    df = pd.DataFrame(resp1.data)
    df.to_csv("order_list1.csv", index=False, encoding="utf-8-sig")




if __name__ == '__main__':
    # 运行异步函数
    asyncio.run(main())