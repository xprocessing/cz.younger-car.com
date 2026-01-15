<?php
// 图片处理工作脚本
// 这个脚本将异步处理图片并更新任务状态

// 设置错误报告
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 设置脚本超时时间
set_time_limit(3600); // 1小时超时

// 初始化应用环境
define('APP_ROOT', realpath(dirname(__FILE__) . '/../..'));

try {
    // 检查命令行参数
    if ($argc < 2) {
        throw new Exception('缺少任务文件参数');
    }
    
    $task_file = $argv[1];
    
    if (!file_exists($task_file)) {
        throw new Exception('任务文件不存在: ' . $task_file);
    }
    
    // 读取任务参数
    $task_data = json_decode(file_get_contents($task_file), true);
    
    if (!$task_data) {
        throw new Exception('任务文件格式错误');
    }
    
    $task_id = $task_data['task_id'];
    $process_types = $task_data['process_types'];
    $processed_images = $task_data['processed_images'];
    $post_params = $task_data['post_params'];
    $user_id = $task_data['user_id'];
    
    // 包含必要的文件
    require_once APP_ROOT . '/admin-panel/config/config.php';
    require_once APP_ROOT . '/admin-panel/includes/database.php';
    require_once APP_ROOT . '/admin-panel/models/AIGC.php';
    require_once APP_ROOT . '/admin-panel/includes/Logger.php';
    
    // 初始化日志记录器
    $logger = Logger::getInstance();
    $logger->info("图片处理工作脚本启动", [
        'task_id' => $task_id,
        'process_types' => $process_types,
        'images_count' => count($processed_images)
    ]);
    
    // 初始化AIGC模型
    $aigcModel = new AIGC();
    
    // 处理图片
    $all_results = [];
    
    // 对于每种选中的处理类型执行处理
    foreach ($process_types as $process_type) {
        // 处理文生图类型（不需要上传图片）
        if ($process_type === 'text_to_image') {
            $text_prompt = $post_params['text_prompt'] ?? '';
            $image_width = (int)($post_params['image_width'] ?? 1024);
            $image_height = (int)($post_params['image_height'] ?? 1024);
            
            if (empty($text_prompt)) {
                throw new Exception('请输入文生图的文字描述');
            }
            
            // 文生图不需要原始图片
            $results = $aigcModel->textToImage($text_prompt, $image_width, $image_height);
            
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
                $width = (int)($post_params['remove_defect_width'] ?? 1200);
                $height = (int)($post_params['remove_defect_height'] ?? 1200);
                $results = $aigcModel->batchRemoveDefect($processed_images, $width, $height);
                break;
                
            case 'crop_png':
                // 批量抠图 - 导出PNG
                $results = $aigcModel->batchCropToPNG($processed_images);
                break;
                
            case 'crop_white_bg':
                // 批量抠图 - 导出白底图
                $width = (int)($post_params['crop_white_bg_width'] ?? 800);
                $height = (int)($post_params['crop_white_bg_height'] ?? 800);
                $subject_ratio = (float)($post_params['crop_white_bg_subject_ratio'] ?? 0.8);
                $results = $aigcModel->batchCropToWhiteBackground($processed_images, $width, $height, $subject_ratio);
                break;
                
            case 'resize':
                // 批量改尺寸
                $resize_option = [];
                if (!empty($post_params['resize_ratio'])) {
                    $resize_option['ratio'] = $post_params['resize_ratio'];
                } elseif (!empty($post_params['resize_width']) && !empty($post_params['resize_height'])) {
                    $resize_option['width'] = (int)$post_params['resize_width'];
                    $resize_option['height'] = (int)$post_params['resize_height'];
                }
                $results = $aigcModel->batchResize($processed_images, $resize_option);
                break;
                
            case 'watermark':
                // 批量打水印
                $watermark_option = [
                    'position' => $post_params['watermark_position'] ?? '右下角'
                ];
                
                if (!empty($post_params['watermark_text'])) {
                    $watermark_option['text'] = $post_params['watermark_text'];
                }
                
                $results = $aigcModel->batchAddWatermark($processed_images, $watermark_option);
                break;
                
            case 'face_swap':
                // 批量模特换脸
                $face_option = [];
                // 这里可以根据实际需求添加更多换脸参数
                $results = $aigcModel->batchFaceSwap($processed_images, $face_option);
                break;
                
            case 'multi_angle':
                // 生成多角度图片
                $angles = explode(',', $post_params['angles'] ?? '30,60,90,120,150,180');
                $angles = array_map('intval', $angles);
                $results = $aigcModel->batchGenerateMultiAngle($processed_images, $angles);
                break;
                
            case 'image_to_image':
                // 图生图
                $image_prompt = $post_params['image_prompt'] ?? '';
                $image_strength = (float)($post_params['image_strength'] ?? 0.5);
                
                if (empty($image_prompt)) {
                    throw new Exception('请输入图生图的文字描述');
                }
                
                $results = $aigcModel->imageToImage($processed_images, $image_prompt, $image_strength);
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
    
    // 将所有结果合并为一个数组，用于显示
    $results = [];
    foreach ($all_results as $type => $type_results) {
        foreach ($type_results as $result) {
            $result['process_type'] = $type;
            $results[] = $result;
        }
    }
    
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
    
    // 保存每个结果
    foreach ($results as $result) {
        $aigcModel->saveTaskResult(
            $task_id,
            $result['original_image'] ? basename($result['original_image']) : 'text_to_image',
            $result['processed'] ? 'success' : 'failed',
            $result['processed'] ? $result['result'] : null,
            $result['processed'] ? null : $result['error']
        );
    }
    
    // 更新任务状态
    $aigcModel->updateTaskStatus($task_id, $success_count > 0 ? 'completed' : 'failed', $success_count, $failed_count);
    
    // 清理临时文件
    foreach ($processed_images as $image_path) {
        if (file_exists($image_path)) {
            unlink($image_path);
        }
    }
    
    // 删除任务文件
    if (file_exists($task_file)) {
        unlink($task_file);
    }
    
    // 记录任务完成日志
    $logger->info("任务处理完成", [
        'task_id' => $task_id,
        'success_count' => $success_count,
        'failed_count' => $failed_count
    ]);
    
} catch (Exception $e) {
    // 记录异常
    if (isset($logger)) {
        $logger->exception($e, "图片处理工作脚本错误");
    } else {
        // 如果Logger未初始化，使用error_log
        error_log('图片处理工作脚本错误: ' . $e->getMessage());
        error_log('错误追踪: ' . $e->getTraceAsString());
    }
    
    // 如果有任务ID，更新任务状态为失败
    if (isset($task_id) && isset($aigcModel)) {
        $aigcModel->updateTaskStatus($task_id, 'failed', 0, 1);
        
        // 保存错误结果
        $aigcModel->saveTaskResult(
            $task_id,
            'error',
            'failed',
            null,
            $e->getMessage()
        );
    }
    
    // 清理临时文件
    if (isset($processed_images)) {
        foreach ($processed_images as $image_path) {
            if (file_exists($image_path)) {
                unlink($image_path);
            }
        }
    }
    
    if (isset($task_file) && file_exists($task_file)) {
        unlink($task_file);
    }
    
    exit(1);
}

exit(0);
