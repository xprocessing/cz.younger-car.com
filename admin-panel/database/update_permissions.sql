-- 更新权限和角色权限的SQL脚本
-- 用于修复外键约束问题

USE cz_data;

-- 1. 删除可能存在的旧角色权限关联（避免外键冲突）
DELETE FROM role_permissions WHERE permission_id IN (28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38);

-- 2. 插入新权限（使用 IGNORE 避免重复）
INSERT IGNORE INTO permissions (name, slug, description, module) VALUES
('查看订单利润', 'order_profit.view', '可以查看订单利润列表', 'order_profit'),
('查看订单利润统计', 'order_profit.stats', '可以查看订单利润统计', 'order_profit'),
('查看店铺', 'store.view', '可以查看店铺列表', 'store'),
('创建店铺', 'store.create', '可以创建新店铺', 'store'),
('编辑店铺', 'store.edit', '可以编辑现有店铺', 'store'),
('删除店铺', 'store.delete', '可以删除店铺', 'store'),
('查看新产品', 'new_products.view', '可以查看新产品列表', 'new_products'),
('创建新产品', 'new_products.create', '可以创建新产品', 'new_products'),
('编辑新产品', 'new_products.edit', '可以编辑新产品', 'new_products'),
('删除新产品', 'new_products.delete', '可以删除新产品', 'new_products'),
('查询运费', 'query.view', '可以查询运费信息', 'query');

-- 3. 为编辑角色分配新权限
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(2, 28), -- 查看订单利润
(2, 29), -- 查看订单利润统计
(2, 30), -- 查看店铺
(2, 31), -- 创建店铺
(2, 32), -- 编辑店铺
(2, 33), -- 删除店铺
(2, 34), -- 查看新产品
(2, 35), -- 创建新产品
(2, 36), -- 编辑新产品
(2, 37), -- 删除新产品
(2, 38); -- 查询运费

-- 4. 为查看者角色分配新权限
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(3, 28), -- 查看订单利润
(3, 29), -- 查看订单利润统计
(3, 30), -- 查看店铺
(3, 34), -- 查看新产品
(3, 38); -- 查询运费

-- 5. 验证权限是否正确插入
SELECT id, name, slug, module FROM permissions WHERE id >= 28 ORDER BY id;
