import requests
import time

def send_request(n_days):
    """
    发送GET请求到指定URL，携带nDaysAgo参数
    :param n_days: nDaysAgo的数值
    """
    # 基础URL和参数
    base_url = "https://cz.younger-car.com/xlingxing/php/get_orders_profit2db.php"
    params = {"nDaysAgo": n_days}
    
    try:
        # 发送GET请求，设置超时时间10秒避免无限等待
        response = requests.get(base_url, params=params, timeout=10)
        # 检查响应状态码
        response.raise_for_status()  # 非200状态码会抛出异常
        
        # 打印执行日志
        print(f"[成功] nDaysAgo={n_days} | 状态码: {response.status_code} | 响应内容: {response.text[:200]}")  # 只打印前200字符避免内容过长
    
    except requests.exceptions.RequestException as e:
        # 捕获所有请求相关异常（网络错误、超时、状态码异常等）
        print(f"[失败] nDaysAgo={n_days} | 错误信息: {str(e)}")

def main():
    """主函数：循环执行请求，从30到1递减，间隔3秒"""
    print("开始执行请求任务，nDaysAgo从30递减到1，每隔3秒执行一次...")  # 提示语更新为30
    print("-" * 80)
    
    # 从30循环到1，步长为-1（核心修改：将90改为30）
    for n_days in range(30, 0, -1):
        send_request(n_days)
        # 最后一次请求后无需等待
        if n_days > 1:
            print(f"等待3秒后执行下一次请求...\n{'-'*80}")
            time.sleep(3)
    
    print("-" * 80)
    print("所有请求执行完成！")

if __name__ == "__main__":
    # 检查requests库是否安装（若未安装会触发ImportError）
    try:
        import requests
    except ImportError:
        print("错误：未安装requests库，请先执行 pip install requests 安装")
    else:
        main()