-- 创建数据库
CREATE DATABASE IF NOT EXISTS cz_data CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cz_data;

-- 用户表
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 角色表
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 权限表
CREATE TABLE IF NOT EXISTS permissions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL UNIQUE,
    slug VARCHAR(50) NOT NULL UNIQUE,
    description TEXT,
    module VARCHAR(50) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 角色权限关联表
CREATE TABLE IF NOT EXISTS role_permissions (
    role_id INT NOT NULL,
    permission_id INT NOT NULL,
    PRIMARY KEY (role_id, permission_id),
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE,
    FOREIGN KEY (permission_id) REFERENCES permissions(id) ON DELETE CASCADE
);

-- 用户角色关联表
CREATE TABLE IF NOT EXISTS user_roles (
    user_id INT NOT NULL,
    role_id INT NOT NULL,
    PRIMARY KEY (user_id, role_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE CASCADE
);

-- 示例数据

-- 插入角色
INSERT INTO roles (name, description) VALUES
('admin', '系统管理员，拥有所有权限'),
('editor', '编辑，拥有内容管理权限'),
('viewer', '查看者，仅拥有查看权限');

-- 插入权限
INSERT INTO permissions (name, slug, description, module) VALUES
-- 用户管理权限
('查看用户', 'users.view', '可以查看用户列表', 'users'),
('创建用户', 'users.create', '可以创建新用户', 'users'),
('编辑用户', 'users.edit', '可以编辑现有用户', 'users'),
('删除用户', 'users.delete', '可以删除用户', 'users'),

-- 角色管理权限
('查看角色', 'roles.view', '可以查看角色列表', 'roles'),
('创建角色', 'roles.create', '可以创建新角色', 'roles'),
('编辑角色', 'roles.edit', '可以编辑现有角色', 'roles'),
('删除角色', 'roles.delete', '可以删除角色', 'roles'),

-- 权限管理权限
('查看权限', 'permissions.view', '可以查看权限列表', 'permissions'),
('创建权限', 'permissions.create', '可以创建新权限', 'permissions'),
('编辑权限', 'permissions.edit', '可以编辑现有权限', 'permissions'),
('删除权限', 'permissions.delete', '可以删除权限', 'permissions'),

-- 数据管理权限
('查看数据', 'data.view', '可以查看数据列表', 'data'),
('创建数据', 'data.create', '可以创建新数据', 'data'),
('编辑数据', 'data.edit', '可以编辑现有数据', 'data'),
('删除数据', 'data.delete', '可以删除数据', 'data'),

-- 运费管理权限
('查看运费', 'yunfei.view', '可以查看运费记录', 'yunfei'),
('创建运费', 'yunfei.create', '可以创建运费记录', 'yunfei'),
('编辑运费', 'yunfei.edit', '可以编辑运费记录', 'yunfei'),
('删除运费', 'yunfei.delete', '可以删除运费记录', 'yunfei'),

-- 商品管理权限
('查看商品', 'products.view', '可以查看商品列表', 'products'),
('创建商品', 'products.create', '可以创建新商品', 'products'),
('编辑商品', 'products.edit', '可以编辑现有商品', 'products'),
('删除商品', 'products.delete', '可以删除商品', 'products'),
('导入商品', 'products.import', '可以导入商品数据', 'products'),
('导出商品', 'products.export', '可以导出商品数据', 'products'),

-- 订单利润管理权限
('查看订单利润', 'order_profit.view', '可以查看订单利润列表', 'order_profit'),
('查看订单利润统计', 'order_profit.stats', '可以查看订单利润统计', 'order_profit'),

-- 店铺管理权限
('查看店铺', 'store.view', '可以查看店铺列表', 'store'),
('创建店铺', 'store.create', '可以创建新店铺', 'store'),
('编辑店铺', 'store.edit', '可以编辑现有店铺', 'store'),
('删除店铺', 'store.delete', '可以删除店铺', 'store'),

-- 仓库管理权限
('查看仓库', 'warehouses.view', '可以查看仓库列表', 'warehouses'),
('创建仓库', 'warehouses.create', '可以创建新仓库', 'warehouses'),
('编辑仓库', 'warehouses.edit', '可以编辑现有仓库', 'warehouses'),
('删除仓库', 'warehouses.delete', '可以删除仓库', 'warehouses'),

-- 库存明细权限
('查看库存明细', 'inventory_details.view', '可以查看库存明细列表', 'inventory_details'),
('创建库存明细', 'inventory_details.create', '可以创建库存明细', 'inventory_details'),
('编辑库存明细', 'inventory_details.edit', '可以编辑库存明细', 'inventory_details'),
('删除库存明细', 'inventory_details.delete', '可以删除库存明细', 'inventory_details'),

-- 新产品管理权限
('查看新产品', 'new_products.view', '可以查看新产品列表', 'new_products'),
('创建新产品', 'new_products.create', '可以创建新产品', 'new_products'),
('编辑新产品', 'new_products.edit', '可以编辑新产品', 'new_products'),
('删除新产品', 'new_products.delete', '可以删除新产品', 'new_products'),

-- 运费查询权限
('查询运费', 'query.view', '可以查询运费信息', 'query');

-- 给角色分配权限

-- 管理员拥有所有权限
INSERT INTO role_permissions (role_id, permission_id) SELECT 1, id FROM permissions;

-- 编辑拥有内容管理权限
INSERT INTO role_permissions (role_id, permission_id) VALUES
(2, 1), -- 查看用户
(2, 13), -- 查看数据
(2, 14), -- 创建数据
(2, 15), -- 编辑数据
(2, 16), -- 删除数据
(2, 22), -- 查看商品
(2, 23), -- 创建商品
(2, 24), -- 编辑商品
(2, 25), -- 删除商品
(2, 26), -- 导入商品
(2, 27), -- 导出商品
(2, 28), -- 查看订单利润
(2, 30), -- 查看订单利润统计
(2, 31), -- 查看店铺
(2, 32), -- 创建店铺
(2, 33), -- 编辑店铺
(2, 34), -- 删除店铺
(2, 35), -- 查看仓库
(2, 36), -- 创建仓库
(2, 37), -- 编辑仓库
(2, 38), -- 删除仓库
(2, 39), -- 查看新产品
(2, 40), -- 创建新产品
(2, 41), -- 编辑新产品
(2, 42), -- 删除新产品
(2, 44), -- 查看库存明细
(2, 45), -- 创建库存明细
(2, 46), -- 编辑库存明细
(2, 47), -- 删除库存明细
(2, 48); -- 查询运费

-- 查看者仅拥有查看权限
INSERT INTO role_permissions (role_id, permission_id) VALUES
(3, 1), -- 查看用户
(3, 5), -- 查看角色
(3, 9), -- 查看权限
(3, 13), -- 查看数据
(3, 22), -- 查看商品
(3, 28), -- 查看订单利润
(3, 29), -- 查看订单利润统计
(3, 30), -- 查看店铺
(3, 35), -- 查看仓库
(3, 44), -- 查看库存明细
(3, 39), -- 查看新产品
(3, 48); -- 查询运费

-- 创建默认管理员用户
INSERT INTO users (username, email, password, full_name) VALUES
('admin', 'admin@example.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '系统管理员');

-- 示例数据表：产品表
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    description TEXT,
    price DECIMAL(10, 2) NOT NULL,
    category VARCHAR(50) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 插入示例产品数据
INSERT INTO products (name, description, price, category) VALUES
('产品1', '这是产品1的描述', 100.00, '电子产品'),
('产品2', '这是产品2的描述', 200.00, '家居用品'),
('产品3', '这是产品3的描述', 300.00, '服装'),
('产品4', '这是产品4的描述', 400.00, '电子产品'),
('产品5', '这是产品5的描述', 500.00, '家居用品');

-- 给管理员分配角色
INSERT INTO user_roles (user_id, role_id) VALUES
(1, 1); -- 管理员用户分配管理员角色

-- 运费表
CREATE TABLE IF NOT EXISTS yunfei (
    id INT AUTO_INCREMENT PRIMARY KEY,
    global_order_no VARCHAR(50) NOT NULL,
    shisuanyunfei JSON DEFAULT NULL,
    create_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_order_no (global_order_no)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
-- 新品开发及进度表
CREATE TABLE IF NOT EXISTS `new_products` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`require_no` CHAR(50) NOT NULL COMMENT '需求编号',
	`img_url` VARCHAR(255),
	`require_title` VARCHAR(255) COMMENT '需求名称',
	`npdId` CHAR(50) COMMENT '新产品id
',
	`sku` CHAR(50),
	`remark` VARCHAR(255) COMMENT '备注',
	`create_time` DATE,
	`current_step` INTEGER COMMENT '当前进度',
	`process_list` JSON COMMENT '进度明细',
	PRIMARY KEY(`id`)
);

---订单利润表
CREATE TABLE IF NOT EXISTS `order_profit` (
	`id` INTEGER NOT NULL AUTO_INCREMENT UNIQUE,
	`store_id` CHAR(50) COMMENT '店铺id',
	`global_order_no` CHAR(50) COMMENT '订单号',
    `warehouse_name` CHAR(50) COMMENT '发货仓库',
	`receiver_country` CHAR(10) COMMENT '收货国家',
	`global_purchase_time` CHAR(30) COMMENT '下单时间',
	`local_sku` CHAR(50) COMMENT 'sku',
	`order_total_amount` CHAR(20) COMMENT '订单总额，为字符串带$货币符号',
    `wms_outbound_cost_amount` CHAR(20) COMMENT '实际出库成本，为字符串带有$货币符号',
	`wms_shipping_price_amount` CHAR(20) COMMENT '实际运费，为字符串带有$货币符号',	
	`profit_amount` CHAR(20) COMMENT '毛利润，为字符串带有$货币符号，有正有负',
	`profit_rate` CHAR(20) COMMENT '利润率（已经计算过，数据库中有正有负,保留小数点两位）',	
	`update_time` DATETIME COMMENT '数据更新时间',
	PRIMARY KEY(`id`)
);


-- 创建店铺信息表
CREATE TABLE `store` (
  `store_id` varchar(64) NOT NULL COMMENT '店铺ID',
  `sid` varchar(50) DEFAULT NULL COMMENT '店铺编号',
  `store_name` varchar(100) NOT NULL COMMENT '店铺名称',
  `platform_code` varchar(20) NOT NULL COMMENT '平台编码',
  `platform_name` varchar(50) NOT NULL COMMENT '平台名称',
  `currency` varchar(10) NOT NULL COMMENT '货币类型',
  `is_sync` tinyint NOT NULL COMMENT '是否同步（1：是，0：否）',
  `status` tinyint NOT NULL COMMENT '状态（1：正常，其他：异常）',
  `country_code` varchar(20) DEFAULT NULL COMMENT '国家/地区编码',
  PRIMARY KEY (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='店铺信息表';


-- 创建产品表
CREATE TABLE IF NOT EXISTS products (
    id INT PRIMARY KEY,  -- 主键，非空且唯一
    cid INT NULL,
    bid INT NULL,
    sku VARCHAR(50) NOT NULL UNIQUE  COMMENT 'sku',  -- sku非空且唯一
    sku_identifier VARCHAR(100) NULL COMMENT 'SKU识别码',
    product_name VARCHAR(255) NULL COMMENT '产品名称',
    pic_url VARCHAR(255) NULL COMMENT '产品图片URL',
    cg_delivery VARCHAR(255) NULL COMMENT '采购：交期',
    cg_transport_costs DECIMAL(10, 2) NULL COMMENT '采购：运输成本',
    purchase_remark TEXT NULL COMMENT '采购备注',
    cg_price DECIMAL(10, 4) NULL COMMENT '采购成本',
    status TINYINT NULL COMMENT '状态（状态：0 停售，1 在售，2 开发中，3 清仓）',
    open_status TINYINT NULL COMMENT '产品是否启用：0 停用，1 启用',
    is_combo TINYINT NULL COMMENT '是否组合产品（1：是，0：否）',
    create_time DATETIME NULL COMMENT '创建时间',
    update_time DATETIME NULL COMMENT '更新时间',
    product_developer_uid INT NULL COMMENT '开发人员ID',
    cg_opt_uid INT NULL COMMENT '采购人员ID',
    cg_opt_username VARCHAR(100) NULL COMMENT '采购人员名称',
    spu VARCHAR(50) NULL,
    ps_id INT NULL COMMENT 'SPU唯一id',
    attribute JSON NULL COMMENT '产品属性（JSON格式）',  -- JSON类型，允许为空
    brand_name VARCHAR(100) NULL COMMENT '品牌名称',
    category_name VARCHAR(100) NULL COMMENT '分类名称',
    status_text VARCHAR(50) NULL COMMENT '状态文本',
    product_developer VARCHAR(100) NULL COMMENT '开发人员',
    supplier_quote JSON NULL COMMENT '供应商报价信息',  -- JSON类型，允许为空
    aux_relation_list JSON NULL,  -- JSON类型，允许为空
    custom_fields JSON NULL COMMENT '自定义字段（JSON格式）',  -- JSON类型，允许为空
    global_tags JSON NULL COMMENT '产品标签（JSON格式）'  -- JSON类型，允许为空
);

USE cz_data;

-- 创建仓库信息表（包含所有JSON数据中出现的字段）
CREATE TABLE IF NOT EXISTS warehouses (
    wid INT PRIMARY KEY,
    type INT NOT NULL,
    sub_type INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    is_delete INT NOT NULL,
    country_code VARCHAR(10),
    wp_id INT,
    wp_name VARCHAR(50),
    t_warehouse_name VARCHAR(50),
    t_warehouse_code VARCHAR(50),
    t_country_area_name VARCHAR(50),
    t_status VARCHAR(10)
);

-- 插入所有文件中data字段的仓库数据
INSERT INTO warehouses (wid, type, sub_type, name, is_delete, country_code, wp_id, wp_name, t_warehouse_name, t_warehouse_code, t_country_area_name, t_status)
VALUES
    -- get_warehouse6.json 数据
    (5830, 6, 0, 'may_auto_store-US美国仓(AWD)', 0, 'US', 0, '', '', '', '', ''),
    (6568, 6, 0, 'YoungerCar-US美国仓(AWD)', 0, 'US', 0, '', '', '', '', ''),
    (6580, 6, 0, 'chiesma-US美国仓(AWD)', 0, 'US', 0, '', '', '', '', ''),
    (6587, 6, 0, 'loyalty-US美国仓(AWD)', 0, 'US', 0, '', '', '', '', ''),
    (6613, 6, 0, 'Auoleru-US美国仓(AWD)', 0, 'US', 0, '', '', '', '', ''),
    
    -- get_warehouse1.json 数据
    (8111, 1, 0, '一件代发仓', 0, '', 0, '', '', '', '', ''),
    (5693, 1, 0, '中国温州仓', 0, 'CN', 0, '', '', '', '', ''),
    (8071, 1, 0, '虚拟仓', 0, '', 0, '', '', '', '', ''),
    (5572, 1, 0, '默认仓库', 0, '', 0, '', '', '', '', ''),
    
    -- get_warehouse4.json 数据
    (5829, 4, 0, 'may_auto_store-US美国仓', 0, '', 0, '', '', '', '', ''),
    (5831, 4, 0, 'may_auto_store-CA加拿大仓', 0, '', 0, '', '', '', '', ''),
    (6567, 4, 0, 'YoungerCar-US美国仓', 0, '', 0, '', '', '', '', ''),
    (6569, 4, 0, 'YoungerCar-CA加拿大仓', 0, '', 0, '', '', '', '', ''),
    (6570, 4, 0, 'YoungerCar-UK英国仓', 0, '', 0, '', '', '', '', ''),
    (6571, 4, 0, 'may_auto_store-UK英国仓', 0, '', 0, '', '', '', '', ''),
    (6572, 4, 0, 'may_auto_store-DE德国仓', 0, '', 0, '', '', '', '', ''),
    (6579, 4, 0, 'chiesma-US美国仓', 0, '', 0, '', '', '', '', ''),
    (6581, 4, 0, 'chiesma-CA加拿大仓', 0, '', 0, '', '', '', '', ''),
    (6585, 4, 0, 'CARIG-JP日本仓', 0, '', 0, '', '', '', '', ''),
    (6586, 4, 0, 'loyalty-US美国仓', 0, '', 0, '', '', '', '', ''),
    (6588, 4, 0, 'loyalty-CA加拿大仓', 0, '', 0, '', '', '', '', ''),
    (6612, 4, 0, 'Auoleru-US美国仓', 0, '', 0, '', '', '', '', ''),
    
    -- get_warehouse3.json 数据
    (5851, 3, 2, 'eBay-运德美东仓', 0, 'US', 339, '运德', '美东仓', 'WDUSNJ', '美国', '1'),
    (5850, 3, 2, 'eBay-运德美西仓', 0, 'US', 339, '运德', '美西仓', 'WDUSLG', '美国', '1'),
    (5842, 3, 2, 'eBay中邮美东仓', 0, 'US', 334, '中邮', '美东仓库', 'USEA', '美国', '1'),
    (5843, 3, 2, 'eBay中邮美西仓', 0, 'US', 334, '中邮', '美西仓库', 'USWE', '美国', '1'),
    (5859, 3, 2, '中邮德国仓', 0, 'DE', 340, '中邮', '德国仓库', 'DE', '德国', '1'),
    (5858, 3, 2, '中邮捷克仓', 0, 'CZ', 340, '中邮', '捷克仓库', 'CZ', '捷克', '1'),
    (5832, 3, 2, '中邮美东仓', 0, 'US', 330, '中邮', '美东仓库', 'USEA', '美国', '1'),
    (5833, 3, 2, '中邮美西仓', 0, 'US', 330, '中邮', '美西仓库', 'USWE', '美国', '1'),
    (5860, 3, 2, '中邮英国仓', 0, 'GB', 340, '中邮', '英国仓库', 'UK', '英国', '1'),
    (5840, 3, 2, '亚马逊-中邮美东仓', 0, 'US', 333, '中邮', '美东仓库', 'USEA', '美国', '1'),
    (5841, 3, 2, '亚马逊-中邮美西仓', 0, 'US', 333, '中邮', '美西仓库', 'USWE', '美国', '1'),
    (5849, 3, 2, '亚马逊-运德美东仓', 0, 'US', 338, '运德', '美东仓', 'WDUSNJ', '美国', '1'),
    (5848, 3, 2, '亚马逊-运德美西仓', 0, 'US', 338, '运德', '美西仓', 'WDUSLG', '美国', '1'),
    (8069, 3, 2, '恒心运德仓 美东仓', 0, 'US', 510, '运德', '美东仓', 'WDUSNJ', '美国', '1'),
    (9159, 3, 2, '恒心运德仓 美东六仓', 0, 'US', 510, '运德', '美东六仓', 'WDUSNJS', '美国', '1'),
    (8070, 3, 2, '恒心运德仓 美西仓', 0, 'US', 510, '运德', '美西仓', 'WDUSLG', '美国', '1'),
    (5838, 3, 2, '独立站-中邮美东仓', 0, 'US', 332, '中邮', '美东仓库', 'USEA', '美国', '1'),
    (5839, 3, 2, '独立站-中邮美西仓', 0, 'US', 332, '中邮', '美西仓库', 'USWE', '美国', '1'),
    (5847, 3, 2, '独立站-运德美东仓', 0, 'US', 337, '运德', '美东仓', 'WDUSNJ', '美国', '1'),
    (5846, 3, 2, '独立站-运德美西仓', 0, 'US', 337, '运德', '美西仓', 'WDUSLG', '美国', '1'),
    (8061, 3, 1, '美东自由仓', 0, '', 0, '', '', '', '', ''),
    (17814, 3, 2, '美东领星自由仓 E commerce logistics', 0, 'US', 1155, '领星WMS', 'E commerce logistics', 'NJ', '美国', '1'),
    (5875, 3, 1, '美西自由仓', 0, '', 0, '', '', '', '', ''),
    (5844, 3, 2, '运德美东仓', 0, 'US', 336, '运德', '美东仓', 'WDUSNJ', '美国', '1'),
    (5845, 3, 2, '运德美西仓', 0, 'US', 336, '运德', '美西仓', 'WDUSLG', '美国', '1');




CREATE TABLE `inventory_details` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键自增ID',
  `wid` INT NOT NULL COMMENT '仓库id',
  `product_id` INT NOT NULL COMMENT '本地产品id',
  `sku` VARCHAR(50) NOT NULL COMMENT '产品SKU编码',
  `seller_id` VARCHAR(30) DEFAULT '' COMMENT '店铺id',
  `fnsku` VARCHAR(50) DEFAULT '' COMMENT 'FNSKU编码',
  `product_total` INT NOT NULL DEFAULT 0 COMMENT '实际库存总量【可用+次品+待检+锁定】',
  `product_valid_num` INT NOT NULL DEFAULT 0 COMMENT '可用量',
  `product_bad_num` INT NOT NULL DEFAULT 0 COMMENT '次品量',
  `product_qc_num` INT NOT NULL DEFAULT 0 COMMENT '待检待上架量',
  `product_lock_num` INT NOT NULL DEFAULT 0 COMMENT '锁定量',
  `good_lock_num` INT NOT NULL DEFAULT 0 COMMENT '良品锁定量',
  `bad_lock_num` INT NOT NULL DEFAULT 0 COMMENT '次品锁定量',
  `stock_cost_total` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '库存成本总计',
  `quantity_receive` VARCHAR(20) DEFAULT '0' COMMENT '待到货量',
  `stock_cost` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '单位库存成本',
  `product_onway` INT NOT NULL DEFAULT 0 COMMENT '调拨在途数量',
  `transit_head_cost` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '调拨在途头程成本',
  `average_age` INT NOT NULL DEFAULT 0 COMMENT '平均库龄(天)',
  `third_inventory` JSON NOT NULL COMMENT '海外仓第三方库存信息【完整嵌套对象，原生JSON格式】',
  `stock_age_list` JSON NOT NULL COMMENT '库龄信息列表【完整数组结构，原生JSON格式】',
  `available_inventory_box_qty` DECIMAL(10,1) DEFAULT 0.0 COMMENT '可用箱装库存数量',
  `purchase_price` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '采购单价',
  `price` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '单位费用',
  `head_stock_price` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '单位头程费用',
  `stock_price` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '单位库存成本',
  PRIMARY KEY (`id`),
  KEY `idx_sku_wid` (`sku`,`wid`) USING BTREE COMMENT 'SKU+仓库ID联合索引，提升业务查询效率'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='库存详情表-原生JSON格式存储，适配MySQL5.7.40，无语法错误';

INSERT INTO `inventory_details` (
  wid,product_id,sku,seller_id,fnsku,product_total,product_valid_num,product_bad_num,product_qc_num,product_lock_num,
  good_lock_num,bad_lock_num,stock_cost_total,quantity_receive,stock_cost,product_onway,transit_head_cost,average_age,
  third_inventory,stock_age_list,available_inventory_box_qty,purchase_price,price,head_stock_price,stock_price
) VALUES
(5848,83506,'NI-C63-FL-GB','0','',1,1,0,0,0,0,0,428.32,'0',428.3190,0,0.00,214,
'{"qty_sellable":"1","qty_reserved":"0","qty_onway":"0","qty_pending":"0","box_qty_sellable":"0","box_qty_reserved":"0","box_qty_onway":"0","box_qty_pending":"0"}',
'[{"name":"0-15天库龄","qty":0},{"name":"16-30天库龄","qty":0},{"name":"31-90天库龄","qty":0},{"name":"91天以上库龄","qty":1}]',
1.0,428.3190,0.0000,0.0000,428.3190),

(5850,83506,'NI-C63-FL-GB','0','',5,5,0,0,0,0,0,1499.55,'0',299.9101,0,0.00,74,
'{"qty_sellable":"5","qty_reserved":"0","qty_onway":"0","qty_pending":"0","box_qty_sellable":"0","box_qty_reserved":"0","box_qty_onway":"0","box_qty_pending":"0"}',
'[{"name":"0-15天库龄","qty":0},{"name":"16-30天库龄","qty":0},{"name":"31-90天库龄","qty":2},{"name":"91天以上库龄","qty":1}]',
5.0,238.2001,0.0000,61.7100,299.9101),

(5847,83506,'NI-C63-FL-GB','0','',3,3,0,0,0,0,0,1220.94,'0',406.9808,0,0.00,77,
'{"qty_sellable":"3","qty_reserved":"0","qty_onway":"0","qty_pending":"0","box_qty_sellable":"0","box_qty_reserved":"0","box_qty_onway":"0","box_qty_pending":"0"}',
'[{"name":"0-15天库龄","qty":0},{"name":"16-30天库龄","qty":0},{"name":"31-90天库龄","qty":2},{"name":"91天以上库龄","qty":1}]',
3.0,281.1257,0.0000,125.8550,406.9808),

(5849,83506,'NI-C63-FL-GB','0','',2,2,0,0,0,0,0,768.09,'0',384.0456,0,0.00,69,
'{"qty_sellable":"2","qty_reserved":"0","qty_onway":"0","qty_pending":"0","box_qty_sellable":"0","box_qty_reserved":"0","box_qty_onway":"0","box_qty_pending":"0"}',
'[{"name":"0-15天库龄","qty":0},{"name":"16-30天库龄","qty":0},{"name":"31-90天库龄","qty":2},{"name":"91天以上库龄","qty":0}]',
2.0,230.0000,0.0000,154.0456,384.0456),

(5851,83506,'NI-C63-FL-GB','0','',0,0,0,0,0,0,0,0.00,'0',0.0000,9,0.00,0,
'{"qty_sellable":"0","qty_reserved":"0","qty_onway":"9","qty_pending":"0","box_qty_sellable":"0","box_qty_reserved":"0","box_qty_onway":"0","box_qty_pending":"0"}',
'[{"name":"0-15天库龄","qty":0},{"name":"16-30天库龄","qty":0},{"name":"31-90天库龄","qty":0},{"name":"91天以上库龄","qty":0}]',
0.0,0.0000,0.0000,0.0000,0.0000),

(5860,83506,'NI-C63-FL-GB','0','',9,9,0,0,0,0,0,2070.00,'0',230.0000,0,0.00,30,
'{"qty_sellable":"9","qty_reserved":"0","qty_onway":"0","qty_pending":"0","box_qty_sellable":"0","box_qty_reserved":"0","box_qty_onway":"0","box_qty_pending":"0"}',
'[{"name":"0-15天库龄","qty":0},{"name":"16-30天库龄","qty":9},{"name":"31-90天库龄","qty":0},{"name":"91天以上库龄","qty":0}]',
9.0,230.0000,0.0000,0.0000,230.0000),

(5693,93391,'NI-C63-FL-GB-2','0','',7,7,0,0,0,0,0,725.56,'0',103.6510,0,0.00,214,
'{"qty_sellable":"","qty_reserved":"","qty_onway":"","qty_pending":"","box_qty_sellable":"","box_qty_reserved":"","box_qty_onway":"","box_qty_pending":""}',
'[{"name":"0-15天库龄","qty":0},{"name":"16-30天库龄","qty":0},{"name":"31-90天库龄","qty":0},{"name":"91天以上库龄","qty":7}]',
7.0,103.6510,0.0000,0.0000,103.6510),

(5693,83506,'NI-C63-FL-GB','0','',2,2,0,0,0,0,0,460.00,'0',230.0000,0,0.00,34,
'{"qty_sellable":"","qty_reserved":"","qty_onway":"","qty_pending":"","box_qty_sellable":"","box_qty_reserved":"","box_qty_onway":"","box_qty_pending":""}',
'[{"name":"0-15天库龄","qty":0},{"name":"16-30天库龄","qty":0},{"name":"31-90天库龄","qty":2},{"name":"91天以上库龄","qty":0}]',
2.0,230.0000,0.0000,0.0000,230.0000);