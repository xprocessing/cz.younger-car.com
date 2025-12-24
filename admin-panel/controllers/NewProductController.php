<?php
require_once APP_ROOT . '/models/NewProduct.php';
require_once APP_ROOT . '/helpers/functions.php';

class NewProductController {
    private $newProductModel;
    
    public function __construct() {
        $this->newProductModel = new NewProduct();
        session_start();
    }
    
    // 显示新产品列表
    public function index() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $products = $this->newProductModel->getAll();
        $title = '新产品管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/new_products/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建新产品页面
    public function create() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $title = '创建新产品';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/new_products/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建新产品请求
    public function createPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/new_products.php');
        }
        
        // 验证必填字段
        if (empty($_POST['require_no'])) {
            showError('需求编号不能为空');
            redirect(APP_URL . '/new_products.php?action=create');
        }
        
        // 检查需求编号是否已存在
        $existingProduct = $this->newProductModel->getByRequireNo($_POST['require_no']);
        if ($existingProduct) {
            showError('需求编号已存在');
            redirect(APP_URL . '/new_products.php?action=create');
        }
        
        $data = [
            'require_no' => $_POST['require_no'],
            'img_url' => $_POST['img_url'] ?? '',
            'require_title' => $_POST['require_title'] ?? '',
            'npdId' => $_POST['npdId'] ?? '',
            'sku' => $_POST['sku'] ?? '',
            'remark' => $_POST['remark'] ?? '',
            'create_time' => $_POST['create_time'] ?? date('Y-m-d'),
            'current_step' => isset($_POST['current_step']) ? (int)$_POST['current_step'] : 0
        ];
        
        // 处理进度明细
        if (!empty($_POST['process_list'])) {
            try {
                $processList = json_decode($_POST['process_list'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['process_list'] = $processList;
                } else {
                    $data['process_list'] = [['step' => 1, 'name' => '初始化', 'status' => 'pending', 'description' => '']];
                }
            } catch (Exception $e) {
                $data['process_list'] = [['step' => 1, 'name' => '初始化', 'status' => 'pending', 'description' => '']];
            }
        }
        
        if ($this->newProductModel->create($data)) {
            showSuccess('新产品创建成功');
        } else {
            showError('新产品创建失败');
        }
        
        redirect(APP_URL . '/new_products.php');
    }
    
    // 显示编辑新产品页面
    public function edit() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/new_products.php');
        }
        
        $product = $this->newProductModel->getById($id);
        if (!$product) {
            showError('新产品不存在');
            redirect(APP_URL . '/new_products.php');
        }
        
        $title = '编辑新产品';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/new_products/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑新产品请求
    public function editPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/new_products.php');
        }
        
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/new_products.php');
        }
        
        // 验证必填字段
        if (empty($_POST['require_no'])) {
            showError('需求编号不能为空');
            redirect(APP_URL . '/new_products.php?action=edit&id=' . $id);
        }
        
        // 检查需求编号是否已存在（排除当前记录）
        $existingProduct = $this->newProductModel->getByRequireNo($_POST['require_no']);
        if ($existingProduct && $existingProduct['id'] != $id) {
            showError('需求编号已存在');
            redirect(APP_URL . '/new_products.php?action=edit&id=' . $id);
        }
        
        $data = [
            'require_no' => $_POST['require_no'],
            'img_url' => $_POST['img_url'] ?? '',
            'require_title' => $_POST['require_title'] ?? '',
            'npdId' => $_POST['npdId'] ?? '',
            'sku' => $_POST['sku'] ?? '',
            'remark' => $_POST['remark'] ?? '',
            'create_time' => $_POST['create_time'] ?? date('Y-m-d'),
            'current_step' => isset($_POST['current_step']) ? (int)$_POST['current_step'] : 0
        ];
        
        // 处理进度明细
        if (!empty($_POST['process_list'])) {
            try {
                $processList = json_decode($_POST['process_list'], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data['process_list'] = $processList;
                }
            } catch (Exception $e) {
                // 保持原样，不更新process_list
            }
        }
        
        if ($this->newProductModel->update($id, $data)) {
            showSuccess('新产品更新成功');
        } else {
            showError('新产品更新失败');
        }
        
        redirect(APP_URL . '/new_products.php');
    }
    
    // 删除新产品
    public function delete() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/new_products.php');
        }
        
        $product = $this->newProductModel->getById($id);
        if (!$product) {
            showError('新产品不存在');
            redirect(APP_URL . '/new_products.php');
        }
        
        if ($this->newProductModel->delete($id)) {
            showSuccess('新产品删除成功');
        } else {
            showError('新产品删除失败');
        }
        
        redirect(APP_URL . '/new_products.php');
    }
    
    // 搜索新产品
    public function search() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $keyword = $_GET['keyword'] ?? '';
        $products = $this->newProductModel->search($keyword);
        $title = '搜索结果: ' . $keyword;
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/new_products/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
}