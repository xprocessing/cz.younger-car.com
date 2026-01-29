import review_order_func as review_order
import time
import json
from datetime import datetime

def main():
    print(f"开始执行订单审核 - {datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # 1. 获取店铺列表
    print("\n=== 步骤1: 获取店铺列表 ===")
    store_list = review_order.get_store_list()
    print(f"获取到 {len(store_list)} 个店铺:")
    for store in store_list:
        print(f"  Store ID: {store['store_id']}, 平台名称: {store['platform_name']}")
    
    # 2. 获取物流渠道列表
    print("\n=== 步骤2: 获取物流渠道列表 ===")
    logistics_list = review_order.get_logistics_list()
    print(f"获取到 {len(logistics_list)} 个物流渠道:")
    for logistics in logistics_list:
        print(f"  Type ID: {logistics['type_id']}, 渠道代码: {logistics['channel_code']}, 物流提供商: {logistics['logistics_provider_name']}, 仓库ID: {logistics['wid']}")
    
    # 3. 获取订单列表
    print("\n=== 步骤3: 获取订单列表 ===")
    orders_list = review_order.get_orders_list()
    # 只处理wid为空的订单
    order_no=input("请输入要处理的系统订单订单号: ")
    orders_to_process = [order for order in orders_list if order["wid"] == "0" and order["global_order_no"] == order_no]
    print(f"获取到 {len(orders_list)} 个订单，其中 {len(orders_to_process)} 个需要处理 (wid为空)")
    
    # 仅处理前5个订单（测试）
    orders_to_process = orders_to_process[:10]
    print(f"本次处理前 {len(orders_to_process)} 个订单:")
    for i, order in enumerate(orders_to_process, 1):
        print(f"  订单 {i}: 全局订单号: {order['global_order_no']}, Store ID: {order['store_id']}, SKU: {order['local_sku']}, 国家: {order['receiver_country_code']}, 城市: {order['city']}, 邮编: {order['postal_code']}, 金额: {order['order_total_amount']}")
    
    # 4. 处理每个订单
    print("\n=== 步骤4: 开始处理订单 ===")
    processed_orders = []
    
    for order in orders_to_process:
        print(f"\n--- 处理订单: {order['global_order_no']} ---")
        
        # 4.1 获取订单对应的平台名称
        platform_name = ""
        for store in store_list:
            if store['store_id'] == order['store_id']:
                platform_name = store['platform_name']
                break
        
        if not platform_name:
            print(f"  错误: 未找到订单 {order['global_order_no']} 对应的平台名称")
            continue
        
        print(f"  订单平台: {platform_name}")
        
        # 4.2 确定订单可用的物流渠道
        available_logistics = []
        platform_keywords = {
            "Amazon": "亚马逊",
            "eBay": "eBay",
            "Shopify": "独立站"
        }
        
        # 确定平台关键词
        platform_keyword = ""
        for key, value in platform_keywords.items():
            if key in platform_name:
                platform_keyword = value
                break
        
        if not platform_keyword:
            print(f"  警告: 未识别的平台名称 '{platform_name}'，将尝试匹配所有物流渠道")
        
        # 筛选可用的物流渠道
        for logistics in logistics_list:
            if platform_keyword and platform_keyword in logistics['logistics_provider_name']:
                available_logistics.append(logistics)
            elif not platform_keyword:  # 如果没有识别出平台关键词，添加所有物流
                available_logistics.append(logistics)
        
        print(f"  找到 {len(available_logistics)} 个可用物流渠道:")
        for logistics in available_logistics:
            print(f"    - {logistics['logistics_provider_name']} ({logistics['channel_code']})")
        
        if not available_logistics:
            print(f"  警告: 未找到 {order['global_order_no']} 订单可用的物流渠道")
            continue
        
        # 4.3 获取商品库存
        print(f"  获取 SKU: {order['local_sku']} 的库存信息...")
        inventory_details = review_order.get_inventory_details(order['local_sku'])
        print(f"  获取到 {len(inventory_details)} 个仓库的库存信息:")
        for detail in inventory_details:
            print(f"    仓库ID: {detail['wid']}, SKU: {detail['sku']}, 可用库存: {detail['product_valid_num']}, 库龄: {detail['average_age']}")
        
        # 4.4 筛选有货的仓库和对应的物流渠道
        available_warehouses = [str(detail['wid']) for detail in inventory_details if detail['product_valid_num'] > 0]
        print(f"  有货的仓库ID: {', '.join(available_warehouses) if available_warehouses else '无'}")
        
        # 根据有货仓库筛选物流渠道
        filtered_logistics = []
        for logistics in available_logistics:
            if logistics['wid'] in available_warehouses:
                filtered_logistics.append(logistics)
        
        print(f"  有货仓库中可用的物流渠道数量: {len(filtered_logistics)}")
        for logistics in filtered_logistics:
            print(f"    - 仓库ID: {logistics['wid']}, 渠道: {logistics['channel_code']}, 物流商: {logistics['logistics_provider_name']}")
        
        if not filtered_logistics:
            print(f"  警告: 未找到有货仓库中可用的物流渠道")
            continue
        
        # 4.5 分离中邮和运德的物流渠道
        ems_logistics = [log for log in filtered_logistics if "中邮" in log['logistics_provider_name']]
        wd_logistics = [log for log in filtered_logistics if "运德" in log['logistics_provider_name']]
        
        print(f"  中邮物流渠道数量: {len(ems_logistics)}")
        print(f"  运德物流渠道数量: {len(wd_logistics)}")
        
        # 4.6 获取商品规格 - 中邮
        ems_best_option = None
        if ems_logistics:
            print(f"  获取中邮仓库商品规格 (SKU: {order['local_sku']}, 平台: {platform_name})...")
            ems_product_spec = review_order.get_ems_product_spec(order['local_sku'], platform_name)
            print(f"  中邮商品规格: {json.dumps(ems_product_spec, ensure_ascii=False)}")
            
            # 4.7 获取中邮运费试算
            if ems_product_spec and ems_product_spec.get('weight'):
                # 准备中邮运费试算参数
                ems_channels = ",".join([log['channel_code'] for log in ems_logistics])
                warehouse = "USEA,USWE"  # 固定值
                weight = ems_product_spec.get('weight', '0')
                length = ems_product_spec.get('length', '0')
                width = ems_product_spec.get('width', '0')
                height = ems_product_spec.get('height', '0')
                postcode = order['postal_code']
                
                print(f"  计算中邮运费 (渠道: {ems_channels}, 仓库: {warehouse}, 邮编: {postcode})...")
                print(f"    商品规格 - 重量: {weight}, 长: {length}, 宽: {width}, 高: {height}")
                
                ems_ship_fee = review_order.get_ems_ship_fee(
                    postcode, weight, warehouse, ems_channels, length, width, height
                )
                
                print(f"  中邮运费试算结果 ({len(ems_ship_fee)}个选项):")
                for fee in ems_ship_fee:
                    print(f"    - 仓库: {fee['warehouse']}, 渠道: {fee['channel_code']}, 运费: {fee['totalFee']} {fee['currency']}")
                
                # 找到最小运费选项
                if ems_ship_fee:
                    min_ems_fee = min(ems_ship_fee, key=lambda x: float(x['totalFee'] or 0))
                    print(f"  中邮最小运费选项: 仓库: {min_ems_fee['warehouse']}, 渠道: {min_ems_fee['channel_code']}, 运费: {min_ems_fee['totalFee']} {min_ems_fee['currency']}")
                    
                    # 找到对应的物流信息
                    for log in ems_logistics:
                        if log['channel_code'] == min_ems_fee['channel_code']:
                            ems_best_option = {
                                'type_id': log['type_id'],
                                'wid': log['wid'],
                                'channel_code': min_ems_fee['channel_code'],
                                'totalFee': float(min_ems_fee['totalFee'] or 0),
                                'currency': min_ems_fee['currency'],
                                'product_spec': ems_product_spec,
                                'ship_fee_details': ems_ship_fee,
                                'provider': '中邮'
                            }
                            break
        
        # 4.8 获取商品规格 - 运德
        wd_best_option = None
        if wd_logistics:
            print(f"  获取运德仓库商品规格 (SKU: {order['local_sku']}, 平台: {platform_name})...")
            wd_product_spec = review_order.get_wd_product_spec(order['local_sku'], platform_name)
            print(f"  运德商品规格: {json.dumps(wd_product_spec, ensure_ascii=False)}")
            
            # 4.9 获取运德运费试算
            if wd_product_spec and wd_product_spec.get('weight'):
                # 准备运德运费试算参数
                wd_channels = ",".join([log['channel_code'] for log in wd_logistics])
                country = order['receiver_country_code']
                city = order['city']
                postcode = order['postal_code']
                weight = wd_product_spec.get('weight', '0')
                length = wd_product_spec.get('length', '0')
                width = wd_product_spec.get('width', '0')
                height = wd_product_spec.get('height', '0')
                signatureService = "0"  # 固定值
                
                print(f"  计算运德运费 (渠道: {wd_channels}, 国家: {country}, 城市: {city}, 邮编: {postcode})...")
                print(f"    商品规格 - 重量: {weight}, 长: {length}, 宽: {width}, 高: {height}")
                
                wd_ship_fee = review_order.get_wd_ship_fee(
                    wd_channels, country, city, postcode, weight, length, width, height, signatureService
                )
                
                print(f"  运德运费试算结果 ({len(wd_ship_fee)}个选项):")
                for fee in wd_ship_fee:
                    print(f"    - 渠道: {fee['channel_code']}, 运费: {fee['totalFee']} {fee['currency']}")
                
                # 找到最小运费选项
                if wd_ship_fee:
                    min_wd_fee = min(wd_ship_fee, key=lambda x: float(x['totalFee'] or 0))
                    print(f"  运德最小运费选项: 渠道: {min_wd_fee['channel_code']}, 运费: {min_wd_fee['totalFee']} {min_wd_fee['currency']}")
                    
                    # 找到对应的物流信息
                    for log in wd_logistics:
                        if log['channel_code'] == min_wd_fee['channel_code']:
                            wd_best_option = {
                                'type_id': log['type_id'],
                                'wid': log['wid'],
                                'channel_code': min_wd_fee['channel_code'],
                                'totalFee': float(min_wd_fee['totalFee'] or 0),
                                'currency': min_wd_fee['currency'],
                                'product_spec': wd_product_spec,
                                'ship_fee_details': wd_ship_fee,
                                'provider': '运德'
                            }
                            break
        
        # 4.10 比较中邮和运德运费，选择较小的一个
        best_option = None
        print("\n  === 运费比较 ===")
        
        if ems_best_option:
            print(f"  中邮最佳选项: 运费 {ems_best_option['totalFee']} {ems_best_option['currency']}")
        
        if wd_best_option:
            # 转换为人民币 (汇率7)
            wd_fee_in_cny = wd_best_option['totalFee'] * 7
            print(f"  运德最佳选项: 运费 {wd_best_option['totalFee']} {wd_best_option['currency']} (约合 {wd_fee_in_cny:.2f} 人民币，汇率7)")
        
        if ems_best_option and wd_best_option:
            wd_fee_in_cny = wd_best_option['totalFee'] * 7
            if ems_best_option['totalFee'] <= wd_fee_in_cny:
                best_option = ems_best_option
                print(f"  选择中邮: {ems_best_option['totalFee']} 人民币 <= {wd_fee_in_cny:.2f} 人民币")
            else:
                best_option = wd_best_option
                print(f"  选择运德: {wd_fee_in_cny:.2f} 人民币 < {ems_best_option['totalFee']} 人民币")
        elif ems_best_option:
            best_option = ems_best_option
            print("  仅中邮有可用选项，选择中邮")
        elif wd_best_option:
            best_option = wd_best_option
            print("  仅运德有可用选项，选择运德")
        
        if not best_option:
            print(f"  警告: 未找到可用的最佳物流选项")
            continue
        
        # 4.11 准备订单修改信息
        order_info = {
            'global_order_no': order['global_order_no'],
            'store_id': order['store_id'],
            'platform_name': platform_name,
            'local_sku': order['local_sku'],
            'country': order['receiver_country_code'],
            'city': order['city'],
            'postal_code': order['postal_code'],
            'order_total_amount': order['order_total_amount'],
            'inventory_details': inventory_details,
            'best_option': best_option,
            'ems_best_option': ems_best_option,
            'wd_best_option': wd_best_option
        }
        
        processed_orders.append(order_info)
        
        # 4.12 暂停一下，避免请求过于频繁
        time.sleep(2)
    
    # 5. 显示处理结果并请求确认
    print("\n=== 步骤5: 处理结果汇总 ===")
    print(f"成功处理 {len(processed_orders)} 个订单")
    
    for i, order_info in enumerate(processed_orders, 1):
        print(f"\n--- 订单 {i}: {order_info['global_order_no']} ---")
        print(f"  平台: {order_info['platform_name']}, SKU: {order_info['local_sku']}")
        print(f"  收货地址: {order_info['country']}, {order_info['city']}, {order_info['postal_code']}")
        print(f"  订单金额: {order_info['order_total_amount']}")
        
        print("\n  库存信息:")
        for detail in order_info['inventory_details']:
            print(f"    仓库ID: {detail['wid']}, 可用库存: {detail['product_valid_num']}, 库龄: {detail['average_age']}")
        
        if order_info['ems_best_option']:
            ems = order_info['ems_best_option']
            print("\n  中邮方案详情:")
            print(f"    渠道: {ems['channel_code']}")
            print(f"    仓库ID: {ems['wid']}")
            print(f"    运费: {ems['totalFee']} {ems['currency']}")
            print(f"    商品规格: 长{ems['product_spec'].get('length', '')}, 宽{ems['product_spec'].get('width', '')}, 高{ems['product_spec'].get('height', '')}, 重量{ems['product_spec'].get('weight', '')}")
        
        if order_info['wd_best_option']:
            wd = order_info['wd_best_option']
            wd_fee_in_cny = wd['totalFee'] * 7
            print("\n  运德方案详情:")
            print(f"    渠道: {wd['channel_code']}")
            print(f"    仓库ID: {wd['wid']}")
            print(f"    运费: {wd['totalFee']} {wd['currency']} (约合 {wd_fee_in_cny:.2f} 人民币，汇率7)")
            print(f"    商品规格: 长{wd['product_spec'].get('length', '')}, 宽{wd['product_spec'].get('width', '')}, 高{wd['product_spec'].get('height', '')}, 重量{wd['product_spec'].get('weight', '')}")
        
        best = order_info['best_option']
        print(f"\n  最终选择: {best['provider']}")
        print(f"    物流渠道: {best['channel_code']}")
        print(f"    仓库ID: {best['wid']}")
        print(f"    运费: {best['totalFee']} {best['currency']}" + (" (已转换为人民币)" if best['provider'] == "运德" else ""))
        
        # 询问是否确认修改
        confirm = input(f"\n是否确认修改订单 {order_info['global_order_no']} ? (y/n): ")
        if confirm.lower() == 'y':
            print(f"  正在修改订单 {order_info['global_order_no']} ...")
            result = review_order.edit_order(best['type_id'], best['wid'], order_info['global_order_no'])
            print(f"  修改结果: {json.dumps(result, ensure_ascii=False)}")
        else:
            print(f"  已跳过修改订单 {order_info['global_order_no']}")
    
    print(f"\n=== 批量审核完成 - {datetime.now().strftime('%Y-%m-%d %H:%M:%S')} ===")

if __name__ == "__main__":
    main()