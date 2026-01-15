<?php
// 测试SSE响应解析逻辑
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>SSE响应解析测试</h1>";

// 创建一个简化的测试函数来验证响应解析逻辑
function testResponseParsing($response, $is_sse = false) {
    echo "<h2>" . ($is_sse ? "SSE格式响应" : "常规JSON响应") . "测试</h2>";
    
    echo "<h3>响应内容:</h3>";
    echo "<pre>" . htmlspecialchars(substr($response, 0, 500)) . (strlen($response) > 500 ? "..." : "") . "</pre>";
    
    // 处理SSE格式
    if ($is_sse) {
        echo "<p>✓ 识别为SSE格式响应</p>";
        
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
                    echo "<p>✗ SSE数据块JSON解析错误: " . json_last_error_msg() . "</p>";
                    continue;
                }
                
                // 保存最后一个结果
                $final_result = $event_result;
                
                // 检查是否是最终结果
                if (isset($event_result['output']['finished']) && $event_result['output']['finished'] === true) {
                    echo "<p>✓ 找到最终结果</p>";
                    break;
                }
            }
        }
        
        // 检查是否有有效的结果
        if ($final_result) {
            echo "<p>✓ 成功解析SSE响应</p>";
            
            // 解析成功响应 - 检查是否有图像URL
            if (isset($final_result['output']['choices'][0]['message']['content'])) {
                $content = $final_result['output']['choices'][0]['message']['content'];
                $images = [];
                
                echo "<h3>内容结构:</h3>";
                echo "<pre>" . print_r($content, true) . "</pre>";
                
                // 提取所有图像URL
                foreach ($content as $item) {
                    if (isset($item['image'])) {
                        $images[] = $item['image'];
                        echo "<p>✓ 提取图像URL: " . htmlspecialchars($item['image']) . "</p>";
                    } elseif (isset($item['type']) && $item['type'] === 'image' && isset($item['image_url'])) {
                        // 另一种可能的图像URL格式
                        $images[] = $item['image_url'];
                        echo "<p>✓ 提取图像URL: " . htmlspecialchars($item['image_url']) . "</p>";
                    }
                }
                
                if (!empty($images)) {
                    echo "<p>✓ 共提取 " . count($images) . " 个图像URL</p>";
                    return true;
                } else {
                    echo "<p>✗ 未提取到图像URL</p>";
                    return false;
                }
            } else {
                echo "<p>✗ 响应结构不完整</p>";
                echo "<pre>" . print_r($final_result, true) . "</pre>";
                return false;
            }
        } else {
            echo "<p>✗ 未找到有效的结果</p>";
            return false;
        }
    } else {
        // 非SSE格式响应，尝试常规JSON解析
        $result = json_decode($response, true);
        
        // 检查JSON解析是否成功
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo "<p>✗ JSON解析错误: " . json_last_error_msg() . "</p>";
            return false;
        }
        
        // 解析成功响应 - 阿里云OSS图像链接格式
        if (isset($result['output']['choices'][0]['message']['content'])) {
            echo "<p>✓ 成功解析常规JSON响应</p>";
            $content = $result['output']['choices'][0]['message']['content'];
            $images = [];
            
            echo "<h3>内容结构:</h3>";
            echo "<pre>" . print_r($content, true) . "</pre>";
            
            // 提取所有图像URL
            foreach ($content as $item) {
                if (isset($item['image'])) {
                    $images[] = $item['image'];
                    echo "<p>✓ 提取图像URL: " . htmlspecialchars($item['image']) . "</p>";
                }
            }
            
            if (!empty($images)) {
                echo "<p>✓ 共提取 " . count($images) . " 个图像URL</p>";
                return true;
            } else {
                echo "<p>✗ 未提取到图像URL</p>";
                return false;
            }
        } else {
            echo "<p>✗ 响应结构不完整</p>";
            echo "<pre>" . print_r($result, true) . "</pre>";
            return false;
        }
    }
}

// 测试1: 用户提供的成功响应格式
$success_response = '{"output":{"choices":[{"finish_reason":"stop","message":{"role":"assistant","content":[{"image":"https://dashscope-result-sz.oss-cn-shenzhen.aliyuncs.com/test_image1.png?Expires=1234567890"},{"image":"https://dashscope-result-sz.oss-cn-shenzhen.aliyuncs.com/test_image2.png?Expires=1234567890"}]}}]},"usage":{"width":1248,"image_count":2,"height":832},"request_id":"bf37ca26-0abe-98e4-8065-1234567890"}';

testResponseParsing($success_response, false);

echo "<hr>";

// 测试2: 模拟SSE格式的成功响应
$success_sse_response = "id:1\nevent:result\n:HTTP_STATUS/200\ndata:" . $success_response . "\n\nid:2\nevent:finish";

testResponseParsing($success_sse_response, true);

echo "<hr>";

echo "<h2>测试总结</h2>";
echo "<p>解析逻辑测试完成。如果所有测试都通过，说明我们的解析逻辑是正确的。</p>";
?>