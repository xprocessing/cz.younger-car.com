import review_order_func as review_order
import os
import datetime

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

def get_ems_shipping_info(order, sku, available_channels):
    """
    获取中邮运费信息
    """
    try:
        # 获取中邮产品规格
        ems_spec = review_order.get_ems_product_spec(sku)
        if not isinstance(ems_spec, dict) or not ems_spec.get('weight'):
            return None, None, None
        
        # 构建渠道code字符串
        codes = [channel.get('code') for channel in available_channels if channel.get('code')]
        if not codes:
            return None, None, None
        
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
            return None, None, None
        
        # 找到最小运费
        min_fee = float('inf')
        min_channel = None
        for fee_item in fees:
            if isinstance(fee_item, dict):
                try:
                    total_fee = float(fee_item.get('totalFee', 'inf'))
                    if total_fee < min_fee:
                        min_fee = total_fee
                        min_channel = fee_item.get('channel')
                except (ValueError, TypeError):
                    pass
        
        # 找到对应channel的完整信息
        selected_channel = None
        if min_channel:
            for channel in available_channels:
                if channel.get('code') in min_channel:
                    selected_channel = channel
                    break
        
        return min_fee, selected_channel, ems_spec
    except Exception as e:
        print(f"中邮运费计算错误: {str(e)}")
    return None, None, None

def get_wd_shipping_info(order, sku, available_channels):
    """
    获取运德运费信息
    """
    try:
        # 获取运德产品规格
        wd_spec = review_order.get_wd_product_spec(sku)
        if not isinstance(wd_spec, dict) or not wd_spec.get('weight'):
            return None, None, None
        
        # 构建渠道code字符串
        codes = [channel.get('code') for channel in available_channels if channel.get('code')]
        if not codes:
            return None, None, None
        
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
            return None, None, None
        
        # 找到最小运费
        min_fee = float('inf')
        min_channel = None
        for fee_item in fees:
            if isinstance(fee_item, dict):
                try:
                    total_fee = float(fee_item.get('totalFee', 'inf'))
                    # 运德运费是美元，转换为人民币（汇率7）
                    if fee_item.get('currency') == 'USD':
                        total_fee *= 7
                    if total_fee < min_fee:
                        min_fee = total_fee
                        min_channel = fee_item.get('channel')
                except (ValueError, TypeError):
                    pass
        
        # 找到对应channel的完整信息
        selected_channel = None
        if min_channel:
            for channel in available_channels:
                if channel.get('code') in min_channel:
                    selected_channel = channel
                    break
        
        return min_fee, selected_channel, wd_spec
    except Exception as e:
        print(f"运德运费计算错误: {str(e)}")
    return None, None, None

