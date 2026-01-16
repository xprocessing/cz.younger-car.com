<?php
require_once ADMIN_PANEL_DIR . '/models/User.php';
require_once ADMIN_PANEL_DIR . '/models/Role.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

class UserController {
    private $userModel;
    private $roleModel;
    
    public function __construct() {
        $this->userModel = new User();
        $this->roleModel = new Role();
        session_start();
    }
    
    // 显示用户列表
    public function index() {
        if (!hasPermission('users.view')) {
            showError('您没有权限访问此页面');
            redirect(APP_URL . '/dashboard.php');
        }
        
        $users = $this->userModel->getAll();
        $title = '用户管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/users/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建用户页面
    public function create() {
        if (!hasPermission('users.create')) {
            showError('您没有权限创建用户');
            redirect(APP_URL . '/users.php');
        }
        
        $roles = $this->roleModel->getAll();
        $title = '创建用户';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/users/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建用户请求
    public function createPost() {
        if (!hasPermission('users.create')) {
            showError('您没有权限创建用户');
            redirect(APP_URL . '/users.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/users.php');
        }
        
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $fullName = $_POST['full_name'] ?? '';
        $status = $_POST['status'] ?? 'active';
        $roleIds = $_POST['roles'] ?? [];
        
        if (empty($username) || empty($email) || empty($password) || empty($fullName)) {
            showError('用户名、邮箱、密码和姓名不能为空');
            redirect(APP_URL . '/users.php?action=create');
        }
        
        // 检查用户名是否已存在
        if ($this->userModel->getByUsername($username)) {
            showError('用户名已存在');
            redirect(APP_URL . '/users.php?action=create');
        }
        
        // 检查邮箱是否已存在
        if ($this->userModel->getByEmail($email)) {
            showError('邮箱已存在');
            redirect(APP_URL . '/users.php?action=create');
        }
        
        // 加密密码
        $hashedPassword = hashPassword($password);
        
        // 创建用户
        $userId = $this->userModel->create([
            'username' => $username,
            'email' => $email,
            'password' => $hashedPassword,
            'full_name' => $fullName,
            'status' => $status
        ]);
        
        // 分配角色
        if (!empty($roleIds)) {
            $this->userModel->updateRoles($userId, $roleIds);
        }
        
        showSuccess('用户创建成功');
        redirect(APP_URL . '/users.php');
    }
    
    // 显示编辑用户页面
    public function edit($id) {
        if (!hasPermission('users.edit')) {
            showError('您没有权限编辑用户');
            redirect(APP_URL . '/users.php');
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            showError('用户不存在');
            redirect(APP_URL . '/users.php');
        }
        
        $roles = $this->roleModel->getAll();
        $userRoles = $this->userModel->getRoles($id);
        $userRoleIds = array_column($userRoles, 'id');
        
        $title = '编辑用户';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/users/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑用户请求
    public function editPost($id) {
        if (!hasPermission('users.edit')) {
            showError('您没有权限编辑用户');
            redirect(APP_URL . '/users.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/users.php');
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            showError('用户不存在');
            redirect(APP_URL . '/users.php');
        }
        
        $username = $_POST['username'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $fullName = $_POST['full_name'] ?? '';
        $status = $_POST['status'] ?? 'active';
        $roleIds = $_POST['roles'] ?? [];
        
        if (empty($username) || empty($email) || empty($fullName)) {
            showError('用户名、邮箱和姓名不能为空');
            redirect(APP_URL . '/users.php?action=edit&id=' . $id);
        }
        
        // 检查用户名是否已存在（排除当前用户）
        $existingUser = $this->userModel->getByUsername($username);
        if ($existingUser && $existingUser['id'] != $id) {
            showError('用户名已存在');
            redirect(APP_URL . '/users.php?action=edit&id=' . $id);
        }
        
        // 检查邮箱是否已存在（排除当前用户）
        $existingEmail = $this->userModel->getByEmail($email);
        if ($existingEmail && $existingEmail['id'] != $id) {
            showError('邮箱已存在');
            redirect(APP_URL . '/users.php?action=edit&id=' . $id);
        }
        
        // 更新用户信息
        $updateData = [
            'username' => $username,
            'email' => $email,
            'full_name' => $fullName,
            'status' => $status
        ];
        
        $this->userModel->update($id, $updateData);
        
        // 如果提供了新密码，则更新密码
        if (!empty($password)) {
            $hashedPassword = hashPassword($password);
            $this->userModel->updatePassword($id, $hashedPassword);
        }
        
        // 更新角色
        if (!empty($roleIds)) {
            $this->userModel->updateRoles($id, $roleIds);
        }
        
        showSuccess('用户更新成功');
        redirect(APP_URL . '/users.php');
    }
    
    // 处理删除用户请求
    public function delete($id) {
        if (!hasPermission('users.delete')) {
            showError('您没有权限删除用户');
            redirect(APP_URL . '/users.php');
        }
        
        $user = $this->userModel->getById($id);
        if (!$user) {
            showError('用户不存在');
            redirect(APP_URL . '/users.php');
        }
        
        // 不允许删除当前登录用户
        if ($id == getCurrentUserId()) {
            showError('不允许删除当前登录用户');
            redirect(APP_URL . '/users.php');
        }
        
        $this->userModel->delete($id);
        showSuccess('用户删除成功');
        redirect(APP_URL . '/users.php');
    }
}
?>