<?php
require_once APP_ROOT . '/models/Costs.php';
require_once APP_ROOT . '/helpers/functions.php';

class CostsController {
    private $costsModel;
    
    public function __construct() {
        $this->costsModel = new Costs();
        session_start();
    }
    
    // 显示成本列表
    public function index() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $platformName = $_GET['platform_name'] ?? '';
        $storeName = $_GET['store_name'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        if ($platformName || $storeName || $startDate || $endDate) {
            $costs = $this->costsModel->searchWithFilters($platformName, $storeName, $startDate, $endDate, $limit, $offset);
            $totalCount = $this->costsModel->getSearchWithFiltersCount($platformName, $storeName, $startDate, $endDate);
        } else {
            $costs = $this->costsModel->getAll($limit, $offset);
            $totalCount = $this->costsModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $platformList = $this->costsModel->getPlatformList();
        $storeList = $this->costsModel->getStoreList();
        $title = '广告费管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/costs/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建成本页面
    public function create() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $title = '创建广告费记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/costs/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建成本请求
    public function createPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/costs.php');
        }
        
        // 验证必填字段
        if (empty($_POST['platform_name'])) {
            showError('平台名称不能为空');
            redirect(APP_URL . '/costs.php?action=create');
        }
        
        if (empty($_POST['store_name'])) {
            showError('店铺名称不能为空');
            redirect(APP_URL . '/costs.php?action=create');
        }
        
        if (empty($_POST['cost'])) {
            showError('日均广告花费不能为空');
            redirect(APP_URL . '/costs.php?action=create');
        }
        
        if (empty($_POST['date'])) {
            showError('日期不能为空');
            redirect(APP_URL . '/costs.php?action=create');
        }
        
        // 验证数值字段
        if (!is_numeric(preg_replace('/[^.-]/', '', $_POST['cost']))) {
            showError('日均广告花费必须是有效的数字');
            redirect(APP_URL . '/costs.php?action=create');
        }
        
        // 检查记录是否已存在
        $existingCost = $this->costsModel->getAllWithFilters(
            $_POST['platform_name'],
            $_POST['store_name'],
            $_POST['date'],
            $_POST['date']
        );
        
        if (!empty($existingCost)) {
            showError('该平台、店铺在该日期的记录已存在');
            redirect(APP_URL . '/costs.php?action=create');
        }
        
        $data = [
            'platform_name' => $_POST['platform_name'],
            'store_name' => $_POST['store_name'],
            'cost' => $_POST['cost'],
            'date' => $_POST['date']
        ];
        
        if ($this->costsModel->create($data)) {
            showSuccess('广告费记录创建成功');
        } else {
            showError('广告费记录创建失败');
        }
        
        redirect(APP_URL . '/costs.php');
    }
    
