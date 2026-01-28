import review_order_func as review_order
import time
import json

# 汇率配置：美元转人民币
USD_TO_CNY_RATE = 7.0

def match_logistics_by_platform(platform_name, logistics_list):
    """
    根据平台名称匹配对应的物流渠道
    :param platform_name: 店铺平台名称（如Amazon/eBay/Shopify）
    :param logistics_list: 物流渠道列表
    :return: 匹配的物流渠道列表
    """
    matched_logistics = []
    for logistics in logistics_list:
        logistics_provider = logistics.get("logistics_provider_name", "")
        # 匹配规则：Amazon→包含亚马逊，eBay→包含eBay，Shopify→包含独立站
        if (platform_name == "Amazon" and "亚马逊" in logistics_provider) or \
           (platform_name == "eBay" and "eBay" in logistics_provider) or \
           (platform_name == "Shopify" and "独立站" in logistics_provider):
            matched_logistics.append(logistics)
    print(f"【match_logistics_by_platform】平台{platform_name}匹配物流渠道数：{len(matched_logistics)}")
    print(f"【match_logistics_by_platform】匹配结果：{[log.get('channel_code') for log in matched_logistics]}")
    return matched_logistics

def filter_logistics_by_wid(logistics_list, valid_wids):
    """
    根据有货的仓库ID筛选物流渠道
    :param logistics_list: 待筛选的物流渠道列表
    :param valid_wids: 有货的仓库ID列表
    :return: 筛选后的物流渠道列表
    """
    if not valid_wids:
        print("【filter_logistics_by_wid】有效仓库ID为空，返回空列表")
        return []
    filtered = [log for log in logistics_list if log.get("wid") in valid_wids]
    print(f"【filter_logistics_by_wid】有效仓库ID：{valid_wids}")
    print(f"【filter_logistics_by_wid】筛选后物流渠道数：{len(filtered)}")
    print(f"【filter_logistics_by_wid】筛选结果：{[log.get('channel_code') for log in filtered]}")
    return filtered

def filter_logistics_by_provider(logistics_list, provider_keyword):
    """
    新增：根据物流商名称关键词筛选（中邮/运德）
    :param logistics_list: 物流渠道列表
    :param provider_keyword: 筛选关键词（"中邮"/"运德"）
    :return: 筛选后的物流渠道列表
    """
    filtered = [log for log in logistics_list if provider_keyword in log.get("logistics_provider_name", "")]
    print(f"【filter_logistics_by_provider】按关键词{provider_keyword}筛选，结果数：{len(filtered)}")
    print(f"【filter_logistics_by_provider】筛选结果：{[log.get('channel_code') for log in filtered]}")
    return filtered

def get_min_fee_shipment(ship_fee_list, logistics_list):
    """
    从运费列表中找到最小运费对应的物流信息
    :param ship_fee_list: 运费试算列表
    :param logistics_list: 物流渠道列表
    :return: 最小运费信息字典（包含totalFee、type_id、wid、channel_code）
    """
    print(f"【get_min_fee_shipment】运费试算列表原始数据：{ship_fee_list}")
    if not ship_fee_list:
        print("【get_min_fee_shipment】运费试算列表为空，返回None")
        return None
    
    # 过滤掉运费为空/0的情况
    valid_fee_list = [item for item in ship_fee_list if item.get("totalFee") and float(item.get("totalFee")) > 0]
    print(f"【get_min_fee_shipment】过滤后有效运费列表：{valid_fee_list}")
    if not valid_fee_list:
        print("【get_min_fee_shipment】无有效运费数据，返回None")
        return None
    
    # 找到最小运费项
    min_fee_item = min(valid_fee_list, key=lambda x: float(x.get("totalFee")))
    min_channel_code = min_fee_item.get("channel_code")
    print(f"【get_min_fee_shipment】最小运费项：{min_fee_item}")
    print(f"【get_min_fee_shipment】最小运费对应渠道编码：{min_channel_code}")
    
    # 匹配对应的type_id和wid
    matched_log = next((log for log in logistics_list if log.get("channel_code") == min_channel_code), None)
    if not matched_log:
        print(f"【get_min_fee_shipment】未匹配到{min_channel_code}对应的物流信息，返回None")
        return None
    
    result = {
        "totalFee": float(min_fee_item.get("totalFee")),
        "currency": min_fee_item.get("currency"),
        "type_id": matched_log.get("type_id"),
        "wid": matched_log.get("wid"),
        "channel_code": min_channel_code
    }
    print(f"【get_min_fee_shipment】最终最小运费信息：{result}")
    return result

