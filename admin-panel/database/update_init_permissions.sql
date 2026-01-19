-- 权限更新SQL文件 - 针对init.sql的全面权限更新
-- 清除现有权限配置并重新创建所有模块的权限
-- 确保管理员拥有所有权限

USE cz_data;

-- 1. 清除现有权限配置
-- 注意：这将删除所有现有的权限和角色权限分配
-- TRUNCATE TABLE role_permissions;
-- TRUNCATE TABLE permissions;

-- 2. 重新创建所有权限
-- 按照模块分组，确保权限完整，与init.sql结构一致

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
('导入仓库数据', 'warehouses.import', '可以导入仓库数据', 'warehouses'),
('导出仓库数据', 'warehouses.export', '可以导出仓库数据', 'warehouses'),

-- 库存明细权限
('查看库存明细', 'inventory_details.view', '可以查看库存明细列表', 'inventory_details'),
('创建库存明细', 'inventory_details.create', '可以创建库存明细', 'inventory_details'),
('编辑库存明细', 'inventory_details.edit', '可以编辑库存明细', 'inventory_details'),
('删除库存明细', 'inventory_details.delete', '可以删除库存明细', 'inventory_details'),
('导入库存明细', 'inventory_details.import', '可以导入库存明细数据', 'inventory_details'),
('导出库存明细', 'inventory_details.export', '可以导出库存明细数据', 'inventory_details'),

-- FBA库存明细权限
('查看FBA库存明细', 'inventory_details_fba.view', '可以查看FBA库存明细列表', 'inventory_details_fba'),
('删除FBA库存明细', 'inventory_details_fba.delete', '可以删除FBA库存明细', 'inventory_details_fba'),
('导入FBA库存明细', 'inventory_details_fba.import', '可以导入FBA库存明细数据', 'inventory_details_fba'),

-- 新产品管理权限
('查看新产品', 'new_products.view', '可以查看新产品列表', 'new_products'),
('创建新产品', 'new_products.create', '可以创建新产品', 'new_products'),
('编辑新产品', 'new_products.edit', '可以编辑新产品', 'new_products'),
('删除新产品', 'new_products.delete', '可以删除新产品', 'new_products'),

-- 运费查询权限
('查询运费', 'query.view', '可以查询运费信息', 'query'),

-- 公司运营费用权限（新增模块）
('查看公司运营费用', 'company_costs.view', '可以查看公司运营费用列表', 'company_costs'),
('创建公司运营费用', 'company_costs.create', '可以创建公司运营费用记录', 'company_costs'),
('编辑公司运营费用', 'company_costs.edit', '可以编辑公司运营费用记录', 'company_costs'),
('删除公司运营费用', 'company_costs.delete', '可以删除公司运营费用记录', 'company_costs'),
('导入公司运营费用', 'company_costs.import', '可以导入公司运营费用数据', 'company_costs'),
('导出公司运营费用', 'company_costs.export', '可以导出公司运营费用数据', 'company_costs'),
('查看公司运营费用统计', 'company_costs.stats', '可以查看公司运营费用统计', 'company_costs'),

-- 店铺费用权限（新增模块）
('查看店铺费用', 'shop_costs.view', '可以查看店铺费用列表', 'shop_costs'),
('创建店铺费用', 'shop_costs.create', '可以创建店铺费用记录', 'shop_costs'),
('编辑店铺费用', 'shop_costs.edit', '可以编辑店铺费用记录', 'shop_costs'),
('删除店铺费用', 'shop_costs.delete', '可以删除店铺费用记录', 'shop_costs'),
('导入店铺费用', 'shop_costs.import', '可以导入店铺费用数据', 'shop_costs'),
('导出店铺费用', 'shop_costs.export', '可以导出店铺费用数据', 'shop_costs'),
('查看店铺费用统计', 'shop_costs.stats', '可以查看店铺费用统计', 'shop_costs'),

