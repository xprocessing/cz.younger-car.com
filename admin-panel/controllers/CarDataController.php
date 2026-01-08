<?php
require_once APP_ROOT . '/models/CarData.php';
require_once APP_ROOT . '/helpers/functions.php';

class CarDataController {
    private $carDataModel;
    
    public function __construct() {
        $this->carDataModel = new CarData();
        session_start();
    }
    
    // 显示车型数据列表
    public function index() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        if (!hasPermission('car_data.view')) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        $make = $_GET['make'] ?? '';
        $make_cn = $_GET['make_cn'] ?? '';
        $model = $_GET['model'] ?? '';
        $year = $_GET['year'] ?? '';
        $market = $_GET['market'] ?? '';
        
        $filters = [
            'keyword' => $keyword,
            'make' => $make,
            'make_cn' => $make_cn,
            'model' => $model,
            'year' => $year,
            'market' => $market
        ];
        
        $carDataList = $this->carDataModel->getAll($filters, $limit, $offset);
        $totalCount = $this->carDataModel->getCount($filters);
        
        $totalPages = ceil($totalCount / $limit);
        $makeList = $this->carDataModel->getMakeList();
        $makeCnList = $this->carDataModel->getMakeCnList();
        $yearList = $this->carDataModel->getYearList();
        $marketList = $this->carDataModel->getMarketList();
        