def process_single_order(order, store_list, logistics_list):
    """
    处理单个订单的完整逻辑：匹配物流→筛选库存→计算运费→选择最优
    :param order: 单个订单字典
    :param store_list: 店铺列表
    :param logistics_list: 物流渠道列表
    :return: 订单处理结果字典
    """
    global_order_no = order.get("global_order_no")
    local_sku = order.get("local_sku")
    store_id = order.get("store_id")
    receiver_country_code = order.get("receiver_country_code")
    city = order.get("city")
    postal_code = order.get("postal_code")
    
    print(f"\n===== 开始处理订单 {global_order_no} =====")
    print(f"【process_single_order】订单基础信息：SKU={local_sku} | 店铺ID={store_id} | 收货国家={receiver_country_code} | 城市={city} | 邮编={postal_code}")
    
    result = {
        "global_order_no": global_order_no,
        "status": "failed",
        "message": "",
        "final_fee": 0,
        "final_type_id": "",
        "final_wid": "",
        "final_channel_code": ""
    }

    # 步骤1：根据store_id匹配platform_name
    print("\n----- 步骤1：匹配店铺平台 -----")
    store_item = next((s for s in store_list if s.get("store_id") == store_id), None)
    if not store_item:
        result["message"] = "未匹配到店铺信息"
        print(f"【process_single_order】{result['message']}")
        return result
    platform_name = store_item.get("platform_name")
    print(f"【process_single_order】匹配到店铺：ID={store_id} | 平台={platform_name}")

    # 步骤2：根据platform_name匹配物流渠道
    print("\n----- 步骤2：匹配平台对应物流渠道 -----")
    matched_logistics = match_logistics_by_platform(platform_name, logistics_list)
    if not matched_logistics:
        result["message"] = "未匹配到可用物流渠道"
        print(f"【process_single_order】{result['message']}")
        return result

    # 步骤3：根据sku获取有货的仓库ID
    print("\n----- 步骤3：查询SKU库存信息 -----")
    print(f"【process_single_order】查询SKU {local_sku} 库存...")
    inventory_details = review_order.get_inventory_details(local_sku)
    print(f"【process_single_order】库存查询结果：{inventory_details}")
    if not inventory_details:
        result["message"] = "未查询到库存信息"
        print(f"【process_single_order】{result['message']}")
        return result
    # 筛选有可用库存的仓库（product_valid_num > 0）
    valid_wids = [item.get("wid") for item in inventory_details if int(item.get("product_valid_num", 0)) > 0]
    print(f"【process_single_order】有货仓库ID列表：{valid_wids}")
    if not valid_wids:
        result["message"] = "无可用库存的仓库"
        print(f"【process_single_order】{result['message']}")
        return result

    # 步骤4：根据有货仓库筛选物流渠道
    print("\n----- 步骤4：筛选有货仓库对应的物流渠道 -----")
    filtered_logistics = filter_logistics_by_wid(matched_logistics, valid_wids)
    if not filtered_logistics:
        result["message"] = "无匹配有货仓库的物流渠道"
        print(f"【process_single_order】{result['message']}")
        return result

    # 步骤5：分别处理中邮/运德运费计算
    print("\n----- 步骤5：计算中邮仓库运费 -----")
    ems_ship_result = None
    # 筛选中邮物流渠道
    ems_logistics = filter_logistics_by_provider(filtered_logistics, "中邮")
    if ems_logistics:
        ems_channel_codes = [log.get("channel_code") for log in ems_logistics if log.get("channel_code")]
        print(f"【process_single_order】中邮可用渠道编码：{ems_channel_codes}")
        if ems_channel_codes:
            # 获取中邮商品规格
            ems_spec = review_order.get_ems_product_spec(local_sku)
            print(f"【process_single_order】中邮商品规格查询结果：{ems_spec}")
            if ems_spec and all([ems_spec.get("weight"), ems_spec.get("length"), ems_spec.get("width"), ems_spec.get("height")]):
                # 校验规格数值有效性
                try:
                    weight = float(ems_spec.get("weight"))
                    length = float(ems_spec.get("length"))
                    width = float(ems_spec.get("width"))
                    height = float(ems_spec.get("height"))
                except (ValueError, TypeError):
                    print("【process_single_order】中邮商品规格数值无效，跳过中邮运费计算")
                else:
                    print("【process_single_order】开始调用中邮运费试算接口（等待3-15秒）...")
                   
                    ems_fee_list = review_order.get_ems_ship_fee(
                        postcode=postal_code,
                        weight=weight,
                        warehouse="USEA,USWE",
                        channels=",".join(ems_channel_codes),
                        length=length,
                        width=width,
                        height=height
                    )
                    time.sleep(10)  # 模拟服务器响应等待
                    print(f"【process_single_order】中邮运费试算结果：{ems_fee_list}")
                    ems_ship_result = get_min_fee_shipment(ems_fee_list, ems_logistics)
                    if ems_ship_result:
                        print(f"【process_single_order】中邮最小运费：{ems_ship_result['totalFee']} {ems_ship_result['currency']}")
                    else:
                        print("【process_single_order】未获取到中邮有效运费数据")
            else:
                print("【process_single_order】中邮仓库商品规格不完整，跳过中邮运费计算")
        else:
            print("【process_single_order】无中邮可用渠道编码，跳过中邮运费计算")
    else:
        print("【process_single_order】无中邮物流渠道，跳过中邮运费计算")

    print("\n----- 步骤6：计算运德仓库运费 -----")
    wd_ship_result = None
    # 筛选运德物流渠道
    wd_logistics = filter_logistics_by_provider(filtered_logistics, "运德")
    if wd_logistics:
        wd_channel_codes = [log.get("channel_code") for log in wd_logistics if log.get("channel_code")]
        print(f"【process_single_order】运德可用渠道编码：{wd_channel_codes}")
        if wd_channel_codes:
            # 获取运德商品规格
            wd_spec = review_order.get_wd_product_spec(local_sku)
            print(f"【process_single_order】运德商品规格查询结果：{wd_spec}")
            if wd_spec and all([wd_spec.get("weight"), wd_spec.get("length"), wd_spec.get("width"), wd_spec.get("height")]):
                # 校验规格数值有效性
                try:
                    weight = float(wd_spec.get("weight"))
                    length = float(wd_spec.get("length"))
                    width = float(wd_spec.get("width"))
                    height = float(wd_spec.get("height"))
                except (ValueError, TypeError):
                    print("【process_single_order】运德商品规格数值无效，跳过运德运费计算")
                else:
                    print("【process_single_order】开始调用运德运费试算接口（等待3-15秒）...")
                    
                    wd_fee_list = review_order.get_wd_ship_fee(
                        channels=",".join(wd_channel_codes),
                        country=receiver_country_code,
                        city=city,
                        postcode=postal_code,
                        weight=weight,
                        length=length,
                        width=width,
                        height=height,
                        signatureService=0
                    )
                    time.sleep(15)  # 模拟服务器响应等待
                    print(f"【process_single_order】运德运费试算结果：{wd_fee_list}")
                    wd_ship_result = get_min_fee_shipment(wd_fee_list, wd_logistics)
                    if wd_ship_result:
                        # 美元转人民币
                        wd_fee_cny = wd_ship_result["totalFee"] * USD_TO_CNY_RATE
                        wd_ship_result["totalFee_cny"] = wd_fee_cny
                        print(f"【process_single_order】运德最小运费：{wd_ship_result['totalFee']} {wd_ship_result['currency']}（折合人民币{wd_fee_cny}元）")
                    else:
                        print("【process_single_order】未获取到运德有效运费数据")
            else:
                print("【process_single_order】运德仓库商品规格不完整，跳过运德运费计算")
        else:
            print("【process_single_order】无运德可用渠道编码，跳过运德运费计算")
    else:
        print("【process_single_order】无运德物流渠道，跳过运德运费计算")

    # 步骤7：比较中邮和运德运费，选择最优
    print("\n----- 步骤7：选择最优运费方案 -----")
    final_choice = None
    compare_list = []
    # 整理可比较的运费列表（统一转为人民币）
    if ems_ship_result and ems_ship_result["currency"] == "CNY":
        compare_list.append({
            "type": "ems",
            "fee_cny": ems_ship_result["totalFee"],
            "detail": ems_ship_result
        })
    if wd_ship_result and wd_ship_result["currency"] == "USD" and "totalFee_cny" in wd_ship_result:
        compare_list.append({
            "type": "wd",
            "fee_cny": wd_ship_result["totalFee_cny"],
            "detail": wd_ship_result
        })
    
    print(f"【process_single_order】可比较的运费方案：{compare_list}")
    if not compare_list:
        result["message"] = "无有效运费数据"
        print(f"【process_single_order】{result['message']}")
        return result
    
    # 选择最小人民币运费
    min_compare = min(compare_list, key=lambda x: x["fee_cny"])
    final_choice = min_compare["detail"]
    print(f"【process_single_order】最优选择：{min_compare['type']} 仓库 | 运费 {min_compare['fee_cny']} 元 | 渠道编码 {final_choice['channel_code']}")

    # 步骤8：修改订单
    print("\n----- 步骤8：执行订单修改 -----")
    print(f"【process_single_order】修改订单 {global_order_no} | type_id={final_choice['type_id']} | wid={final_choice['wid']}")
    edit_result = review_order.edit_order(
        type_id=final_choice.get("type_id"),
        wid=final_choice.get("wid"),
        global_order_no=global_order_no
    )
    print(f"【process_single_order】订单修改接口返回：{edit_result}")
    
    if edit_result.get("code") == 0:
        result["status"] = "success"
        result["message"] = "订单修改成功"
        result["final_fee"] = min_compare["fee_cny"]
        result["final_type_id"] = final_choice.get("type_id")
        result["final_wid"] = final_choice.get("wid")
        result["final_channel_code"] = final_choice.get("channel_code")
        print(f"【process_single_order】订单 {global_order_no} 处理成功")
    else:
        result["message"] = f"订单修改失败：{edit_result.get('message')}"
        print(f"【process_single_order】订单 {global_order_no} 处理失败：{result['message']}")
    
    print(f"\n===== 结束处理订单 {global_order_no} =====")
    return result

