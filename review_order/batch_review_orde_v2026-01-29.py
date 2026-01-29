import review_order_func as review_order
import time

# 汇率配置（美元转人民币）
USD_TO_CNY_RATE = 7.0

def get_platform_logistics_mapping(logistics_list):
    """
    构建平台与物流渠道的映射关系，便于快速匹配
    :param logistics_list: 物流渠道列表（来自get_logistics_list）
    :return: 映射字典，key为平台关键词，value为匹配的物流渠道列表
    """
    mapping = {
        "Amazon": [],
        "eBay": [],
        "Shopify": []
    }
    for logistics in logistics_list:
        provider_name = logistics.get("logistics_provider_name", "")
        # 匹配对应平台的物流渠道
        if "亚马逊" in provider_name:
            mapping["Amazon"].append(logistics)
        if "eBay" in provider_name:
            mapping["eBay"].append(logistics)
        if "独立站" in provider_name:
            mapping["Shopify"].append(logistics)
    print(f"✅ 平台-物流渠道映射构建完成：{mapping.keys()}")
    return mapping

def get_store_platform_mapping(store_list):
    """
    构建店铺ID与平台名称的映射关系
    :param store_list: 店铺列表（来自get_store_list）
    :return: 映射字典，key为store_id，value为platform_name
    """
    mapping = {store["store_id"]: store["platform_name"] for store in store_list}
    print(f"✅ 店铺-平台映射构建完成，共{len(mapping)}个店铺")
    return mapping

def filter_valid_logistics_by_inventory(logistics_list, valid_wids):
    """
    根据有货的仓库ID筛选可用的物流渠道
    :param logistics_list: 初始匹配的物流渠道列表
    :param valid_wids: 有货的仓库ID列表
    :return: 筛选后的物流渠道列表
    """
    valid_logistics = [
        logis for logis in logistics_list 
        if logis.get("wid") in valid_wids
    ]
    print(f"✅ 库存筛选后可用物流渠道数量：{len(valid_logistics)}（有货仓库：{valid_wids}）")
    return valid_logistics

def get_min_fee_logistics(fee_list, logistics_list):
    """
    从运费列表中找到最小运费对应的物流渠道信息
    :param fee_list: 运费试算列表
    :param logistics_list: 对应的物流渠道列表
    :return: 最小运费信息（字典），包含totalFee、type_id、wid、channel_code
    """
    if not fee_list or not logistics_list:
        print("⚠️ 运费列表/物流渠道列表为空，无最小运费可选")
        return None
    
    # 构建运费与物流渠道的关联
    fee_logis_map = {}
    for fee_item in fee_list:
        channel_code = fee_item.get("channel_code")
        total_fee = fee_item.get("totalFee", 0)
        # 找到对应channel_code的物流渠道
        for logis in logistics_list:
            if logis.get("channel_code") == channel_code:
                fee_logis_map[total_fee] = {
                    "totalFee": total_fee,
                    "type_id": logis.get("type_id"),
                    "wid": logis.get("wid"),
                    "channel_code": channel_code,
                    "currency": fee_item.get("currency")
                }
                break
    
    # 找到最小运费
    if not fee_logis_map:
        print("⚠️ 未匹配到运费对应的物流渠道")
        return None
    min_fee = min(fee_logis_map.keys())
    min_fee_info = fee_logis_map[min_fee]
    print(f"✅ 最小运费筛选完成：{min_fee_info}")
    return min_fee_info

