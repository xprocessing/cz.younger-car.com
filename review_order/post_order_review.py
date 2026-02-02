import requests
import json
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



# 调用函数，传入测试参数（可选参数wd_yunfei、ems_yunfei可省略，默认空字典）
response = post_order_review(
    store_id="110574573074617856",
    global_order_no="ORD2026020201031112",
    local_sku="SKU12345",
    receiver_country_code="US",
    city="New York",
    postal_code="10001",
    wd_yunfei={"US": "10.00","US1": "15.00"},
    ems_yunfei={"US": "15.00","US1": "15.00"},
    wid=1,
    logistics_type_id=10,
    estimated_yunfei="50.00",
    review_remark="测试订单"
)

# 处理响应结果
if response:
  
    # 1. 先打印原始响应信息，用于调试（关键：先确认接口返回了什么）
    print(f"响应状态码：{response.status_code}")
    print(f"响应内容（原始文本）：{response.text}")
    print(response.json().get("success"))
    
