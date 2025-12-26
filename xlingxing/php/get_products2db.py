import requests
import time

# 基础URL（抽离固定地址，参数通过字典传递更规范，避免URL拼接错误）
BASE_URL = "https://cz.younger-car.com/xlingxing/php/get_products.php"

def send_request(offset):
    """
    发起单个GET请求，包含异常处理
    :param offset: 当前请求的offset参数值
    """
    # 构造请求参数
    params = {
        "length": 100,
        "offset": offset
    }
    
    try:
        # 发起请求，设置10秒超时（避免请求卡住）
        response = requests.get(BASE_URL, params=params, timeout=10)
        # 检查响应状态码（非200则抛出异常）
        response.raise_for_status()
        
        # 打印请求结果（便于查看执行状态）
        print(f"第 {current_request + 1} 次请求 | offset={offset} | 状态码={response.status_code} | 响应长度={len(response.text)}")
        
    except requests.exceptions.RequestException as e:
        # 捕获所有请求异常，不中断脚本
        print(f"第 {current_request + 1} 次请求 | offset={offset} | 请求失败: {str(e)}")

if __name__ == "__main__":
    # 初始化参数
    total_requests = 38  # 总请求次数
    initial_offset = 1   # 初始offset值
    offset_step = 100    # 每次offset增加的数值
    interval = 3         # 请求间隔（秒）
    
    current_offset = initial_offset
    
    # 循环发起指定次数的请求
    for current_request in range(total_requests):
        # 发起当前请求
        send_request(current_offset)
        
        # 更新offset（为下一次请求做准备）
        current_offset += offset_step
        
        # 最后一次请求后不执行延时
        if current_request < total_requests - 1:
            time.sleep(interval)
    
    print("\n所有38次请求已执行完成！")