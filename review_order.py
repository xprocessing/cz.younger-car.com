#获取所有店铺信息 https://cz.younger-car.com/xlingxing/php/get_store.php
#获取所有物流渠道信息 https://cz.younger-car.com/xlingxing/php/get_listUsedLogisticsType.php?provider_type=2
#获取待审核订单列表信息（sku，订单号，店铺id，订单创建时间，订单状态等） https://cz.younger-car.com/xlingxing/php/get_orders.php?nDaysAgo=2 
#通过sku查询库存 https://cz.younger-car.com/xlingxing/php/get_inventoryDetails.php?sku=NI-C63-FL-GB

#通过sku获取中邮的产品规格 http://cz.younger-car.com/yunfei_kucun/api_ems/get_product_list.php?page=1&pageSize=10&product_sku=NI-C63-FL-GB
#获取中邮运费试算api http://cz.younger-car.com/yunfei_kucun/api_ems/get_ship_fee_api.php?postcode=90210&weight=1.5&warehouse=USEA,USWE&channel=USPS-PRIORITY,AMAZON-GROUND&length=26&width=20&height=2

#通过sku获取运德的产品规格 https://cz.younger-car.com/yunfei_kucun/api_wd/get_product_list.php?sku=NI-C63-FL-GB
#获取运德的运费试算  https://cz.younger-car.com/yunfei_kucun/api_wd/get_ship_fee.php
#修改领星订单 仓库+物流渠道
#数据存入数据库，使用post请求。
#可视化运行大屏+钉钉通知。


# 订单审核数据表 order_review
"""
CREATE TABLE IF NOT EXISTS `order_review` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
    `store_id` CHAR(50) COMMENT '店铺id',
	`global_order_no` CHAR(50) NOT NULL COMMENT '订单号',
    `local_sku` CHAR(50) NOT NULL COMMENT '本地sku',
    `receiver_country_code` CHAR(10) NOT NULL COMMENT '国家',
    `city` CHAR(50) NOT NULL COMMENT '城市',
    `postal_code` CHAR(20) NOT NULL COMMENT '邮编',
    `wd_yunfei` JSON DEFAULT NULL COMMENT '运德运费（试算数据）',
    `ems_yunfei` JSON DEFAULT NULL COMMENT '中邮运费（试算数据）',
    `wid` INT DEFAULT NULL COMMENT '仓库wid',
    `logistics_type_id` INT DEFAULT NULL COMMENT '物流方式id',
    `estimated_yunfei` VARCHAR(20) DEFAULT NULL COMMENT '预估邮费(带美元/人民币符号)',
	`review_status` VARCHAR(20) DEFAULT NULL COMMENT '审单状态（null/自动审核/人工审核）',
	`review_time` DATETIME COMMENT '审单时间',	
	`review_remark` VARCHAR(255) COMMENT '审单备注',
	PRIMARY KEY(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='订单审核';

"""
#