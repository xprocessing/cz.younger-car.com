<?php
require_once APP_ROOT . '/models/AIGC.php';
require_once APP_ROOT . '/helpers/functions.php';

class AIGCController {
    private $aigcModel;
    
    public function __construct() {
        $this->aigcModel = new AIGC();
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
        
        // 处理图片
        $all_results = [];
        
        if (empty($process_types)) {
            showError('请至少选择一种处理类型');
            redirect(APP_URL . '/aigc.php');
        }
        
        // 对于每种选中的处理类型执行处理
        foreach ($process_types as $process_type) {
            // 处理文生图类型（不需要上传图片）
            if ($process_type === 'text_to_image') {
                $text_prompt = $_POST['text_prompt'] ?? '';
                $image_width = (int)($_POST['image_width'] ?? 1024);
                $image_height = (int)($_POST['image_height'] ?? 1024);
                
                if (empty($text_prompt)) {
                    showError('请输入文生图的文字描述');
                    redirect(APP_URL . '/aigc.php');
                }
                
                // 文生图不需要原始图片
                $results = $this->aigcModel->textToImage($text_prompt, $image_width, $image_height);
                
                // 将结果添加到所有结果中
                if (!empty($results)) {
                    $all_results[$process_type] = $results;
                }
                
                continue;
            }
            
            // 如果不是文生图类型，需要确保有图片可以处理
            if (empty($processed_images)) {
                continue;
            }
            
            switch ($process_type) {
                case 'remove_defect':
                    // 批量去除瑕疵，调整亮度对比度
                    $width = (int)($_POST['remove_defect_width'] ?? 1200);
                    $height = (int)($_POST['remove_defect_height'] ?? 1200);
                    $results = $this->aigcModel->batchRemoveDefect($processed_images, $width, $height);
                    break;
                    
                case 'crop_png':
                    // 批量抠图 - 导出PNG
                    $results = $this->aigcModel->batchCropToPNG($processed_images);
                    break;
                    
                case 'crop_white_bg':
                    // 批量抠图 - 导出白底图
                    $width = (int)($_POST['crop_white_bg_width'] ?? 800);
                    $height = (int)($_POST['crop_white_bg_height'] ?? 800);
                    $subject_ratio = (float)($_POST['crop_white_bg_subject_ratio'] ?? 0.8);
                    $results = $this->aigcModel->batchCropToWhiteBackground($processed_images, $width, $height, $subject_ratio);
                    break;
                    
                case 'resize':
                    // 批量改尺寸
                    $resize_option = [];
                    if (!empty($_POST['resize_ratio'])) {
                        $resize_option['ratio'] = $_POST['resize_ratio'];
                    } elseif (!empty($_POST['resize_width']) && !empty($_POST['resize_height'])) {
                        $resize_option['width'] = (int)$_POST['resize_width'];
                        $resize_option['height'] = (int)$_POST['resize_height'];
                    }
                    $results = $this->aigcModel->batchResize($processed_images, $resize_option);
                    break;
                    
                case 'watermark':
                    // 批量打水印
                    $watermark_option = [
                        'position' => $_POST['watermark_position'] ?? '右下角'
                    ];
                    
                    if (!empty($_POST['watermark_text'])) {
                        $watermark_option['text'] = $_POST['watermark_text'];
                    } elseif (!empty($_FILES['watermark_image']['tmp_name'])) {
                        $watermark_image = $_FILES['watermark_image'];
                        if ($watermark_image['error'] === UPLOAD_ERR_OK) {
                            $watermark_option['image_path'] = $watermark_image['tmp_name'];
                        }
                    }
                    
                    $results = $this->aigcModel->batchAddWatermark($processed_images, $watermark_option);
                    break;
                    
                case 'face_swap':
                    // 批量模特换脸
                    $face_option = [];
                    // 这里可以根据实际需求添加更多换脸参数
                    $results = $this->aigcModel->batchFaceSwap($processed_images, $face_option);
                    break;
                    
                case 'multi_angle':
                    // 生成多角度图片
                    $angles = explode(',', $_POST['angles'] ?? '30,60,90,120,150,180');
                    $angles = array_map('intval', $angles);
                    $results = $this->aigcModel->batchGenerateMultiAngle($processed_images, $angles);
                    break;
                    
                // 模板功能已废弃，不再支持
                case 'use_template':
                    showError('模板功能已废弃，不再支持');
                    redirect(APP_URL . '/aigc.php');
                    break;
                    
                case 'image_to_image':
                    // 图生图
                    $image_prompt = $_POST['image_prompt'] ?? '';
                    $image_strength = (float)($_POST['image_strength'] ?? 0.5);
                    
                    if (empty($image_prompt)) {
                        showError('请输入图生图的文字描述');
                        redirect(APP_URL . '/aigc.php');
                    }
                    
                    $results = $this->aigcModel->imageToImage($processed_images, $image_prompt, $image_strength);
                    break;
                    
                default:
                    // 跳过无效的处理类型
                    continue 2;
            }
            
            // 将当前处理类型的结果添加到所有结果中
            if (!empty($results)) {
                $all_results[$process_type] = $results;
            }
        }
        
        // 如果没有任何处理结果
        if (empty($all_results)) {
            showError('所有处理类型都处理失败');
            redirect(APP_URL . '/aigc.php');
        }
        
        // 将所有结果合并为一个数组，用于显示
        $results = [];
        foreach ($all_results as $type => $type_results) {
            foreach ($type_results as $result) {
                $result['process_type'] = $type;
                $results[] = $result;
            }
        }
        
        // 清理临时文件
        foreach ($processed_images as $image_path) {
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // 保存任务结果到数据库
        $user_id = $_SESSION['user_id'];
        $task_name = "图片处理任务 - " . date('Y-m-d H:i:s');
        $task_params = [
            'process_types' => $process_types,
            'processed_images_count' => count($processed_images),
            'params' => $_POST
        ];
        
        // 计算成功和失败的数量
        $success_count = 0;
        $failed_count = 0;
        foreach ($results as $result) {
            if ($result['processed']) {
                $success_count++;
            } else {
                $failed_count++;
            }
        }
        
        // 创建任务
        $task_id = $this->aigcModel->createTask($user_id, $task_name, $process_types[0], $task_params, count($results));
        
        // 保存每个结果
        if ($task_id) {
            foreach ($results as $result) {
                $this->aigcModel->saveTaskResult(
                    $task_id,
                    $result['original_image'] ? basename($result['original_image']) : 'text_to_image',
                    $result['processed'] ? 'success' : 'failed',
                    $result['processed'] ? $result['result'] : null,
                    $result['processed'] ? null : $result['error']
                );
            }
            
            // 更新任务状态
            $this->aigcModel->updateTaskStatus($task_id, 'completed', $success_count, $failed_count);
        }
        
        // 处理完成后跳转到任务历史页面，显示所有任务结果
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
}