def process_single_order(order, store_platform_map, platform_logistics_map, logistics_list_all):
    """
    处理单个订单的完整逻辑：匹配物流→筛选库存→计算运费→选择最优渠道
    :param order: 单个订单字典
    :param store_platform_map: 店铺-平台映射
    :param platform_logistics_map: 平台-物流渠道映射
    :param logistics_list_all: 所有物流渠道列表
    :return: 订单处理结果字典
    """
    order_no = order.get("global_order_no")
    store_id = order.get("store_id")
    sku = order.get("local_sku")
    postal_code = order.get("postal_code")
    country_code = order.get("receiver_country_code")
    city = order.get("city")
    
    print(f"\n=====================================================")
    print(f"📌 开始处理订单：{order_no}（SKU：{sku} | 店铺ID：{store_id}）")
    print(f"📋 订单基础信息：邮编={postal_code} | 国家={country_code} | 城市={city}")
    
    # 步骤1：匹配订单对应的平台和初始物流渠道
    platform_name = store_platform_map.get(store_id, "")
    print(f"✅ 订单对应平台：{platform_name}（店铺ID：{store_id}）")
    initial_logistics = platform_logistics_map.get(platform_name, [])
    if not initial_logistics:
        print(f"❌ 订单{order_no}无匹配的初始物流渠道，跳过")
        return {"order_no": order_no, "status": "failed", "reason": "无匹配物流渠道"}
    
    # 步骤2：获取有货的仓库ID
    inventory_details = review_order.get_inventory_details(sku)
    print(f"✅ 库存查询结果：{inventory_details}")
    valid_wids = [item["wid"] for item in inventory_details if item.get("product_valid_num", 0) > 0]
    if not valid_wids:
        print(f"❌ 订单{order_no}SKU={sku}无可用库存，跳过")
        return {"order_no": order_no, "status": "failed", "reason": "无可用库存"}
    
    # 步骤3：根据库存筛选可用物流渠道
    valid_logistics = filter_valid_logistics_by_inventory(initial_logistics, valid_wids)
    if not valid_logistics:
        print(f"❌ 订单{order_no}无库存匹配的可用物流渠道，跳过")
        return {"order_no": order_no, "status": "failed", "reason": "无库存匹配的物流渠道"}
    
    # 拆分中邮/运德物流渠道
    ems_logistics = [logis for logis in valid_logistics if "中邮" in logis.get("logistics_provider_name", "")]
    wd_logistics = [logis for logis in valid_logistics if "运德" in logis.get("logistics_provider_name", "")]
    print(f"✅ 中邮可用物流渠道：{len(ems_logistics)} | 运德可用物流渠道：{len(wd_logistics)}")
    
    # 初始化运费结果
    ems_min_fee = None
    wd_min_fee = None
    
    # 步骤4：处理中邮运费试算
    if ems_logistics:
        # 获取中邮商品规格
        ems_spec = review_order.get_ems_product_spec(sku)
        print(f"✅ 中邮商品规格（SKU={sku}）：{ems_spec}")
        if not ems_spec:
            print(f"⚠️ 中邮商品规格获取失败，跳过中邮运费计算")
        else:
            # 构造中邮运费参数
            ems_channels = ",".join([logis["channel_code"] for logis in ems_logistics])
            ems_postcode = postal_code
            ems_weight = ems_spec.get("weight", 0)
            ems_length = ems_spec.get("length", 0)
            ems_width = ems_spec.get("width", 0)
            ems_height = ems_spec.get("height", 0)
            ems_warehouse = "USEA,USWE"
            
            # 调用中邮运费试算（增加超时提示）
            print(f"⏳ 正在请求中邮运费试算（渠道：{ems_channels}），请等待...")
            time.sleep(10)  # 模拟等待（可选）
            ems_fee_list = review_order.get_ems_ship_fee(
                ems_postcode, ems_weight, ems_warehouse,
                ems_channels, ems_length, ems_width, ems_height
            )
            print(f"✅ 中邮运费试算结果：{ems_fee_list}")
            ems_min_fee = get_min_fee_logistics(ems_fee_list, ems_logistics)
            if ems_min_fee:
                ems_min_fee["totalFee_cny"] = float(ems_min_fee["totalFee"])  # 中邮本身是人民币
                print(f"🏆 中邮最小运费：{ems_min_fee['totalFee']} {ems_min_fee['currency']}（¥{ems_min_fee['totalFee_cny']}）")
    
    # 步骤5：处理运德运费试算
    if wd_logistics:
        # 获取运德商品规格
        wd_spec = review_order.get_wd_product_spec(sku)
        print(f"✅ 运德商品规格（SKU={sku}）：{wd_spec}")
        if not wd_spec:
            print(f"⚠️ 运德商品规格获取失败，跳过运德运费计算")
        else:
            # 构造运德运费参数
            wd_channels = ",".join([logis["channel_code"] for logis in wd_logistics])
            wd_country = country_code
            wd_city = city
            wd_postcode = postal_code
            wd_weight = wd_spec.get("weight", 0)
            wd_length = wd_spec.get("length", 0)
            wd_width = wd_spec.get("width", 0)
            wd_height = wd_spec.get("height", 0)
            wd_signature = 0
            
            # 调用运德运费试算
            print(f"⏳ 正在请求运德运费试算（渠道：{wd_channels}），请等待...")
            time.sleep(10)  # 模拟等待（可选）
            wd_fee_list = review_order.get_wd_ship_fee(
                wd_channels, wd_country, wd_city, wd_postcode,
                wd_weight, wd_length, wd_width, wd_height, wd_signature
            )
            print(f"✅ 运德运费试算结果：{wd_fee_list}")
            wd_min_fee = get_min_fee_logistics(wd_fee_list, wd_logistics)
            if wd_min_fee:
                # 美元转人民币
                wd_min_fee["totalFee_usd"] = float(wd_min_fee["totalFee"])
                wd_min_fee["totalFee_cny"] = wd_min_fee["totalFee_usd"] * USD_TO_CNY_RATE
                print(f"🏆 运德最小运费：{wd_min_fee['totalFee']} {wd_min_fee['currency']}（¥{wd_min_fee['totalFee_cny']}）")
    
    # 步骤6：比较中邮和运德的最小运费（统一转人民币）
    final_choice = None
    ems_fee_cny = ems_min_fee["totalFee_cny"] if ems_min_fee else float("inf")
    wd_fee_cny = wd_min_fee["totalFee_cny"] if wd_min_fee else float("inf")
    
    if ems_fee_cny < wd_fee_cny:
        final_choice = ems_min_fee
        final_choice["source"] = "中邮"
    elif wd_fee_cny < ems_fee_cny:
        final_choice = wd_min_fee
        final_choice["source"] = "运德"
    else:
        print("❌ 中邮/运德运费均无有效数据，无法选择最优渠道")
        return {"order_no": order_no, "status": "failed", "reason": "无有效运费数据"}
    
    print(f"✅ 最终最优选择：{final_choice['source']}（运费¥{final_choice['totalFee_cny']}）")
    
    # 步骤7：组装订单处理结果
    result = {
        "order_no": order_no,
        "status": "success",
        "order_info": order,
        "inventory_info": inventory_details,
        "ems_spec": ems_spec,
        "wd_spec": wd_spec,
        "ems_min_fee": ems_min_fee,
        "wd_min_fee": wd_min_fee,
        "final_choice": final_choice
    }
    return result

