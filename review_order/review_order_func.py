import requests
# 1.获取店铺列表
def get_store_list():
    # 目标接口地址
    url = "https://cz.younger-car.com/xlingxing/php/get_store.php"
    store_list = []
    
    try:
        # 发送GET请求（若接口需要POST，可改为requests.post）
        response = requests.get(url, timeout=10)
        # 检查请求是否成功
        response.raise_for_status()
        
        # 解析JSON数据
        json_data = response.json()
        
        # 提取data下的list数组（参照store.json的结构）
        store_data_list = json_data.get("data", {}).get("list", [])
        
        # 遍历提取store_id和platform_name
        for store in store_data_list:
            store_id = store.get("store_id")
            platform_name = store.get("platform_name")
            # 仅添加有有效数据的项
            if store_id and platform_name:
                store_list.append({
                    "store_id": store_id,
                    "platform_name": platform_name
                })
                
    except requests.exceptions.RequestException as e:
        print(f"请求接口失败: {e}")
    except ValueError as e:
        print(f"解析JSON数据失败: {e}")
    except Exception as e:
        print(f"未知错误: {e}")
    
    return store_list
# 2.获取物流渠道列表
def get_logistics_list():
    """
    请求指定接口解析JSON数据，提取type_id、code、logistics_provider_name、wid字段组成数组返回
    :return: logistics_list - 包含提取字段的字典数组
    """
    # 初始化返回数组
    logistics_list = []
    # 接口地址
    url = "https://cz.younger-car.com/xlingxing/php/get_listUsedLogisticsType.php?provider_type=2"
    
    try:
        # 发送GET请求，设置超时时间避免无限等待
        response = requests.get(url, timeout=10)
        # 检查请求是否成功（状态码200）
        response.raise_for_status()
        
        # 解析JSON数据
        json_data = response.json()
        
        # 检查data字段是否存在且为列表（参照logistics.json结构）
        if "data" in json_data and isinstance(json_data["data"], list):
            # 遍历data中的每个物流类型项
            for item in json_data["data"]:
                # 提取指定字段，处理字段可能不存在的情况（设置默认值）
                logistics_item = {
                    "type_id": item.get("type_id", ""),
                    "channel_code": item.get("code", ""),
                    "logistics_provider_name": item.get("logistics_provider_name", ""),
                    "wid": item.get("wid", "")
                }
                # 将提取的字段字典加入数组
                logistics_list.append(logistics_item)
    
    except requests.exceptions.Timeout:
        print("错误：请求接口超时")
    except requests.exceptions.HTTPError as e:
        print(f"错误：HTTP请求失败，状态码 {response.status_code}，详情：{e}")
    except requests.exceptions.RequestException as e:
        print(f"错误：请求接口异常，详情：{e}")
    except ValueError:
        print("错误：返回数据不是有效的JSON格式")
    except Exception as e:
        print(f"未知错误：{e}")
    
    return logistics_list
# 3.获取订单列表
def get_orders_list():
    """
    从指定接口获取订单数据，解析并提取指定字段返回orders_list数组
    """
    # 初始化返回数组
    orders_list = []
    # 接口地址
    url = "https://cz.younger-car.com/xlingxing/php/get_orders.php?nDaysAgo=2"
    
    try:
        # 发送GET请求获取数据（添加超时控制）
        response = requests.get(url, timeout=30)
        # 检查请求是否成功
        response.raise_for_status()
        # 解析JSON数据
        resp_data = response.json()
        
        # 检查返回数据结构是否符合预期
        if resp_data.get("code") == 0 and "data" in resp_data and "list" in resp_data["data"]:
            order_list = resp_data["data"]["list"]
            
            # 遍历每个订单提取字段
            for order in order_list:
                # 提取商品SKU（item_info是数组，取第一个元素的local_sku）
                local_sku = ""
                if order.get("item_info") and len(order["item_info"]) > 0:
                    local_sku = order["item_info"][0].get("local_sku", "")
                    
                    
                #local_sku不能为空，否则跳过该订单
                if not local_sku:
                    #print(f"订单 {order.get('global_order_no', '')} 商品SKU为空，跳过")
                    continue
                
                # 提取订单总金额（transaction_info是数组，取第一个元素的order_total_amount）
                order_total_amount = ""
                if order.get("transaction_info") and len(order["transaction_info"]) > 0:
                    order_total_amount = order["transaction_info"][0].get("order_total_amount", "")
                
                # 构造单条订单数据字典
                order_item = {
                    "global_order_no": order.get("global_order_no", ""),
                    "store_id": order.get("store_id", ""),
                    "local_sku": local_sku,
                    "global_purchase_time": order.get("global_purchase_time", 0),
                    "wid": order.get("wid", ""),
                    "receiver_country_code": order.get("address_info", {}).get("receiver_country_code", ""),
                    "city": order.get("address_info", {}).get("city", ""),
                    "postal_code": order.get("address_info", {}).get("postal_code", ""),
                    "order_total_amount": order_total_amount
                }
                orders_list.append(order_item)
                
    except requests.exceptions.RequestException as e:
        print(f"网络请求异常: {e}")
    except json.JSONDecodeError as e:
        print(f"JSON解析异常: {e}")
    except Exception as e:
        print(f"其他异常: {e}")
    
    return orders_list