-- 订单其他费用权限（新增模块）
('查看订单其他费用', 'order_other_costs.view', '可以查看订单其他费用列表', 'order_other_costs'),
('创建订单其他费用', 'order_other_costs.create', '可以创建订单其他费用记录', 'order_other_costs'),
('编辑订单其他费用', 'order_other_costs.edit', '可以编辑订单其他费用记录', 'order_other_costs'),
('删除订单其他费用', 'order_other_costs.delete', '可以删除订单其他费用记录', 'order_other_costs'),
('导入订单其他费用', 'order_other_costs.import', '可以导入订单其他费用数据', 'order_other_costs'),
('导出订单其他费用', 'order_other_costs.export', '可以导出订单其他费用数据', 'order_other_costs'),
('查看订单其他费用统计', 'order_other_costs.stats', '可以查看订单其他费用统计', 'order_other_costs');

-- 3. 给角色分配权限

-- 管理员角色 (role_id=1) - 拥有所有权限（与init.sql一致）
INSERT INTO role_permissions (role_id, permission_id) 
SELECT 1, id FROM permissions;

-- 编辑角色 (role_id=2) - 拥有内容管理权限
INSERT INTO role_permissions (role_id, permission_id) VALUES
-- 用户管理
(2, (SELECT id FROM permissions WHERE slug = 'users.view')),

-- 数据管理
(2, (SELECT id FROM permissions WHERE slug = 'data.view')),
(2, (SELECT id FROM permissions WHERE slug = 'data.create')),
(2, (SELECT id FROM permissions WHERE slug = 'data.edit')),
(2, (SELECT id FROM permissions WHERE slug = 'data.delete')),

-- 商品管理
(2, (SELECT id FROM permissions WHERE slug = 'products.view')),
(2, (SELECT id FROM permissions WHERE slug = 'products.create')),
(2, (SELECT id FROM permissions WHERE slug = 'products.edit')),
(2, (SELECT id FROM permissions WHERE slug = 'products.delete')),
(2, (SELECT id FROM permissions WHERE slug = 'products.import')),
(2, (SELECT id FROM permissions WHERE slug = 'products.export')),

-- 订单利润管理
(2, (SELECT id FROM permissions WHERE slug = 'order_profit.view')),
(2, (SELECT id FROM permissions WHERE slug = 'order_profit.stats')),

-- 店铺管理
(2, (SELECT id FROM permissions WHERE slug = 'store.view')),
(2, (SELECT id FROM permissions WHERE slug = 'store.create')),
(2, (SELECT id FROM permissions WHERE slug = 'store.edit')),
(2, (SELECT id FROM permissions WHERE slug = 'store.delete')),

-- 仓库管理
(2, (SELECT id FROM permissions WHERE slug = 'warehouses.view')),
(2, (SELECT id FROM permissions WHERE slug = 'warehouses.create')),
(2, (SELECT id FROM permissions WHERE slug = 'warehouses.edit')),
(2, (SELECT id FROM permissions WHERE slug = 'warehouses.delete')),
(2, (SELECT id FROM permissions WHERE slug = 'warehouses.import')),
(2, (SELECT id FROM permissions WHERE slug = 'warehouses.export')),

-- 库存明细管理
(2, (SELECT id FROM permissions WHERE slug = 'inventory_details.view')),
(2, (SELECT id FROM permissions WHERE slug = 'inventory_details.create')),
(2, (SELECT id FROM permissions WHERE slug = 'inventory_details.edit')),
(2, (SELECT id FROM permissions WHERE slug = 'inventory_details.delete')),
(2, (SELECT id FROM permissions WHERE slug = 'inventory_details.import')),
(2, (SELECT id FROM permissions WHERE slug = 'inventory_details.export')),

