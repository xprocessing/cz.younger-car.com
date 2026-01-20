<?php
require_once ADMIN_PANEL_DIR . '/models/TrackCosts.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

class TrackCostsController {
    private $trackCostsModel;
    
    public function __construct() {
        $this->trackCostsModel = new TrackCosts();
        session_start();
    }
    
    // 显示成本列表
    public function index() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $trackName = $_GET['track_name'] ?? '';
        $costType = $_GET['cost_type'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        if ($trackName || $costType || $startDate || $endDate) {
            $costs = $this->trackCostsModel->searchWithFilters($trackName, $costType, $startDate, $endDate, $limit, $offset);
            $totalCount = $this->trackCostsModel->getSearchWithFiltersCount($trackName, $costType, $startDate, $endDate);
        } else {
            $costs = $this->trackCostsModel->getAll($limit, $offset);
            $totalCount = $this->trackCostsModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $trackList = $this->trackCostsModel->getTrackList();
        $costTypeList = $this->trackCostsModel->getCostTypeList();
        $title = '赛道费用管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/track_costs/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建成本页面
    public function create() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $title = '创建赛道费用记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/track_costs/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建成本请求
    public function createPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/track_costs.php');
        }
        
        // 验证必填字段
        if (empty($_POST['track_name'])) {
            showError('赛道名称不能为空');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=create');
        }
        
        if (empty($_POST['cost'])) {
            showError('费用金额不能为空');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=create');
        }
        
        if (empty($_POST['cost_date'])) {
            showError('日期不能为空');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=create');
        }
        
        if (empty($_POST['cost_type'])) {
            showError('费用类型不能为空');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=create');
        }
        
        // 验证数值字段
        $cost = trim($_POST['cost']);
        // 替换逗号为小数点，支持国际化数字格式
        $cost = str_replace(',', '.', $cost);
        // 检查是否是有效的数字格式：只能包含数字、一个小数点，连字符只能在开头
        if (!preg_match('/^-?\d+(\.\d+)?$/', $cost)) {
            showError('费用金额必须是有效的数字');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=create');
        }
        
        // 检查记录是否已存在
        $existingCost = $this->trackCostsModel->getAllWithFilters(
            $_POST['track_name'],
            $_POST['cost_type'],
            $_POST['cost_date'],
            $_POST['cost_date']
        );
        
        if (!empty($existingCost)) {
            showError('该赛道、费用类型在该日期的记录已存在');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=create');
        }
        
        $data = [
            'track_name' => $_POST['track_name'],
            'cost' => $_POST['cost'],
            'cost_type' => $_POST['cost_type'],
            'cost_date' => $_POST['cost_date'],
            'remark' => $_POST['remark'] ?? null
        ];
        
        if ($this->trackCostsModel->create($data)) {
            showSuccess('赛道费用记录创建成功');
        } else {
            showError('赛道费用记录创建失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/track_costs.php');
    }
    
    // 显示编辑成本页面
    public function edit() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(ADMIN_PANEL_URL . '/track_costs.php');
        }
        
        $cost = $this->trackCostsModel->getById($id);
        if (!$cost) {
            showError('赛道费用记录不存在');
            redirect(ADMIN_PANEL_URL . '/track_costs.php');
        }
        