# 4.获取库存详情
def get_inventory_details(sku):
    """
    根据传入的sku拼接URL并请求接口，提取指定字段返回库存详情数组
    
    参数: sku (str): 产品sku编码
    
    返回: list: 库存详情数组，每个元素为字典，包含wid, sku, product_valid_num,average_age字段；
              若请求/解析失败，返回空列表
    """
    # 基础URL模板
    base_url = "https://cz.younger-car.com/xlingxing/php/get_inventoryDetails.php"
    # 拼接完整URL
    url = f"{base_url}?sku={sku}"
    
    # 初始化返回结果
    inventory_details = []
    
    try:
        # 发送GET请求（如需设置超时/请求头可自行补充）
        response = requests.get(url, timeout=10)
        # 校验请求状态码
        response.raise_for_status()
        
        # 解析JSON数据
        json_data = response.json()
        
        # 提取data数组中的数据（参照inventory.json结构）
        data_list = json_data.get("data", [])
        for item in data_list:
            # 提取指定字段，做空值兜底
            detail = {
                "wid": item.get("wid", ""),
                "sku": item.get("sku", ""),
                "product_valid_num": item.get("product_valid_num", 0),
                "average_age": item.get("average_age", 0)
            }
            inventory_details.append(detail)
    
    except requests.exceptions.RequestException as e:
        # 捕获网络请求相关异常（超时、连接失败、状态码错误等）
        print(f"请求接口失败: {e}")
    except ValueError as e:
        # 捕获JSON解析异常
        print(f"解析JSON数据失败: {e}")
    except Exception as e:
        # 捕获其他未知异常
        print(f"获取库存详情异常: {e}")
    
    return inventory_details
# 5.获取中邮产品规格
def get_ems_product_spec(sku,platform_name="公共仓"):
    """
    根据传入的sku获取产品规格信息
    
    Args:
        sku (str): 产品的sku编码
        
    Returns:
        dict: 包含sku, weight, length, width, height的字典
              如果请求失败或数据异常，返回空字典
    """
    # 1. 拼接URL
    base_url = "http://cz.younger-car.com/yunfei_kucun/api_ems/get_product.php"
    params = {
       
        "sku": sku,
        "platform_name": platform_name
    }
    
    try:
        # 2. 发送HTTP请求
        response = requests.get(base_url, params=params, timeout=10)
        # 检查请求是否成功
        response.raise_for_status()
        
        # 3. 解析JSON数据
        json_data = response.json()
        
        # 4. 提取指定字段并构建字典
        product_spec = {
            "sku": json_data.get("sku", ""),
            "weight": json_data.get("weight", ""),
            "length": json_data.get("length", ""),
            "width": json_data.get("width", ""),
            "height": json_data.get("height", "")
        }
        
        return product_spec
    
    except requests.exceptions.RequestException as e:
        print(f"请求出错: {e}")
        return {}
    except ValueError as e:
        print(f"JSON解析失败: {e}")
        return {}
    except Exception as e:
        print(f"未知错误: {e}")
        return {}

