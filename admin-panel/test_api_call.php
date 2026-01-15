<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>API调用测试</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .section { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
        pre { background: #f4f4f4; padding: 10px; border-radius: 4px; overflow-x: auto; }
        button { background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; }
        button:hover { background: #0056b3; }
        input[type="file"] { margin: 10px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1>阿里云API调用测试</h1>
        
        <div class="section">
            <h2>API配置</h2>
            <form action="test_api_call.php" method="POST" enctype="multipart/form-data">
                <div>
                    <label>API Key:</label>
                    <input type="text" name="api_key" value="sk-236a30287aa1456188b522083787f6a4" style="width: 100%; padding: 8px; margin: 5px 0;">
                </div>
                
                <div>
                    <label>测试图片:</label>
                    <input type="file" name="test_image" accept="image/*" required>
                </div>
                
                <div>
                    <label>提示词:</label>
                    <textarea name="prompt" rows="3" style="width: 100%; padding: 8px; margin: 5px 0;">请去除图片瑕疵，调整亮度对比度，保持1200x1200像素，返回jpg格式。</textarea>
                </div>
                
                <button type="submit">测试API调用</button>
            </form>
        </div>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="section">
                <h2>测试结果</h2>
                
                <?php
                // 获取表单数据
                $api_key = $_POST['api_key'];
                $prompt = $_POST['prompt'];
                $image_file = $_FILES['test_image'];
                
                // 验证图片上传
                if ($image_file['error'] !== UPLOAD_ERR_OK) {
                    echo '<p style="color: red;">图片上传失败！</p>';
                    exit;
                }
                
                // 读取并编码图片
                $image_data = base64_encode(file_get_contents($image_file['tmp_name']));
                
                // API配置
                $api_url = 'https://dashscope.aliyuncs.com/api/v1/services/aigc/multimodal-generation/generation';
                $headers = [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . $api_key
                ];
                
                // 构建请求参数
                $payload = [
                    'model' => 'qwen-image',
                    'input' => [
                        'prompt' => $prompt,
                        'image' => $image_data
                    ],
                    'parameters' => [
                        'seed' => 12345,
                        'temperature' => 0.7,
                        'top_p' => 0.9,
                        'max_tokens' => 1024
                    ]
                ];
                
                echo '<h3>1. 请求信息</h3>';
                echo '<pre>API URL: ' . $api_url . '</pre>';
                echo '<pre>请求头: ' . json_encode($headers, JSON_PRETTY_PRINT) . '</pre>';
                echo '<pre>请求参数: ' . json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                
                // 发起API请求
                echo '<h3>2. 发送请求...</h3>';
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $api_url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload, JSON_UNESCAPED_UNICODE));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                
                $start_time = microtime(true);
                $response = curl_exec($ch);
                $end_time = microtime(true);
                
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $curl_errno = curl_errno($ch);
                $curl_error = curl_error($ch);
                
                curl_close($ch);
                
                // 显示请求结果
                echo '<h3>3. 请求结果</h3>';
                echo '<pre>HTTP状态码: ' . $http_code . '</pre>';
                echo '<pre>CURL错误码: ' . $curl_errno . '</pre>';
                echo '<pre>CURL错误信息: ' . $curl_error . '</pre>';
                echo '<pre>请求耗时: ' . round(($end_time - $start_time) * 1000, 2) . 'ms</pre>';
                
                echo '<h3>4. API响应</h3>';
                if (!empty($response)) {
                    // 尝试解析JSON响应
                    $response_json = json_decode($response, true);
                    if ($response_json) {
                        echo '<pre>API响应(JSON格式): ' . json_encode($response_json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . '</pre>';
                    } else {
                        echo '<pre>API响应(原始格式): ' . $response . '</pre>';
                    }
                } else {
                    echo '<pre>API响应: 空响应</pre>';
                }
                
                // 错误分析
                echo '<h3>5. 错误分析</h3>';
                if ($http_code === 200) {
                    echo '<p style="color: green;">API调用成功！</p>';
                    
                    // 检查是否有结果
                    if (isset($response_json['output']['text'])) {
                        echo '<p>成功获取到API返回结果！</p>';
                        
                        // 检查是否返回图片数据
                        if (strlen($response_json['output']['text']) > 100) {
                            echo '<h4>图片预览:</h4>';
                            echo '<img src="data:image/jpeg;base64,' . $response_json['output']['text'] . '" style="max-width: 100%; max-height: 400px; border: 1px solid #ccc; padding: 10px; border-radius: 4px;">';
                        }
                    }
                } else {
                    echo '<p style="color: red;">API调用失败！</p>';
                    
                    // 常见错误分析
                    if ($http_code === 400) {
                        echo '<p><strong>错误码400分析:</strong></p>';
                        echo '<ul>';
                        echo '<li>请求参数格式不正确</li>';
                        echo '<li>API密钥无效或过期</li>';
                        echo '<li>模型名称错误</li>';
                        echo '<li>输入内容不符合要求</li>';
                        echo '</ul>';
                    } elseif ($http_code === 401) {
                        echo '<p><strong>错误码401分析:</strong> API密钥无效或认证失败！</p>';
                    } elseif ($http_code === 403) {
                        echo '<p><strong>错误码403分析:</strong> 权限不足或请求被拒绝！</p>';
                    } elseif ($http_code === 429) {
                        echo '<p><strong>错误码429分析:</strong> API调用频率过高！</p>';
                    } else {
                        echo '<p><strong>错误码' . $http_code . '分析:</strong> 服务器错误，请稍后重试！</p>';
                    }
                    
                    echo '<p><strong>排查建议:</strong></p>';
                    echo '<ul>';
                    echo '<li>检查API密钥是否正确</li>';
                    echo '<li>确认阿里云账户状态正常</li>';
                    echo '<li>尝试使用更简单的提示词</li>';
                    echo '<li>减少图片尺寸或质量</li>';
                    echo '<li>检查网络连接是否正常</li>';
                    echo '</ul>';
                }
                
                // 调试信息
                echo '<h3>6. 调试信息</h3>';
                echo '<pre>PHP版本: ' . phpversion() . '</pre>';
                echo '<pre>OpenSSL版本: ' . OPENSSL_VERSION_TEXT . '</pre>';
                echo '<pre>cURL版本: ' . curl_version()['version'] . '</pre>';
                ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>