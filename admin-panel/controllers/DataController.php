<?php
require_once APP_ROOT . '/models/Product.php';
require_once APP_ROOT . '/helpers/functions.php';

class DataController {
    private $productModel;
    
    public function __construct() {
        $this->productModel = new Product();
        session_start();
    }
    
    // 显示产品列表
    public function index() {
        if (!hasPermission('data.view')) {
            showError('您没有权限访问此页面');
            redirect(APP_URL . '/dashboard.php');
        }
        
        // 获取过滤条件
        $filters = [];
        if (isset($_GET['search'])) {
            $filters['search'] = $_GET['search'];
        }
        if (isset($_GET['category'])) {
            $filters['category'] = $_GET['category'];
        }
        if (isset($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        
        $products = $this->productModel->getAll($filters);
        $categories = $this->productModel->getAllCategories();
        $title = '数据管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/data/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建产品页面
    public function create() {
        if (!hasPermission('data.create')) {
            showError('您没有权限创建数据');
            redirect(APP_URL . '/data.php');
        }
        
        $categories = $this->productModel->getAllCategories();
        $title = '创建产品';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/data/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建产品请求
    public function createPost() {
        if (!hasPermission('data.create')) {
            showError('您没有权限创建数据');
            redirect(APP_URL . '/data.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/data.php');
        }
        
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';
        $category = $_POST['category'] ?? '';
        $status = $_POST['status'] ?? 'active';
        
        if (empty($name) || empty($price) || empty($category)) {
            showError('产品名称、价格和分类不能为空');
            redirect(APP_URL . '/data.php?action=create');
        }
        
        // 创建产品
        $this->productModel->create([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category' => $category,
            'status' => $status
        ]);
        
        showSuccess('产品创建成功');
        redirect(APP_URL . '/data.php');
    }
    
    // 显示编辑产品页面
    public function edit($id) {
        if (!hasPermission('data.edit')) {
            showError('您没有权限编辑数据');
            redirect(APP_URL . '/data.php');
        }
        
        $product = $this->productModel->getById($id);
        if (!$product) {
            showError('产品不存在');
            redirect(APP_URL . '/data.php');
        }
        
        $categories = $this->productModel->getAllCategories();
        $title = '编辑产品';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/data/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑产品请求
    public function editPost($id) {
        if (!hasPermission('data.edit')) {
            showError('您没有权限编辑数据');
            redirect(APP_URL . '/data.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/data.php');
        }
        
        $product = $this->productModel->getById($id);
        if (!$product) {
            showError('产品不存在');
            redirect(APP_URL . '/data.php');
        }
        
        $name = $_POST['name'] ?? '';
        $description = $_POST['description'] ?? '';
        $price = $_POST['price'] ?? '';
        $category = $_POST['category'] ?? '';
        $status = $_POST['status'] ?? 'active';
        
        if (empty($name) || empty($price) || empty($category)) {
            showError('产品名称、价格和分类不能为空');
            redirect(APP_URL . '/data.php?action=edit&id=' . $id);
        }
        
        // 更新产品
        $this->productModel->update($id, [
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'category' => $category,
            'status' => $status
        ]);
        
        showSuccess('产品更新成功');
        redirect(APP_URL . '/data.php');
    }
    
    // 处理删除产品请求
    public function delete($id) {
        if (!hasPermission('data.delete')) {
            showError('您没有权限删除数据');
            redirect(APP_URL . '/data.php');
        }
        
        $product = $this->productModel->getById($id);
        if (!$product) {
            showError('产品不存在');
            redirect(APP_URL . '/data.php');
        }
        
        $this->productModel->delete($id);
        showSuccess('产品删除成功');
        redirect(APP_URL . '/data.php');
    }
}
?>