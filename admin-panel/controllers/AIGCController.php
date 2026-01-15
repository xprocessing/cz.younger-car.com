<?php
require_once APP_ROOT . '/models/AIGC.php';
require_once APP_ROOT . '/helpers/functions.php';
require_once APP_ROOT . '/includes/Logger.php';

class AIGCController {
    private $aigcModel;
    private $logger;
    
    public function __construct() {
        $this->aigcModel = new AIGC();
        $this->logger = Logger::getInstance();
        session_start();
    }
    
    // 显示AI图片处理模块主页面
    public function index() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $title = 'AI图片处理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/aigc/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理图片上传和批量处理
    public function processImages() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/aigc.php');
        }
        
        // 获取处理类型
        $process_types = $_POST['process_types'] ?? [];
        
        // 检查是否包含文生图类型
        $has_text_to_image = in_array('text_to_image', $process_types);
        
        $processed_images = [];
        
        // 如果不是纯文生图请求，需要验证文件上传
        if (!$has_text_to_image || (count($process_types) > 1 && $has_text_to_image)) {
            // 验证文件上传
            if (!isset($_FILES['images']) || !isset($_FILES['images']['tmp_name']) || !is_array($_FILES['images']['tmp_name']) || empty($_FILES['images']['tmp_name'])) {
                showError('请选择要上传的图片');
                redirect(APP_URL . '/aigc.php');
            }
            
            $images = $_FILES['images'];
            $temp_dir = APP_ROOT . '/public/temp/';
            
            // 创建临时目录（如果不存在）
            if (!is_dir($temp_dir)) {
                mkdir($temp_dir, 0755, true);
            }
            
            // 保存上传的图片到临时目录
            for ($i = 0; $i < count($images['tmp_name']); $i++) {
                if ($images['error'][$i] === UPLOAD_ERR_OK) {
                    $file_name = $images['name'][$i];
                    $file_tmp = $images['tmp_name'][$i];
                    $file_type = $images['type'][$i];
                    $file_size = $images['size'][$i];
                    
                    // 验证图片类型
                    $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($file_type, $allowed_types)) {
                        showError('不支持的图片格式：' . $file_name);
                        continue;
                    }
                    
                    // 验证图片大小（限制2MB）
                    if ($file_size > 2 * 1024 * 1024) {
                        showError('图片过大：' . $file_name . '，限制2MB');
                        continue;
                    }
                    
                    // 生成唯一文件名
                    $unique_name = uniqid() . '_' . $file_name;
                    $target_path = $temp_dir . $unique_name;
                    
                    if (move_uploaded_file($file_tmp, $target_path)) {
                        $processed_images[] = $target_path;
                    } else {
                        showError('上传失败：' . $file_name);
                    }
                }
            }
            
            // 如果不是纯文生图请求，需要确保有图片被上传
            if (empty($processed_images)) {
                showError('没有有效的图片被上传');
                redirect(APP_URL . '/aigc.php');
            }
        }
        
        // 如果只是纯文生图请求，不需要处理上传的图片
        if ($has_text_to_image && count($process_types) === 1) {
            $processed_images = [];
        }
        
        // 保存任务结果到数据库
        $user_id = $_SESSION['user_id'];
        $task_name = "图片处理任务 - " . date('Y-m-d H:i:s');
        $task_params = [
            'process_types' => $process_types,
            'processed_images_count' => count($processed_images),
            'params' => $_POST,
            'images' => $processed_images // 保存临时图片路径
        ];
        
        // 创建任务
        $task_id = $this->aigcModel->createTask($user_id, $task_name, $process_types[0], $task_params, count($processed_images) > 0 ? count($processed_images) : 1);
        
        if (!$task_id) {
            $this->logger->error("创建任务失败", [
                'user_id' => $user_id,
                'task_name' => $task_name,
                'process_types' => $process_types
            ]);
            showError('创建任务失败');
            redirect(APP_URL . '/aigc.php');
        }
        
        // 记录任务创建成功
        $this->logger->task("create", $task_id, $task_name, $process_types[0], $user_id, [
            'processed_images_count' => count($processed_images),
            'process_types' => $process_types
        ]);
        
        // 使用异步处理图片，提高用户体验
        // 创建一个临时文件来存储处理参数
        $temp_task_file = APP_ROOT . '/public/temp/task_' . $task_id . '.json';
        file_put_contents($temp_task_file, json_encode([
            'task_id' => $task_id,
            'process_types' => $process_types,
            'processed_images' => $processed_images,
            'post_params' => $_POST,
            'user_id' => $user_id
        ]));
        
        // 启动异步处理进程
        $php_executable = PHP_BINARY;
        $worker_script = APP_ROOT . '/admin-panel/scripts/process_images_worker.php';
        
        // 使用exec启动异步进程
        exec("$php_executable $worker_script $temp_task_file > /dev/null 2>&1 &");
        
        // 立即返回任务历史页面，用户可以在那里查看进度
        redirect(APP_URL . '/aigc.php?action=taskHistory');
        exit();
    }
    
    // 模板管理相关方法已废弃
    
    // 显示任务历史页面
    public function taskHistory() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $user_id = $_SESSION['user_id'];
        $tasks = $this->aigcModel->getUserTasks($user_id);
        $title = '任务历史';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/aigc/task_history.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示任务详情
    public function taskDetail() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $task_id = (int)($_GET['id'] ?? 0);
        if ($task_id <= 0) {
            showError('无效的任务ID');
            redirect(APP_URL . '/aigc.php?action=taskHistory');
        }
        
        $task = $this->aigcModel->getTaskById($task_id);
        $results = $this->aigcModel->getTaskResults($task_id);
        $title = '任务详情';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/aigc/task_detail.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 获取任务详情的JSON数据（用于AJAX请求）
    public function getTaskDetail() {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => '未登录']);
            exit();
        }
        
        $task_id = (int)($_GET['id'] ?? 0);
        if ($task_id <= 0) {
            echo json_encode(['success' => false, 'message' => '无效的任务ID']);
            exit();
        }
        
        $task = $this->aigcModel->getTaskById($task_id);
        $results = $this->aigcModel->getTaskResults($task_id);
        
        if (!$task) {
            echo json_encode(['success' => false, 'message' => '任务不存在']);
            exit();
        }
        
        // 返回JSON数据
        echo json_encode(['success' => true, 'task' => $task, 'results' => $results]);
        exit();
    }
}
