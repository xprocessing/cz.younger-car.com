import review_order_func as review_order

# review_order.get_store_list() 获取店铺列表，store_id 对应platform_name
# review_order.get_logistics_list() 获取物流渠道列表，type_id，code，logistics_provider_name，wid 仓库id
# review_order.get_orders_list() 获取订单列表，global_order_no，store_id，local_sku，receiver_country_code，city，postal_code
# review_order.get_inventory_details(sku) 获取商品库存详情，参数sku=local_sku 结果：wid 是 仓库id,product_valid_num 是可用库存，average_age是库龄

# review_order.get_ems_product_spec(sku) 通过参数sku=local_sku 获取中邮仓库商品规格 weight  length width height
# review_order.get_wd_product_spec(sku) 通过参数sku=local_sku 获取运德仓库 商品规格 weight  length width height
# review_order.get_ems_ship_fee(postcode, weight, warehouse, channel, length, width, height) 获取中邮运费试算数据列表。 
# 其中参数 postcode对应的是postal_code，weight length width height对应review_order.get_ems_product_spec(sku)的weight length width height
# 其中参数 warehouse是固定值"USEA,USWE", channel是code的,拼接字符串

# review_order.get_wd_ship_fee(channelCode, country, city, postcode, weight, length, width, height, signatureService) 获取运德运费试算数据列表。
#其中参数channelCode 是code的,拼接字符负串。 country是receiver_country_code，city是city，postcode是postal_code，weight length width height对应review_order.get_wd_product_spec(sku)的weight length width height

# review_order.edit_order(type_id, wid, global_order_no) 修改领星订单数据。


#具体业务逻辑：获取店铺列表，物流渠道列表备用。 获取所有订单，遍历订单，查询订单可用的物流渠道和仓库，根据库存再次筛选有货的仓库，然后获取商品在中邮和运德仓库的规格，根据规格，订单信息，可用的code，获取两个仓库对应的运费试算数据，并找到最小运费，及对应的type_id和wid
#1.获取订单列表（测试先遍历前5个订单，后续再遍历所有订单）
#2.根据订单的store_id查找对应的platform_name，根据platform_name匹配对应的logistics_provider_name，比如：Amazon 匹配logistics_provider_name包含 亚马逊 的， eBay匹配eBay， Shopify匹配独立站，通过匹配，可以确定这个订单可用的code，type_id，wid
#3.根据订单的local_sku ，通过review_order.get_inventory_details(sku) 获取有货的仓库id：wid， 通过wid再次筛选可用的code，type_id
#4.通过订单的sku，查找在中邮仓库的规格，然后根据规格，订单信息，可用的code，获取运费试算数据，并找到最小运费，及对应的type_id和wid
#5.通过订单的sku，查找在运德仓库的规格，然后根据规格，订单信息，可用的code，获取运费试算数据，并找到最小运费，及对应的type_id和wid
#6.比较中邮和运德的运费，选择较小的一个，修改订单的type_id，wid，global_order_no（中邮的运费是人民币，运德的运费是美元，需要按照汇率7转换为人民币，再比较大小）

#7.输出订单的信息，可发仓库的信息，对应的渠道的信息，中邮和运德仓库规格的信息，中邮和运德运费试算结果，最小运费及对应的type_id和wid







def match_logistics_channels(platform_name, logistics_list):
    """
    根据平台名称匹配对应的物流渠道
    """
    matched_channels = []
    for channel in logistics_list:
        provider_name = channel.get('logistics_provider_name', '')
        if platform_name == 'Amazon' and '亚马逊' in provider_name:
            matched_channels.append(channel)
        elif platform_name == 'eBay' and 'eBay' in provider_name:
            matched_channels.append(channel)
        elif platform_name == 'Shopify' and '独立站' in provider_name:
            matched_channels.append(channel)
    return matched_channels

def filter_available_channels(channels, available_wids):
    """
    根据可用仓库ID筛选物流渠道
    """
    return [channel for channel in channels if str(channel.get('wid', '')) in available_wids]

