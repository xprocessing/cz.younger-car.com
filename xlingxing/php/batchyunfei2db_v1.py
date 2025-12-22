import requests
import aiohttp
import asyncio
import json
from typing import List, Dict, Optional


def to_float(value) -> Optional[float]:
    """
    安全转换值为浮点型，处理非数字情况
    """
    if value is None:
        return None
    try:
        return float(value)
    except (ValueError, TypeError):
        return None


def get_orders() -> List[Dict]:
    """
    从指定接口获取待审核订单，提取并校验所需字段
    返回：有效订单的字段字典列表
    """
    # 订单接口地址
    orders_api = "https://cz.younger-car.com/xdata/php/get_orders.php"
    
    try:
        # 发送GET请求获取订单数据
        response = requests.get(
            url=orders_api,
            timeout=30,
            headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
        )
        response.raise_for_status()  # 抛出HTTP状态码异常
        orders_data = response.json()

        # 校验数据基础结构
        if not isinstance(orders_data, dict) or "data" not in orders_data or "list" not in orders_data["data"]:
            print("❌ 订单数据结构异常，缺少data/list字段")
            return []

        order_list = orders_data["data"]["list"]
        valid_orders = []

        # 遍历订单列表，提取字段
        for idx, order in enumerate(order_list, 1):
            try:
                # 1. 提取订单号
                global_order_no = order.get("global_order_no")
                if not global_order_no:
                    print(f"⚠️ 第{idx}个订单缺少【订单号】，跳过")
                    continue

                # 2. 提取地址信息
                address_info = order.get("address_info", {})
                receiver_country_code = address_info.get("receiver_country_code")
                postcode = address_info.get("postal_code")
                city = address_info.get("city")

                # 3. 提取物流信息（并转换为浮点型）
                logistics_info = order.get("logistics_info", {})
                pre_fee_weight = to_float(logistics_info.get("pre_fee_weight"))  # 重量(g)
                pre_pkg_length = to_float(logistics_info.get("pre_pkg_length"))  # 长度
                pre_pkg_width = to_float(logistics_info.get("pre_pkg_width"))    # 宽度
                pre_pkg_height = to_float(logistics_info.get("pre_pkg_height"))  # 高度

                # 4. 校验所有必填字段是否完整
                missing_fields = []
                if not receiver_country_code:
                    missing_fields.append("国家代码")
                if not postcode:
                    missing_fields.append("邮编")
                if not city:
                    missing_fields.append("城市")
                if pre_fee_weight is None:
                    missing_fields.append("重量")
                if pre_pkg_length is None:
                    missing_fields.append("长度")
                if pre_pkg_width is None:
                    missing_fields.append("宽度")
                if pre_pkg_height is None:
                    missing_fields.append("高度")

                if missing_fields:
                    print(f"⚠️ 第{idx}个订单({global_order_no})缺少字段：{','.join(missing_fields)}，跳过")
                    continue

                # 5. 换算重量为千克
                weight_kg = pre_fee_weight / 1000

                # 6. 整理有效订单数据
                valid_orders.append({
                    "global_order_no": global_order_no,
                    "receiver_country_code": receiver_country_code,
                    "postcode": postcode,
                    "city": city,
                    "weight": weight_kg,
                    "length": pre_pkg_length,
                    "width": pre_pkg_width,
                    "height": pre_pkg_height
                })

            except Exception as e:
                print(f"⚠️ 处理第{idx}个订单时出错：{str(e)}，跳过")
                continue

        return valid_orders

    except requests.exceptions.RequestException as e:
        print(f"❌ 获取订单数据失败：{str(e)}")
        return []
    except json.JSONDecodeError:
        print(f"❌ 订单接口返回非JSON格式数据")
        return []
    except Exception as e:
        print(f"❌ 处理订单数据时发生未知错误：{str(e)}")
        return []


async def fetch_single_url(session: aiohttp.ClientSession, url: str, order_no: str):
    """
    异步请求单个订单的查询URL
    """
    try:
        print(f"\n📡 开始请求订单 {order_no}：{url}")
        async with session.get(
            url=url,
            timeout=30,
            headers={"User-Agent": "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36"}
        ) as response:
            # 获取响应结果（仅打印前200字符避免输出过长）
            response_text = await response.text()
            print(f"✅ 订单 {order_no} 请求完成 | 状态码：{response.status} | 响应内容：{response_text[:200]}...")
    except aiohttp.ClientError as e:
        print(f"❌ 订单 {order_no} 请求失败：{str(e)}")
    except asyncio.TimeoutError:
        print(f"❌ 订单 {order_no} 请求超时（30秒）")
    except Exception as e:
        print(f"❌ 订单 {order_no} 请求发生未知错误：{str(e)}")


async def batch_fetch_urls(valid_orders: List[Dict]):
    """
    批量异步请求订单查询URL，每隔5秒发起一个请求
    """
    if not valid_orders:
        print("\n📭 没有有效订单需要处理")
        return

    # 拼接每个订单的查询URL
    base_url = "https://cz.younger-car.com/chayunfei2db.php"
    request_tasks = []
    for order in valid_orders:
        # 拼接URL参数
        url_params = {
            "global_order_no": order["global_order_no"],
            "receiver_country_code": order["receiver_country_code"],
            "postcode": order["postcode"],
            "weight": order["weight"],
            "length": order["length"],
            "width": order["width"],
            "height": order["height"],
            "city": order["city"]
        }
        # 构建完整URL（自动处理参数编码）
        query_url = f"{base_url}?{'&'.join([f'{k}={v}' for k, v in url_params.items()])}"
        request_tasks.append((query_url, order["global_order_no"]))

    # 创建异步会话，逐个请求（间隔5秒）
    async with aiohttp.ClientSession() as session:
        for idx, (url, order_no) in enumerate(request_tasks):
            await fetch_single_url(session, url, order_no)
            # 最后一个请求不需要等待
            if idx < len(request_tasks) - 1:
                print(f"\n⏳ 等待5秒后发送下一个请求...")
                await asyncio.sleep(5)


def main():
    """
    主函数：获取订单 → 异步请求查询URL
    """
    print("===== 开始处理订单 =====")
    # 1. 获取有效订单
    valid_orders = get_orders()
    print(f"\n📊 共获取到 {len(valid_orders)} 个有效订单")

    # 2. 异步请求订单查询URL
    if valid_orders:
        print("\n===== 开始异步请求订单查询URL =====")
        asyncio.run(batch_fetch_urls(valid_orders))
    else:
        print("\n🚫 无有效订单，程序结束")

    print("\n===== 订单处理完成 =====")


if __name__ == "__main__":
    main()