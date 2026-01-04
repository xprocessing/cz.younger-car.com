-- 库存明细权限更新脚本
-- 执行此脚本以添加库存明细权限

-- 插入库存明细权限
INSERT IGNORE INTO permissions (name, slug, description, module) VALUES
('查看库存明细', 'inventory_details.view', '可以查看库存明细列表', 'inventory_details'),
('创建库存明细', 'inventory_details.create', '可以创建库存明细', 'inventory_details'),
('编辑库存明细', 'inventory_details.edit', '可以编辑库存明细', 'inventory_details'),
('删除库存明细', 'inventory_details.delete', '可以删除库存明细', 'inventory_details');

-- 为编辑角色添加库存明细权限
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(2, 44), -- 查看库存明细
(2, 45), -- 创建库存明细
(2, 46), -- 编辑库存明细
(2, 47); -- 删除库存明细

-- 为查看者角色添加库存明细查看权限
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(3, 44); -- 查看库存明细
