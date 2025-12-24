<?php
require_once APP_ROOT . '/models/OrderProfit.php';
require_once APP_ROOT . '/helpers/functions.php';

class OrderProfitController {
    private $orderProfitModel;
    
    public function __construct() {
        $this->orderProfitModel = new OrderProfit();
        session_start();
    }
    
    // 显示订单利润列表
    public function index() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        $storeId = $_GET['store_id'] ?? '';
        $rateMin = $_GET['rate_min'] ?? '';
        $rateMax = $_GET['rate_max'] ?? '';
        
        if ($keyword || $storeId || $rateMin || $rateMax) {
            $profits = $this->orderProfitModel->searchWithFilters($keyword, $storeId, $rateMin, $rateMax, $limit, $offset);
            $totalCount = $this->orderProfitModel->getSearchWithFiltersCount($keyword, $storeId, $rateMin, $rateMax);
        } else {
            $profits = $this->orderProfitModel->getAll($limit, $offset);
            $totalCount = $this->orderProfitModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $storeList = $this->orderProfitModel->getStoreList();
        $title = '订单利润管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_profit/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建订单利润页面
    public function create() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $title = '创建订单利润';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_profit/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建订单利润请求
    public function createPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/order_profit.php');
        }
        
        // 验证必填字段
        if (empty($_POST['global_order_no'])) {
            showError('订单号不能为空');
            redirect(APP_URL . '/order_profit.php?action=create');
        }
        
        // 检查订单号是否已存在
        $existingProfit = $this->orderProfitModel->getByOrderNo($_POST['global_order_no']);
        if ($existingProfit) {
            showError('订单号已存在');
            redirect(APP_URL . '/order_profit.php?action=create');
        }
        
        $data = [
            'store_id' => $_POST['store_id'] ?? '',
            'global_order_no' => $_POST['global_order_no'],
            'receiver_country' => $_POST['receiver_country'] ?? '',
            'global_purchase_time' => $_POST['global_purchase_time'] ?? '',
            'local_sku' => $_POST['local_sku'] ?? '',
            'order_total_amount' => $_POST['order_total_amount'] ?? '0.00',
            'profit_amount' => $_POST['profit_amount'] ?? '0.00',
            'profit_rate' => $_POST['profit_rate'] ?? '0.00',
            'wms_outbound_cost_amount' => $_POST['wms_outbound_cost_amount'] ?? '0.00',
            'wms_shipping_price_amount' => $_POST['wms_shipping_price_amount'] ?? '0.00',
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        if ($this->orderProfitModel->create($data)) {
            showSuccess('订单利润创建成功');
        } else {
            showError('订单利润创建失败');
        }
        
        redirect(APP_URL . '/order_profit.php');
    }
    