        $title = '编辑赛道费用记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/track_costs/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑成本请求
    public function editPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/track_costs.php');
        }
        
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(ADMIN_PANEL_URL . '/track_costs.php');
        }
        
        // 验证必填字段
        if (empty($_POST['track_name'])) {
            showError('赛道名称不能为空');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['cost'])) {
            showError('费用金额不能为空');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['cost_date'])) {
            showError('日期不能为空');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['cost_type'])) {
            showError('费用类型不能为空');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=edit&id=' . $id);
        }
        
        // 验证数值字段
        $cost = trim($_POST['cost']);
        // 替换逗号为小数点，支持国际化数字格式
        $cost = str_replace(',', '.', $cost);
        // 检查是否是有效的数字格式：只能包含数字、一个小数点，连字符只能在开头
        if (!preg_match('/^-?\d+(\.\d+)?$/', $cost)) {
            showError('费用金额必须是有效的数字');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=edit&id=' . $id);
        }
        
        // 检查记录是否已存在（排除当前记录）
        $existingCosts = $this->trackCostsModel->getAllWithFilters(
            $_POST['track_name'],
            $_POST['cost_type'],
            $_POST['cost_date'],
            $_POST['cost_date']
        );
        
        foreach ($existingCosts as $existingCost) {
            if ($existingCost['id'] != $id) {
                showError('该赛道、费用类型在该日期的记录已存在');
                redirect(ADMIN_PANEL_URL . '/track_costs.php?action=edit&id=' . $id);
            }
        }
        
        $data = [
            'track_name' => $_POST['track_name'],
            'cost' => $_POST['cost'],
            'cost_type' => $_POST['cost_type'],
            'cost_date' => $_POST['cost_date'],
            'remark' => $_POST['remark'] ?? null
        ];
        
        if ($this->trackCostsModel->update($id, $data)) {
            showSuccess('赛道费用记录更新成功');
        } else {
            showError('赛道费用记录更新失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/track_costs.php');
    }
    
    // 删除成本记录
    public function delete() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(ADMIN_PANEL_URL . '/track_costs.php');
        }
        
        $cost = $this->trackCostsModel->getById($id);
        if (!$cost) {
            showError('赛道费用记录不存在');
            redirect(ADMIN_PANEL_URL . '/track_costs.php');
        }
        
        if ($this->trackCostsModel->delete($id)) {
            showSuccess('赛道费用记录删除成功');
        } else {
            showError('赛道费用记录删除失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/track_costs.php');
    }
    
    // 搜索成本记录
    public function search() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $trackName = $_GET['track_name'] ?? '';
        $costType = $_GET['cost_type'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $costs = $this->trackCostsModel->searchWithFilters($trackName, $costType, $startDate, $endDate, $limit, $offset);
        $totalCount = $this->trackCostsModel->getSearchWithFiltersCount($trackName, $costType, $startDate, $endDate);
        
        $totalPages = ceil($totalCount / $limit);
        $trackList = $this->trackCostsModel->getTrackList();
        $costTypeList = $this->trackCostsModel->getCostTypeList();
        $title = '搜索结果';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/track_costs/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 批量导入页面
    public function import() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $title = '批量导入赛道费用记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/track_costs/import.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理批量导入
    public function importPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            showError('无效的请求方式');
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=import');
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
                redirect(ADMIN_PANEL_URL . '/track_costs.php?action=import');
            }
            
            $filePath = $file['tmp_name'];
            $handle = fopen($filePath, 'r');
            
            if (!$handle) {
                showError('无法读取文件');
                redirect(ADMIN_PANEL_URL . '/track_costs.php?action=import');
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
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=import');
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
            redirect(ADMIN_PANEL_URL . '/track_costs.php?action=import');
        }
        
        try {
            $result = $this->trackCostsModel->batchInsert($data);
            showSuccess("成功导入 {$successCount} 条记录，跳过 {$errorCount} 条记录");
            
            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
            }
        } catch (Exception $e) {
            showError('导入失败：' . $e->getMessage());
        }
        
        redirect(ADMIN_PANEL_URL . '/track_costs.php');
    }
    
    // 处理导入的单行数据
    private function processImportRow($row, &$data, &$rowCount, &$successCount, &$errorCount, &$errors) {
        // 验证必填字段
        if (empty($row[0]) || trim($row[0]) === '') {
            $errors[] = "第 {$rowCount} 行：赛道名称不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[1]) || trim($row[1]) === '') {
            $errors[] = "第 {$rowCount} 行：费用金额不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[2]) || trim($row[2]) === '') {
            $errors[] = "第 {$rowCount} 行：费用类型不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[3]) || trim($row[3]) === '') {
            $errors[] = "第 {$rowCount} 行：日期不能为空";
            $errorCount++;
            return;
        }
        
        // 验证数值字段
        $cost = trim($row[1]);
        // 替换逗号为小数点，支持国际化数字格式
        $cost = str_replace(',', '.', $cost);
        if (!preg_match('/^-?\d+(\.\d+)?$/', $cost)) {
            $errors[] = "第 {$rowCount} 行：费用金额不是有效的数字";
            $errorCount++;
            return;
        }
        
        // 验证日期格式
        if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $row[3])) {
            $errors[] = "第 {$rowCount} 行：日期格式不正确，应为YYYY-MM-DD";
            $errorCount++;
            return;
        }
        
        // 将日期格式化为标准的YYYY-MM-DD格式（确保月份和日期为两位数）
        $dateParts = explode('-', $row[3]);
        if (count($dateParts) === 3) {
            $year = $dateParts[0];
            $month = str_pad($dateParts[1], 2, '0', STR_PAD_LEFT);
            $day = str_pad($dateParts[2], 2, '0', STR_PAD_LEFT);
            $row[3] = "{$year}-{$month}-{$day}";
        }
        
        // 检查记录是否已存在
        $existingCosts = $this->trackCostsModel->getAllWithFilters(
            trim($row[0]),
            trim($row[2]),
            trim($row[3]),
            trim($row[3])
        );
        
        if (!empty($existingCosts)) {
            $errors[] = "第 {$rowCount} 行：该赛道、费用类型在该日期的记录已存在";
            $errorCount++;
            return;
        }
        
        $data[] = [
            'track_name' => trim($row[0]),
            'cost' => trim($row[1]),
            'cost_type' => trim($row[2]),
            'cost_date' => trim($row[3]),
            'remark' => isset($row[4]) ? trim($row[4]) : null
        ];
        $successCount++;
    }
    
    // 导出成本记录
    public function export() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $trackName = $_GET['track_name'] ?? '';
        $costType = $_GET['cost_type'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        $costs = $this->trackCostsModel->getAllWithFilters($trackName, $costType, $startDate, $endDate);
        
        if (empty($costs)) {
            showError('没有数据可导出');
            redirect(ADMIN_PANEL_URL . '/track_costs.php');
        }
        
        // 设置CSV头信息
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="track_costs_export_' . date('YmdHis') . '.csv"');
        
        // 创建输出流
        $output = fopen('php://output', 'w');
        
        // 写入BOM，确保Excel能正确识别UTF-8编码
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // 写入表头
        fputcsv($output, ['赛道名称', '费用金额', '费用类型', '日期', '备注']);
        
        // 写入数据
        foreach ($costs as $cost) {
            fputcsv($output, [
                $cost['track_name'],
                $cost['cost'],
                $cost['cost_type'],
                $cost['cost_date'],
                $cost['remark'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    // 统计功能
    public function statistics() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        // 获取上个月按赛道名称的费用统计
        $lastMonthByTrack = $this->trackCostsModel->getLastMonthByTrack();
        
        // 获取上个月按费用类型的费用统计
        $lastMonthByType = $this->trackCostsModel->getLastMonthByType();
        
        // 计算总费用
        $totalByTrack = 0;
        foreach ($lastMonthByTrack as $item) {
            $totalByTrack += (float)$item['total_cost'];
        }
        
        $totalByType = 0;
        foreach ($lastMonthByType as $item) {
            $totalByType += (float)$item['total_cost'];
        }
        
        $title = '赛道费用统计';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/track_costs/statistics.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
}