# 6.获取运德产品规格
def get_wd_product_spec(sku,platform_name="公共仓"):
    """
    根据传入的sku从新的API地址获取产品规格信息
    
    Args:
        sku (str): 产品的sku编码
        
    Returns:
        dict: 包含sku, weight, length, width, height的字典
              如果请求失败或数据异常，返回空字典
    """
    # 1. 定义基础URL和请求参数
    base_url = "https://cz.younger-car.com/yunfei_kucun/api_wd/get_product.php"
    params = {
        "sku": sku,
        "platform_name": platform_name
    }
    
    try:
        # 2. 发送HTTP GET请求，设置超时时间10秒
        response = requests.get(base_url, params=params, timeout=10)
        # 检查HTTP响应状态码（非200则抛出异常）
        response.raise_for_status()
        
        # 3. 解析返回的JSON数据
        json_data = response.json()
        
        # 4. 提取指定字段构建目标字典
        # 处理API返回列表的情况
        if isinstance(json_data, list):
            # 如果是列表，尝试获取第一个元素
            if json_data:
                first_item = json_data[0]
                product_spec = {
                    "sku": first_item.get("sku", ""),
                    "weight": first_item.get("weight", ""),
                    "length": first_item.get("length", ""),
                    "width": first_item.get("width", ""),
                    "height": first_item.get("height", "")
                }
            else:
                return {}
        else:
            # 正常情况：返回字典
            product_spec = {
                "sku": json_data.get("sku", ""),
                "weight": json_data.get("weight", ""),
                "length": json_data.get("length", ""),
                "width": json_data.get("width", ""),
                "height": json_data.get("height", "")
            }
        
        return product_spec
    
    except requests.exceptions.RequestException as e:
        # 捕获所有请求相关异常（网络错误、超时、HTTP错误等）
        print(f"请求API出错: {e}")
        return {}
    except ValueError as e:
        # 捕获JSON解析失败的异常
        print(f"JSON数据解析失败: {e}")
        return {}
    except Exception as e:
        # 捕获其他未知异常，避免函数崩溃
        print(f"未知错误发生: {e}")
        return {}
# 7.获取中邮运费试算
def get_ems_ship_fee(postcode, weight, warehouse, channels, length, width, height):
    """
    获取中邮运费试算结果
    
    参数:
        postcode (str/int): 邮编，如 90210
        weight (float/int): 重量，如 1.5
        warehouse (str): 仓库，多个用逗号分隔，如 "USEA,USWE"
        channel (str): 物流渠道，多个用逗号分隔，如 "USPS-PRIORITY,AMAZON-GROUND"
        length (float/int): 长度，如 26
        width (float/int): 宽度，如 20
        height (float/int): 高度，如 2
    
    返回:
        list: 包含运费信息的字典列表，每个字典包含 warehouse、channel、totalFee 字段
    """
    # 基础 API URL
    base_url = "http://cz.younger-car.com/yunfei_kucun/api_ems/get_ship_fee_api.php"
    
    # 构造请求参数
    params = {
        "postcode": postcode,
        "weight": weight,
        "warehouse": warehouse,
        "channels": channels,
        "length": length,
        "width": width,
        "height": height
    }
    
    try:
        # 发送 GET 请求
        response = requests.get(base_url, params=params, timeout=30)
        # 检查请求是否成功
        response.raise_for_status()
        
        # 解析 JSON 数据
        json_data = response.json()
        
        # 提取需要的字段，构造返回的字典列表
        ship_fee = []
        for item in json_data:
            ship_fee.append({
                "warehouse": item.get("warehouse"),
                "channel_code": item.get("channel_code"),
                "totalFee": item.get("totalFee"),
                "currency": item.get("currency")
            })
        
        return ship_fee
    
    except requests.exceptions.RequestException as e:
        # 捕获请求相关异常（超时、连接失败、HTTP错误等）
        print(f"请求 API 时发生错误: {e}")
        return []
    except ValueError as e:
        # 捕获 JSON 解析异常
        print(f"解析返回的 JSON 数据失败: {e}")
        return []
    except Exception as e:
        # 捕获其他未知异常
        print(f"未知错误: {e}")
        return []

