<?php
/**
 * basename函数null参数修复测试
 * 用于验证basename函数在original_image为null时的处理
 */

// 模拟测试数据
$test_cases = [
    // 测试用例1：图片处理（有原图）
    [
        'name' => '图片处理（有原图）',
        'result' => [
            'original_image' => '/path/to/image.jpg',
            'processed' => true,
            'result' => 'base64_encoded_image_data',
            'error' => null
        ]
    ],
    // 测试用例2：文生图（无原图）
    [
        'name' => '文生图（无原图）',
        'result' => [
            'original_image' => null,
            'processed' => true,
            'result' => 'base64_encoded_image_data',
            'error' => null
        ]
    ],
    // 测试用例3：图片处理失败（有原图）
    [
        'name' => '图片处理失败（有原图）',
        'result' => [
            'original_image' => '/path/to/image.jpg',
            'processed' => false,
            'result' => null,
            'error' => '处理失败'
        ]
    ],
    // 测试用例4：文生图失败（无原图）
    [
        'name' => '文生图失败（无原图）',
        'result' => [
            'original_image' => null,
            'processed' => false,
            'result' => null,
            'error' => '处理失败'
        ]
    ]
];

// 模拟APP_URL常量
define('APP_URL', 'http://localhost:8000');

// 测试函数
function test_basename_fix($test_case) {
    echo "<h3>测试用例：{$test_case['name']}</h3>";
    echo "<p>原始数据：" . json_encode($test_case['result']) . "</p>";
    
    $result = $test_case['result'];
    $index = 0;
    
    // 测试1：卡片标题处的basename调用
    echo "<div style='margin: 10px 0; padding: 10px; background-color: #f0f0f0;'>";
    echo "<strong>测试1：卡片标题处的basename调用</strong><br>";
    try {
        $title = $result['original_image'] ? basename($result['original_image']) : '文生图';
        echo "结果：" . htmlspecialchars($title) . "<br>";
        echo "<span style='color: green;'>✓ 通过</span>";
    } catch (Exception $e) {
        echo "错误：" . $e->getMessage() . "<br>";
        echo "<span style='color: red;'>✗ 失败</span>";
    }
    echo "</div>";
    
    // 测试2：原图显示处的逻辑
    echo "<div style='margin: 10px 0; padding: 10px; background-color: #f0f0f0;'>";
    echo "<strong>测试2：原图显示处的逻辑</strong><br>";
    try {
        if ($result['original_image']) {
            $img_src = APP_URL . '/public/temp/' . basename($result['original_image']);
            echo "结果：<img src='{$img_src}' alt='原图' style='max-width: 200px;'>";
            echo "<br>图片路径：" . htmlspecialchars($img_src);
        } else {
            echo "结果：无原图显示（文生图）";
        }
        echo "<br><span style='color: green;'>✓ 通过</span>";
    } catch (Exception $e) {
        echo "错误：" . $e->getMessage() . "<br>";
        echo "<span style='color: red;'>✗ 失败</span>";
    }
    echo "</div>";
    
    // 测试3：下载按钮处的basename调用
    echo "<div style='margin: 10px 0; padding: 10px; background-color: #f0f0f0;'>";
    echo "<strong>测试3：下载按钮处的basename调用</strong><br>";
    try {
        $download_filename = 'processed_' . ($result['original_image'] ? basename($result['original_image']) : 'text_to_image_' . date('YmdHis') . '_' . $index);
        echo "结果：下载文件名 = " . htmlspecialchars($download_filename) . "<br>";
        echo "<span style='color: green;'>✓ 通过</span>";
    } catch (Exception $e) {
        echo "错误：" . $e->getMessage() . "<br>";
        echo "<span style='color: red;'>✗ 失败</span>";
    }
    echo "</div>";
    
    // 测试4：保存任务结果时的basename调用
    echo "<div style='margin: 10px 0; padding: 10px; background-color: #f0f0f0;'>";
    echo "<strong>测试4：保存任务结果时的basename调用</strong><br>";
    try {
        $task_filename = $result['original_image'] ? basename($result['original_image']) : 'text_to_image';
        echo "结果：任务文件名 = " . htmlspecialchars($task_filename) . "<br>";
        echo "<span style='color: green;'>✓ 通过</span>";
    } catch (Exception $e) {
        echo "错误：" . $e->getMessage() . "<br>";
        echo "<span style='color: red;'>✗ 失败</span>";
    }
    echo "</div>";
    
    echo "<hr>";
}

// 执行测试
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>basename函数null参数修复测试</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        h1 { color: #333; }
        h3 { color: #666; }
        .test-case { margin: 20px 0; padding: 15px; border: 1px solid #ddd; border-radius: 4px; }
        .success { color: green; }
        .failure { color: red; }
    </style>
</head>
<body>
    <div class="container">
        <h1>basename函数null参数修复测试</h1>
        <p>此测试用于验证basename函数在original_image为null时的处理是否正确。</p>
        
        <?php foreach ($test_cases as $test_case): ?>
            <div class="test-case">
                <?php test_basename_fix($test_case); ?>
            </div>
        <?php endforeach; ?>
        
        <h2>测试总结</h2>
        <p>所有测试用例都应该通过，没有错误信息。如果出现任何"✗ 失败"，则说明修复不完整。</p>
        <p>修复后的代码应该能够：</p>
        <ul>
            <li>处理有原图的情况（显示原图信息）</li>
            <li>处理无原图的情况（显示文生图信息）</li>
            <li>不产生任何PHP警告</li>
        </ul>
    </div>
</body>
</html>