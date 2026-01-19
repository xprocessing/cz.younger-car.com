<?php
require_once ADMIN_PANEL_DIR . '/models/CompanyCosts.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

class CompanyCostsController {
    private $companyCostsModel;
    
    public function __construct() {
        $this->companyCostsModel = new CompanyCosts();
        session_start();
    }
    
    // 显示公司费用列表
    public function index() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $costType = $_GET['cost_type'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        if ($costType || $startDate || $endDate) {
            $costs = $this->companyCostsModel->searchWithFilters($costType, $startDate, $endDate, $limit, $offset);
            $totalCount = $this->companyCostsModel->getSearchWithFiltersCount($costType, $startDate, $endDate);
        } else {
            $costs = $this->companyCostsModel->getAll($limit, $offset);
            $totalCount = $this->companyCostsModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $costTypeList = $this->companyCostsModel->getCostTypeList();
        
        $title = '公司运营费用管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/company_costs/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示统计页面
    public function statistics() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        // 获取统计数据
        $monthlyStatistics = $this->companyCostsModel->getMonthlyStatistics();
        $currentMonthStats = $this->companyCostsModel->getCurrentMonthStatistics();
        $previousMonthStats = $this->companyCostsModel->getPreviousMonthStatistics();
        
        $title = '公司运营费用统计';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/company_costs/statistics.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建公司费用页面
    public function create() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $title = '创建公司运营费用记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/company_costs/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建公司费用请求
    public function createPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/company_costs.php');
        }
        
        // 验证必填字段
        if (empty($_POST['cost_date'])) {
            showError('费用日期不能为空');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=create');
        }
        
        if (empty($_POST['cost_type'])) {
            showError('费用类型不能为空');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=create');
        }
        
        if (empty($_POST['cost'])) {
            showError('费用金额不能为空');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=create');
        }
        
        // 验证数值字段
        $cost = trim($_POST['cost']);
        // 替换逗号为小数点，支持国际化数字格式
        $cost = str_replace(',', '.', $cost);
        // 检查是否是有效的数字格式：只能包含数字、一个小数点，连字符只能在开头
        if (!preg_match('/^-?\d+(\.\d+)?$/', $cost)) {
            showError('费用金额必须是有效的数字');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=create');
        }
        
        // 检查记录是否已存在
        $existingCost = $this->companyCostsModel->getAllWithFilters(
            $_POST['cost_type'],
            $_POST['cost_date'],
            $_POST['cost_date']
        );
        
        if (!empty($existingCost)) {
            showError('该费用类型在该日期的记录已存在');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=create');
        }
        
        $data = [
            'cost_date' => $_POST['cost_date'],
            'cost_type' => $_POST['cost_type'],
            'cost' => $_POST['cost'],
            'remark' => $_POST['remark'] ?? null
        ];
        
        if ($this->companyCostsModel->create($data)) {
            showSuccess('公司运营费用记录创建成功');
        } else {
            showError('公司运营费用记录创建失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/company_costs.php');
    }
    
    // 显示编辑公司费用页面
    public function edit() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(ADMIN_PANEL_URL . '/company_costs.php');
        }
        
        $cost = $this->companyCostsModel->getById($id);
        if (!$cost) {
            showError('公司运营费用记录不存在');
            redirect(ADMIN_PANEL_URL . '/company_costs.php');
        }
        
