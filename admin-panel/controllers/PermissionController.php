<?php
require_once ADMIN_PANEL_DIR . '/models/Permission.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

class PermissionController {
    private $permissionModel;
    
    public function __construct() {
        $this->permissionModel = new Permission();
        session_start();
    }
    
    // 显示权限列表
    public function index() {
        if (!hasPermission('permissions.view')) {
            showError('您没有权限访问此页面');
            redirect(APP_URL . '/dashboard.php');
        }
        
        $permissions = $this->permissionModel->getAllByModule();
        $title = '权限管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/permissions/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建权限页面
    public function create() {
        if (!hasPermission('permissions.create')) {
            showError('您没有权限创建权限');
            redirect(APP_URL . '/permissions.php');
        }
        
        $title = '创建权限';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/permissions/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建权限请求
    public function createPost() {
        if (!hasPermission('permissions.create')) {
            showError('您没有权限创建权限');
            redirect(APP_URL . '/permissions.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/permissions.php');
        }
        
        $name = $_POST['name'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $description = $_POST['description'] ?? '';
        $module = $_POST['module'] ?? '';
        $customModule = $_POST['custom_module'] ?? '';
        
        // 优先使用自定义模块
        if (!empty($customModule)) {
            $module = $customModule;
        }
        
        if (empty($name) || empty($slug) || empty($module)) {
            showError('权限名称、标识和模块不能为空');
            redirect(APP_URL . '/permissions.php?action=create');
        }
        
        // 检查权限slug是否已存在
        if ($this->permissionModel->getBySlug($slug)) {
            showError('权限标识已存在');
            redirect(APP_URL . '/permissions.php?action=create');
        }
        
        // 创建权限
        $this->permissionModel->create([
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'module' => $module
        ]);
        
        showSuccess('权限创建成功');
        redirect(APP_URL . '/permissions.php');
    }
    
    // 显示编辑权限页面
    public function edit($id) {
        if (!hasPermission('permissions.edit')) {
            showError('您没有权限编辑权限');
            redirect(APP_URL . '/permissions.php');
        }
        
        $permission = $this->permissionModel->getById($id);
        if (!$permission) {
            showError('权限不存在');
            redirect(APP_URL . '/permissions.php');
        }
        
        $title = '编辑权限';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/permissions/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑权限请求
    public function editPost($id) {
        if (!hasPermission('permissions.edit')) {
            showError('您没有权限编辑权限');
            redirect(APP_URL . '/permissions.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/permissions.php');
        }
        
        $permission = $this->permissionModel->getById($id);
        if (!$permission) {
            showError('权限不存在');
            redirect(APP_URL . '/permissions.php');
        }
        
        $name = $_POST['name'] ?? '';
        $slug = $_POST['slug'] ?? '';
        $description = $_POST['description'] ?? '';
        $module = $_POST['module'] ?? '';
        $customModule = $_POST['custom_module'] ?? '';
        
        // 优先使用自定义模块
        if (!empty($customModule)) {
            $module = $customModule;
        }
        
        if (empty($name) || empty($slug) || empty($module)) {
            showError('权限名称、标识和模块不能为空');
            redirect(APP_URL . '/permissions.php?action=edit&id=' . $id);
        }
        
        // 检查权限slug是否已存在（排除当前权限）
        $existingPermission = $this->permissionModel->getBySlug($slug);
        if ($existingPermission && $existingPermission['id'] != $id) {
            showError('权限标识已存在');
            redirect(APP_URL . '/permissions.php?action=edit&id=' . $id);
        }
        
        // 更新权限
        $this->permissionModel->update($id, [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'module' => $module
        ]);
        
        showSuccess('权限更新成功');
        redirect(APP_URL . '/permissions.php');
    }
    
    // 处理删除权限请求
    public function delete($id) {
        if (!hasPermission('permissions.delete')) {
            showError('您没有权限删除权限');
            redirect(APP_URL . '/permissions.php');
        }
        
        $permission = $this->permissionModel->getById($id);
        if (!$permission) {
            showError('权限不存在');
            redirect(APP_URL . '/permissions.php');
        }
        
        $this->permissionModel->delete($id);
        showSuccess('权限删除成功');
        redirect(APP_URL . '/permissions.php');
    }
}
?>