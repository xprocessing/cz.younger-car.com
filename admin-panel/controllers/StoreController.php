<?php
require_once ADMIN_PANEL_DIR . '/models/Store.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

class StoreController {
    private $storeModel;
    
    public function __construct() {
        $this->storeModel = new Store();
        session_start();
    }
    
    // 显示店铺列表
    public function index() {
        if (!hasPermission('store.view')) {
            showError('您没有权限访问此页面');
            redirect(ADMIN_PANEL_URL . '/dashboard.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        
        if ($keyword) {
            $stores = $this->storeModel->search($keyword, $limit, $offset);
            $totalCount = $this->storeModel->getSearchCount($keyword);
        } else {
            $stores = $this->storeModel->getAll($limit, $offset);
            $totalCount = $this->storeModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $title = '店铺管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/store/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建店铺页面
    public function create() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $title = '创建店铺';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/store/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建店铺请求
    public function createPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/store.php');
        }
        
        // 验证必填字段
        if (empty($_POST['store_id'])) {
            showError('店铺ID不能为空');
            redirect(ADMIN_PANEL_URL . '/store.php?action=create');
        }
        
        if (empty($_POST['store_name'])) {
            showError('店铺名称不能为空');
            redirect(ADMIN_PANEL_URL . '/store.php?action=create');
        }
        
        if (empty($_POST['platform_code'])) {
            showError('平台编码不能为空');
            redirect(ADMIN_PANEL_URL . '/store.php?action=create');
        }
        
        if (empty($_POST['platform_name'])) {
            showError('平台名称不能为空');
            redirect(ADMIN_PANEL_URL . '/store.php?action=create');
        }
        
        if (empty($_POST['currency'])) {
            showError('货币类型不能为空');
            redirect(ADMIN_PANEL_URL . '/store.php?action=create');
        }
        
        // 检查店铺ID是否已存在
        $existingStore = $this->storeModel->getById($_POST['store_id']);
        if ($existingStore) {
            showError('店铺ID已存在');
            redirect(ADMIN_PANEL_URL . '/store.php?action=create');
        }
        
        $data = [
            'store_id' => $_POST['store_id'],
            'sid' => $_POST['sid'] ?? '',
            'store_name' => $_POST['store_name'],
            'platform_code' => $_POST['platform_code'],
            'platform_name' => $_POST['platform_name'],
            'currency' => $_POST['currency'],
            'is_sync' => (int)($_POST['is_sync'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1),
            'country_code' => $_POST['country_code'] ?? '',
            'store_manager_name' => $_POST['store_manager_name'] ?? '',
            'track_manager_name' => $_POST['track_manager_name'] ?? ''
        ];
        
        if ($this->storeModel->create($data)) {
            showSuccess('店铺创建成功');
        } else {
            showError('店铺创建失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/store.php');
    }
    
    // 显示编辑店铺页面
    public function edit() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $storeId = $_GET['id'] ?? '';
        if (empty($storeId)) {
            redirect(ADMIN_PANEL_URL . '/store.php');
        }
        
        $store = $this->storeModel->getById($storeId);
        if (!$store) {
            showError('店铺不存在');
            redirect(ADMIN_PANEL_URL . '/store.php');
        }
        
        $title = '编辑店铺';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/store/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑店铺请求
    public function editPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/store.php');
        }
        
        $storeId = $_POST['store_id'] ?? '';
        if (empty($storeId)) {
            redirect(ADMIN_PANEL_URL . '/store.php');
        }
        
        // 验证必填字段
        if (empty($_POST['store_name'])) {
            showError('店铺名称不能为空');
            redirect(ADMIN_PANEL_URL . '/store.php?action=edit&id=' . $storeId);
        }
        
        if (empty($_POST['platform_code'])) {
            showError('平台编码不能为空');
            redirect(ADMIN_PANEL_URL . '/store.php?action=edit&id=' . $storeId);
        }
        
        if (empty($_POST['platform_name'])) {
            showError('平台名称不能为空');
            redirect(ADMIN_PANEL_URL . '/store.php?action=edit&id=' . $storeId);
        }
        
        if (empty($_POST['currency'])) {
            showError('货币类型不能为空');
            redirect(ADMIN_PANEL_URL . '/store.php?action=edit&id=' . $storeId);
        }
        
        $data = [
            'sid' => $_POST['sid'] ?? '',
            'store_name' => $_POST['store_name'],
            'platform_code' => $_POST['platform_code'],
            'platform_name' => $_POST['platform_name'],
            'currency' => $_POST['currency'],
            'is_sync' => (int)($_POST['is_sync'] ?? 0),
            'status' => (int)($_POST['status'] ?? 1),
            'country_code' => $_POST['country_code'] ?? '',
            'store_manager_name' => $_POST['store_manager_name'] ?? '',
            'track_manager_name' => $_POST['track_manager_name'] ?? ''
        ];
        
        if ($this->storeModel->update($storeId, $data)) {
            showSuccess('店铺更新成功');
        } else {
            showError('店铺更新失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/store.php');
    }
    
    // 处理删除店铺请求
    public function delete() {
        if (!hasPermission('store.delete')) {
            showError('您没有权限删除店铺');
            redirect(ADMIN_PANEL_URL . '/store.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/store.php');
        }
        
        $storeId = $_POST['id'] ?? '';
        if (empty($storeId)) {
            redirect(ADMIN_PANEL_URL . '/store.php');
        }
        
        if ($this->storeModel->delete($storeId)) {
            showSuccess('店铺删除成功');
        } else {
            showError('店铺删除失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/store.php');
    }
}