def main():
    """
    主函数：初始化数据→处理订单→输出结果
    """
    print("===== 开始执行批量订单处理程序 =====")
    
    # 1. 初始化基础数据
    print("\n----- 初始化基础数据 -----")
    print("【main】获取店铺列表...")
    store_list = review_order.get_store_list()
    print(f"【main】店铺列表获取结果：{store_list}")
    if not store_list:
        print("【main】未获取到店铺列表，程序退出")
        return
    
    print("【main】获取物流渠道列表...")
    logistics_list = review_order.get_logistics_list()
    print(f"【main】物流渠道列表获取结果：{logistics_list}")
    if not logistics_list:
        print("【main】未获取到物流渠道列表，程序退出")
        return
    
    print("【main】获取订单列表...")
    orders_list = review_order.get_orders_list()
    print(f"【main】订单列表获取结果：{orders_list}")
    if not orders_list:
        print("【main】未获取到订单列表，程序退出")
        return
    
    # 2. 筛选处理wid=0的订单，测试阶段取前5个
    target_orders = [order for order in orders_list if order.get("wid") == "0"][:10]  # 测试阶段前5个
    print(f"\n【main】筛选出wid=0的订单数：{len(target_orders)}")
    if not target_orders:
        print("【main】无wid=0的订单需要处理，程序退出")
        return
    print(f"【main】待处理订单（前5个）：{[o.get('global_order_no') for o in target_orders]}")

    # 3. 遍历处理每个订单
    print("\n----- 开始遍历处理订单 -----")
    process_results = []
    for idx, order in enumerate(target_orders, 1):
        print(f"\n=== 开始处理第 {idx} 个订单 ===")
        try:
            order_result = process_single_order(order, store_list, logistics_list)
            process_results.append(order_result)
        except Exception as e:
            error_msg = f"订单 {order.get('global_order_no')} 处理异常：{str(e)}"
            print(f"【main】{error_msg}")
            process_results.append({
                "global_order_no": order.get("global_order_no"),
                "status": "failed",
                "message": error_msg,
                "final_fee": 0,
                "final_type_id": "",
                "final_wid": "",
                "final_channel_code": ""
            })
        print(f"=== 结束处理第 {idx} 个订单 ===\n")

    # 4. 输出处理结果汇总
    print("\n===== 订单处理结果汇总 =====")
    success_count = 0
    failed_count = 0
    for res in process_results:
        print(f"\n订单 {res['global_order_no']}：")
        print(f"  状态：{res['status']}")
        print(f"  信息：{res['message']}")
        if res["status"] == "success":
            success_count += 1
            print(f"  最优运费：{res['final_fee']} 元")
            print(f"  物流类型ID：{res['final_type_id']}")
            print(f"  仓库ID：{res['final_wid']}")
            print(f"  渠道编码：{res['final_channel_code']}")
        else:
            failed_count += 1
    
    print(f"\n===== 汇总统计 =====")
    print(f"总处理订单数：{len(process_results)}")
    print(f"成功数：{success_count}")
    print(f"失败数：{failed_count}")
    
    # 5. 保存结果到JSON文件
    print(f"\n【main】保存处理结果到 order_process_result.json...")
    with open("order_process_result.json", "w", encoding="utf-8") as f:
        json.dump(process_results, f, ensure_ascii=False, indent=4)
    print("【main】处理结果已保存完成")
    
    print("\n===== 批量订单处理程序执行结束 =====")

if __name__ == "__main__":
    main()