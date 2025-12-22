import json
import aiohttp
import asyncio
import time
import requests
import schedule
import logging
from typing import Dict, Set, List, Optional

# ===================== 配置项 =====================
# 日志配置
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    datefmt='%Y-%m-%d %H:%M:%S'
)
logger = logging.getLogger(__name__)

# API地址配置
GET_ORDERS_URL = "https://cz.younger-car.com/xlingxing/php/get_orders.php"
QUERY_FREIGHT_TEMPLATE = "https://cz.younger-car.com/yunfei_kucun/chayunfei2db.php?global_order_no={}&receiver_country_code={}&postcode={}&weight={}&length={}&width={}&height={}&city={}"

# 文件路径
SKU_FILE_PATH = "skuweightlwh.json"

# 时间配置（秒）
ASYNC_REQUEST_INTERVAL = 5  # 异步请求间隔
REQUEST_TIMEOUT = 30        # 请求超时时间
SCHEDULE_INTERVAL = 3       # 定时任务间隔（分钟）

# 全局变量
PROCESSED_ORDERS: Set[str] = set()  # 存储已处理的订单号（内存级）
SKU_DATA: Dict[str, Dict[str, float]] = {}  # SKU重量尺寸数据


# ===================== 核心函数 =====================
def load_sku_data() -> None:
    """加载SKU重量尺寸数据（每次执行任务时重新加载，支持文件更新）"""
    global SKU_DATA
    try:
        with open(SKU_FILE_PATH, 'r', encoding='utf-8') as f:
            SKU_DATA = json.load(f)
        logger.info(f"成功加载SKU数据，共{len(SKU_DATA)}个SKU")
    except FileNotFoundError:
        logger.error(f"SKU文件不存在：{SKU_FILE_PATH}")
        SKU_DATA = {}
    except json.JSONDecodeError as e:
        logger.error(f"SKU文件格式错误：{str(e)}")
        SKU_DATA = {}
    except Exception as e:
        logger.error(f"加载SKU数据失败：{str(e)}")
        SKU_DATA = {}


def get_pending_orders() -> List[Dict]:
    """获取待审核订单列表"""
    try:
        # 发起同步请求获取订单数据
        response = requests.get(
            GET_ORDERS_URL,
            timeout=REQUEST_TIMEOUT,
            headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
        )
        response.raise_for_status()  # 非200状态码抛出异常
        
        # 解析JSON数据
        order_json = response.json()
        order_list = order_json.get("data", {}).get("list", [])
        logger.info(f"成功获取待审核订单，共{len(order_list)}条")
        return order_list

    except requests.exceptions.RequestException as e:
        logger.error(f"获取订单失败：{str(e)}")
        return []
    except json.JSONDecodeError as e:
        logger.error(f"订单数据解析失败：{str(e)}")
        return []
    except Exception as e:
        logger.error(f"处理订单数据异常：{str(e)}")
        return []


async def query_freight(session: aiohttp.ClientSession, order_no: str, url: str) -> None:
    """异步发起运费查询请求"""
    try:
        async with session.get(
            url,
            timeout=aiohttp.ClientTimeout(total=REQUEST_TIMEOUT),
            headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
        ) as response:
            # 记录响应信息（仅打印前100字符避免日志过长）
            resp_text = await response.text()
            logger.info(
                f"订单{order_no}查询完成 | 状态码：{response.status} "
                f"| 响应：{resp_text[:100]}..."
            )
    except asyncio.TimeoutError:
        logger.error(f"订单{order_no}查询超时")
    except aiohttp.ClientError as e:
        logger.error(f"订单{order_no}查询失败：{str(e)}")
    except Exception as e:
        logger.error(f"订单{order_no}查询异常：{str(e)}")


