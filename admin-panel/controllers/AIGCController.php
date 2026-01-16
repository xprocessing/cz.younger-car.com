<?php
require_once ADMIN_PANEL_DIR . '/models/AIGC.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';
require_once ADMIN_PANEL_DIR . '/includes/Logger.php';

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
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        // 使用VIEWS_DIR加载视图
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/aigc/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理图片
    public function processImages() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            showError('无效的请求方法');
            redirect(ADMIN_PANEL_URL . '/aigc.php');
        }
        
        // 检查是否选择了处理类型
        if (empty($_POST['process_types'])) {
            showError('请选择处理类型');
            redirect(ADMIN_PANEL_URL . '/aigc.php');
        }
        
        $process_types = $_POST['process_types'];
        $processed_images = [];
        
        // 检查是否有文生图类型
        $has_text_to_image = in_array('text_to_image', $process_types);
        
        // 如果不是纯文生图请求，需要处理上传的图片
        if (!$has_text_to_image || count($process_types) > 1) {
            // 检查是否上传了图片
            if (empty($_FILES['images']['name'][0])) {
                showError('请上传图片');
                redirect(ADMIN_PANEL_URL . '/aigc.php');
            }
            
            // 处理上传的图片
            $images = $_FILES['images'];
            
            // 创建临时目录（如果不存在）
            $temp_dir = PUBLIC_DIR . '/temp/';
            if (!file_exists($temp_dir)) {
                mkdir($temp_dir, 0755, true);
            }
            
            // 处理每个上传的图片
            for ($i = 0; $i < count($images['name']); $i++) {
                $image_name = $images['name'][$i];
                $image_tmp = $images['tmp_name'][$i];
                $image_size = $images['size'][$i];
                $image_error = $images['error'][$i];
                
                // 检查是否上传成功
                if ($image_error !== UPLOAD_ERR_OK) {
                    showError('图片上传失败: ' . $image_error);
                    redirect(ADMIN_PANEL_URL . '/aigc.php');
                }
                
                // 检查文件类型
                $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
                $allowed_exts = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (!in_array($image_ext, $allowed_exts)) {
                    showError('不支持的图片格式: ' . $image_ext);
                    redirect(ADMIN_PANEL_URL . '/aigc.php');
                }
                
                // 检查文件大小
                if ($image_size > 2 * 1024 * 1024) { // 2MB
                    showError('图片大小不能超过2MB');
                    redirect(ADMIN_PANEL_URL . '/aigc.php');
                }
                
                // 生成唯一文件名
                $unique_name = uniqid() . '_' . $image_name;
                $temp_path = $temp_dir . $unique_name;
                
                // 移动图片到临时目录
                if (!move_uploaded_file($image_tmp, $temp_path)) {
                    showError('保存图片失败');
                    redirect(ADMIN_PANEL_URL . '/aigc.php');
                }
                
                // 添加到处理列表
                $processed_images[] = $temp_path;
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
            redirect(ADMIN_PANEL_URL . '/aigc.php');
        }
        
        // 记录任务创建成功
        $this->logger->task("create", $task_id, $task_name, $process_types[0], $user_id, [
            'processed_images_count' => count($processed_images),
            'process_types' => $process_types
        ]);
        
        // 使用异步处理图片，提高用户体验
        // 创建一个临时文件来存储处理参数
        $temp_task_file = PUBLIC_DIR . '/temp/task_' . $task_id . '.json';
        file_put_contents($temp_task_file, json_encode([
            'task_id' => $task_id,
            'process_types' => $process_types,
            'processed_images' => $processed_images,
            'post_params' => $_POST,
            'user_id' => $user_id
        ]));
        
        // 启动异步处理进程
        $php_executable = PHP_BINARY;
        $worker_script = ADMIN_PANEL_DIR . '/scripts/process_images_worker.php';
        
        // 使用exec启动异步进程
        exec("$php_executable $worker_script $temp_task_file > /dev/null 2>&1 &");
        
        // 立即返回任务历史页面，用户可以在那里查看进度
        redirect(ADMIN_PANEL_URL . '/aigc.php?action=taskHistory');
        exit();
    }
    
    // 显示任务历史页面
    public function taskHistory() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $user_id = $_SESSION['user_id'];
        $tasks = $this->aigcModel->getUserTasks($user_id);
        $title = '任务历史';
        
        // 使用VIEWS_DIR加载视图
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/aigc/task_history.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示任务详情
    public function taskDetail() {
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $task_id = (int)($_GET['id'] ?? 0);
        if ($task_id <= 0) {
            showError('无效的任务ID');
            redirect(ADMIN_PANEL_URL . '/aigc.php?action=taskHistory');
        }
        
        $task = $this->aigcModel->getTaskById($task_id);
        $results = $this->aigcModel->getTaskResults($task_id);
        $title = '任务详情';
        
        // 使用VIEWS_DIR加载视图
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/aigc/task_history.php'; // 使用task_history.php代替缺失的task_detail.php
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
