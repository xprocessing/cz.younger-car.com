-- 仓库管理权限更新脚本
-- 执行此脚本以添加仓库管理权限

-- 插入仓库管理权限
INSERT IGNORE INTO permissions (name, slug, description, module) VALUES
('查看仓库', 'warehouses.view', '可以查看仓库列表', 'warehouses'),
('创建仓库', 'warehouses.create', '可以创建新仓库', 'warehouses'),
('编辑仓库', 'warehouses.edit', '可以编辑现有仓库', 'warehouses'),
('删除仓库', 'warehouses.delete', '可以删除仓库', 'warehouses');

-- 为编辑角色添加仓库管理权限
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(2, 35), -- 查看仓库
(2, 36), -- 创建仓库
(2, 37), -- 编辑仓库
(2, 38); -- 删除仓库

-- 为查看者角色添加仓库查看权限
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(3, 35); -- 查看仓库
