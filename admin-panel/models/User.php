<?php
require_once APP_ROOT . '/includes/database.php';

class User {
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance();
    }
    
    // 根据ID获取用户
    public function getById($id) {
        $sql = "SELECT * FROM users WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 根据用户名获取用户
    public function getByUsername($username) {
        $sql = "SELECT * FROM users WHERE username = ?";
        $stmt = $this->db->query($sql, [$username]);
        return $stmt->fetch();
    }
    
    // 根据邮箱获取用户
    public function getByEmail($email) {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $this->db->query($sql, [$email]);
        return $stmt->fetch();
    }
    
    // 获取所有用户
    public function getAll() {
        $sql = "SELECT * FROM users ORDER BY id DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // 创建用户
    public function create($data) {
        $sql = "INSERT INTO users (username, email, password, full_name, status) VALUES (?, ?, ?, ?, ?)";
        $params = [
            $data['username'],
            $data['email'],
            $data['password'],
            $data['full_name'],
            $data['status'] ?? 'active'
        ];
        $this->db->query($sql, $params);
        return $this->db->lastInsertId();
    }
    
    // 更新用户
    public function update($id, $data) {
        $sql = "UPDATE users SET username = ?, email = ?, full_name = ?, status = ? WHERE id = ?";
        $params = [
            $data['username'],
            $data['email'],
            $data['full_name'],
            $data['status'],
            $id
        ];
        return $this->db->query($sql, $params)->rowCount() > 0;
    }
    
    // 更新密码
    public function updatePassword($id, $password) {
        $sql = "UPDATE users SET password = ? WHERE id = ?";
        return $this->db->query($sql, [$password, $id])->rowCount() > 0;
    }
    
    // 删除用户
    public function delete($id) {
        $sql = "DELETE FROM users WHERE id = ?";
        return $this->db->query($sql, [$id])->rowCount() > 0;
    }
    
    // 验证用户登录
    public function verifyLogin($username, $password) {
        $user = $this->getByUsername($username);
        if (!$user) {
            return false;
        }
        
        if ($user['status'] !== 'active') {
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            return $user;
        }
        
        return false;
    }
    
    // 获取用户角色
    public function getRoles($userId) {
        $sql = "SELECT r.* FROM roles r 
                JOIN user_roles ur ON r.id = ur.role_id 
                WHERE ur.user_id = ?";
        $stmt = $this->db->query($sql, [$userId]);
        return $stmt->fetchAll();
    }
    
    // 给用户分配角色
    public function assignRole($userId, $roleId) {
        $sql = "INSERT IGNORE INTO user_roles (user_id, role_id) VALUES (?, ?)";
        return $this->db->query($sql, [$userId, $roleId])->rowCount() > 0;
    }
    
    // 移除用户角色
    public function removeRole($userId, $roleId) {
        $sql = "DELETE FROM user_roles WHERE user_id = ? AND role_id = ?";
        return $this->db->query($sql, [$userId, $roleId])->rowCount() > 0;
    }
    
    // 更新用户角色
    public function updateRoles($userId, $roleIds) {
        // 先移除所有角色
        $sql = "DELETE FROM user_roles WHERE user_id = ?";
        $this->db->query($sql, [$userId]);
        
        // 再添加新角色
        $success = true;
        foreach ($roleIds as $roleId) {
            if (!$this->assignRole($userId, $roleId)) {
                $success = false;
            }
        }
        
        return $success;
    }
}
?>