-- 创建数据库
CREATE DATABASE IF NOT EXISTS cz CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cz;

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
('删除运费', 'yunfei.delete', '可以删除运费记录', 'yunfei');

-- 给角色分配权限

-- 管理员拥有所有权限
INSERT INTO role_permissions (role_id, permission_id) SELECT 1, id FROM permissions;

-- 编辑拥有内容管理权限
INSERT INTO role_permissions (role_id, permission_id) VALUES
(2, 1), -- 查看用户
(2, 13), -- 查看数据
(2, 14), -- 创建数据
(2, 15), -- 编辑数据
(2, 16); -- 删除数据

-- 查看者仅拥有查看权限
INSERT INTO role_permissions (role_id, permission_id) VALUES
(3, 1), -- 查看用户
(3, 5), -- 查看角色
(3, 9), -- 查看权限
(3, 13); -- 查看数据

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
	`global_order_no` CHAR(50) COMMENT '订单号
',
	`receiver_country` CHAR(10) COMMENT '收货国家',
	`global_purchase_time` CHAR(30) COMMENT '下单时间',
	`local_sku` CHAR(50) COMMENT 'sku',
	`order_total_amount` CHAR(20) COMMENT '订单总额',	
	`profit_amount` CHAR(20) COMMENT '毛利润',
	`profit_rate` CHAR(20) COMMENT '利润率',
	`wms_outbound_cost_amount` CHAR(20) COMMENT '实际出库成本，币种CNY',
	`wms_shipping_price_amount` CHAR(20) COMMENT '实际运费，币种CNY',
	`update_time` DATETIME COMMENT '数据更新时间',
	PRIMARY KEY(`id`)
);


