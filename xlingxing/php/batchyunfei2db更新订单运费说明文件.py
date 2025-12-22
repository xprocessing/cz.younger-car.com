#版本3
#开发python脚本
# 1. 读取本地 skuweightlwh.json文件，格式为
{
  "A-05C6-FL2-CF": {
    "weight": 3.0,
    "length": 100.0,
    "width": 27.0,
    "height": 12.0
  },
  "A-05C6-FL2-GB": {
    "weight": 1.6,
    "length": 100.0,
    "width": 27.0,
    "height": 12.0
  }
}

#2.//访问https://cz.younger-car.com/xdata/php/get_orders.php获取最新待审核订单 的json数据orders

# orders.data.list #就是订单列表数组，遍历数组获取每个订单数据字段
# global_order_no = orders.data.list[0].global_order_no #订单号
# receiver_country_code= orders.data.list[0].address_info.receiver_country_code #国家代码
# postcode= orders.data.list[0].address_info.postal_code #邮编
# city= orders.data.list[0].address_info.city #城市
# local_sku = orders.data.list[0].item_info[0].local_sku #本地sku


# weight 为json文件中local_sku对应的weight  #重量kg
# length 为json文件中local_sku对应的length  #长度
# width 为json文件中local_sku对应的width  #宽度
# height 为json文件中local_sku对应的height  #高度



#3.根据订单数据字段拼接地址
# https://cz.younger-car.com/chayunfei2db.php?global_order_no=103645723765286447&receiver_country_code=US&postcode=30318-3230&weight=26.91&length=115.0&width=39.0&height=30.0&city=ATLANTA

# 4.如果所有字段都存在，访问该地址，采用异步方式，每隔5秒发起一个请求，直到所有请求完成

# 5.每隔5分钟 执行一次查询 get_orders.php 获取最新待审核订单 的json数据orders

# 6.补充避免重复查询逻辑，将global_order_no存在内存，检查global_order_no是否查询过，如果查询过，则跳过，否则查询


















# 版本 v1 v2
#开发python脚本
#1.//访问https://cz.younger-car.com/xdata/php/get_orders.php获取最新待审核订单 的json数据orders

# orders.data.list #就是订单列表数组，遍历数组获取每个订单数据字段
# global_order_no = orders.data.list[0].global_order_no #订单号
# receiver_country_code= orders.data.list[0].address_info.receiver_country_code #国家代码
# postcode= orders.data.list[0].address_info.postal_code #邮编
# city= orders.data.list[0].address_info.city #城市
# weight = orders.data.list[0].logistics_info.pre_fee_weight #重量g,要除以1000换算成千克
# length = orders.data.list[0].logistics_info.pre_pkg_length #长度
# width =orders.data.list[0].logistics_info.pre_pkg_width #宽度
# height=orders.data.list[0].logistics_info.pre_pkg_height #高度



#2.根据订单数据字段拼接地址
# https://cz.younger-car.com/chayunfei2db.php?global_order_no=103645723765286447&receiver_country_code=US&postcode=30318-3230&weight=26.91&length=115.0&width=39.0&height=30.0&city=ATLANTA
# 3.如果所有字段都存在，访问该地址，采用异步方式，每隔5秒发起一个请求，直到所有请求完成

# 补充避免重复查询逻辑，将global_order_no存在内存，检查global_order_no是否查询过，如果查询过，则跳过，否则查询



