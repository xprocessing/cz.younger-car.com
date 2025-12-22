<?php
require_once APP_ROOT . '/models/Role.php';
require_once APP_ROOT . '/models/Permission.php';
require_once APP_ROOT . '/helpers/functions.php';

class RoleController {
    private $roleModel;
    private $permissionModel;
    
    public function __construct() {
        $this->roleModel = new Role();
        $this->permissionModel = new Permission();
        session_start();
    }
    
    // 显示角色列表
    public function index() {
        if (!hasPermission('roles.view')) {
            showError('您没有权限访问此页面');
            redirect(APP_URL . '/dashboard.php');
        }
        
        $roles = $this->roleModel->getAll();
        $title = '角色管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/roles/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建角色页面
    public function create() {
        if (!hasPermission('roles.create')) {
            showError('您没有权限创建角色');
            redirect(APP_URL . '/roles.php');
        }
        
        $permissions = $this->permissionModel->getAllByModule();
        $title = '创建角色';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/roles/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建角色请求
    public function createPost() {
        if (!hasPermission('roles.create')) {
            showError('您没有权限创建角色');
            redirect(APP_URL . '/roles.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/roles.php');
        }
        
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $permissionIds = $_POST['permissions'] ?? [];
        
        if (empty($name)) {
            showError('角色名称不能为空');
            redirect(APP_URL . '/roles.php?action=create');
        }
        
        // 检查角色名称是否已存在
        if ($this->roleModel->getByName($name)) {
            showError('角色名称已存在');
            redirect(APP_URL . '/roles.php?action=create');
        }
        
        // 创建角色
        $roleId = $this->roleModel->create([
            'name' => $name,
            'description' => $description
        ]);
        
        // 分配权限
        if (!empty($permissionIds)) {
            $this->roleModel->updatePermissions($roleId, $permissionIds);
        }
        
        showSuccess('角色创建成功');
        redirect(APP_URL . '/roles.php');
    }
    
    // 显示编辑角色页面
    public function edit($id) {
        if (!hasPermission('roles.edit')) {
            showError('您没有权限编辑角色');
            redirect(APP_URL . '/roles.php');
        }
        
        $role = $this->roleModel->getById($id);
        if (!$role) {
            showError('角色不存在');
            redirect(APP_URL . '/roles.php');
        }
        
        $permissions = $this->permissionModel->getAllByModule();
        $rolePermissions = $this->roleModel->getPermissions($id);
        $rolePermissionIds = array_column($rolePermissions, 'id');
        
        $title = '编辑角色';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/roles/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑角色请求
    public function editPost($id) {
        if (!hasPermission('roles.edit')) {
            showError('您没有权限编辑角色');
            redirect(APP_URL . '/roles.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/roles.php');
        }
        
        $role = $this->roleModel->getById($id);
        if (!$role) {
            showError('角色不存在');
            redirect(APP_URL . '/roles.php');
        }
        
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $permissionIds = $_POST['permissions'] ?? [];
        
        if (empty($name)) {
            showError('角色名称不能为空');
            redirect(APP_URL . '/roles.php?action=edit&id=' . $id);
        }
        
        // 检查角色名称是否已存在（排除当前角色）
        $existingRole = $this->roleModel->getByName($name);
        if ($existingRole && $existingRole['id'] != $id) {
            showError('角色名称已存在');
            redirect(APP_URL . '/roles.php?action=edit&id=' . $id);
        }
        
        // 更新角色
        $this->roleModel->update($id, [
            'name' => $name,
            'description' => $description
        ]);
        
        // 更新权限
        $this->roleModel->updatePermissions($id, $permissionIds);
        
        showSuccess('角色更新成功');
        redirect(APP_URL . '/roles.php');
    }
    
    // 处理删除角色请求
    public function delete($id) {
        if (!hasPermission('roles.delete')) {
            showError('您没有权限删除角色');
            redirect(APP_URL . '/roles.php');
        }
        
        $role = $this->roleModel->getById($id);
        if (!$role) {
            showError('角色不存在');
            redirect(APP_URL . '/roles.php');
        }
        
        // 不允许删除admin角色
        if ($role['name'] === 'admin') {
            showError('不允许删除管理员角色');
            redirect(APP_URL . '/roles.php');
        }
        
        $this->roleModel->delete($id);
        showSuccess('角色删除成功');
        redirect(APP_URL . '/roles.php');
    }
}
?>