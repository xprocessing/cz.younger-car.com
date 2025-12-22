<?php
require_once APP_ROOT . '/includes/database.php';

class Role {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取角色
    public function getById($id) {
        $sql = "SELECT * FROM roles WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 根据名称获取角色
    public function getByName($name) {
        $sql = "SELECT * FROM roles WHERE name = ?";
        $stmt = $this->db->query($sql, [$name]);
        return $stmt->fetch();
    }
    
    // 获取所有角色
    public function getAll() {
        $sql = "SELECT * FROM roles ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // 创建角色
    public function create($data) {
        $sql = "INSERT INTO roles (name, description) VALUES (?, ?)";
        $params = [
            $data['name'],
            $data['description'] ?? ''
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // 更新角色
    public function update($id, $data) {
        $sql = "UPDATE roles SET name = ?, description = ? WHERE id = ?";
        $params = [
            $data['name'],
            $data['description'] ?? '',
            $id
        ];
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
    
    // 删除角色
    public function delete($id) {
        $sql = "DELETE FROM roles WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }
    
    // 检查用户是否有指定角色
    public function checkUserRole($userId, $roleName) {
        $sql = "SELECT COUNT(*) as count FROM user_roles ur 
                JOIN roles r ON ur.role_id = r.id 
                WHERE ur.user_id = ? AND r.name = ?";
        $stmt = $this->db->query($sql, [$userId, $roleName]);
        $result = $stmt->fetch();
        return $result['count'] > 0;
    }
    
    // 获取角色权限
    public function getPermissions($roleId) {
        $sql = "SELECT p.* FROM permissions p 
                JOIN role_permissions rp ON p.id = rp.permission_id 
                WHERE rp.role_id = ?";
        $stmt = $this->db->query($sql, [$roleId]);
        return $stmt->fetchAll();
    }
    
    // 给角色分配权限
    public function assignPermission($roleId, $permissionId) {
        $sql = "INSERT IGNORE INTO role_permissions (role_id, permission_id) VALUES (?, ?)";
        return $this->db->query($sql, [$roleId, $permissionId])->rowCount() > 0;
    }
    
    // 移除角色权限
    public function removePermission($roleId, $permissionId) {
        $sql = "DELETE FROM role_permissions WHERE role_id = ? AND permission_id = ?";
        return $this->db->query($sql, [$roleId, $permissionId])->rowCount() > 0;
    }
    
    // 更新角色权限
    public function updatePermissions($roleId, $permissionIds) {
        // 先移除所有权限
        $sql = "DELETE FROM role_permissions WHERE role_id = ?";
        $this->db->query($sql, [$roleId]);
        
        // 再添加新权限
        $success = true;
        foreach ($permissionIds as $permissionId) {
            if (!$this->assignPermission($roleId, $permissionId)) {
                $success = false;
            }
        }
        
        return $success;
    }
}
?>