def process_single_order(order, store_list, logistics_list):
    """
    处理单个订单
    """
    # 提取订单信息
    global_order_no = order.get('global_order_no')
    store_id = order.get('store_id')
    local_sku = order.get('local_sku')
    receiver_country_code = order.get('receiver_country_code')
    city = order.get('city')
    postal_code = order.get('postal_code')
    
    # 初始化结果字典
    result = {
        'order_no': global_order_no,
        'store_id': store_id,
        'sku': local_sku,
        'country': receiver_country_code,
        'city': city,
        'postal_code': postal_code,
        'platform': None,
        'available_warehouses': [],
        'matched_channels': [],
        'available_channels': [],
        'ems_spec': None,
        'wd_spec': None,
        'ems_fee': None,
        'ems_channel': None,
        'wd_fee': None,
        'wd_channel': None,
        'best_fee': None,
        'best_channel': None,
        'best_type_id': None,
        'best_wid': None,
        'edit_status': False,
        'edit_message': '',
        'status': 'processing',
        'message': ''
    }
    
    # 1. 验证订单数据
    if not all([global_order_no, store_id, local_sku]):
        result['status'] = 'failed'
        result['message'] = '订单数据不完整'
        return result
    
    # 2. 查找店铺信息
    platform_name = None
    for store in store_list:
        if store.get('store_id') == store_id:
            platform_name = store.get('platform_name')
            break
    
    if not platform_name:
        result['status'] = 'failed'
        result['message'] = f'未找到店铺信息: {store_id}'
        return result
    
    result['platform'] = platform_name
    
    # 3. 匹配物流渠道
    matched_channels = match_logistics_channels(platform_name, logistics_list)
    if not matched_channels:
        result['status'] = 'failed'
        result['message'] = f'未匹配到物流渠道: {platform_name}'
        return result
    
    result['matched_channels'] = [channel.get('code') for channel in matched_channels]
    
    # 4. 获取库存详情
    inventory_details = review_order.get_inventory_details(local_sku)
    if not inventory_details:
        result['status'] = 'failed'
        result['message'] = f'未找到库存信息: {local_sku}'
        return result
    
    # 筛选有货的仓库
    available_wids = [str(item.get('wid')) for item in inventory_details if item.get('product_valid_num', 0) > 0]
    if not available_wids:
        result['status'] = 'failed'
        result['message'] = f'商品无库存: {local_sku}'
        return result
    
    result['available_warehouses'] = available_wids
    
    # 5. 根据库存筛选物流渠道
    available_channels = filter_available_channels(matched_channels, available_wids)
    if not available_channels:
        result['status'] = 'failed'
        result['message'] = f'无可用物流渠道: {local_sku}'
        return result
    
    result['available_channels'] = [channel.get('code') for channel in available_channels]
    
    # 6. 获取中邮运费信息
    ems_fee, ems_channel, ems_spec = get_ems_shipping_info(order, local_sku, available_channels)
    result['ems_fee'] = ems_fee
    result['ems_channel'] = ems_channel.get('code') if ems_channel else None
    result['ems_spec'] = ems_spec
    
    # 7. 获取运德运费信息
    wd_fee, wd_channel, wd_spec = get_wd_shipping_info(order, local_sku, available_channels)
    result['wd_fee'] = wd_fee
    result['wd_channel'] = wd_channel.get('code') if wd_channel else None
    result['wd_spec'] = wd_spec
    
    # 8. 选择最优运费
    best_fee = float('inf')
    best_channel = None
    
    if ems_fee and ems_fee < best_fee:
        best_fee = ems_fee
        best_channel = ems_channel
    
    if wd_fee and wd_fee < best_fee:
        best_fee = wd_fee
        best_channel = wd_channel
    
    if not best_channel:
        result['status'] = 'failed'
        result['message'] = f'无法计算运费: {local_sku}'
        return result
    
    result['best_fee'] = best_fee
    result['best_channel'] = best_channel.get('code')
    result['best_type_id'] = best_channel.get('type_id')
    result['best_wid'] = best_channel.get('wid')
    
    # 9. 修改订单
    if result['best_type_id'] and result['best_wid']:
        edit_result = review_order.edit_order(result['best_type_id'], result['best_wid'], global_order_no)
        if edit_result.get('code') == 0:
            result['edit_status'] = True
            result['edit_message'] = edit_result.get('message', '修改成功')
            result['status'] = 'success'
            result['message'] = '订单处理成功'
        else:
            result['edit_status'] = False
            result['edit_message'] = edit_result.get('message', '修改失败')
            result['status'] = 'failed'
            result['message'] = '订单修改失败'
    else:
        result['status'] = 'failed'
        result['message'] = '渠道信息不完整'
    
    return result

def format_spec_info(spec):
    """
    格式化规格信息
    """
    if not spec:
        return '无规格信息'
    weight = spec.get('weight', '未知')
    length = spec.get('length', '未知')
    width = spec.get('width', '未知')
    height = spec.get('height', '未知')
    return f"重量: {weight}, 尺寸: {length}x{width}x{height}"

def print_order_result(result):
    """
    打印订单处理结果
    """
    print("=" * 100)
    print(f"订单号: {result['order_no']}")
    print(f"平台: {result['platform']}")
    print(f"SKU: {result['sku']}")
    print(f"收货地址: {result['country']}, {result['city']}, {result['postal_code']}")
    print("-" * 100)
    print(f"可用仓库: {', '.join(result['available_warehouses'])}")
    print(f"匹配渠道: {', '.join(result['matched_channels'])}")
    print(f"可用渠道: {', '.join(result['available_channels'])}")
    print("-" * 100)
    print(f"中邮规格: {format_spec_info(result['ems_spec'])}")
    print(f"中邮运费: {result['ems_fee'] if result['ems_fee'] else '无'} (渠道: {result['ems_channel'] if result['ems_channel'] else '无'})")
    print(f"运德规格: {format_spec_info(result['wd_spec'])}")
    print(f"运德运费: {result['wd_fee'] if result['wd_fee'] else '无'} (渠道: {result['wd_channel'] if result['wd_channel'] else '无'})")
    print("-" * 100)
    print(f"最优选择: {result['best_channel']} (运费: {result['best_fee'] if result['best_fee'] else '无'})")
    print(f"选择的仓库: {result['best_wid']}")
    print(f"选择的物流类型: {result['best_type_id']}")
    print(f"修改状态: {'成功' if result['edit_status'] else '失败'}")
    print(f"修改消息: {result['edit_message']}")
    print(f"处理状态: {result['status']}")
    print(f"处理消息: {result['message']}")
    print("=" * 100)

