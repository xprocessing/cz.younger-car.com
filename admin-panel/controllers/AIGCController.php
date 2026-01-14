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
        
        $templates = $this->aigcModel->getAllTemplates();
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
        
        // 验证文件上传
        if (empty($_FILES['images']['tmp_name'])) {
            showError('请选择要上传的图片');
            redirect(APP_URL . '/aigc.php');
        }
        
        $images = $_FILES['images'];
        $processed_images = [];
        $temp_dir = APP_ROOT . '/public/temp/';
        
        // 创建临时目录
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
                
                // 验证图片大小（限制10MB）
                if ($file_size > 10 * 1024 * 1024) {
                    showError('图片过大：' . $file_name . '，限制10MB');
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
        
        if (empty($processed_images)) {
            showError('没有有效的图片被上传');
            redirect(APP_URL . '/aigc.php');
        }
        
        // 处理图片
        $process_type = $_POST['process_type'] ?? '';
        $results = [];
        
        switch ($process_type) {
            case 'remove_defect':
                // 批量去除瑕疵，调整亮度对比度
                $width = (int)($_POST['width'] ?? 1200);
                $height = (int)($_POST['height'] ?? 1200);
                $results = $this->aigcModel->batchRemoveDefect($processed_images, $width, $height);
                break;
                
            case 'crop_png':
                // 批量抠图 - 导出PNG
                $results = $this->aigcModel->batchCropToPNG($processed_images);
                break;
                
            case 'crop_white_bg':
                // 批量抠图 - 导出白底图
                $width = (int)($_POST['width'] ?? 800);
                $height = (int)($_POST['height'] ?? 800);
                $subject_ratio = (float)($_POST['subject_ratio'] ?? 0.8);
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
                
            case 'use_template':
                // 使用模板批量处理
                $template_id = (int)($_POST['template_id'] ?? 0);
                if ($template_id > 0) {
                    $results = $this->aigcModel->batchProcessWithTemplate($processed_images, $template_id);
                } else {
                    showError('请选择有效的模板');
                    redirect(APP_URL . '/aigc.php');
                }
                break;
                
            default:
                showError('请选择有效的处理类型');
                redirect(APP_URL . '/aigc.php');
        }
        
        // 清理临时文件
        foreach ($processed_images as $image_path) {
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
        
        // 显示处理结果
        $title = '图片处理结果';
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/aigc/result.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 模板管理相关方法
    
    // 获取所有模板
    public function getTemplates() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $templates = $this->aigcModel->getAllTemplates();
        header('Content-Type: application/json');
        echo json_encode($templates);
        exit;
    }
    
    // 创建模板
    public function createTemplate() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/aigc.php');
        }
        
        // 验证必填字段
        if (empty($_POST['name'])) {
            showError('模板名称不能为空');
            redirect(APP_URL . '/aigc.php');
        }
        
        if (empty($_POST['template_type'])) {
            showError('模板类型不能为空');
            redirect(APP_URL . '/aigc.php');
        }
        
        if (empty($_POST['params'])) {
            showError('模板参数不能为空');
            redirect(APP_URL . '/aigc.php');
        }
        
        // 解析参数JSON
        $params = json_decode($_POST['params'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            showError('参数JSON格式错误');
            redirect(APP_URL . '/aigc.php');
        }
        
        $data = [
            'name' => $_POST['name'],
            'template_type' => $_POST['template_type'],
            'params' => json_encode($params),
            'description' => $_POST['description'] ?? null
        ];
        
        $result = $this->aigcModel->createTemplate($data);
        
        if ($result) {
            showSuccess('模板创建成功');
        } else {
            showError('模板创建失败');
        }
        
        redirect(APP_URL . '/aigc.php');
    }
    
    // 更新模板
    public function updateTemplate() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/aigc.php');
        }
        
        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            showError('无效的模板ID');
            redirect(APP_URL . '/aigc.php');
        }
        
        // 验证必填字段
        if (empty($_POST['name'])) {
            showError('模板名称不能为空');
            redirect(APP_URL . '/aigc.php');
        }
        
        if (empty($_POST['template_type'])) {
            showError('模板类型不能为空');
            redirect(APP_URL . '/aigc.php');
        }
        
        if (empty($_POST['params'])) {
            showError('模板参数不能为空');
            redirect(APP_URL . '/aigc.php');
        }
        
        // 解析参数JSON
        $params = json_decode($_POST['params'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            showError('参数JSON格式错误');
            redirect(APP_URL . '/aigc.php');
        }
        
        $data = [
            'name' => $_POST['name'],
            'template_type' => $_POST['template_type'],
            'params' => json_encode($params),
            'description' => $_POST['description'] ?? null
        ];
        
        $result = $this->aigcModel->updateTemplate($id, $data);
        
        if ($result) {
            showSuccess('模板更新成功');
        } else {
            showError('模板更新失败');
        }
        
        redirect(APP_URL . '/aigc.php');
    }
    
    // 删除模板
    public function deleteTemplate() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $id = (int)($_GET['id'] ?? 0);
        if ($id <= 0) {
            showError('无效的模板ID');
            redirect(APP_URL . '/aigc.php');
        }
        
        $result = $this->aigcModel->deleteTemplate($id);
        
        if ($result) {
            showSuccess('模板删除成功');
        } else {
            showError('模板删除失败');
        }
        
        redirect(APP_URL . '/aigc.php');
    }
}
