<?php
require_once APP_ROOT . '/config/config.php';
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
    
    // 模板功能已废弃，相关方法已删除
    
    // 调用阿里云百炼API处理图片
    public function callAliyunAPI($prompt, $image_data = null, $max_retries = 3, $retry_delay = 1000) {
        // 优化提示词，使用更简洁中立的表述，避免触发内容审核
        $safe_prompt = "图片处理任务：" . $prompt;
        
        // 根据阿里云API文档调整参数格式
        $payload = [
            'model' => 'wan2.6-image', // 使用正确的模型名称
            'input' => [
                'messages' => [
                    [
                        'role' => 'user',
                        'content' => []
                    ]
                ]
            ],
            'parameters' => [
                'prompt_extend' => true,
                'watermark' => false,
                'n' => 1,
                'enable_interleave' => true, // 文生图时设置为true
                'size' => '1024*1024',
                'stream' => true // 添加stream=true参数，API要求必须为true
            ]
        ];
        
        // 添加文本提示词
        $payload['input']['messages'][0]['content'][] = [
            'text' => $safe_prompt
        ];
        
        // 如果有图片数据，添加到content中
        if ($image_data) {
            $payload['input']['messages'][0]['content'][] = [
                'image' => $image_data
            ];
            // 对于有图片的情况，设置正确的参数
            $payload['parameters']['enable_interleave'] = false;
        } else {
            // 对于没有图片的情况（文生图），设置正确的参数
            $payload['parameters']['enable_interleave'] = true;
        }
        
        $headers = [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key,
            'x-dashscope-sse: enable' // 添加SSE支持头，与stream=true匹配
        ];
        
        $retry_count = 0;
        $last_error = null;
        
        while ($retry_count < $max_retries) {
            if ($retry_count > 0) {
                // 重试之间等待一段时间
                usleep($retry_delay * 1000); // 转换为微秒
                $retry_delay *= 2; // 指数退避
                error_log("API调用重试 ({$retry_count}/{$max_retries})");
            }
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $this->api_url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
            
            $start_time = microtime(true);
            $response = curl_exec($ch);
            $end_time = microtime(true);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curl_errno = curl_errno($ch);
            $curl_error = curl_error($ch);
            $execution_time = round(($end_time - $start_time) * 1000, 2);
            
            // 在PHP 8.0+中不再需要显式关闭curl资源，会自动释放
            
            // 添加更详细的调试信息
            error_log("[API调用] URL: " . $this->api_url);
            error_log("[API调用] 请求头: " . json_encode($headers));
            error_log("[API调用] 请求参数: " . json_encode($payload, JSON_UNESCAPED_UNICODE));
            error_log("[API调用] HTTP状态码: " . $http_code);
            error_log("[API调用] CURL错误码: " . $curl_errno);
            error_log("[API调用] CURL错误信息: " . $curl_error);
            error_log("[API调用] 响应长度: " . strlen($response ?? ''));
            error_log("[API调用] 执行时间: " . $execution_time . "ms");
            
            if (!empty($response)) {
                error_log("[API调用] 响应内容: " . substr($response, 0, 1000) . (strlen($response) > 1000 ? '...' : ''));
            } else {
                error_log("[API调用] 空响应");
            }
            
            // 检查CURL错误
            if ($curl_errno !== 0) {
                $last_error = "CURL错误 ({$curl_errno}): {$curl_error}";
                error_log("[API调用] 失败: {$last_error}");
                $retry_count++;
                continue;
            }
            
            // 检查HTTP状态码
            if ($http_code === 200) {
                // 处理流式响应（SSE格式）
                if (strpos($response, 'event:result') !== false) {
                    error_log("[API调用] 处理SSE格式响应");
                    
                    // 分割SSE事件
                    $events = explode("\n\n", $response);
                    $final_result = null;
                    
                    foreach ($events as $event) {
                        if (empty(trim($event))) continue;
                        
                        // 提取data字段
                        if (preg_match('/data:({.*})/s', $event, $matches)) {
                            $data = trim($matches[1]);
                            if (empty($data)) continue;
                            
                            // 解析单个data块的JSON
                            $event_result = json_decode($data, true);
                            if (json_last_error() !== JSON_ERROR_NONE) {
                                error_log("[API调用] SSE数据块JSON解析错误: " . json_last_error_msg() . "，数据: " . $data);
                                continue;
                            }
                            
                            // 保存最后一个结果
                            $final_result = $event_result;
                            
                            // 检查是否是最终结果
                            if (isset($event_result['output']['finished']) && $event_result['output']['finished'] === true) {
                                break;
                            }
                        }
                    }
                    
                    // 检查是否有有效的结果
                    if ($final_result) {
                        // 解析成功响应 - 检查是否有图像URL
                        if (isset($final_result['output']['choices'][0]['message']['content'])) {
                            $content = $final_result['output']['choices'][0]['message']['content'];
                            $images = [];
                            
                            // 提取所有图像URL
                            foreach ($content as $item) {
                                if (isset($item['image'])) {
                                    // 清理可能存在的反引号和空格
                                    $image_url = trim($item['image'], " `\n\r");
                                    $images[] = $image_url;
                                } elseif (isset($item['type']) && $item['type'] === 'image' && isset($item['image_url'])) {
                                    // 另一种可能的图像URL格式
                                    $image_url = trim($item['image_url'], " `\n\r");
                                    $images[] = $image_url;
                                }
                            }
                            
                            if (!empty($images)) {
                                error_log("[API调用] 成功获取 " . count($images) . " 个图像URL");
                                return [
                                    'success' => true,
                                    'data' => $images[0], // 返回第一张图像
                                    'all_images' => $images,
                                    'usage' => $final_result['usage'] ?? [],
                                    'request_id' => $final_result['request_id'] ?? '',
                                    'execution_time' => $execution_time
                                ];
                            }
                        }
                        
                        // 检查是否是文本响应（这可能是API的中间状态）
                        if (isset($final_result['output']['choices'][0]['message']['content'])) {
                            error_log("[API调用] SSE响应解析成功，但未找到图像URL，这可能是API的中间状态或提示词问题");
                            // 对于文生图任务，如果得到文本响应，可能是提示词需要优化
                            return [
                                'success' => false,
                                'error' => 'API返回文本响应而非图像，请检查提示词是否明确要求生成图像',
                                'response' => $final_result,
                                'execution_time' => $execution_time
                            ];
                        }
                    }
                    
                    error_log("[API调用] SSE响应解析完成，但未找到有效结果");
                    return [
                        'success' => false,
                        'error' => 'SSE响应解析失败，未找到有效结果',
                        'response' => $response,
                        'execution_time' => $execution_time
                    ];
                } else {
                    // 非SSE格式响应，尝试常规JSON解析
                    $result = json_decode($response, true);
                    
                    // 检查JSON解析是否成功
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        error_log("[API调用] JSON解析错误: " . json_last_error_msg());
                        $last_error = "API响应格式错误: JSON解析失败";
                        $retry_count++;
                        continue;
                    }
                    
                    // 解析成功响应 - 阿里云OSS图像链接格式
                    if (isset($result['output']['choices'][0]['message']['content'])) {
                        $content = $result['output']['choices'][0]['message']['content'];
                        $images = [];
                        
                        // 提取所有图像URL
                    foreach ($content as $item) {
                        if (isset($item['image'])) {
                            // 清理可能存在的反引号和空格
                            $image_url = trim($item['image'], " `\n\r");
                            $images[] = $image_url;
                        }
                    }
                        
                        if (!empty($images)) {
                            error_log("[API调用] 成功获取 " . count($images) . " 个图像URL");
                            return [
                                'success' => true,
                                'data' => $images[0], // 返回第一张图像
                                'all_images' => $images,
                                'usage' => $result['usage'] ?? [],
                                'request_id' => $result['request_id'] ?? '',
                                'execution_time' => $execution_time
                            ];
                        }
                    }
                }
                
                // 处理流式响应错误
                if (strpos($response, 'event:error') !== false) {
                    error_log("[API调用] SSE流式响应错误");
                    return [
                        'success' => false,
                        'error' => 'SSE流式响应错误',
                        'response' => $response,
                        'execution_time' => $execution_time
                    ];
                }
                
                // 如果以上格式都不匹配
                error_log("[API调用] 未知响应格式");
                return [
                    'success' => false,
                    'error' => 'API返回格式错误',
                    'response' => $result,
                    'execution_time' => $execution_time
                ];
            } else {
                // 尝试解析错误响应
                $error_result = json_decode($response, true);
                $error_msg = isset($error_result['message']) ? $error_result['message'] : 'API调用失败';
                $error_code = isset($error_result['code']) ? $error_result['code'] : 'UnknownError';
                
                error_log("[API调用] 失败: HTTP {$http_code}, 错误码: {$error_code}, 消息: {$error_msg}");
                
                // 检查是否是可以重试的错误
                $retryable_errors = ['RequestTimeOut', 'ServiceUnavailable', 'InternalServerError'];
                if (in_array($error_code, $retryable_errors)) {
                    $last_error = "API错误 ({$error_code}): {$error_msg}，将重试...";
                    $retry_count++;
                } else {
                    // 不可重试的错误，直接返回
                    return [
                        'success' => false,
                        'error' => "{$error_code}: {$error_msg}",
                        'response' => $response,
                        'http_code' => $http_code,
                        'error_code' => $error_code,
                        'execution_time' => $execution_time
                    ];
                }
            }
        }
        
        // 所有重试都失败
        error_log("[API调用] 所有 {$max_retries} 次重试都失败");
        return [
            'success' => false,
            'error' => $last_error ?? 'API调用失败，已重试 ' . $max_retries . ' 次',
            'response' => $response,
            'retry_count' => $retry_count
        ];
        
        
        /* 模拟API响应（需要时可以启用）
        if ($image_data === null) {
            // 文生图模拟响应，返回一个示例图片的base64编码
            // 使用一个简单的SVG图片作为示例
            $svg_image = '<svg xmlns="http://www.w3.org/2000/svg" width="1024" height="1024"><rect width="1024" height="1024" fill="#f0f0f0"/><text x="512" y="512" font-size="64" text-anchor="middle" dy=".3em" fill="#333">文生图示例</text><text x="512" y="600" font-size="32" text-anchor="middle" dy=".3em" fill="#666">这是一个模拟生成的图片</text></svg>';
            $image_data = base64_encode($svg_image);
        }
        
        return [
            'success' => true,
            'data' => $image_data, // 返回图片数据作为模拟结果
            'message' => '这是一个模拟的API响应，实际环境中将调用真实的阿里云API'
        ];
        */
    }
    
    // 批量去除瑕疵，调整亮度对比度
    public function batchRemoveDefect($images, $width = 1200, $height = 1200) {
        $results = [];
        
        foreach ($images as $index => $image) {
            try {
                // 验证图片文件是否存在
                if (!file_exists($image)) {
                    throw new Exception("图片文件不存在: {$image}");
                }
                
                // 读取并编码图片
                $image_data = base64_encode(file_get_contents($image));
                
                // 使用更简洁的提示词，避免触发内容审核
                $prompt = "请去除图片瑕疵，调整亮度对比度，保持{$width}x{$height}像素，返回jpg格式。";
                
                // 调用API
                $response = $this->callAliyunAPI($prompt, $image_data);
                
                // 记录结果
                $results[] = [
                    'original_image' => $image,
                    'processed' => $response['success'],
                    'result' => $response['success'] ? $response['data'] : null,
                    'error' => $response['success'] ? null : $response['error']
                ];
            } catch (Exception $e) {
                // 记录异常
                $results[] = [
                    'original_image' => $image,
                    'processed' => false,
                    'result' => null,
                    'error' => $e->getMessage()
                ];
                
                error_log("图片处理异常 ({$image}): " . $e->getMessage());
            }
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
    
    // 使用模板批量处理图片功能已废弃，相关方法已删除
    
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
    
    // 创建新任务
    public function createTask($user_id, $task_name, $task_type, $task_params, $total_count = 0, $original_filename = null, $original_path = null) {
        $sql = "INSERT INTO aigc_tasks (user_id, task_name, task_type, task_status, task_params, total_count, original_filename, original_path, started_at) VALUES (?, ?, ?, 'processing', ?, ?, ?, ?, NOW())";
        $params = [
            $user_id,
            $task_name,
            $task_type,
            json_encode($task_params),
            $total_count,
            $original_filename,
            $original_path
        ];
        
        $result = $this->db->query($sql, $params);
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    // 更新任务状态
    public function updateTaskStatus($task_id, $task_status, $success_count = null, $failed_count = null) {
        $sql = "UPDATE aigc_tasks SET task_status = ?";
        $params = [$task_status];
        
        if ($task_status == 'completed' || $task_status == 'failed') {
            $sql .= ", completed_at = NOW()";
        }
        
        if ($success_count !== null) {
            $sql .= ", success_count = ?";
            $params[] = $success_count;
        }
        
        if ($failed_count !== null) {
            $sql .= ", failed_count = ?";
            $params[] = $failed_count;
        }
        
        $sql .= " WHERE id = ?";
        $params[] = $task_id;
        
        return $this->db->query($sql, $params);
    }
    
    // 保存任务结果（合并到任务表中）
    public function saveTaskResult($task_id, $original_filename, $process_status, $result_url = null, $error_message = null) {
        $sql = "UPDATE aigc_tasks SET original_filename = ?, process_status = ?, result_url = ?, error_message = ? WHERE id = ?";
        $params = [
            $original_filename,
            $process_status,
            $result_url,
            $error_message,
            $task_id
        ];
        
        return $this->db->query($sql, $params);
    }
    
    // 获取用户的任务历史
    public function getUserTasks($user_id, $limit = 20, $offset = 0) {
        $sql = "SELECT * FROM aigc_tasks WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $stmt = $this->db->query($sql, [$user_id, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    // 获取任务的详细信息
    public function getTaskById($task_id) {
        $sql = "SELECT * FROM aigc_tasks WHERE id = ?";
        $stmt = $this->db->query($sql, [$task_id]);
        return $stmt->fetch();
    }
    
    // 获取任务的结果（现在直接从任务表中获取）
    public function getTaskResults($task_id) {
        $sql = "SELECT id, original_filename, original_path, process_status, result_url, error_message, created_at FROM aigc_tasks WHERE id = ?";
        $stmt = $this->db->query($sql, [$task_id]);
        $result = $stmt->fetch();
        // 返回数组格式，保持与原方法兼容
        return $result ? [$result] : [];
    }
    
    // 文生图
    public function textToImage($prompt, $width = 1024, $height = 1024) {
        $results = [];
        
        // 文生图不需要原始图片，直接调用API
        // 使用非常明确的提示词，确保API生成图像
        $api_prompt = "请根据以下描述生成一张高质量图片：{$prompt}，图片尺寸为{$width}x{$height}像素，保持图片清晰，色彩鲜艳。";
        
        $response = $this->callAliyunAPI($api_prompt);
        $results[] = [
            'original_image' => null,
            'processed' => $response['success'],
            'result' => $response['success'] ? $response['data'] : null,
            'error' => $response['success'] ? null : $response['error']
        ];
        
        return $results;
    }
    
    // 图生图
    public function imageToImage($images, $prompt, $strength = 0.5) {
        $results = [];
        
        foreach ($images as $index => $image) {
            $image_data = base64_encode(file_get_contents($image));
            // 根据strength参数生成不同的提示词，strength值越小，生成的图片越接近原图
            $strength_prompt = "根据原图生成新图片，相似度为{$strength}（值越小越接近原图），";
            $api_prompt = "{$strength_prompt}新图片的描述：{$prompt}，保持图片质量清晰。";
            
            $response = $this->callAliyunAPI($api_prompt, $image_data);
            $results[] = [
                'original_image' => $image,
                'processed' => $response['success'],
                'result' => $response['success'] ? $response['data'] : null,
                'error' => $response['success'] ? null : $response['error']
            ];
        }
        
        return $results;
    }
}
