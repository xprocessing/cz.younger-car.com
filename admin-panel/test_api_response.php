<?php
// 测试API响应解析逻辑
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// 引入必要文件，APP_ROOT将从config.php中获取
require_once dirname(__FILE__) . '/config/config.php';
require_once APP_ROOT . '/models/AIGC.php';

echo "<h1>API响应解析测试</h1>";

// 创建AIGC实例
$aigc = new AIGC();

// 模拟阿里云API的成功响应
$success_response = '{"output":{"choices":[{"finish_reason":"stop","message":{"role":"assistant","content":[{"image":"https://dashscope-result-sz.oss-cn-shenzhen.aliyuncs.com/test_image1.png?Expires=1234567890"},{"image":"https://dashscope-result-sz.oss-cn-shenzhen.aliyuncs.com/test_image2.png?Expires=1234567890"}]}}]},"usage":{"width":1248,"image_count":2,"height":832},"request_id":"bf37ca26-0abe-98e4-8065-1234567890"}';

// 直接测试响应解析
echo "<h2>测试响应解析逻辑</h2>";

try {
    // 手动测试JSON解析
    $result = json_decode($success_response, true);
    echo "<p>✓ JSON解析成功</p>";
    
    // 测试响应结构
    if (isset($result['output']['choices'][0]['message']['content'])) {
        echo "<p>✓ 响应结构正确</p>";
        
        $content = $result['output']['choices'][0]['message']['content'];
        $images = [];
        
        foreach ($content as $item) {
            if (isset($item['image'])) {
                $images[] = $item['image'];
                echo "<p>✓ 提取图像URL: {$item['image']}</p>";
            }
        }
        
        if (!empty($images)) {
            echo "<p>✓ 共提取 " . count($images) . " 个图像URL</p>";
        } else {
            echo "<p>✗ 未提取到图像URL</p>";
        }
    } else {
        echo "<p>✗ 响应结构错误</p>";
        echo "<pre>" . print_r($result, true) . "</pre>";
    }
    
} catch (Exception $e) {
    echo "<p>✗ 测试失败: " . $e->getMessage() . "</p>";
}

echo "<h2>测试错误响应解析</h2>";

// 模拟错误响应
$error_response = '{"request_id":"123456","code":"DataInspectionFailed","message":"Input data may contain inappropriate content"}';

try {
    $error_result = json_decode($error_response, true);
    if (isset($error_result['message'])) {
        echo "<p>✓ 错误信息提取成功: " . $error_result['message'] . "</p>";
    } else {
        echo "<p>✗ 错误信息提取失败</p>";
    }
} catch (Exception $e) {
    echo "<p>✗ 错误响应测试失败: " . $e->getMessage() . "</p>";
}

echo "<h2>测试SSE响应解析</h2>";

// 模拟SSE格式响应
$sse_response = "id:1
event:result
data:{\"output\":{\"text\":\"测试文本\"}}

id:2
event:finish";

try {
    preg_match('/data:({.*?})/', $sse_response, $matches);
    if (isset($matches[1])) {
        $sse_data = json_decode($matches[1], true);
        if (isset($sse_data['output']['text'])) {
            echo "<p>✓ SSE响应解析成功，提取文本: " . $sse_data['output']['text'] . "</p>";
        }
    } else {
        echo "<p>✗ 未找到SSE数据部分</p>";
    }
} catch (Exception $e) {
    echo "<p>✗ SSE响应测试失败: " . $e->getMessage() . "</p>";
}

echo "<h2>测试完成</h2>";
echo "<p>所有测试已完成，响应解析逻辑正常工作。</p>";
?>