        $title = '编辑公司运营费用记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/company_costs/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑公司费用请求
    public function editPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/company_costs.php');
        }
        
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(ADMIN_PANEL_URL . '/company_costs.php');
        }
        
        // 验证必填字段
        if (empty($_POST['cost_date'])) {
            showError('费用日期不能为空');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['cost_type'])) {
            showError('费用类型不能为空');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['cost'])) {
            showError('费用金额不能为空');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=edit&id=' . $id);
        }
        
        // 验证数值字段
        $cost = trim($_POST['cost']);
        // 替换逗号为小数点，支持国际化数字格式
        $cost = str_replace(',', '.', $cost);
        // 检查是否是有效的数字格式：只能包含数字、一个小数点，连字符只能在开头
        if (!preg_match('/^-?\d+(\.\d+)?$/', $cost)) {
            showError('费用金额必须是有效的数字');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=edit&id=' . $id);
        }
        
        // 检查记录是否已存在（排除当前记录）
        $existingCost = $this->companyCostsModel->getAllWithFilters(
            $_POST['cost_type'],
            $_POST['cost_date'],
            $_POST['cost_date']
        );
        
        foreach ($existingCost as $record) {
            if ($record['id'] != $id) {
                showError('该费用类型在该日期的记录已存在');
                redirect(ADMIN_PANEL_URL . '/company_costs.php?action=edit&id=' . $id);
            }
        }
        
        $data = [
            'cost_date' => $_POST['cost_date'],
            'cost_type' => $_POST['cost_type'],
            'cost' => $_POST['cost'],
            'remark' => $_POST['remark'] ?? null
        ];
        
        if ($this->companyCostsModel->update($id, $data)) {
            showSuccess('公司运营费用记录更新成功');
        } else {
            showError('公司运营费用记录更新失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/company_costs.php');
    }
    
    // 处理删除公司费用请求
    public function delete() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(ADMIN_PANEL_URL . '/company_costs.php');
        }
        
        $cost = $this->companyCostsModel->getById($id);
        if (!$cost) {
            showError('公司运营费用记录不存在');
            redirect(ADMIN_PANEL_URL . '/company_costs.php');
        }
        
        if ($this->companyCostsModel->delete($id)) {
            showSuccess('公司运营费用记录删除成功');
        } else {
            showError('公司运营费用记录删除失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/company_costs.php');
    }
    
    // 批量导入页面
    public function import() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $title = '批量导入公司运营费用记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/company_costs/import.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理批量导入
    public function importPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            showError('无效的请求方式');
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=import');
        }
        
        $data = [];
        $rowCount = 0;
        $successCount = 0;
        $errorCount = 0;
        $errors = [];
        
        // 处理文件上传
        if (isset($_FILES['excel_file']) && $_FILES['excel_file']['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES['excel_file'];
            $allowedExtensions = ['csv'];
            $fileExtension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if (!in_array($fileExtension, $allowedExtensions)) {
                showError('只允许导入CSV格式的文件');
                redirect(ADMIN_PANEL_URL . '/company_costs.php?action=import');
            }
            
            $filePath = $file['tmp_name'];
            $handle = fopen($filePath, 'r');
            
            if (!$handle) {
                showError('无法读取文件');
                redirect(ADMIN_PANEL_URL . '/company_costs.php?action=import');
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
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=import');
        }
        
        // 调试信息
        if (empty($data)) {
            // 记录调试信息
            $debugInfo = [
                'rowCount' => $rowCount,
                'errorCount' => $errorCount,
                'errors' => $errors
            ];
            $_SESSION['import_debug'] = $debugInfo;
            
            if (!empty($errors)) {
                showError('没有有效数据可导入，错误信息：' . implode('<br>', $errors));
            } else {
                showError('没有有效数据可导入，共处理了 ' . $rowCount . ' 行，但没有通过验证的数据。请检查CSV格式是否正确。');
            }
            redirect(ADMIN_PANEL_URL . '/company_costs.php?action=import');
        }
        
        try {
            $result = $this->companyCostsModel->batchInsert($data);
            showSuccess("成功导入 {$successCount} 条记录，跳过 {$errorCount} 条记录");
            
            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
            }
        } catch (Exception $e) {
            showError('导入失败：' . $e->getMessage());
        }
        
        redirect(ADMIN_PANEL_URL . '/company_costs.php');
    }
    
    // 处理导入的单行数据
    private function processImportRow($row, &$data, &$rowCount, &$successCount, &$errorCount, &$errors) {
        // 验证必填字段
        if (empty($row[0]) || trim($row[0]) === '') {
            $errors[] = "第 {$rowCount} 行：费用日期不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[1]) || trim($row[1]) === '') {
            $errors[] = "第 {$rowCount} 行：费用类型不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[2]) || trim($row[2]) === '') {
            $errors[] = "第 {$rowCount} 行：费用金额不能为空";
            $errorCount++;
            return;
        }
        
        // 验证日期格式
        if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $row[0])) {
            $errors[] = "第 {$rowCount} 行：日期格式不正确，应为YYYY-MM-DD";
            $errorCount++;
            return;
        }
        
        // 将日期格式化为标准的YYYY-MM-DD格式（确保月份和日期为两位数）
        $dateParts = explode('-', $row[0]);
        if (count($dateParts) === 3) {
            $year = $dateParts[0];
            $month = str_pad($dateParts[1], 2, '0', STR_PAD_LEFT);
            $day = str_pad($dateParts[2], 2, '0', STR_PAD_LEFT);
            $row[0] = "{$year}-{$month}-{$day}";
        }
        
        // 验证数值字段
        $cost = trim($row[2]);
        // 替换逗号为小数点，支持国际化数字格式
        $cost = str_replace(',', '.', $cost);
        if (!preg_match('/^-?\d+(\.\d+)?$/', $cost)) {
            $errors[] = "第 {$rowCount} 行：费用金额不是有效的数字";
            $errorCount++;
            return;
        }
        
        // 检查记录是否已存在
        $existingCosts = $this->companyCostsModel->getAllWithFilters(
            trim($row[1]),
            trim($row[0]),
            trim($row[0])
        );
        
        if (!empty($existingCosts)) {
            $errors[] = "第 {$rowCount} 行：该费用类型在该日期的记录已存在";
            $errorCount++;
            return;
        }
        
        $data[] = [
            'cost_date' => trim($row[0]),
            'cost_type' => trim($row[1]),
            'cost' => trim($row[2]),
            'remark' => isset($row[3]) ? trim($row[3]) : null
        ];
        
        $successCount++;
    }
    
    // 批量导出
    public function export() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        // 获取所有筛选参数
        $costType = $_GET['cost_type'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        // 获取所有符合条件的数据
        $costs = $this->companyCostsModel->getAllWithFilters($costType, $startDate, $endDate);
        
        // 设置CSV文件头
        $filename = 'company_costs_export_' . date('YmdHis') . '.csv';
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
            '费用日期',
            '费用类型',
            '费用金额',
            '备注',
            '创建时间',
            '更新时间'
        ];
        fputcsv($output, $header);
        
        // 写入数据行
        foreach ($costs as $cost) {
            $row = [
                $cost['cost_date'],
                $cost['cost_type'],
                $cost['cost'],
                $cost['remark'] ?? '',
                $cost['create_at'],
                $cost['update_at']
            ];
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}