<?php
require_once APP_ROOT . '/models/Products.php';
require_once APP_ROOT . '/helpers/functions.php';

class ProductsController {
    private $productsModel;
    
    public function __construct() {
        $this->productsModel = new Products();
        session_start();
    }
    
    public function index() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        $brand = $_GET['brand'] ?? '';
        $category = $_GET['category'] ?? '';
        $spu = $_GET['spu'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        if ($keyword || $brand || $category || $spu || $startDate || $endDate) {
            $products = $this->productsModel->searchWithFilters($keyword, $brand, $category, $spu, $startDate, $endDate, $limit, $offset);
            $totalCount = $this->productsModel->getSearchWithFiltersCount($keyword, $brand, $category, $spu, $startDate, $endDate);
        } else {
            $products = $this->productsModel->getAll($limit, $offset);
            $totalCount = $this->productsModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $brandList = $this->productsModel->getBrandList();
        $categoryList = $this->productsModel->getCategoryList();
        $spuList = $this->productsModel->getSpuList();
        $title = '商品管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/products/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function create() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $title = '创建商品';
        $brandList = $this->productsModel->getBrandList();
        $categoryList = $this->productsModel->getCategoryList();
        $spuList = $this->productsModel->getSpuList();
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/products/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function createPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/products.php');
        }
        
        if (empty($_POST['sku'])) {
            showError('SKU不能为空');
            redirect(APP_URL . '/products.php?action=create');
        }
        
        $existingProduct = $this->productsModel->getBySku($_POST['sku']);
        if ($existingProduct) {
            showError('SKU已存在');
            redirect(APP_URL . '/products.php?action=create');
        }
        
        $data = [
            'sku' => $_POST['sku'],
            'spu' => $_POST['spu'] ?? '',
            'brand' => $_POST['brand'] ?? '',
            'category' => $_POST['category'] ?? '',
            'product_name' => $_POST['product_name'] ?? '',
            'product_name_en' => $_POST['product_name_en'] ?? '',
            'cost_price' => $_POST['cost_price'] ?? '0.00',
            'sale_price' => $_POST['sale_price'] ?? '0.00',
            'weight' => $_POST['weight'] ?? '0.00',
            'status' => $_POST['status'] ?? '1',
            'create_time' => date('Y-m-d H:i:s'),
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        if ($this->productsModel->create($data)) {
            showSuccess('商品创建成功');
        } else {
            showError('商品创建失败');
        }
        
        redirect(APP_URL . '/products.php');
    }
    
    public function edit() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/products.php');
        }
        
        $product = $this->productsModel->getById($id);
        if (!$product) {
            showError('商品记录不存在');
            redirect(APP_URL . '/products.php');
        }
        
