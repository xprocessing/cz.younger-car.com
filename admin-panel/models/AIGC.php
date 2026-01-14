<?php
require_once APP_ROOT . '/includes/database.php';

class AIGC {
    private $db;
    private $api_id;
    private $api_key;
    private $api_url;
    
    public function __construct() {
        $this->db = Database::getInstance();
        $this->api_id = ALIYUN_API_ID;
        $this->api_key = ALIYUN_API_KEY;
        $this->api_url = 'https://dashscope.aliyuncs.com/api/v1/services/aigc/multimodal-generation/generation';
    }
    
    // 获取单个模板
    public function getTemplateById($id) {
        $sql = "SELECT * FROM aigc_templates WHERE id = ?";
        $stmt = $this->db->query($sql, [$id]);
        return $stmt->fetch();
    }
    
    // 获取所有模板
    public function getAllTemplates() {
        $sql = "SELECT * FROM aigc_templates ORDER BY created_at DESC";
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    // 根据类型获取模板
    public function getTemplatesByType($template_type) {
        $sql = "SELECT * FROM aigc_templates WHERE template_type = ? ORDER BY created_at DESC";
        $stmt = $this->db->query($sql, [$template_type]);
        return $stmt->fetchAll();
    }
    
    // 创建模板
    public function createTemplate($data) {
        $sql = "INSERT INTO aigc_templates (name, template_type, params, description, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())";
        $params = [
            $data['name'],
            $data['template_type'],
            $data['params'],
            $data['description'] ?? null
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 更新模板
    public function updateTemplate($id, $data) {
        $sql = "UPDATE aigc_templates SET name = ?, template_type = ?, params = ?, description = ?, updated_at = NOW() WHERE id = ?";
        $params = [
            $data['name'],
            $data['template_type'],
            $data['params'],
            $data['description'] ?? null,
            $id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 删除模板
    public function deleteTemplate($id) {
        $sql = "DELETE FROM aigc_templates WHERE id = ?";
        return $this->db->query($sql, [$id]);
    }
    
    // 调用阿里云百炼API处理图片
    public function callAliyunAPI($prompt, $image_data = null) {
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        ];
        
        $payload = [
            'model' => 'qwen-image',
            'input' => [
                'prompt' => $prompt
            ],
            'parameters' => [
                'seed' => 12345,
                'temperature' => 0.7,
                'top_p' => 0.9,
                'max_tokens' => 1024
            ]
        ];
        
        // 如果有图片数据，添加到payload
        if ($image_data) {
            $payload['input']['image'] = $image_data;
        }
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->api_url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($http_code !== 200) {
            return [
                'success' => false,
                'error' => 'API调用失败，HTTP状态码: ' . $http_code,
                'response' => $response
            ];
        }
        
        $result = json_decode($response, true);
        
        if (isset($result['output']['text'])) {
            return [
                'success' => true,
                'data' => $result['output']['text']
            ];
        } else {
            return [
                'success' => false,
                'error' => 'API返回格式错误',
                'response' => $result
            ];
        }
    }
    
    // 批量去除瑕疵，调整亮度对比度
    public function batchRemoveDefect($images, $width = 1200, $height = 1200) {
        $results = [];
        
        foreach ($images as $index => $image) {
            $image_data = base64_encode(file_get_contents($image));
            $prompt = "请去除这张图片的所有瑕疵，调整亮度和对比度使其更加清晰，并将图片尺寸调整为{$width}x{$height}像素，保存为jpg格式。";
            
            $response = $this->callAliyunAPI($prompt, $image_data);
            $results[] = [
                'original_image' => $image,
                'processed' => $response['success'],
                'result' => $response['success'] ? $response['data'] : null,
                'error' => $response['success'] ? null : $response['error']
            ];
        }
        
        return $results;
    }
    
    // 批量抠图 - 导出PNG
    public function batchCropToPNG($images) {
        $results = [];
        
        foreach ($images as $index => $image) {
            $image_data = base64_encode(file_get_contents($image));
            $prompt = "请将这张图片中的主体对象从背景中分离出来，导出为透明背景的PNG格式图片。";
            
            $response = $this->callAliyunAPI($prompt, $image_data);
            $results[] = [
                'original_image' => $image,
                'processed' => $response['success'],
                'result' => $response['success'] ? $response['data'] : null,
                'error' => $response['success'] ? null : $response['error']
            ];
        }
        
        return $results;
    }
    
    // 批量抠图 - 导出白底图
    public function batchCropToWhiteBackground($images, $width = 800, $height = 800, $subject_ratio = 0.8) {
        $results = [];
        
        foreach ($images as $index => $image) {
            $image_data = base64_encode(file_get_contents($image));
            $subject_ratio_percent = $subject_ratio * 100;
            $prompt = "请将这张图片中的主体对象从背景中分离出来，将其放置在{$width}x{$height}像素的白色背景上，确保主体对象占比约{$subject_ratio_percent}%，导出为JPG格式图片。";
            
            $response = $this->callAliyunAPI($prompt, $image_data);
            $results[] = [
                'original_image' => $image,
                'processed' => $response['success'],
                'result' => $response['success'] ? $response['data'] : null,
                'error' => $response['success'] ? null : $response['error']
            ];
        }
        
        return $results;
    }
    
    // 批量改尺寸
    public function batchResize($images, $resize_option) {
        $results = [];
        
        foreach ($images as $index => $image) {
            $image_data = base64_encode(file_get_contents($image));
            
            // 根据resize_option生成不同的提示词
            $size_prompt = $this->getResizePrompt($resize_option);
            $prompt = "请根据以下要求调整这张图片的尺寸：{$size_prompt}，保持图片比例正确，确保图片质量清晰。";
            
            $response = $this->callAliyunAPI($prompt, $image_data);
            $results[] = [
                'original_image' => $image,
                'processed' => $response['success'],
                'result' => $response['success'] ? $response['data'] : null,
                'error' => $response['success'] ? null : $response['error']
            ];
        }
        
        return $results;
    }
    
    // 批量打水印
    public function batchAddWatermark($images, $watermark_option) {
        $results = [];
        
        foreach ($images as $index => $image) {
            $image_data = base64_encode(file_get_contents($image));
            
            // 根据watermark_option生成不同的提示词
            $watermark_prompt = $this->getWatermarkPrompt($watermark_option);
            $prompt = "请根据以下要求为这张图片添加水印：{$watermark_prompt}，确保水印清晰可见但不影响图片主体内容。";
            
            $response = $this->callAliyunAPI($prompt, $image_data);
            $results[] = [
                'original_image' => $image,
                'processed' => $response['success'],
                'result' => $response['success'] ? $response['data'] : null,
                'error' => $response['success'] ? null : $response['error']
            ];
        }
        
        return $results;
    }
    
    // 批量模特换脸
    public function batchFaceSwap($images, $face_option) {
        $results = [];
        
        foreach ($images as $index => $image) {
            $image_data = base64_encode(file_get_contents($image));
            $prompt = "请将图片中的模特面部替换为指定的面部特征，保持整体风格一致，确保换脸效果自然真实。";
            
            $response = $this->callAliyunAPI($prompt, $image_data);
            $results[] = [
                'original_image' => $image,
                'processed' => $response['success'],
                'result' => $response['success'] ? $response['data'] : null,
                'error' => $response['success'] ? null : $response['error']
            ];
        }
        
        return $results;
    }
    
    // 生成多角度图片
    public function batchGenerateMultiAngle($images, $angles = [30, 60, 90, 120, 150, 180]) {
        $results = [];
        
        foreach ($images as $index => $image) {
            $image_data = base64_encode(file_get_contents($image));
            $angle_list = implode('度、', $angles);
            $angle_list .= '度';
            $prompt = "请基于这张图片生成多角度视图，包括以下角度：{$angle_list}，确保每个角度的视图都保持物体的完整性和比例正确性。";
            
            $response = $this->callAliyunAPI($prompt, $image_data);
            $results[] = [
                'original_image' => $image,
                'processed' => $response['success'],
                'result' => $response['success'] ? $response['data'] : null,
                'error' => $response['success'] ? null : $response['error']
            ];
        }
        
        return $results;
    }
    
    // 使用模板批量处理图片
    public function batchProcessWithTemplate($images, $template_id) {
        $template = $this->getTemplateById($template_id);
        if (!$template) {
            return ['success' => false, 'error' => '模板不存在'];
        }
        
        $params = json_decode($template['params'], true);
        $template_type = $template['template_type'];
        
        switch ($template_type) {
            case 'remove_defect':
                $width = $params['width'] ?? 1200;
                $height = $params['height'] ?? 1200;
                return $this->batchRemoveDefect($images, $width, $height);
            case 'crop':
                if ($params['output_type'] === 'png') {
                    return $this->batchCropToPNG($images);
                } else {
                    $width = $params['width'] ?? 800;
                    $height = $params['height'] ?? 800;
                    $subject_ratio = $params['subject_ratio'] ?? 0.8;
                    return $this->batchCropToWhiteBackground($images, $width, $height, $subject_ratio);
                }
            case 'resize':
                return $this->batchResize($images, $params);
            case 'watermark':
                return $this->batchAddWatermark($images, $params);
            case 'face_swap':
                return $this->batchFaceSwap($images, $params);
            case 'multi_angle':
                $angles = $params['angles'] ?? [30, 60, 90, 120, 150, 180];
                return $this->batchGenerateMultiAngle($images, $angles);
            default:
                return ['success' => false, 'error' => '不支持的模板类型'];
        }
    }
    
    // 辅助方法：生成改尺寸的提示词
    private function getResizePrompt($option) {
        if (isset($option['ratio'])) {
            return "将图片调整为{$option['ratio']}的比例";
        } elseif (isset($option['width']) && isset($option['height'])) {
            return "将图片调整为{$option['width']}x{$option['height']}像素的尺寸";
        } else {
            return "保持原图比例，调整图片尺寸";
        }
    }
    
    // 辅助方法：生成打水印的提示词
    private function getWatermarkPrompt($option) {
        $position = $option['position'] ?? '右下角';
        
        if (isset($option['text'])) {
            return "在图片{$position}添加文字水印：'{$option['text']}'";
        } elseif (isset($option['image_path'])) {
            return "在图片{$position}添加图片水印";
        } else {
            return "在图片{$position}添加水印";
        }
    }
    
    // 将base64编码的图片保存为文件
    public function saveBase64Image($base64_string, $output_path) {
        // 去除base64编码中的前缀
        $base64_string = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $base64_string);
        // 解码并保存文件
        return file_put_contents($output_path, base64_decode($base64_string));
    }
}
