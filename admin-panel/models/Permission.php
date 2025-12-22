<?php
require_once APP_ROOT . '/includes/database.php';

class Permission {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取权限
    public function getById($id) {
        $sql = "SELECT * FROM permissions WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 根据slug获取权限
    public function getBySlug($slug) {
        $sql = "SELECT * FROM permissions WHERE slug = ?";
        $stmt = $this->db->query($sql, [$slug]);
        return $stmt->fetch();
    }
    
    // 获取所有权限
    public function getAll() {
        $sql = "SELECT * FROM permissions ORDER BY module, id";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // 按模块分组获取权限
    public function getAllByModule() {
        $permissions = $this->getAll();
        $grouped = [];
        
        foreach ($permissions as $permission) {
            $module = $permission['module'];
            if (!isset($grouped[$module])) {
                $grouped[$module] = [];
            }
            $grouped[$module][] = $permission;
        }
        
        return $grouped;
    }
    
    // 创建权限
    public function create($data) {
        $sql = "INSERT INTO permissions (name, slug, description, module) VALUES (?, ?, ?, ?)";
        $params = [
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['module']
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // 更新权限
    public function update($id, $data) {
        $sql = "UPDATE permissions SET name = ?, slug = ?, description = ?, module = ? WHERE id = ?";
        $params = [
            $data['name'],
            $data['slug'],
            $data['description'] ?? '',
            $data['module'],
            $id
        ];
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
    
    // 删除权限
    public function delete($id) {
        $sql = "DELETE FROM permissions WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }
    
    // 检查用户是否有指定权限
    public function checkUserPermission($userId, $permissionSlug) {
        $sql = "SELECT COUNT(*) as count FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                JOIN user_roles ur ON rp.role_id = ur.role_id 
                WHERE ur.user_id = ? AND p.slug = ?";
        $stmt = $this->db->query($sql, [$userId, $permissionSlug]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    // 获取用户所有权限
    public function getUserPermissions($userId) {
        $sql = "SELECT DISTINCT p.* FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                JOIN user_roles ur ON rp.role_id = ur.role_id 
                WHERE ur.user_id = ? ORDER BY p.module, p.id";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetchAll();
    }
    
    // 获取用户权限slug列表
    public function getUserPermissionSlugs($userId) {
        $permissions = $this->getUserPermissions($userId);
        $slugs = [];
        
        foreach ($permissions as $permission) {
            $slugs[] = $permission['slug'];
        }
        
        return $slugs;
    }
}
?>