def confirm_and_edit_order(process_result):
    """
    弹窗提示订单信息并确认是否修改订单
    :param process_result: 订单处理结果字典
    :return: 修改订单的结果
    """
    if process_result["status"] != "success":
        print(f"❌ 订单{process_result['order_no']}处理失败，无需修改")
        return None
    
    order_no = process_result["order_no"]
    final_choice = process_result["final_choice"]
    
    # 打印确认提示信息
    print(f"\n=====================================================")
    print(f"📝 订单修改确认（订单号：{order_no}）")
    print(f"1. 订单基础信息：{process_result['order_info']}")
    print(f"2. 库存信息：{process_result['inventory_info']}")
    print(f"3. 中邮规格：{process_result['ems_spec']}")
    print(f"4. 运德规格：{process_result['wd_spec']}")
    print(f"5. 中邮最小运费：{process_result['ems_min_fee']}")
    print(f"6. 运德最小运费：{process_result['wd_min_fee']}")
    print(f"7. 最终选择：{final_choice}")
    print(f"=====================================================")
    
    # 交互确认
    confirm = input("❓ 是否确认修改该订单？(y/n)：")
    if confirm.lower() != "y":
        print(f"✅ 用户取消修改订单{order_no}")
        return {"order_no": order_no, "edit_status": "cancelled"}
    
    # 调用修改订单函数
    type_id = final_choice.get("type_id")
    wid = final_choice.get("wid")
    print(f"⏳ 正在修改订单{order_no}（type_id：{type_id} | wid：{wid}）...")
    edit_result = review_order.edit_order(type_id, wid, order_no)
    print(f"✅ 订单修改结果：{edit_result}")
    return {
        "order_no": order_no,
        "edit_status": "completed",
        "edit_result": edit_result
    }

def main():
    """主函数：执行完整的订单批量处理流程"""
    print("🚀 开始执行批量订单审核流程...")
    
    # 1. 初始化基础数据
    print("\n【第一步】获取基础数据（店铺/物流渠道）")
    store_list = review_order.get_store_list()
    print(f"✅ 获取店铺列表：共{len(store_list)}条")
    
    logistics_list_all = review_order.get_logistics_list()
    print(f"✅ 获取物流渠道列表：共{len(logistics_list_all)}条")
    
    # 构建映射关系
    store_platform_map = get_store_platform_mapping(store_list)
    platform_logistics_map = get_platform_logistics_mapping(logistics_list_all)
    
    # 2. 获取订单列表并筛选（只处理wid=0的订单，测试取前5个）
    print("\n【第二步】获取并筛选订单列表")
    orders_list = review_order.get_orders_list()
    print(f"✅ 原始订单总数：{len(orders_list)}")
    
    # 筛选wid=0的订单
    target_orders = [order for order in orders_list if order.get("wid") == "0"]
    print(f"✅ 筛选后wid=0的订单数：{len(target_orders)}")
    
    # 测试阶段取前5个
    test_orders = target_orders[:10]
    print(f"✅ 测试阶段处理前{len(test_orders)}个订单：{[o['global_order_no'] for o in test_orders]}")
    
    # 3. 遍历处理每个订单
    print("\n【第三步】批量处理订单")
    process_results = []
    for order in test_orders:
        result = process_single_order(order, store_platform_map, platform_logistics_map, logistics_list_all)
        process_results.append(result)
        
        # 处理完单个订单后执行修改确认
        if result and result["status"] == "success":
            edit_result = confirm_and_edit_order(result)
            result["edit_result"] = edit_result
    
    # 4. 输出最终汇总
    print("\n=====================================================")
    print("📊 批量订单处理汇总")
    success_count = len([r for r in process_results if r and r["status"] == "success"])
    fail_count = len(process_results) - success_count
    print(f"✅ 处理成功：{success_count} 个")
    print(f"❌ 处理失败：{fail_count} 个")
    for res in process_results:
        if res:
            print(f"- 订单{res['order_no']}：{res['status']} | 最终选择：{res.get('final_choice', {}).get('source', '无')}")

if __name__ == "__main__":
    main()