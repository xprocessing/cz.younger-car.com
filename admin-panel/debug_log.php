<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>查看调试日志</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 1000px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        pre {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
            font-family: Consolas, Monaco, 'Andale Mono', 'Ubuntu Mono', monospace;
            font-size: 14px;
            line-height: 1.5;
        }
        .section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background-color: #0056b3;
        }
        .info {
            background-color: #d1ecf1;
            color: #0c5460;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>查看调试日志</h1>
        
        <div class="info">
            <h3>PHP内置服务器日志</h3>
            <p>在Windows环境下使用PHP内置服务器时，日志会直接显示在运行服务器的命令行窗口中。</p>
        </div>
        
        <h2>1. 查看PHP内置服务器日志</h2>
        <ol>
            <li>找到运行PHP内置服务器的命令行窗口（终端）</li>
            <li>在这个窗口中，您可以看到所有的请求和错误信息</li>
            <li>我们添加的调试信息会以类似 "PHP Notice: AIGCController::processImages() - 开始处理文件上传" 的形式显示</li>
        </ol>
        
        <h2>2. 实时查看日志</h2>
        <p>如果您的终端窗口已经关闭，需要重新启动服务器：</p>
        <pre>cd "c:\Users\hzf16\Desktop\cz.younger-car.com"
php -S localhost:8000 -t admin-panel</pre>
        
        <h2>3. 常见问题排查</h2>
        <div class="section">
            <h3>文件上传失败可能的原因：</h3>
            <ul>
                <li>临时目录不存在或不可写</li>
                <li>文件类型验证未通过</li>
                <li>文件大小超过限制</li>
                <li>move_uploaded_file函数执行失败</li>
            </ul>
        </div>
        
        <div class="section">
            <h3>快速检查上传状态：</h3>
            <form action="debug_log.php" method="POST" enctype="multipart/form-data">
                <input type="file" name="test_file" accept="image/*">
                <button type="submit">快速测试上传</button>
            </form>
            
            <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
                <div style="margin-top: 20px;">
                    <h4>上传结果：</h4>
                    <pre><?php print_r($_FILES); ?></pre>
                    <p>临时文件是否存在：<?php echo file_exists($_FILES['test_file']['tmp_name']) ? '是' : '否'; ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>