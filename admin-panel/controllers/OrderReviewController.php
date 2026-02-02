<?php
require_once ADMIN_PANEL_DIR . '/models/OrderReview.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

class OrderReviewController {
    private $orderReviewModel;
    
    public function __construct() {
        $this->orderReviewModel = new OrderReview();
        session_start();
    }
    
    // 显示订单审核列表
    public function index() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        $reviewStatus = $_GET['review_status'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        if ($keyword || $reviewStatus || $startDate || $endDate) {
            $orderReviews = $this->orderReviewModel->searchWithFilters($keyword, $reviewStatus, $startDate, $endDate, $limit, $offset);
            $totalCount = $this->orderReviewModel->getSearchWithFiltersCount($keyword, $reviewStatus, $startDate, $endDate);
        } else {
            $orderReviews = $this->orderReviewModel->getAll($limit, $offset);
            $totalCount = $this->orderReviewModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $reviewStatusList = $this->orderReviewModel->getReviewStatusList();
        $countryList = $this->orderReviewModel->getCountryList();
        
        $title = '订单审核管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_review/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建订单审核页面
    public function create() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $title = '创建订单审核记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_review/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建订单审核请求
    public function createPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/order_review.php');
        }
        
        // 验证必填字段
        if (empty($_POST['global_order_no'])) {
            showError('订单号不能为空');
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=create');
        }
        
        if (empty($_POST['local_sku'])) {
            showError('本地SKU不能为空');
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=create');
        }
        
        if (empty($_POST['receiver_country_code'])) {
            showError('国家不能为空');
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=create');
        }
        
        // 检查记录是否已存在
        $existingOrder = $this->orderReviewModel->getByOrderNo($_POST['global_order_no']);
        if ($existingOrder) {
            showError('该订单号的审核记录已存在');
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=create');
        }
        
        // 处理JSON字段
        $wdYunfei = !empty($_POST['wd_yunfei']) ? json_encode($_POST['wd_yunfei']) : null;
        $emsYunfei = !empty($_POST['ems_yunfei']) ? json_encode($_POST['ems_yunfei']) : null;
        
        $data = [
            'store_id' => $_POST['store_id'] ?? null,
            'global_order_no' => $_POST['global_order_no'],
            'local_sku' => $_POST['local_sku'],
            'receiver_country_code' => $_POST['receiver_country_code'],
            'city' => $_POST['city'] ?? '',
            'postal_code' => $_POST['postal_code'] ?? '',
            'wd_yunfei' => $wdYunfei,
            'ems_yunfei' => $emsYunfei,
            'wid' => $_POST['wid'] ?? null,
            'logistics_type_id' => $_POST['logistics_type_id'] ?? null,
            'estimated_yunfei' => $_POST['estimated_yunfei'] ?? null,
            'review_status' => $_POST['review_status'] ?? null,
            'review_time' => $_POST['review_time'] ?? null,
            'review_remark' => $_POST['review_remark'] ?? null
        ];
        
        if ($this->orderReviewModel->create($data)) {
            showSuccess('订单审核记录创建成功');
        } else {
            showError('订单审核记录创建失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/order_review.php');
    }
    
    // 显示编辑订单审核页面
    public function edit() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(ADMIN_PANEL_URL . '/order_review.php');
        }
        
        $orderReview = $this->orderReviewModel->getById($id);
        if (!$orderReview) {
            showError('订单审核记录不存在');
            redirect(ADMIN_PANEL_URL . '/order_review.php');
        }
        
        // 解析JSON字段
        if (!empty($orderReview['wd_yunfei'])) {
            $orderReview['wd_yunfei'] = json_decode($orderReview['wd_yunfei'], true);
        }
        if (!empty($orderReview['ems_yunfei'])) {
            $orderReview['ems_yunfei'] = json_decode($orderReview['ems_yunfei'], true);
        }
        