def get_min_ems_shipping_fee(order, sku, available_channels):
    """
    获取中邮最小运费及对应渠道
    """
    try:
        # 获取中邮产品规格
        ems_spec = review_order.get_ems_product_spec(sku)
        if not isinstance(ems_spec, dict) or not ems_spec.get('weight'):
            return None, None
        
        # 构建渠道code字符串
        codes = [channel.get('code') for channel in available_channels if channel.get('code')]
        if not codes:
            return None, None
        
        channel_str = ','.join(codes)
        
        # 获取运费试算
        postcode = order.get('postal_code', '')
        weight = ems_spec.get('weight')
        length = ems_spec.get('length', 0)
        width = ems_spec.get('width', 0)
        height = ems_spec.get('height', 0)
        warehouse = 'USEA,USWE'
        
        fees = review_order.get_ems_ship_fee(postcode, weight, warehouse, channel_str, length, width, height)
        if not isinstance(fees, list) or not fees:
            return None, None
        
        # 找到最小运费
        min_fee = float('inf')
        min_channel = None
        for fee_item in fees:
            if not isinstance(fee_item, dict):
                continue
            total_fee = float(fee_item.get('totalFee', 'inf'))
            if total_fee < min_fee:
                min_fee = total_fee
                min_channel = fee_item.get('channel')
        
        # 找到对应channel的type_id和wid
        if min_channel:
            for channel in available_channels:
                if channel.get('code') in min_channel:
                    return min_fee, channel
    except Exception as e:
        print(f"中邮运费计算错误: {str(e)}")
    
    return None, None

def get_min_wd_shipping_fee(order, sku, available_channels):
    """
    获取运德最小运费及对应渠道
    """
    try:
        # 获取运德产品规格
        wd_spec = review_order.get_wd_product_spec(sku)
        if not isinstance(wd_spec, dict) or not wd_spec.get('weight'):
            return None, None
        
        # 构建渠道code字符串
        codes = [channel.get('code') for channel in available_channels if channel.get('code')]
        if not codes:
            return None, None
        
        channelCode = ','.join(codes)
        
        # 获取运费试算
        postcode = order.get('postal_code', '')
        country = order.get('receiver_country_code', '')
        city = order.get('city', '')
        weight = wd_spec.get('weight')
        length = wd_spec.get('length', 0)
        width = wd_spec.get('width', 0)
        height = wd_spec.get('height', 0)
        signatureService = 0
        
        fees = review_order.get_wd_ship_fee(channelCode, country, city, postcode, weight, length, width, height, signatureService)
        if not isinstance(fees, list) or not fees:
            return None, None
        
        # 找到最小运费
        min_fee = float('inf')
        min_channel = None
        for fee_item in fees:
            if not isinstance(fee_item, dict):
                continue
            try:
                total_fee = float(fee_item.get('totalFee', 'inf'))
                # 运德运费是美元，转换为人民币（汇率7）
                if fee_item.get('currency') == 'USD':
                    total_fee *= 7
                if total_fee < min_fee:
                    min_fee = total_fee
                    min_channel = fee_item.get('channel')
            except (ValueError, TypeError):
                continue
        
        # 找到对应channel的type_id和wid
        if min_channel:
            for channel in available_channels:
                if channel.get('code') in min_channel:
                    return min_fee, channel
    except Exception as e:
        print(f"运德运费计算错误: {str(e)}")
    
    return None, None

