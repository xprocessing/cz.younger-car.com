-- FBA库存明细权限更新脚本
-- 执行此脚本以添加和更新FBA库存明细权限

-- 插入FBA库存明细权限（使用IGNORE避免重复插入）
INSERT IGNORE INTO permissions (name, slug, description, module) VALUES
('查看FBA库存明细', 'inventory_details_fba.view', '可以查看FBA库存明细列表', 'inventory_details_fba'),
('创建FBA库存明细', 'inventory_details_fba.create', '可以创建FBA库存明细', 'inventory_details_fba'),
('编辑FBA库存明细', 'inventory_details_fba.edit', '可以编辑FBA库存明细', 'inventory_details_fba'),
('删除FBA库存明细', 'inventory_details_fba.delete', '可以删除FBA库存明细', 'inventory_details_fba');

-- 获取FBA库存明细权限的ID
SET @view_perm_id = (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.view');
SET @create_perm_id = (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.create');
SET @edit_perm_id = (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.edit');
SET @delete_perm_id = (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.delete');

-- 为编辑角色添加FBA库存明细权限
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(2, @view_perm_id),  -- 查看FBA库存明细
(2, @create_perm_id),  -- 创建FBA库存明细
(2, @edit_perm_id),  -- 编辑FBA库存明细
(2, @delete_perm_id);  -- 删除FBA库存明细

-- 为查看者角色添加FBA库存明细查看权限
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(3, @view_perm_id);  -- 查看FBA库存明细

-- 验证权限设置
SELECT r.name AS role_name, p.name AS permission_name, p.slug AS permission_slug
FROM roles r
JOIN role_permissions rp ON r.id = rp.role_id
JOIN permissions p ON rp.permission_id = p.id
WHERE p.module = 'inventory_details_fba'
ORDER BY r.name, p.slug;