    // 显示编辑成本页面
    public function edit() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/costs.php');
        }
        
        $cost = $this->costsModel->getById($id);
        if (!$cost) {
            showError('广告费记录不存在');
            redirect(APP_URL . '/costs.php');
        }
        
        $title = '编辑广告费记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/costs/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑成本请求
    public function editPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/costs.php');
        }
        
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/costs.php');
        }
        
        // 验证必填字段
        if (empty($_POST['platform_name'])) {
            showError('平台名称不能为空');
            redirect(APP_URL . '/costs.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['store_name'])) {
            showError('店铺名称不能为空');
            redirect(APP_URL . '/costs.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['cost'])) {
            showError('日均广告花费不能为空');
            redirect(APP_URL . '/costs.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['date'])) {
            showError('日期不能为空');
            redirect(APP_URL . '/costs.php?action=edit&id=' . $id);
        }
        
        // 验证数值字段
        if (!is_numeric(preg_replace('/[^.-]/', '', $_POST['cost']))) {
            showError('日均广告花费必须是有效的数字');
            redirect(APP_URL . '/costs.php?action=edit&id=' . $id);
        }
        
        // 检查记录是否已存在（排除当前记录）
        $existingCosts = $this->costsModel->getAllWithFilters(
            $_POST['platform_name'],
            $_POST['store_name'],
            $_POST['date'],
            $_POST['date']
        );
        
        foreach ($existingCosts as $existingCost) {
            if ($existingCost['id'] != $id) {
                showError('该平台、店铺在该日期的记录已存在');
                redirect(APP_URL . '/costs.php?action=edit&id=' . $id);
            }
        }
        
        $data = [
            'platform_name' => $_POST['platform_name'],
            'store_name' => $_POST['store_name'],
            'cost' => $_POST['cost'],
            'date' => $_POST['date']
        ];
        
        if ($this->costsModel->update($id, $data)) {
            showSuccess('广告费记录更新成功');
        } else {
            showError('广告费记录更新失败');
        }
        
        redirect(APP_URL . '/costs.php');
    }
    
    // 删除成本记录
    public function delete() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(APP_URL . '/costs.php');
        }
        
        $cost = $this->costsModel->getById($id);
        if (!$cost) {
            showError('广告费记录不存在');
            redirect(APP_URL . '/costs.php');
        }
        
        if ($this->costsModel->delete($id)) {
            showSuccess('广告费记录删除成功');
        } else {
            showError('广告费记录删除失败');
        }
        
        redirect(APP_URL . '/costs.php');
    }
    
    // 搜索成本记录
    public function search() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $platformName = $_GET['platform_name'] ?? '';
        $storeName = $_GET['store_name'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $costs = $this->costsModel->searchWithFilters($platformName, $storeName, $startDate, $endDate, $limit, $offset);
        $totalCount = $this->costsModel->getSearchWithFiltersCount($platformName, $storeName, $startDate, $endDate);
        
        $totalPages = ceil($totalCount / $limit);
        $platformList = $this->costsModel->getPlatformList();
        $storeList = $this->costsModel->getStoreList();
        $title = '搜索结果';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/costs/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 批量导入页面
    public function import() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $title = '批量导入广告费记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/costs/import.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理批量导入
    public function importPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            showError('无效的请求方式');
            redirect(APP_URL . '/costs.php?action=import');
        }
        
        $data = [];
        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // 处理文件上传
        if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['csv_file'];
            $allowedExtensions = ['csv'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                showError('只允许导入CSV格式的文件');
                redirect(APP_URL . '/costs.php?action=import');
            }
            
            $filePath = $file['tmp_name'];
            $handle = fopen($filePath, 'r');
            
            if (!$handle) {
                showError('无法读取文件');
                redirect(APP_URL . '/costs.php?action=import');
            }
            
            // 读取表头
            fgetcsv($handle);
            
            // 读取数据行
            while (($row = fgetcsv($handle)) !== false && $rowCount < 1000) {
                $rowCount++;
                $this->processImportRow($row, $data, $rowCount, $successCount, $errorCount, $errors);
            }
            
            fclose($handle);
        } 
        // 处理粘贴导入
        elseif (isset($_POST['csv_content']) && !empty(trim($_POST['csv_content']))) {
            $csvContent = trim($_POST['csv_content']);
            $lines = explode("\n", $csvContent);
            
            // 跳过表头
            array_shift($lines);
            
            foreach ($lines as $line) {
                if (empty(trim($line))) continue;
                
                $row = str_getcsv($line);
                $rowCount++;
                $this->processImportRow($row, $data, $rowCount, $successCount, $errorCount, $errors);
            }
        } else {
            showError('请选择文件或粘贴CSV内容');
            redirect(APP_URL . '/costs.php?action=import');
        }
        
        if (empty($data)) {
            showError('没有有效数据可导入');
            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
            }
            redirect(APP_URL . '/costs.php?action=import');
        }
        
        try {
            $result = $this->costsModel->batchInsert($data);
            showSuccess("成功导入 {$successCount} 条记录，跳过 {$errorCount} 条记录");
            
            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
            }
        } catch (Exception $e) {
            showError('导入失败：' . $e->getMessage());
        }
        
        redirect(APP_URL . '/costs.php');
    }
    
    // 处理导入的单行数据
    private function processImportRow($row, &$data, &$rowCount, &$successCount, &$errorCount, &$errors) {
        // 验证必填字段
        if (empty($row[0]) || trim($row[0]) === '') {
            $errors[] = "第 {$rowCount} 行：平台名称不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[1]) || trim($row[1]) === '') {
            $errors[] = "第 {$rowCount} 行：店铺名称不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[2]) || trim($row[2]) === '') {
            $errors[] = "第 {$rowCount} 行：日均广告花费不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[3]) || trim($row[3]) === '') {
            $errors[] = "第 {$rowCount} 行：日期不能为空";
            $errorCount++;
            return;
        }
        
        // 验证数值字段
        if (!is_numeric(preg_replace('/[^.-]/', '', $row[2]))) {
            $errors[] = "第 {$rowCount} 行：日均广告花费不是有效的数字";
            $errorCount++;
            return;
        }
        
        // 验证日期格式
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $row[3])) {
            $errors[] = "第 {$rowCount} 行：日期格式不正确，应为YYYY-MM-DD";
            $errorCount++;
            return;
        }
        
        // 检查记录是否已存在
        $existingCosts = $this->costsModel->getAllWithFilters(
            trim($row[0]),
            trim($row[1]),
            trim($row[3]),
            trim($row[3])
        );
        
        if (!empty($existingCosts)) {
            $errors[] = "第 {$rowCount} 行：该平台、店铺在该日期的记录已存在";
            $errorCount++;
            return;
        }
        
        $data[] = [
            'platform_name' => trim($row[0]),
            'store_name' => trim($row[1]),
            'cost' => trim($row[2]),
            'date' => trim($row[3])
        ];
        
        $successCount++;
    }
    
    // 批量导出
    public function export() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        // 获取所有筛选参数
        $platformName = $_GET['platform_name'] ?? '';
        $storeName = $_GET['store_name'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        // 获取所有符合条件的数据
        $costs = $this->costsModel->getAllWithFilters($platformName, $storeName, $startDate, $endDate);
        
        // 设置CSV文件头
        $filename = 'costs_export_' . date('YmdHis') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=' . $filename);
        header('Pragma: no-cache');
        header('Expires: 0');
        
        // 创建文件指针
        $output = fopen('php://output', 'w');
        
        // 写入BOM以支持Excel中文显示
        fwrite($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // 写入CSV表头
        $header = [
            '平台名称',
            '店铺名称',
            '日均广告花费（美元）',
            '日期',
            '创建时间',
            '更新时间'
        ];
        fputcsv($output, $header);
        
        // 写入数据行
        foreach ($costs as $cost) {
            $row = [
                $cost['platform_name'],
                $cost['store_name'],
                $cost['cost'],
                $cost['date'],
                $cost['create_at'],
                $cost['update_at']
            ];
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}