        $title = '车型数据管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/car_data/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建车型数据页面
    public function create() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        if (!hasPermission('car_data.create')) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        $title = '创建车型数据';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/car_data/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建车型数据请求
    public function createPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        if (!hasPermission('car_data.create')) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/car_data.php');
        }
        
        $data = [
            'make' => $_POST['make'] ?? null,
            'make_cn' => $_POST['make_cn'] ?? null,
            'model' => $_POST['model'] ?? null,
            'year' => !empty($_POST['year']) ? (int)$_POST['year'] : null,
            'trim' => $_POST['trim'] ?? null,
            'trim_description' => $_POST['trim_description'] ?? null,
            'market' => $_POST['market'] ?? null
        ];
        
        if ($this->carDataModel->create($data)) {
            showSuccess('车型数据创建成功');
        } else {
            showError('车型数据创建失败');
        }
        
        redirect(APP_URL . '/car_data.php');
    }
    
    // 显示编辑车型数据页面
    public function edit($id) {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        if (!hasPermission('car_data.edit')) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        $carData = $this->carDataModel->getById($id);
        
        if (!$carData) {
            showError('车型数据不存在');
            redirect(APP_URL . '/car_data.php');
        }
        
        $title = '编辑车型数据';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/car_data/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑车型数据请求
    public function editPost($id) {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        if (!hasPermission('car_data.edit')) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/car_data.php');
        }
        
        $carData = $this->carDataModel->getById($id);
        
        if (!$carData) {
            showError('车型数据不存在');
            redirect(APP_URL . '/car_data.php');
        }
        
        $data = [
            'make' => $_POST['make'] ?? null,
            'make_cn' => $_POST['make_cn'] ?? null,
            'model' => $_POST['model'] ?? null,
            'year' => !empty($_POST['year']) ? (int)$_POST['year'] : null,
            'trim' => $_POST['trim'] ?? null,
            'trim_description' => $_POST['trim_description'] ?? null,
            'market' => $_POST['market'] ?? null
        ];
        
        if ($this->carDataModel->update($id, $data)) {
            showSuccess('车型数据更新成功');
        } else {
            showError('车型数据更新失败');
        }
        
        redirect(APP_URL . '/car_data.php');
    }
    
    // 处理删除车型数据请求
    public function delete($id) {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        if (!hasPermission('car_data.delete')) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        $carData = $this->carDataModel->getById($id);
        
        if (!$carData) {
            showError('车型数据不存在');
            redirect(APP_URL . '/car_data.php');
        }
        
        if ($this->carDataModel->delete($id)) {
            showSuccess('车型数据删除成功');
        } else {
            showError('车型数据删除失败');
        }
        
        redirect(APP_URL . '/car_data.php');
    }
    
    // 显示导入车型数据页面
    public function import() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        if (!hasPermission('car_data.create')) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        $title = '导入车型数据';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/car_data/import.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理导入车型数据请求
    public function importPost() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        if (!hasPermission('car_data.create')) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/car_data.php');
        }
        
        $dataArray = [];
        
        // 检查是否有文件上传
        $hasFile = isset($_FILES['import_file']) && $_FILES['import_file']['error'] !== UPLOAD_ERR_NO_FILE;
        // 检查是否有文本粘贴
        $hasText = !empty($_POST['import_text']);
        
        if (!$hasFile && !$hasText) {
            showError('请选择CSV文件或粘贴CSV文本');
            redirect(APP_URL . '/car_data.php?action=import');
        }
        
        if ($hasFile) {
            // 文件上传方式
            $file = $_FILES['import_file'];
            $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            if ($fileType !== 'csv') {
                showError('仅支持CSV文件导入');
                redirect(APP_URL . '/car_data.php?action=import');
            }
            
            $handle = fopen($file['tmp_name'], 'r');
            
            if ($handle) {
                // 跳过表头
                fgetcsv($handle);
                
                while (($data = fgetcsv($handle)) !== false) {
                    if (count($data) >= 7) {
                        $dataArray[] = [
                            'make' => $data[0] ?? null,
                            'make_cn' => $data[1] ?? null,
                            'model' => $data[2] ?? null,
                            'year' => !empty($data[3]) ? (int)$data[3] : null,
                            'trim' => $data[4] ?? null,
                            'trim_description' => $data[5] ?? null,
                            'market' => $data[6] ?? null
                        ];
                    }
                }
                
                fclose($handle);
            }
        }
        
        if ($hasText) {
            // 文本粘贴方式
            $csvText = $_POST['import_text'];
            
            // 检查并移除UTF-8 BOM
            if (substr($csvText, 0, 3) === chr(0xEF) . chr(0xBB) . chr(0xBF)) {
                $csvText = substr($csvText, 3);
            }
            
            // 按行分割文本
            $lines = explode("\n", $csvText);
            
            if (count($lines) >= 2) {
                // 跳过表头行
                array_shift($lines);
                
                foreach ($lines as $line) {
                    $line = trim($line);
                    if (empty($line)) continue;
                    
                    // 使用str_getcsv解析CSV行
                    $data = str_getcsv($line);
                    
                    if (count($data) >= 7) {
                        $dataArray[] = [
                            'make' => $data[0] ?? null,
                            'make_cn' => $data[1] ?? null,
                            'model' => $data[2] ?? null,
                            'year' => !empty($data[3]) ? (int)$data[3] : null,
                            'trim' => $data[4] ?? null,
                            'trim_description' => $data[5] ?? null,
                            'market' => $data[6] ?? null
                        ];
                    }
                }
            }
        }
        
        if (!empty($dataArray)) {
            if ($this->carDataModel->import($dataArray)) {
                showSuccess('车型数据导入成功，共导入 ' . count($dataArray) . ' 条记录');
            } else {
                showError('车型数据导入失败');
            }
        } else {
            showError('文件中没有有效数据');
        }
        
        redirect(APP_URL . '/car_data.php');
    }
    
    // 导出车型数据
    public function export() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        if (!hasPermission('car_data.view')) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        $keyword = $_GET['keyword'] ?? '';
        $make = $_GET['make'] ?? '';
        $make_cn = $_GET['make_cn'] ?? '';
        $model = $_GET['model'] ?? '';
        $year = $_GET['year'] ?? '';
        $market = $_GET['market'] ?? '';
        
        $filters = [
            'keyword' => $keyword,
            'make' => $make,
            'make_cn' => $make_cn,
            'model' => $model,
            'year' => $year,
            'market' => $market
        ];
        
        $carDataList = $this->carDataModel->getAll($filters);
        
        // 设置响应头
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=car_data_export_' . date('Ymd_His') . '.csv');
        
        // 创建输出流
        $output = fopen('php://output', 'w');
        
        // 添加UTF-8 BOM，确保中文显示正常
        fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));
        
        // 写入表头
        fputcsv($output, ['品牌(英文)', '品牌(中文)', '车型', '年份', '配置版本', '配置描述', '销售市场']);
        
        // 写入数据
        foreach ($carDataList as $carData) {
            fputcsv($output, [
                $carData['make'] ?? '',
                $carData['make_cn'] ?? '',
                $carData['model'] ?? '',
                $carData['year'] ?? '',
                $carData['trim'] ?? '',
                $carData['trim_description'] ?? '',
                $carData['market'] ?? ''
            ]);
        }
        
        fclose($output);
        exit;
    }
}
?>