    // 显示编辑订单利润页面
    public function edit() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/order_profit.php');
        }
        
        $profit = $this->orderProfitModel->getById($id);
        if (!$profit) {
            showError('订单利润记录不存在');
            redirect(APP_URL . '/order_profit.php');
        }
        
        $title = '编辑订单利润';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_profit/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑订单利润请求
    public function editPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/order_profit.php');
        }
        
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/order_profit.php');
        }
        
        // 验证必填字段
        if (empty($_POST['global_order_no'])) {
            showError('订单号不能为空');
            redirect(APP_URL . '/order_profit.php?action=edit&id=' . $id);
        }
        
        // 检查订单号是否已存在（排除当前记录）
        $existingProfit = $this->orderProfitModel->getByOrderNo($_POST['global_order_no']);
        if ($existingProfit && $existingProfit['id'] != $id) {
            showError('订单号已存在');
            redirect(APP_URL . '/order_profit.php?action=edit&id=' . $id);
        }
        
        $data = [
            'store_id' => $_POST['store_id'] ?? '',
            'global_order_no' => $_POST['global_order_no'],
            'receiver_country' => $_POST['receiver_country'] ?? '',
            'global_purchase_time' => $_POST['global_purchase_time'] ?? '',
            'local_sku' => $_POST['local_sku'] ?? '',
            'order_total_amount' => $_POST['order_total_amount'] ?? '0.00',
            'profit_amount' => $_POST['profit_amount'] ?? '0.00',
            'profit_rate' => $_POST['profit_rate'] ?? '0.00',
            'wms_outbound_cost_amount' => $_POST['wms_outbound_cost_amount'] ?? '0.00',
            'wms_shipping_price_amount' => $_POST['wms_shipping_price_amount'] ?? '0.00',
            'update_time' => date('Y-m-d H:i:s')
        ];
        
        if ($this->orderProfitModel->update($id, $data)) {
            showSuccess('订单利润更新成功');
        } else {
            showError('订单利润更新失败');
        }
        
        redirect(APP_URL . '/order_profit.php');
    }
    
    // 删除订单利润
    public function delete() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/order_profit.php');
        }
        
        $profit = $this->orderProfitModel->getById($id);
        if (!$profit) {
            showError('订单利润记录不存在');
            redirect(APP_URL . '/order_profit.php');
        }
        
        if ($this->orderProfitModel->delete($id)) {
            showSuccess('订单利润删除成功');
        } else {
            showError('订单利润删除失败');
        }
        
        redirect(APP_URL . '/order_profit.php');
    }
    
    // 搜索订单利润
    public function search() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $keyword = $_GET['keyword'] ?? '';
        $storeId = $_GET['store_id'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        if ($keyword) {
            $profits = $this->orderProfitModel->search($keyword, $limit, $offset);
            $totalCount = $this->orderProfitModel->getSearchCount($keyword);
        } elseif ($storeId) {
            $profits = $this->orderProfitModel->getByStoreId($storeId, $limit, $offset);
            $totalCount = $this->orderProfitModel->getCount();
        } else {
            $profits = $this->orderProfitModel->getAll($limit, $offset);
            $totalCount = $this->orderProfitModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $storeList = $this->orderProfitModel->getStoreList();
        $title = '搜索结果: ' . ($keyword ?: '按店铺筛选');
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_profit/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 统计页面
    public function stats() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $startDate = $_GET['start_date'] ?? date('Y-m-01'); // 默认当月第一天
        $endDate = $_GET['end_date'] ?? date('Y-m-d'); // 默认今天
        $storeId = $_GET['store_id'] ?? '';
        
        $stats = $this->orderProfitModel->getProfitStats($startDate, $endDate, $storeId);
        $storeList = $this->orderProfitModel->getStoreList();
        $title = '利润统计';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_profit/stats.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 批量导入页面
    public function import() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $title = '批量导入订单利润';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_profit/import.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理批量导入
    public function importPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_FILES['excel_file'])) {
            showError('请选择要导入的文件');
            redirect(APP_URL . '/order_profit.php?action=import');
        }
        
        $file = $_FILES['excel_file'];
        if ($file['error'] !== UPLOAD_ERR_OK) {
            showError('文件上传失败');
            redirect(APP_URL . '/order_profit.php?action=import');
        }
        
        // 验证文件类型
        $allowedExtensions = ['csv'];
        $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            showError('只允许导入CSV格式的文件');
            redirect(APP_URL . '/order_profit.php?action=import');
        }
        
        // 简单的CSV处理示例
        $filePath = $file['tmp_name'];
        $handle = fopen($filePath, 'r');
        
        if (!$handle) {
            showError('无法读取文件');
            redirect(APP_URL . '/order_profit.php?action=import');
        }
        
        $data = [];
        $header = fgetcsv($handle); // 读取表头
        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        while (($row = fgetcsv($handle)) !== false && $rowCount < 1000) { // 限制最多1000条
            $rowCount++;
            
            // 验证必填字段
            if (empty($row[1]) || trim($row[1]) === '') { // global_order_no 是必填字段
                $errors[] = "第 {$rowCount} 行：订单号不能为空";
                $errorCount++;
                continue;
            }
            
            // 检查订单号是否已存在
            $existingProfit = $this->orderProfitModel->getByOrderNo(trim($row[1]));
            if ($existingProfit) {
                $errors[] = "第 {$rowCount} 行：订单号 '{$row[1]}' 已存在";
                $errorCount++;
                continue;
            }
            
            // 验证数值字段
            if (isset($row[8]) && trim($row[8]) !== '' && !is_numeric(preg_replace('/[^\d.-]/', '', $row[8]))) {
                $errors[] = "第 {$rowCount} 行：WMS出库成本不是有效的数字";
                $errorCount++;
                continue;
            }
            
            if (isset($row[9]) && trim($row[9]) !== '' && !is_numeric(preg_replace('/[^\d.-]/', '', $row[9]))) {
                $errors[] = "第 {$rowCount} 行：WMS运费不是有效的数字";
                $errorCount++;
                continue;
            }
            
            $data[] = [
                'store_id' => $row[0] ?? '',
                'global_order_no' => $row[1] ?? '',
                'receiver_country' => $row[2] ?? '',
                'global_purchase_time' => $row[3] ?? '',
                'local_sku' => $row[4] ?? '',
                'order_total_amount' => $row[5] ?? '0.00',
                'profit_amount' => $row[6] ?? '0.00',
                'profit_rate' => $row[7] ?? '0.00',
                'wms_outbound_cost_amount' => $row[8] ?? '0.00',
                'wms_shipping_price_amount' => $row[9] ?? '0.00',
                'update_time' => date('Y-m-d H:i:s')
            ];
            $successCount++;
        }
        
        fclose($handle);
        
        if (empty($data)) {
            showError('文件中没有有效数据');
            // 如果有错误信息，显示错误
            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
            }
            redirect(APP_URL . '/order_profit.php?action=import');
        }
        
        try {
            $result = $this->orderProfitModel->batchInsert($data);
            showSuccess("成功导入 {$successCount} 条记录，跳过 {$errorCount} 条记录");
            
            // 如果有错误信息，显示错误
            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
            }
        } catch (Exception $e) {
            showError('导入失败：' . $e->getMessage());
        }
        
        redirect(APP_URL . '/order_profit.php');
    }
}