def process_order(order, store_list, logistics_list):
    """
    处理单个订单的审核逻辑
    """
    global_order_no = order.get('global_order_no')
    store_id = order.get('store_id')
    local_sku = order.get('local_sku')
    
    if not all([global_order_no, store_id, local_sku]):
        print(f"订单数据不完整: {global_order_no}")
        return None
    
    # 1. 查找店铺信息
    platform_name = None
    for store in store_list:
        if store.get('store_id') == store_id:
            platform_name = store.get('platform_name')
            break
    
    if not platform_name:
        print(f"未找到店铺信息: {store_id}")
        return None
    
    # 2. 匹配物流渠道
    matched_channels = match_logistics_channels(platform_name, logistics_list)
    if not matched_channels:
        print(f"未匹配到物流渠道: {platform_name}")
        return None
    
    # 3. 获取库存详情
    inventory_details = review_order.get_inventory_details(local_sku)
    if not inventory_details:
        print(f"未找到库存信息: {local_sku}")
        return None
    
    # 筛选有货的仓库
    available_wids = [str(item.get('wid')) for item in inventory_details if item.get('product_valid_num', 0) > 0]
    if not available_wids:
        print(f"商品无库存: {local_sku}")
        return None
    
    # 4. 根据库存筛选物流渠道
    available_channels = filter_available_channels(matched_channels, available_wids)
    if not available_channels:
        print(f"无可用物流渠道: {local_sku}")
        return None
    
    # 5. 获取中邮最小运费
    ems_fee, ems_channel = get_min_ems_shipping_fee(order, local_sku, available_channels)
    
    # 6. 获取运德最小运费
    wd_fee, wd_channel = get_min_wd_shipping_fee(order, local_sku, available_channels)
    
    # 7. 比较选择最优渠道
    best_fee = float('inf')
    best_channel = None
    
    if ems_fee and ems_fee < best_fee:
        best_fee = ems_fee
        best_channel = ems_channel
    
    if wd_fee and wd_fee < best_fee:
        best_fee = wd_fee
        best_channel = wd_channel
    
    if not best_channel:
        print(f"无法计算运费: {local_sku}")
        return None
    
    # 8. 修改订单
    type_id = best_channel.get('type_id')
    wid = best_channel.get('wid')
    
    if not all([type_id, wid]):
        print(f"渠道信息不完整: {best_channel.get('code')}")
        return None
    
    result = review_order.edit_order(type_id, wid, global_order_no)
    return {
        'global_order_no': global_order_no,
        'platform_name': platform_name,
        'local_sku': local_sku,
        'best_fee': best_fee,
        'best_channel': best_channel.get('code'),
        'type_id': type_id,
        'wid': wid,
        'edit_result': result
    }

if __name__ == "__main__":
    print("开始批量订单审核...")
    
    # 1. 获取基础数据
    store_list = review_order.get_store_list()
    logistics_list = review_order.get_logistics_list()
    orders_list = review_order.get_orders_list()
    
    print(f"获取到店铺: {len(store_list)} 个")
    print(f"获取到物流渠道: {len(logistics_list)} 个")
    print(f"获取到订单: {len(orders_list)} 个")
    
    # 2. 处理订单（测试先处理前5个）
    processed_count = 0
    success_count = 0
    error_count = 0
    
    # 创建开发日志目录
    import os
    log_dir = "../开发日志"
    if not os.path.exists(log_dir):
        os.makedirs(log_dir)
    
    # 生成日志文件
    import datetime
    today = datetime.datetime.now().strftime("%Y%m%d")
    log_file = os.path.join(log_dir, f"{today}_批量订单审核.log")
    
    with open(log_file, 'w', encoding='utf-8') as f:
        f.write(f"批量订单审核日志 - {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        f.write(f"店铺数量: {len(store_list)}\n")
        f.write(f"物流渠道数量: {len(logistics_list)}\n")
        f.write(f"订单数量: {len(orders_list)}\n")
        f.write("=" * 80 + "\n")
        
        # 处理前5个订单
        for i, order in enumerate(orders_list[:5]):
            print(f"处理订单 {i+1}/{len(orders_list[:5])}: {order.get('global_order_no')}")
            
            try:
                result = process_order(order, store_list, logistics_list)
                processed_count += 1
                
                if result:
                    success_count += 1
                    status = "成功" if result['edit_result'].get('code') == 0 else "失败"
                    log_msg = f"订单: {result['global_order_no']} | 平台: {result['platform_name']} | SKU: {result['local_sku']} | 最优渠道: {result['best_channel']} | 运费: {result['best_fee']:.2f} | 状态: {status} | 消息: {result['edit_result'].get('message')}\n"
                    print(log_msg.strip())
                    f.write(log_msg)
                else:
                    error_count += 1
                    log_msg = f"订单: {order.get('global_order_no')} | 处理失败: 无法完成审核\n"
                    print(log_msg.strip())
                    f.write(log_msg)
                    
            except Exception as e:
                error_count += 1
                log_msg = f"订单: {order.get('global_order_no')} | 处理异常: {str(e)}\n"
                print(log_msg.strip())
                f.write(log_msg)
            
            print()
        
        # 写入统计信息
        f.write("=" * 80 + "\n")
        f.write(f"处理完成 - {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        f.write(f"总处理订单: {processed_count}\n")
        f.write(f"成功: {success_count}\n")
        f.write(f"失败: {error_count}\n")
    
    print("\n批量订单审核完成！")
    print(f"处理订单: {processed_count} 个")
    print(f"成功: {success_count} 个")
    print(f"失败: {error_count} 个")
    print(f"详细日志已保存到: {log_file}")
  
