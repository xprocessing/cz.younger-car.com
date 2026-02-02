# 订单审核API测试文档

## API接口信息

**接口地址**: `admin-panel/api_order_review.php`

**请求方式**: POST

**Content-Type**: application/json

**权限验证**: 不需要

## 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|--------|------|
| store_id | string | 否 | 店铺ID |
| global_order_no | string | 是 | 订单号 |
| local_sku | string | 是 | 本地SKU |
| receiver_country_code | string | 是 | 国家代码 |
| city | string | 否 | 城市 |
| postal_code | string | 否 | 邮编 |
| wid | int | 否 | 仓库ID |
| logistics_type_id | int | 否 | 物流方式ID |
| estimated_yunfei | string | 否 | 预估邮费 |
| review_status | string | 否 | 审单状态 |
| review_time | string | 否 | 审单时间 |
| review_remark | string | 否 | 审单备注 |
| wd_yunfei | object | 否 | 运德运费（JSON对象） |
| ems_yunfei | object | 否 | 中邮运费（JSON对象） |

## 请求示例

```json
{
    "store_id": "STORE001",
    "global_order_no": "ORD20260202001",
    "local_sku": "SKU12345",
    "receiver_country_code": "US",
    "city": "New York",
    "postal_code": "10001",
    "wid": 1,
    "logistics_type_id": 10,
    "estimated_yunfei": "50.00",
    "review_status": "待审核",
    "review_time": "2026-02-02 10:00:00",
    "review_remark": "测试订单",
    "wd_yunfei": {
        "price": 45.00,
        "currency": "USD"
    },
    "ems_yunfei": {
        "price": 320.00,
        "currency": "CNY"
    }
}
```

## 响应格式

### 成功响应

```json
{
    "success": true,
    "message": "订单审核记录创建成功",
    "data": {
        "id": 1,
        "global_order_no": "ORD20260202001",
        "local_sku": "SKU12345",
        "store_id": "STORE001"
    }
}
```

### 失败响应

```json
{
    "success": false,
    "message": "错误信息"
}
```

## 错误码说明

| 错误信息 | 说明 |
|----------|------|
| 只支持POST请求 | 请求方法错误 |
| JSON格式错误 | 请求体不是有效的JSON |
| 订单号不能为空 | 缺少必填字段 |
| 本地SKU不能为空 | 缺少必填字段 |
| 国家不能为空 | 缺少必填字段 |
| 该订单号的审核记录已存在 | 订单号重复 |
| 订单审核记录创建失败 | 数据库插入失败 |
| 服务器错误 | 服务器内部错误 |

## 测试命令

### 使用curl测试

```bash
curl -X POST http://localhost/admin-panel/api_order_review.php \
  -H "Content-Type: application/json" \
  -d '{
    "store_id": "STORE001",
    "global_order_no": "ORD20260202001",
    "local_sku": "SKU12345",
    "receiver_country_code": "US",
    "city": "New York",
    "postal_code": "10001",
    "wid": 1,
    "logistics_type_id": 10,
    "estimated_yunfei": "50.00",
    "review_status": "待审核",
    "review_time": "2026-02-02 10:00:00",
    "review_remark": "测试订单",
    "wd_yunfei": {
      "price": 45.00,
      "currency": "USD"
    },
    "ems_yunfei": {
      "price": 320.00,
      "currency": "CNY"
    }
  }'
```

### 使用Python测试

```python
import requests
import json

url = 'http://localhost/admin-panel/api_order_review.php'
headers = {'Content-Type': 'application/json'}
data = {
    'store_id': 'STORE001',
    'global_order_no': 'ORD20260202001',
    'local_sku': 'SKU12345',
    'receiver_country_code': 'US',
    'city': 'New York',
    'postal_code': '10001',
    'wid': 1,
    'logistics_type_id': 10,
    'estimated_yunfei': '50.00',
    'review_status': '待审核',
    'review_time': '2026-02-02 10:00:00',
    'review_remark': '测试订单',
    'wd_yunfei': {
        'price': 45.00,
        'currency': 'USD'
    },
    'ems_yunfei': {
        'price': 320.00,
        'currency': 'CNY'
    }
}

response = requests.post(url, headers=headers, json=data)
print(response.json())
```
