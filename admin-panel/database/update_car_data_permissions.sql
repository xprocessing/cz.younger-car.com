-- 车型数据管理权限更新脚本

-- 步骤1: 插入车型数据管理权限
INSERT IGNORE INTO permissions (name, slug, description, module) VALUES
('查看车型数据', 'car_data.view', '可以查看车型数据列表', 'car_data'),
('创建车型数据', 'car_data.create', '可以创建新的车型数据', 'car_data'),
('编辑车型数据', 'car_data.edit', '可以编辑现有车型数据', 'car_data'),
('删除车型数据', 'car_data.delete', '可以删除车型数据', 'car_data');

-- 步骤2: 获取新创建的权限ID
SET @car_data_view_id = (SELECT id FROM permissions WHERE slug = 'car_data.view');
SET @car_data_create_id = (SELECT id FROM permissions WHERE slug = 'car_data.create');
SET @car_data_edit_id = (SELECT id FROM permissions WHERE slug = 'car_data.edit');
SET @car_data_delete_id = (SELECT id FROM permissions WHERE slug = 'car_data.delete');

-- 步骤3: 将权限分配给管理员角色 (role_id = 1)
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(1, @car_data_view_id),
(1, @car_data_create_id),
(1, @car_data_edit_id),
(1, @car_data_delete_id);

-- 步骤4: 可选择将权限分配给其他角色
-- 例如分配给编辑角色 (role_id = 2)
-- INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
-- (2, @car_data_view_id),
-- (2, @car_data_create_id),
-- (2, @car_data_edit_id);

-- 步骤5: 验证权限创建
SELECT id, name, slug, module FROM permissions WHERE module = 'car_data' ORDER BY id;

-- 步骤6: 验证角色权限分配
SELECT r.name as role_name, p.name as permission_name, p.slug as permission_slug 
FROM role_permissions rp 
JOIN roles r ON rp.role_id = r.id 
JOIN permissions p ON rp.permission_id = p.id 
WHERE p.module = 'car_data' 
ORDER BY r.name, p.name;