# 测试示例
# 8.获取运德运费试算
def get_wd_ship_fee(channels, country, city, postcode, weight, length, width, height, signatureService):
    """
    获取运德运费试算结果
    
    参数:
        channelCode (str): 物流渠道编码，多个用逗号分隔，如 "AMGDCA,CAUSPSGA"
        country (str): 国家，如 "US"
        city (str): 城市，如 "LOS ANGELES"
        postcode (str/int): 邮编，如 90001
        weight (float/int): 重量，如 0.079
        length (float/int): 长度，如 26
        width (float/int): 宽度，如 20
        height (float/int): 高度，如 2
        signatureService (int/str): 签收服务，0 表示不开启，1 表示开启
    
    返回:
        list: 包含运费信息的字典列表，每个字典包含 channel、totalFee、currency 字段
    """
    # 基础 API URL
    base_url = "http://cz.younger-car.com/yunfei_kucun/api_wd/get_ship_fee_api.php"
    
    # 构造请求参数（注意参数名和传入参数严格对应）
    params = {
        "channels": channels,
        "country": country,
        "city": city,
        "postcode": postcode,
        "weight": weight,
        "length": length,
        "width": width,
        "height": height,
        "signatureService": signatureService
    }
    
    try:
        # 发送 GET 请求，设置超时时间避免无限等待
        response = requests.get(base_url, params=params, timeout=30)
        # 检查 HTTP 响应状态码（非 200 则抛出异常）
        response.raise_for_status()
        
        # 解析返回的 JSON 数据
        json_data = response.json()
        
        # 提取指定字段，构造目标字典列表
        ship_fee = []
        for item in json_data:
            ship_fee.append({
                "channel_code": item.get("channel_code"),       # 物流渠道
                "totalFee": item.get("totalFee"),     # 总运费
                "currency": item.get("currency")      # 货币单位
            })
        
        return ship_fee
    
    except requests.exceptions.RequestException as e:
        # 捕获所有请求相关异常（超时、连接失败、HTTP 错误等）
        print(f"请求运德运费 API 失败: {e}")
        return []
    except ValueError as e:
        # 捕获 JSON 解析失败异常（返回数据非合法 JSON）
        print(f"解析运费 JSON 数据失败: {e}")
        return []
    except Exception as e:
        # 捕获其他未知异常，避免程序崩溃
        print(f"获取运德运费时发生未知错误: {e}")
        return []

# 测试示例
# 9.修改领星订单：通过订单号+ 仓库+物流渠道 发起请求执行
def edit_order(type_id, wid, global_order_no):
    """
    根据传入的订单参数调用编辑订单API，返回精简的结果字典
    
    Args:
        type_id (str/int): 类型ID（如203571748136745984）
        wid (str/int): 仓库ID（如5832）
        global_order_no (str/int): 全局订单号（如103662673459556100）
        
    Returns:
        dict: 包含global_order_no、code、message的结果字典
              请求失败/数据异常时返回包含默认值的字典（便于后续判断）
    """
    # 1. 定义API基础地址和请求参数
    base_url = "https://cz.younger-car.com/xlingxing/php/edit_order.php"
    params = {
        "type_id": type_id,
        "wid": wid,
        "global_order_no": global_order_no
    }
    
    # 初始化默认返回结果（保证字段始终存在）
    edit_order_result = {
        "global_order_no": str(global_order_no),  # 统一转为字符串，保证类型一致
        "code": -1,  # 异常状态码（正常返回会覆盖）
        "message": "请求失败或数据异常"  # 异常提示
    }
    
    try:
        # 2. 发送GET请求，设置10秒超时，适配HTTPS协议
        response = requests.get(base_url, params=params, timeout=10)
        # 检查HTTP响应状态码（非200则抛出异常）
        response.raise_for_status()
        
        # 3. 解析JSON数据
        json_data = response.json()
        
        # 4. 提取指定字段并更新结果字典
        edit_order_result["code"] = json_data.get("code", -1)  # 无code字段时设为-1
        edit_order_result["message"] = json_data.get("message", "未返回提示信息")
        
        return edit_order_result
    
    except requests.exceptions.RequestException as e:
        # 捕获网络超时、连接失败、HTTPS证书错误、HTTP错误等请求异常
        edit_order_result["message"] = f"请求API失败: {str(e)}"
        print(f"请求异常: {e}")
        return edit_order_result
    except ValueError as e:
        # 捕获JSON解析失败（如返回非JSON格式数据）
        edit_order_result["message"] = f"JSON解析失败: {str(e)}"
        print(f"解析异常: {e}")
        return edit_order_result
    except Exception as e:
        # 兜底捕获所有未知异常
        edit_order_result["message"] = f"未知错误: {str(e)}"
        print(f"未知异常: {e}")
        return edit_order_result

