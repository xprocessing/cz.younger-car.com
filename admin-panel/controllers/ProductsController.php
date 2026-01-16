<?php
require_once ADMIN_PANEL_DIR . '/models/Products.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

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
        $status = $_GET['status'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $products = $this->productsModel->searchWithFilters($keyword, '', '', $status, $brand, $category, $limit, $offset);
        $totalCount = $this->productsModel->getSearchWithFiltersCount($keyword, '', '', $status, $brand, $category);
        
        $totalPages = ceil($totalCount / $limit);
        $brandList = $this->productsModel->getBrandList();
        $categoryList = $this->productsModel->getCategoryList();
        $title = '商品管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/products/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function create() {
        if (!hasPermission('products.create')) {
            showError('您没有权限创建商品');
            redirect(APP_URL . '/products.php');
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
            'sku_identifier' => $_POST['sku_identifier'] ?? '',
            'spu' => $_POST['spu'] ?? '',
            'ps_id' => !empty($_POST['ps_id']) ? (int)$_POST['ps_id'] : null,
            'brand_name' => $_POST['brand_name'] ?? '',
            'category_name' => $_POST['category_name'] ?? '',
            'product_name' => $_POST['product_name'] ?? '',
            'pic_url' => $_POST['pic_url'] ?? '',
            'cg_price' => $_POST['cg_price'] ?? '0.0000',
            'cg_transport_costs' => $_POST['cg_transport_costs'] ?? '0.00',
            'cg_delivery' => $_POST['cg_delivery'] ?? '',
            'purchase_remark' => $_POST['purchase_remark'] ?? '',
            'status' => $_POST['status'] ?? '1',
            'open_status' => $_POST['open_status'] ?? '1',
            'is_combo' => $_POST['is_combo'] ?? '0',
            'product_developer' => $_POST['product_developer'] ?? '',
            'product_developer_uid' => !empty($_POST['product_developer_uid']) ? (int)$_POST['product_developer_uid'] : null,
            'cg_opt_username' => $_POST['cg_opt_username'] ?? '',
            'cg_opt_uid' => !empty($_POST['cg_opt_uid']) ? (int)$_POST['cg_opt_uid'] : null
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
        if (!hasPermission('products.edit')) {
            showError('您没有权限编辑商品');
            redirect(APP_URL . '/products.php');
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
            'sku_identifier' => $_POST['sku_identifier'] ?? '',
            'spu' => $_POST['spu'] ?? '',
            'ps_id' => !empty($_POST['ps_id']) ? (int)$_POST['ps_id'] : null,
            'brand_name' => $_POST['brand_name'] ?? '',
            'category_name' => $_POST['category_name'] ?? '',
            'product_name' => $_POST['product_name'] ?? '',
            'pic_url' => $_POST['pic_url'] ?? '',
            'cg_price' => $_POST['cg_price'] ?? '0.0000',
            'cg_transport_costs' => $_POST['cg_transport_costs'] ?? '0.00',
            'cg_delivery' => $_POST['cg_delivery'] ?? '',
            'purchase_remark' => $_POST['purchase_remark'] ?? '',
            'status' => $_POST['status'] ?? '1',
            'open_status' => $_POST['open_status'] ?? '1',
            'is_combo' => $_POST['is_combo'] ?? '0',
            'product_developer' => $_POST['product_developer'] ?? '',
            'product_developer_uid' => !empty($_POST['product_developer_uid']) ? (int)$_POST['product_developer_uid'] : null,
            'cg_opt_username' => $_POST['cg_opt_username'] ?? '',
            'cg_opt_uid' => !empty($_POST['cg_opt_uid']) ? (int)$_POST['cg_opt_uid'] : null
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
        if (!hasPermission('products.view')) {
            showError('您没有权限搜索商品');
            redirect(APP_URL . '/products.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        $brand = $_GET['brand'] ?? '';
        $category = $_GET['category'] ?? '';
        $status = $_GET['status'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $products = $this->productsModel->searchWithFilters($keyword, '', '', $status, $brand, $category, $limit, $offset);
        $totalCount = $this->productsModel->getSearchWithFiltersCount($keyword, '', '', $status, $brand, $category);
        
        $totalPages = ceil($totalCount / $limit);
        $brandList = $this->productsModel->getBrandList();
        $categoryList = $this->productsModel->getCategoryList();
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
        if (!hasPermission('products.import')) {
            showError('您没有权限导入商品');
            redirect(APP_URL . '/products.php?action=import');
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
            
            if (isset($row[8]) && trim($row[8]) !== '' && !is_numeric(preg_replace('/[^\d.-]/', '', $row[8]))) {
                $errors[] = "第 {$rowCount} 行：采购成本不是有效的数字";
                $errorCount++;
                continue;
            }
            
            if (isset($row[9]) && trim($row[9]) !== '' && !is_numeric(preg_replace('/[^\d.-]/', '', $row[9]))) {
                $errors[] = "第 {$rowCount} 行：运输成本不是有效的数字";
                $errorCount++;
                continue;
            }
            
            $data[] = [
                'sku' => $row[0] ?? '',
                'sku_identifier' => $row[1] ?? '',
                'spu' => $row[2] ?? '',
                'ps_id' => !empty($row[3]) ? (int)$row[3] : null,
                'brand_name' => $row[4] ?? '',
                'category_name' => $row[5] ?? '',
                'product_name' => $row[6] ?? '',
                'pic_url' => $row[7] ?? '',
                'cg_price' => $row[8] ?? '0.0000',
                'cg_transport_costs' => $row[9] ?? '0.00',
                'cg_delivery' => $row[10] ?? '',
                'purchase_remark' => $row[11] ?? '',
                'status' => $row[12] ?? '1',
                'open_status' => $row[13] ?? '1',
                'is_combo' => $row[14] ?? '0',
                'product_developer' => $row[15] ?? '',
                'product_developer_uid' => null,
                'cg_opt_username' => $row[16] ?? '',
                'cg_opt_uid' => null
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
        $status = $_GET['status'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $products = $this->productsModel->export($keyword, '', '', $status, $brand, $category);
        
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
            'SKU识别码',
            'SPU',
            'SPU ID',
            '品牌名称',
            '分类名称',
            '商品名称',
            '商品图片URL',
            '采购成本',
            '运输成本',
            '采购交付',
            '采购备注',
            '状态',
            '启用状态',
            '是否组合',
            '商品开发员',
            '开发员UID',
            '采购员用户名',
            '采购员UID',
            '创建时间'
        ];
        fputcsv($output, $header);
        
        foreach ($products as $product) {
            $statusText = '';
            switch($product['status']) {
                case '0':
                    $statusText = '停售';
                    break;
                case '1':
                    $statusText = '在售';
                    break;
                case '2':
                    $statusText = '开发中';
                    break;
                case '3':
                    $statusText = '清仓';
                    break;
                default:
                    $statusText = '未知';
            }
            
            $openStatusText = $product['open_status'] == '1' ? '启用' : '停用';
            $isComboText = $product['is_combo'] == '1' ? '是' : '否';
            
            $row = [
                $product['id'],
                $product['sku'],
                $product['sku_identifier'],
                $product['spu'],
                $product['ps_id'],
                $product['brand_name'],
                $product['category_name'],
                $product['product_name'],
                $product['pic_url'],
                $product['cg_price'],
                $product['cg_transport_costs'],
                $product['cg_delivery'],
                $product['purchase_remark'],
                $statusText,
                $openStatusText,
                $isComboText,
                $product['product_developer'],
                $product['product_developer_uid'],
                $product['cg_opt_username'],
                $product['cg_opt_uid'],
                $product['create_time']
            ];
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
    
    public function stats() {
        if (!hasPermission('products.view')) {
            showError('您没有权限查看商品统计');
            redirect(APP_URL . '/dashboard.php');
        }
        
        $categoryStats = $this->productsModel->getCategoryStats();
        $statusStats = $this->productsModel->getStatusStats();
        $brandStats = $this->productsModel->getBrandStats();
        $totalProducts = $this->productsModel->getTotalProducts();
        $totalCategories = $this->productsModel->getTotalCategories();
        $totalBrands = $this->productsModel->getTotalBrands();
        $activeProducts = $this->productsModel->getActiveProducts();
        
        $title = '商品统计';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/products/stats.php';
    }
}
