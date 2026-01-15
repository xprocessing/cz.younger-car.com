<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文件上传测试</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f5f5f5;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="file"] {
            margin-bottom: 10px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        pre {
            background-color: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            overflow-x: auto;
            white-space: pre-wrap;
        }
        .section {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>文件上传测试</h1>
        
        <form action="test_file_upload.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="images">选择图片（支持批量上传）</label>
                <input type="file" id="images" name="images[]" multiple accept="image/*">
            </div>
            <button type="submit">上传测试</button>
        </form>
        
        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <div class="section">
                <h2>上传结果</h2>
                
                <h3>1. 原始 FILES 数据：</h3>
                <pre><?php print_r($_FILES); ?></pre>
                
                <h3>2. 上传文件检查：</h3>
                <?php if (isset($_FILES['images'])): ?>
                    <p>文件数量：<?php echo count($_FILES['images']['name']); ?></p>
                    
                    <?php for ($i = 0; $i < count($_FILES['images']['name']); $i++): ?>
                        <div style="margin-top: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 4px;">
                            <p>文件 #<?php echo $i + 1; ?>：</p>
                            <ul>
                                <li>文件名：<?php echo $_FILES['images']['name'][$i]; ?></li>
                                <li>临时路径：<?php echo $_FILES['images']['tmp_name'][$i]; ?></li>
                                <li>文件类型：<?php echo $_FILES['images']['type'][$i]; ?></li>
                                <li>文件大小：<?php echo $_FILES['images']['size'][$i]; ?> bytes</li>
                                <li>错误码：<?php echo $_FILES['images']['error'][$i]; ?></li>
                                <li>临时文件存在：<?php echo file_exists($_FILES['images']['tmp_name'][$i]) ? '是' : '否'; ?></li>
                            </ul>
                        </div>
                    <?php endfor; ?>
                    
                <?php else: ?>
                    <p>没有收到文件数据</p>
                <?php endif; ?>
                
                <h3>3. PHP 配置信息：</h3>
                <pre>
upload_max_filesize: <?php echo ini_get('upload_max_filesize'); ?>
post_max_size: <?php echo ini_get('post_max_size'); ?>
max_file_uploads: <?php echo ini_get('max_file_uploads'); ?>
file_uploads: <?php echo ini_get('file_uploads'); ?>
                    </pre>
                    
                <h3>4. POST 数据：</h3>
                <pre><?php print_r($_POST); ?></pre>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>