# 将发送订单数据，存入数据库
def post_order_review(
    store_id,
    global_order_no,
    local_sku,
    receiver_country_code,
    city,
    postal_code,
    wid,
    logistics_type_id,
    estimated_yunfei,    
    wd_yunfei=None,
    ems_yunfei=None,
    review_remark=None
):
    """
    向固定API地址发送订单审核的POST请求（JSON格式）
    :param store_id: 店铺ID，如"STORE001"
    :param global_order_no: 全局订单号，如"ORD20260202001"
    :param local_sku: 本地SKU，如"SKU12345"
    :param receiver_country_code: 收件人国家编码，如"US"
    :param city: 城市，如"New York"
    :param postal_code: 邮政编码，如"10001"
    :param wid: 仓库ID，整数类型，如1
    :param logistics_type_id: 物流类型ID，整数类型，如10
    :param estimated_yunfei: 预估运费，字符串格式，如"50.00"    
    :param wd_yunfei: 海外仓运费，可选参数，默认空字典
    :param wd_yunfei: EMS运费，可选参数，默认空字典
    :return: 请求响应对象（包含响应状态、响应内容等）
    :param review_remark: 审核备注，如"测试订单"
    """
    # 固定API地址
    api_url = "https://cz.younger-car.com/admin-panel/api/api_order_review.php"
    
    # 处理可选参数，默认设为空字典
    wd_yunfei = wd_yunfei if wd_yunfei is not None else {}
    ems_yunfei = ems_yunfei if ems_yunfei is not None else {}
    review_remark = review_remark if review_remark is not None else ""
    
    # 构造请求体数据（对应curl中的-d参数）
    request_data = {
        "store_id": store_id,
        "global_order_no": global_order_no,
        "local_sku": local_sku,
        "receiver_country_code": receiver_country_code,
        "city": city,
        "postal_code": postal_code,
        "wd_yunfei": wd_yunfei,
        "ems_yunfei": ems_yunfei,
        "wid": wid,
        "logistics_type_id": logistics_type_id,
        "estimated_yunfei": estimated_yunfei,
        "review_remark": review_remark
    }
    
    # 构造请求头（对应curl中的-H参数）
    request_headers = {
        "Content-Type": "application/json"
    }
    
    try:
        # 发送POST请求（json参数会自动将字典转为JSON字符串，无需手动json.dumps）
        response = requests.post(
            url=api_url,
            json=request_data,
            headers=request_headers
        )
        
        # 主动抛出HTTP错误（如404、500等状态码）
        response.raise_for_status()
        
        return response
    
    except requests.exceptions.RequestException as e:
        # 捕获所有请求相关异常并打印
        print(f"请求发送失败：{str(e)}")
        return None



# 测试示例
# 调用函数并打印结果
if __name__ == "__main__":
    store_list = get_store_list()    
    print("提取的店铺数据列表：")
    for item in store_list:
        print(item)
   
    logistics_list = get_logistics_list()
    print("提取的物流渠道数据列表：")
    for item in logistics_list:
        print(item)
  
    
    orders_list = get_orders_list()
    # 剔除wid不为空的订单，只处理wid为空的订单
    #orders_list = [item for item in orders_list if item["wid"] == ""]
    print("提取的订单数据列表：")
    for item in orders_list:
        print(item)
    
    print("获取的库存数据列表：")
    sku = "NI-C63-FL-GB"
    inventory_details = get_inventory_details(sku)
    print(inventory_details)    

    print("获取的中邮产品规格数据字典：")
    product_spec = get_ems_product_spec(sku)
    print(product_spec)
   
    print("获取的运德产品规格数据字典：")
    product_spec = get_wd_product_spec(sku)
    print(product_spec)
    
    print("获取的中邮运费试算数据列表：")
    postcode = "90210"
    weight = "1.5"
    warehouse = "USEA,USWE"
    channel = "USPS-PRIORITY,AMAZON-GROUND"
    length = "50"
    width = "20"
    height = "2"
    ship_fee = get_ems_ship_fee(postcode, weight, warehouse, channel, length, width, height)
    print(ship_fee)
    
    print("获取的运德运费试算数据列表：")
    channelCode = "AMGDCA,CAUSPSGA"
    country = "US"
    city = "LOS ANGELES"
    postcode = "90001"
    weight = "1.5"
    length = "26"
    width = "20"
    height = "20"
    signatureService = "0"
    ship_fee = get_wd_ship_fee(channelCode, country, city, postcode, weight, length, width, height, signatureService)
    print(ship_fee)

    print("修改领星订单数据列表：")
    type_id = "203571748136745984"
    wid = "5832"
    global_order_no = "103662673459556100"
    edit_order_result = edit_order(type_id, wid, global_order_no)
    print(edit_order_result)
   
