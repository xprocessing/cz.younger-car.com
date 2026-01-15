<?php
// 测试文件上传和API调用
require_once 'config/config.php';

// 确保temp目录存在并可写
$temp_dir = APP_ROOT . '/public/temp/';
if (!is_dir($temp_dir)) {
    mkdir($temp_dir, 0777, true);
    echo "创建临时目录成功: $temp_dir<br>";
} else {
    echo "临时目录已存在: $temp_dir<br>";
}

// 检查目录权限
if (is_writable($temp_dir)) {
    echo "临时目录可写<br>";
} else {
    echo "临时目录不可写！<br>";
    chmod($temp_dir, 0777);
    echo "已尝试修改权限为777<br>";
    if (is_writable($temp_dir)) {
        echo "权限修改成功<br>";
    } else {
        echo "权限修改失败<br>";
    }
}

// 测试文件上传处理
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['images'])) {
    echo "<h3>文件上传测试结果：</h3>";
    echo "收到的文件数量: " . count($_FILES['images']['tmp_name']) . "<br>";
    
    $processed_images = [];
    
    for ($i = 0; $i < count($_FILES['images']['tmp_name']); $i++) {
        echo "<h4>文件 #$i:</h4>";
        echo "文件名: " . $_FILES['images']['name'][$i] . "<br>";
        echo "文件类型: " . $_FILES['images']['type'][$i] . "<br>";
        echo "文件大小: " . $_FILES['images']['size'][$i] . " bytes<br>";
        echo "错误码: " . $_FILES['images']['error'][$i] . "<br>";
        echo "临时路径: " . $_FILES['images']['tmp_name'][$i] . "<br>";
        
        if ($_FILES['images']['error'][$i] === UPLOAD_ERR_OK) {
            // 验证文件类型
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (in_array($_FILES['images']['type'][$i], $allowed_types)) {
                echo "✓ 图片格式支持<br>";
            } else {
                echo "✗ 不支持的图片格式<br>";
                continue;
            }
            
            // 验证文件大小
            if ($_FILES['images']['size'][$i] <= 2 * 1024 * 1024) {
                echo "✓ 图片大小符合要求<br>";
            } else {
                echo "✗ 图片过大<br>";
                continue;
            }
            
            // 保存文件
            $unique_name = uniqid() . '_' . $_FILES['images']['name'][$i];
            $target_path = $temp_dir . $unique_name;
            
            if (move_uploaded_file($_FILES['images']['tmp_name'][$i], $target_path)) {
                echo "✓ 文件保存成功: $target_path<br>";
                $processed_images[] = $target_path;
            } else {
                echo "✗ 文件保存失败<br>";
            }
        } else {
            echo "✗ 上传错误: " . $_FILES['images']['error'][$i] . "<br>";
        }
        
        echo "<hr>";
    }
    
    if (!empty($processed_images)) {
        echo "<h3>✓ 总共有 " . count($processed_images) . " 个文件上传成功！</h3>";
        
        // 测试API调用
        echo "<h3>API调用测试：</h3>";
        require_once 'models/AIGC.php';
        $aigc = new AIGC();
        
        $image = $processed_images[0];
        $image_data = base64_encode(file_get_contents($image));
        $prompt = "请将这张图片转换为黑白图片。";
        
        echo "正在调用API...<br>";
        // API URL是私有属性，不直接显示
        echo "提示词: " . $prompt . "<br>";
        
        $start_time = microtime(true);
        $response = $aigc->callAliyunAPI($prompt, $image_data);
        $end_time = microtime(true);
        $execution_time = round(($end_time - $start_time) * 1000, 2);
        
        echo "API调用耗时: " . $execution_time . "ms<br>";
        
        if ($response['success']) {
            echo "<h4>✓ API调用成功！</h4>";
            
            if (isset($response['message'])) {
                echo "提示信息: " . $response['message'] . "<br>";
            }
            
            // 如果返回的是图片数据，显示预览
            if (isset($response['data']) && strlen($response['data']) > 100) {
                echo "<h5>处理结果预览：</h5>";
                echo "<img src='data:image/png;base64," . $response['data'] . "' style='max-width: 100%; max-height: 400px; border: 1px solid #ccc; padding: 10px; border-radius: 4px;'>";
            } else {
                echo "返回结果: " . htmlspecialchars($response['data']) . "<br>";
            }
            
            // 显示完整的响应结构（如果有）
            if (isset($response['full_response'])) {
                echo "<h5>完整响应结构：</h5>";
                echo "<pre>" . htmlspecialchars(print_r($response['full_response'], true)) . "</pre>";
            }
            
        } else {
            echo "<h4 style='color: red;'>✗ API调用失败！</h4>";
            echo "错误信息: " . htmlspecialchars($response['error']) . "<br>";
            
            // 显示详细的错误信息
            if (isset($response['response'])) {
                echo "<h5>详细响应：</h5>";
                echo "<pre style='background-color: #f5f5f5; padding: 10px; border-radius: 4px; max-height: 300px; overflow-y: auto;'>";
                if (is_array($response['response'])) {
                    echo htmlspecialchars(print_r($response['response'], true));
                } else {
                    echo htmlspecialchars($response['response']);
                }
                echo "</pre>";
            }
            
            // 提示调试信息
            echo "<p>调试信息已记录到服务器错误日志中。</p>";
        }
        
    } else {
        echo "<h3>✗ 没有文件上传成功！</h3>";
    }
} else {
    // 显示上传表单
    echo "<h2>文件上传测试</h2>";
    echo "<form method='post' enctype='multipart/form-data'>";
    echo "<input type='file' name='images[]' multiple accept='image/*'><br><br>";
    echo "<input type='submit' value='上传测试'>";
    echo "</form>";
}
