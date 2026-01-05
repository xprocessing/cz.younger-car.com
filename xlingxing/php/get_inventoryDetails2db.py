import asyncio
import aiohttp
from all_skus_data import all_skus_list
#需要定期更新sku数据
# 并发请求数量
CONCURRENT_NUM = 10
# 每批请求间隔时间(秒)
INTERVAL = 3
# 请求URL模板
URL_TEMPLATE = "https://cz.younger-car.com/xlingxing/php/get_inventoryDetails2db.php?sku={}"

async def fetch_sku(session, sku):
    """发送单个SKU的请求并处理响应"""
    url = URL_TEMPLATE.format(sku)
    try:
        async with session.get(url) as response:
            # 这里可以根据需要处理响应内容，例如获取状态码或响应文本
            status = response.status
            # 如果需要获取响应内容可以使用：await response.text()
            print(f"SKU: {sku}, 状态码: {status}")
            return {"sku": sku, "status": status}
    except Exception as e:
        print(f"SKU: {sku} 请求失败: {str(e)}")
        return {"sku": sku, "error": str(e)}

async def process_batch(session, batch):
    """处理一批SKU（并发请求）"""
    tasks = [fetch_sku(session, sku) for sku in batch]
    results = await asyncio.gather(*tasks)
    return results

async def main():
    """主函数：分批处理所有SKU"""
    # 分割SKU列表为多个批次
    batches = [all_skus_list[i:i+CONCURRENT_NUM] for i in range(0, len(all_skus_list), CONCURRENT_NUM)]
    
    async with aiohttp.ClientSession() as session:
        for i, batch in enumerate(batches, 1):
            print(f"开始处理第 {i}/{len(batches)} 批，共 {len(batch)} 个SKU")
            await process_batch(session, batch)
            # 最后一批不需要等待
            if i < len(batches):
                print(f"等待 {INTERVAL} 秒后处理下一批...")
                await asyncio.sleep(INTERVAL)
    
    print("所有SKU库存更新处理完成，请每周更新sku list数据。")

if __name__ == "__main__":
    asyncio.run(main())