        $title = '编辑商品';
        $brandList = $this->productsModel->getBrandList();
        $categoryList = $this->productsModel->getCategoryList();
        $spuList = $this->productsModel->getSpuList();
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/products/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function editPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/products.php');
        }
        
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/products.php');
        }
        
        if (empty($_POST['sku'])) {
            showError('SKU不能为空');
            redirect(APP_URL . '/products.php?action=edit&id=' . $id);
        }
        
        $existingProduct = $this->productsModel->getBySku($_POST['sku']);
        if ($existingProduct && $existingProduct['id'] != $id) {
            showError('SKU已存在');
            redirect(APP_URL . '/products.php?action=edit&id=' . $id);
        }
        
        $data = [
            'sku' => $_POST['sku'],
            'spu' => $_POST['spu'] ?? '',
            'brand' => $_POST['brand'] ?? '',
            'category' => $_POST['category'] ?? '',
            'product_name' => $_POST['product_name'] ?? '',
            'product_name_en' => $_POST['product_name_en'] ?? '',
            'cost_price' => $_POST['cost_price'] ?? '0.00',
            'sale_price' => $_POST['sale_price'] ?? '0.00',
            'weight' => $_POST['weight'] ?? '0.00',
            'status' => $_POST['status'] ?? '1',
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        if ($this->productsModel->update($id, $data)) {
            showSuccess('商品更新成功');
        } else {
            showError('商品更新失败');
        }
        
        redirect(APP_URL . '/products.php');
    }
    
    public function delete() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/products.php');
        }
        
        $product = $this->productsModel->getById($id);
        if (!$product) {
            showError('商品记录不存在');
            redirect(APP_URL . '/products.php');
        }
        
        if ($this->productsModel->delete($id)) {
            showSuccess('商品删除成功');
        } else {
            showError('商品删除失败');
        }
        
        redirect(APP_URL . '/products.php');
    }
    
    public function batchDelete() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/products.php');
        }
        
        $ids = $_POST['ids'] ?? [];
        if (empty($ids)) {
            showError('请选择要删除的记录');
            redirect(APP_URL . '/products.php');
        }
        
        $count = $this->productsModel->batchDelete($ids);
        showSuccess("成功删除 {$count} 条记录");
        redirect(APP_URL . '/products.php');
    }
    
    public function search() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        $brand = $_GET['brand'] ?? '';
        $category = $_GET['category'] ?? '';
        $spu = $_GET['spu'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $products = $this->productsModel->searchWithFilters($keyword, $brand, $category, $spu, $startDate, $endDate, $limit, $offset);
        $totalCount = $this->productsModel->getSearchWithFiltersCount($keyword, $brand, $category, $spu, $startDate, $endDate);
        
        $totalPages = ceil($totalCount / $limit);
        $brandList = $this->productsModel->getBrandList();
        $categoryList = $this->productsModel->getCategoryList();
        $spuList = $this->productsModel->getSpuList();
        $title = '搜索结果';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/products/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function import() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $title = '批量导入商品';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/products/import.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function importPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['excel_file'])) {
            showError('请选择要导入的文件');
            redirect(APP_URL . '/products.php?action=import');
        }
        
        $file = $_FILES['excel_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            showError('文件上传失败');
            redirect(APP_URL . '/products.php?action=import');
        }
        
        $allowedExtensions = ['csv'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            showError('只允许导入CSV格式的文件');
            redirect(APP_URL . '/products.php?action=import');
        }
        
        $filePath = $file['tmp_name'];
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            showError('无法读取文件');
            redirect(APP_URL . '/products.php?action=import');
        }
        
        $data = [];
        $header = fgetcsv($handle);
        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        while (($row = fgetcsv($handle)) !== false && $rowCount < 1000) {
            $rowCount++;
            
            if (empty($row[0]) || trim($row[0]) === '') {
                $errors[] = "第 {$rowCount} 行：SKU不能为空";
                $errorCount++;
                continue;
            }
            
            $existingProduct = $this->productsModel->getBySku(trim($row[0]));
            if ($existingProduct) {
                $errors[] = "第 {$rowCount} 行：SKU '{$row[0]}' 已存在";
                $errorCount++;
                continue;
            }
            
            if (isset($row[6]) && trim($row[6]) !== '' && !is_numeric(preg_replace('/[^\d.-]/', '', $row[6]))) {
                $errors[] = "第 {$rowCount} 行：成本价不是有效的数字";
                $errorCount++;
                continue;
            }
            
            if (isset($row[7]) && trim($row[7]) !== '' && !is_numeric(preg_replace('/[^\d.-]/', '', $row[7]))) {
                $errors[] = "第 {$rowCount} 行：销售价不是有效的数字";
                $errorCount++;
                continue;
            }
            
            if (isset($row[8]) && trim($row[8]) !== '' && !is_numeric(preg_replace('/[^\d.-]/', '', $row[8]))) {
                $errors[] = "第 {$rowCount} 行：重量不是有效的数字";
                $errorCount++;
                continue;
            }
            
            $data[] = [
                'sku' => $row[0] ?? '',
                'spu' => $row[1] ?? '',
                'brand' => $row[2] ?? '',
                'category' => $row[3] ?? '',
                'product_name' => $row[4] ?? '',
                'product_name_en' => $row[5] ?? '',
                'cost_price' => $row[6] ?? '0.00',
                'sale_price' => $row[7] ?? '0.00',
                'weight' => $row[8] ?? '0.00',
                'status' => $row[9] ?? '1',
                'create_time' => date('Y-m-d H:i:s'),
                'update_time' => date('Y-m-d H:i:s')
            ];
            $successCount++;
        }
        
        fclose($handle);
        
        if (empty($data)) {
            showError('文件中没有有效数据');
            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
            }
            redirect(APP_URL . '/products.php?action=import');
        }
        
        try {
            $result = $this->productsModel->batchInsert($data);
            showSuccess("成功导入 {$successCount} 条记录，跳过 {$errorCount} 条记录");
            
            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
            }
        } catch (Exception $e) {
            showError('导入失败：' . $e->getMessage());
        }
        
        redirect(APP_URL . '/products.php');
    }
    
    public function export() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $keyword = $_GET['keyword'] ?? '';
        $brand = $_GET['brand'] ?? '';
        $category = $_GET['category'] ?? '';
        $spu = $_GET['spu'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $products = $this->productsModel->getAllWithFilters($keyword, $brand, $category, $spu, $startDate, $endDate);
        
        $filename = 'products_export_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        $output = fopen('php://output', 'w');
        
        fwrite($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        $header = [
            'ID',
            'SKU',
            'SPU',
            '品牌',
            '分类',
            '商品名称',
            '商品名称(英文)',
            '成本价',
            '销售价',
            '重量',
            '状态',
            '创建时间',
            '更新时间'
        ];
        fputcsv($output, $header);
        
        foreach ($products as $product) {
            $row = [
                $product['id'],
                $product['sku'],
                $product['spu'],
                $product['brand'],
                $product['category'],
                $product['product_name'],
                $product['product_name_en'],
                $product['cost_price'],
                $product['sale_price'],
                $product['weight'],
                $product['status'] == '1' ? '启用' : '禁用',
                $product['create_time'],
                $product['update_time']
            ];
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}