-- FBA库存明细管理
(2, (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.view')),
(2, (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.delete')),
(2, (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.import')),

-- 新产品管理
(2, (SELECT id FROM permissions WHERE slug = 'new_products.view')),
(2, (SELECT id FROM permissions WHERE slug = 'new_products.create')),
(2, (SELECT id FROM permissions WHERE slug = 'new_products.edit')),
(2, (SELECT id FROM permissions WHERE slug = 'new_products.delete')),

-- 运费查询
(2, (SELECT id FROM permissions WHERE slug = 'query.view')),

-- 新增模块权限
-- 公司运营费用
(2, (SELECT id FROM permissions WHERE slug = 'company_costs.view')),
(2, (SELECT id FROM permissions WHERE slug = 'company_costs.create')),
(2, (SELECT id FROM permissions WHERE slug = 'company_costs.edit')),
(2, (SELECT id FROM permissions WHERE slug = 'company_costs.delete')),
(2, (SELECT id FROM permissions WHERE slug = 'company_costs.import')),
(2, (SELECT id FROM permissions WHERE slug = 'company_costs.export')),
(2, (SELECT id FROM permissions WHERE slug = 'company_costs.stats')),

-- 店铺费用
(2, (SELECT id FROM permissions WHERE slug = 'shop_costs.view')),
(2, (SELECT id FROM permissions WHERE slug = 'shop_costs.create')),
(2, (SELECT id FROM permissions WHERE slug = 'shop_costs.edit')),
(2, (SELECT id FROM permissions WHERE slug = 'shop_costs.delete')),
(2, (SELECT id FROM permissions WHERE slug = 'shop_costs.import')),
(2, (SELECT id FROM permissions WHERE slug = 'shop_costs.export')),
(2, (SELECT id FROM permissions WHERE slug = 'shop_costs.stats')),

-- 订单其他费用
(2, (SELECT id FROM permissions WHERE slug = 'order_other_costs.view')),
(2, (SELECT id FROM permissions WHERE slug = 'order_other_costs.create')),
(2, (SELECT id FROM permissions WHERE slug = 'order_other_costs.edit')),
(2, (SELECT id FROM permissions WHERE slug = 'order_other_costs.delete')),
(2, (SELECT id FROM permissions WHERE slug = 'order_other_costs.import')),
(2, (SELECT id FROM permissions WHERE slug = 'order_other_costs.export')),
(2, (SELECT id FROM permissions WHERE slug = 'order_other_costs.stats'));

-- 查看者角色 (role_id=3) - 仅拥有查看权限
INSERT INTO role_permissions (role_id, permission_id) VALUES
-- 基础查看权限
(3, (SELECT id FROM permissions WHERE slug = 'users.view')),
(3, (SELECT id FROM permissions WHERE slug = 'roles.view')),
(3, (SELECT id FROM permissions WHERE slug = 'permissions.view')),
(3, (SELECT id FROM permissions WHERE slug = 'data.view')),
(3, (SELECT id FROM permissions WHERE slug = 'products.view')),
(3, (SELECT id FROM permissions WHERE slug = 'order_profit.view')),
(3, (SELECT id FROM permissions WHERE slug = 'order_profit.stats')),
(3, (SELECT id FROM permissions WHERE slug = 'store.view')),
(3, (SELECT id FROM permissions WHERE slug = 'warehouses.view')),
(3, (SELECT id FROM permissions WHERE slug = 'inventory_details.view')),
(3, (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.view')),
(3, (SELECT id FROM permissions WHERE slug = 'new_products.view')),
(3, (SELECT id FROM permissions WHERE slug = 'query.view')),

-- 新增模块查看权限
(3, (SELECT id FROM permissions WHERE slug = 'company_costs.view')),
(3, (SELECT id FROM permissions WHERE slug = 'company_costs.stats')),
(3, (SELECT id FROM permissions WHERE slug = 'shop_costs.view')),
(3, (SELECT id FROM permissions WHERE slug = 'shop_costs.stats')),
(3, (SELECT id FROM permissions WHERE slug = 'order_other_costs.view')),
(3, (SELECT id FROM permissions WHERE slug = 'order_other_costs.stats'));

-- 4. 验证权限配置

-- 显示所有权限
SELECT id, name, slug, description, module 
FROM permissions 
ORDER BY module, name;

-- 显示角色权限分配情况
SELECT r.name as role_name, COUNT(rp.permission_id) as permission_count
FROM roles r
LEFT JOIN role_permissions rp ON r.id = rp.role_id
GROUP BY r.id, r.name;

-- 确认管理员拥有所有权限
SELECT '管理员拥有的权限数量:', COUNT(rp.permission_id) as admin_permissions
FROM role_permissions rp
WHERE rp.role_id = 1;

SELECT '系统总权限数量:', COUNT(*) as total_permissions
FROM permissions;

-- 显示管理员角色的具体权限
SELECT p.name as permission_name, p.module, p.slug 
FROM role_permissions rp
JOIN permissions p ON rp.permission_id = p.id
WHERE rp.role_id = 1
ORDER BY p.module, p.name;

-- 权限更新完成
SELECT '全面权限更新完成！管理员拥有所有权限。' as message;