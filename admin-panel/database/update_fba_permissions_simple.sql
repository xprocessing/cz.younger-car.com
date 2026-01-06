-- Simple FBA inventory permissions update script
-- This version uses minimal Chinese characters to avoid encoding issues

USE cz_data;

-- Step 1: Get the permission ID for inventory_details_fba.view
SET @perm_view_id = (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.view');
SET @perm_delete_id = (SELECT id FROM permissions WHERE slug = 'inventory_details_fba.delete');

-- Step 2: Add permissions to editor role (ID=2)
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(2, @perm_view_id),
(2, @perm_delete_id);

-- Step 3: Add view permission to viewer role (ID=3)
INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES
(3, @perm_view_id);

-- Step 4: Ensure admin role (ID=1) has all permissions
-- This will reassign all permissions to admin role
DELETE FROM role_permissions WHERE role_id = 1;
INSERT INTO role_permissions (role_id, permission_id) SELECT 1, id FROM permissions;

-- Step 5: Verify the changes
SELECT 'Editor role permissions:' as verification;
SELECT p.slug, p.name FROM permissions p
JOIN role_permissions rp ON p.id = rp.permission_id
WHERE rp.role_id = 2 AND p.module = 'inventory_details_fba';

SELECT 'Viewer role permissions:' as verification;
SELECT p.slug, p.name FROM permissions p
JOIN role_permissions rp ON p.id = rp.permission_id
WHERE rp.role_id = 3 AND p.module = 'inventory_details_fba';

SELECT 'Admin role permissions count:' as verification;
SELECT COUNT(*) as permission_count FROM role_permissions WHERE role_id = 1;