async def process_orders_async() -> None:
    """异步处理订单并发起运费查询"""
    # 获取待审核订单
    order_list = get_pending_orders()
    if not order_list:
        logger.info("暂无待审核订单，跳过处理")
        return

    # 筛选有效订单（字段完整+未处理+SKU数据存在）
    valid_tasks = []
    for order in order_list:
        # 1. 提取订单号并检查是否已处理
        order_no = order.get("global_order_no")
        if not order_no:
            logger.warning("订单缺少global_order_no，跳过")
            continue
        if order_no in PROCESSED_ORDERS:
            logger.info(f"订单{order_no}已处理，跳过")
            continue

        # 2. 提取地址信息
        addr_info = order.get("address_info", {})
        country_code = addr_info.get("receiver_country_code")
        postcode = addr_info.get("postal_code")
        city = addr_info.get("city")
        if not all([country_code, postcode, city]):
            logger.warning(f"订单{order_no}地址信息不完整，跳过")
            continue

        # 3. 提取SKU信息
        item_info = order.get("item_info", [])
        if not item_info:
            logger.warning(f"订单{order_no}无商品信息，跳过")
            continue
        local_sku = item_info[0].get("local_sku")
        if not local_sku:
            logger.warning(f"订单{order_no}无local_sku，跳过")
            continue

        # 4. 获取SKU对应的重量尺寸
        sku_info = SKU_DATA.get(local_sku)
        if not sku_info:
            logger.warning(f"订单{order_no}的SKU[{local_sku}]无重量尺寸数据，跳过")
            continue
        weight = sku_info.get("weight")
        length = sku_info.get("length")
        width = sku_info.get("width")
        height = sku_info.get("height")
        if not all([weight, length, width, height]):
            logger.warning(f"订单{order_no}的SKU[{local_sku}]尺寸数据不完整，跳过")
            continue

        # 5. 拼接查询URL
        query_url = QUERY_FREIGHT_TEMPLATE.format(
            order_no, country_code, postcode,
            weight, length, width, height, city
        )
        valid_tasks.append({"order_no": order_no, "url": query_url})
        
        # 标记为已处理（避免重复）
        PROCESSED_ORDERS.add(order_no)

    # 无有效任务直接返回
    if not valid_tasks:
        logger.info("暂无有效订单需要查询运费")
        return

    # 异步执行查询（每隔5秒一个请求）
    async with aiohttp.ClientSession() as session:
        for idx, task in enumerate(valid_tasks):
            logger.info(f"开始处理第{idx+1}/{len(valid_tasks)}个订单：{task['order_no']}")
            await query_freight(session, task["order_no"], task["url"])
            
            # 最后一个任务不需要间隔
            if idx < len(valid_tasks) - 1:
                logger.info(f"等待{ASYNC_REQUEST_INTERVAL}秒后处理下一个订单")
                await asyncio.sleep(ASYNC_REQUEST_INTERVAL)


def run_task() -> None:
    """执行单次任务（加载SKU+处理订单）"""
    logger.info("="*50 + " 开始执行定时任务 " + "="*50)
    # 重新加载SKU数据（支持文件热更新）
    load_sku_data()
    # 异步处理订单
    asyncio.run(process_orders_async())
    logger.info("="*50 + " 定时任务执行完成 " + "="*50)


# ===================== 程序入口 =====================
if __name__ == "__main__":
    try:
        # 初始化加载SKU数据
        load_sku_data()
        
        # 立即执行一次任务
        logger.info("首次执行任务（非定时）")
        run_task()

        # 配置定时任务（每隔5分钟执行）
        schedule.every(SCHEDULE_INTERVAL).minutes.do(run_task)
        logger.info(f"定时任务已启动，每隔{SCHEDULE_INTERVAL}分钟执行一次")

        # 循环运行定时任务
        while True:
            schedule.run_pending()
            time.sleep(1)

    except KeyboardInterrupt:
        logger.info("程序被用户手动中断，退出")
    except Exception as e:
        logger.error(f"程序异常退出：{str(e)}")