        $title = '编辑订单审核记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_review/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑订单审核请求
    public function editPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/order_review.php');
        }
        
        $id = $_POST['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(ADMIN_PANEL_URL . '/order_review.php');
        }
        
        // 验证必填字段
        if (empty($_POST['global_order_no'])) {
            showError('订单号不能为空');
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['local_sku'])) {
            showError('本地SKU不能为空');
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['receiver_country_code'])) {
            showError('国家不能为空');
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=edit&id=' . $id);
        }
        
        // 检查记录是否已存在（排除当前记录）
        $existingOrder = $this->orderReviewModel->getByOrderNo($_POST['global_order_no']);
        if ($existingOrder && $existingOrder['id'] != $id) {
            showError('该订单号的审核记录已存在');
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=edit&id=' . $id);
        }
        
        // 处理JSON字段
        $wdYunfei = !empty($_POST['wd_yunfei']) ? json_encode($_POST['wd_yunfei']) : null;
        $emsYunfei = !empty($_POST['ems_yunfei']) ? json_encode($_POST['ems_yunfei']) : null;
        
        $data = [
            'store_id' => $_POST['store_id'] ?? null,
            'global_order_no' => $_POST['global_order_no'],
            'local_sku' => $_POST['local_sku'],
            'receiver_country_code' => $_POST['receiver_country_code'],
            'city' => $_POST['city'] ?? '',
            'postal_code' => $_POST['postal_code'] ?? '',
            'wd_yunfei' => $wdYunfei,
            'ems_yunfei' => $emsYunfei,
            'wid' => $_POST['wid'] ?? null,
            'logistics_type_id' => $_POST['logistics_type_id'] ?? null,
            'estimated_yunfei' => $_POST['estimated_yunfei'] ?? null,
            'review_status' => $_POST['review_status'] ?? null,
            'review_time' => $_POST['review_time'] ?? null,
            'review_remark' => $_POST['review_remark'] ?? null
        ];
        
        if ($this->orderReviewModel->update($id, $data)) {
            showSuccess('订单审核记录更新成功');
        } else {
            showError('订单审核记录更新失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/order_review.php');
    }
    
    // 处理删除订单审核请求
    public function delete() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $id = $_GET['id'] ?? 0;
        if (!$id) {
            showError('无效的ID');
            redirect(ADMIN_PANEL_URL . '/order_review.php');
        }
        
        $orderReview = $this->orderReviewModel->getById($id);
        if (!$orderReview) {
            showError('订单审核记录不存在');
            redirect(ADMIN_PANEL_URL . '/order_review.php');
        }
        
        if ($this->orderReviewModel->delete($id)) {
            showSuccess('订单审核记录删除成功');
        } else {
            showError('订单审核记录删除失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/order_review.php');
    }
    
    // 批量导入页面
    public function import() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $title = '批量导入订单审核记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/order_review/import.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理批量导入
    public function importPost() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            showError('无效的请求方式');
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=import');
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
                redirect(ADMIN_PANEL_URL . '/order_review.php?action=import');
            }
            
            $filePath = $file['tmp_name'];
            $handle = fopen($filePath, 'r');
            
            if (!$handle) {
                showError('无法读取文件');
                redirect(ADMIN_PANEL_URL . '/order_review.php?action=import');
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
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=import');
        }
        
        // 调试信息
        if (empty($data)) {
            if (!empty($errors)) {
                showError('没有有效数据可导入，错误信息：' . implode('<br>', $errors));
            } else {
                showError('没有有效数据可导入，共处理了 ' . $rowCount . ' 行，但没有通过验证的数据。请检查CSV格式是否正确。');
            }
            redirect(ADMIN_PANEL_URL . '/order_review.php?action=import');
        }
        
        try {
            $result = $this->orderReviewModel->batchInsert($data);
            showSuccess("成功导入 {$successCount} 条记录，跳过 {$errorCount} 条记录");
            
            if (!empty($errors)) {
                $_SESSION['import_errors'] = $errors;
            }
        } catch (Exception $e) {
            showError('导入失败：' . $e->getMessage());
        }
        
        redirect(ADMIN_PANEL_URL . '/order_review.php');
    }
    
    // 处理导入的单行数据
    private function processImportRow($row, &$data, &$rowCount, &$successCount, &$errorCount, &$errors) {
        // 验证必填字段
        if (empty($row[1]) || trim($row[1]) === '') {
            $errors[] = "第 {$rowCount} 行：订单号不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[2]) || trim($row[2]) === '') {
            $errors[] = "第 {$rowCount} 行：本地SKU不能为空";
            $errorCount++;
            return;
        }
        
        if (empty($row[3]) || trim($row[3]) === '') {
            $errors[] = "第 {$rowCount} 行：国家不能为空";
            $errorCount++;
            return;
        }
        
        // 检查记录是否已存在
        $existingOrder = $this->orderReviewModel->getByOrderNo(trim($row[1]));
        if ($existingOrder) {
            $errors[] = "第 {$rowCount} 行：该订单号的审核记录已存在";
            $errorCount++;
            return;
        }
        
        $importData = [
            'store_id' => isset($row[0]) ? trim($row[0]) : null,
            'global_order_no' => trim($row[1]),
            'local_sku' => trim($row[2]),
            'receiver_country_code' => trim($row[3]),
            'city' => isset($row[4]) ? trim($row[4]) : '',
            'postal_code' => isset($row[5]) ? trim($row[5]) : '',
            'wid' => isset($row[6]) && !empty(trim($row[6])) ? trim($row[6]) : null,
            'logistics_type_id' => isset($row[7]) && !empty(trim($row[7])) ? trim($row[7]) : null,
            'estimated_yunfei' => isset($row[8]) ? trim($row[8]) : null,
            'review_status' => isset($row[9]) ? trim($row[9]) : null,
            'review_time' => isset($row[10]) && !empty(trim($row[10])) ? trim($row[10]) : null,
            'review_remark' => isset($row[11]) ? trim($row[11]) : null
        ];
        
        $data[] = $importData;
        $successCount++;
    }
    
    // 批量导出
    public function export() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        // 获取所有筛选参数
        $keyword = $_GET['keyword'] ?? '';
        $reviewStatus = $_GET['review_status'] ?? '';
        $startDate = $_GET['start_date'] ?? '';
        $endDate = $_GET['end_date'] ?? '';
        
        // 获取所有符合条件的数据
        $orderReviews = $this->orderReviewModel->getAllWithFilters($keyword, $reviewStatus, $startDate, $endDate);
        
        // 设置CSV文件头
        $filename = 'order_review_export_' . date('YmdHis') . '.csv';
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
            '店铺ID',
            '订单号',
            '本地SKU',
            '国家',
            '城市',
            '邮编',
            '仓库ID',
            '物流方式ID',
            '预估邮费',
            '审单状态',
            '审单时间',
            '审单备注'
        ];
        fputcsv($output, $header);
        
        // 写入数据行
        foreach ($orderReviews as $orderReview) {
            $row = [
                $orderReview['store_id'] ?? '',
                $orderReview['global_order_no'],
                $orderReview['local_sku'],
                $orderReview['receiver_country_code'],
                $orderReview['city'] ?? '',
                $orderReview['postal_code'] ?? '',
                $orderReview['wid'] ?? '',
                $orderReview['logistics_type_id'] ?? '',
                $orderReview['estimated_yunfei'] ?? '',
                $orderReview['review_status'] ?? '',
                $orderReview['review_time'] ?? '',
                $orderReview['review_remark'] ?? ''
            ];
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}
?>