def main():
    """
    主函数
    """
    print("开始批量订单审核 v2...")
    print(f"当前时间: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}")
    
    # 1. 获取基础数据
    print("\n获取基础数据...")
    store_list = review_order.get_store_list()
    logistics_list = review_order.get_logistics_list()
    orders_list = review_order.get_orders_list()
    
    print(f"获取到店铺: {len(store_list)} 个")
    print(f"获取到物流渠道: {len(logistics_list)} 个")
    print(f"获取到订单: {len(orders_list)} 个")
    
    # 2. 创建日志目录
    log_dir = "../开发日志"
    if not os.path.exists(log_dir):
        os.makedirs(log_dir)
    
    # 3. 生成日志文件
    today = datetime.datetime.now().strftime("%Y%m%d")
    log_file = os.path.join(log_dir, f"{today}_批量订单审核_v2.log")
    
    # 4. 处理订单
    processed_count = 0
    success_count = 0
    failed_count = 0
    
    with open(log_file, 'w', encoding='utf-8') as f:
        # 写入日志头部
        f.write(f"批量订单审核日志 v2\n")
        f.write(f"生成时间: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        f.write(f"店铺数量: {len(store_list)}\n")
        f.write(f"物流渠道数量: {len(logistics_list)}\n")
        f.write(f"订单数量: {len(orders_list)}\n")
        f.write("=" * 120 + "\n")
        
        # 处理前5个订单（测试用）
        total_orders = min(5, len(orders_list))
        print(f"\n开始处理 {total_orders} 个订单...")
        
        for i, order in enumerate(orders_list[:total_orders]):
            print(f"\n处理订单 {i+1}/{total_orders}: {order.get('global_order_no')}")
            
            try:
                # 处理订单
                result = process_single_order(order, store_list, logistics_list)
                processed_count += 1
                
                # 打印结果
                print_order_result(result)
                
                # 写入日志
                log_entry = f"订单: {result['order_no']}\n"
                log_entry += f"平台: {result['platform']}\n"
                log_entry += f"SKU: {result['sku']}\n"
                log_entry += f"地址: {result['country']}, {result['city']}, {result['postal_code']}\n"
                log_entry += f"可用仓库: {', '.join(result['available_warehouses'])}\n"
                log_entry += f"匹配渠道: {', '.join(result['matched_channels'])}\n"
                log_entry += f"可用渠道: {', '.join(result['available_channels'])}\n"
                log_entry += f"中邮: {result['ems_fee'] if result['ems_fee'] else '无'} ({result['ems_channel'] if result['ems_channel'] else '无'})\n"
                log_entry += f"运德: {result['wd_fee'] if result['wd_fee'] else '无'} ({result['wd_channel'] if result['wd_channel'] else '无'})\n"
                log_entry += f"最优: {result['best_fee'] if result['best_fee'] else '无'} ({result['best_channel'] if result['best_channel'] else '无'})\n"
                log_entry += f"选择: type_id={result['best_type_id']}, wid={result['best_wid']}\n"
                log_entry += f"状态: {result['status']}\n"
                log_entry += f"消息: {result['message']}\n"
                log_entry += f"修改: {'成功' if result['edit_status'] else '失败'} - {result['edit_message']}\n"
                log_entry += "-" * 120 + "\n"
                
                f.write(log_entry)
                
                # 统计结果
                if result['status'] == 'success':
                    success_count += 1
                else:
                    failed_count += 1
                    
            except Exception as e:
                failed_count += 1
                error_msg = f"处理订单 {order.get('global_order_no')} 时发生异常: {str(e)}\n"
                print(error_msg)
                f.write(error_msg)
        
        # 写入统计信息
        f.write("=" * 120 + "\n")
        f.write(f"处理完成时间: {datetime.datetime.now().strftime('%Y-%m-%d %H:%M:%S')}\n")
        f.write(f"总处理订单: {processed_count}\n")
        f.write(f"成功: {success_count}\n")
        f.write(f"失败: {failed_count}\n")
        f.write(f"成功率: {success_count/processed_count*100:.2f}%\n")
    
    # 5. 输出总结
    print(f"\n批量订单审核完成！")
    print(f"处理订单: {processed_count} 个")
    print(f"成功: {success_count} 个")
    print(f"失败: {failed_count} 个")
    print(f"成功率: {success_count/processed_count*100:.2f}%")
    print(f"详细日志已保存到: {log_file}")

if __name__ == "__main__":
    main()
