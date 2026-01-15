<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeoAI GC - AI图片处理</title>
    <!-- 引入Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
    <!-- 引入Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- 自定义样式 -->
    <style>
        :root {
            --primary-color: #6c5ce7;
            --secondary-color: #00cec9;
            --dark-bg: #1a1a2e;
            --darker-bg: #16213e;
            --card-bg: #1f2833;
            --text-color: #ffffff;
            --text-secondary: #b3b3b3;
            --border-color: #2c3e50;
        }
        
        body {
            background-color: var(--dark-bg);
            color: var(--text-color);
            font-family: 'Microsoft YaHei', sans-serif;
        }
        
        .app-container {
            display: flex;
            height: 100vh;
            overflow: hidden;
        }
        
        /* 侧边栏样式 */
        .sidebar {
            width: 250px;
            background-color: var(--darker-bg);
            padding: 20px 0;
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
        }
        
        .sidebar-header {
            padding: 0 20px 20px;
            border-bottom: 1px solid var(--border-color);
        }
        
        .sidebar-header h2 {
            color: var(--text-color);
            font-size: 24px;
            font-weight: 700;
            margin: 0;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 20px 0;
            flex-grow: 1;
        }
        
        .sidebar-nav li {
            margin-bottom: 5px;
        }
        
        .sidebar-nav a {
            display: flex;
            align-items: center;
            padding: 12px 20px;
            color: var(--text-secondary);
            text-decoration: none;
            font-size: 16px;
            transition: all 0.3s ease;
            border-left: 3px solid transparent;
        }
        
        .sidebar-nav a:hover {
            background-color: rgba(108, 92, 231, 0.1);
            color: var(--text-color);
            border-left-color: var(--primary-color);
        }
        
        .sidebar-nav a.active {
            background-color: rgba(108, 92, 231, 0.2);
            color: var(--text-color);
            border-left-color: var(--primary-color);
            font-weight: 600;
        }
        
        .sidebar-nav a i {
            margin-right: 12px;
            font-size: 18px;
            width: 20px;
            text-align: center;
        }
        
        /* 主内容区域样式 */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            overflow-y: auto;
        }
        
        .content-header {
            margin-bottom: 30px;
        }
        
        .content-header h1 {
            color: var(--text-color);
            font-size: 28px;
            font-weight: 700;
            margin: 0;
        }
        
        /* 卡片样式 */
        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        
        .card-header {
            background-color: rgba(108, 92, 231, 0.1);
            border-bottom: 1px solid var(--border-color);
            padding: 15px 20px;
            border-radius: 8px 8px 0 0;
        }
        
        .card-header h3 {
            color: var(--text-color);
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }
        
        .card-body {
            padding: 20px;
        }
        
        /* 表单样式 */
        .form-group label {
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .form-control {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 4px;
            padding: 10px 12px;
            transition: all 0.3s ease;
        }
        
        .form-control:focus {
            background-color: rgba(255, 255, 255, 0.15);
            border-color: var(--primary-color);
            color: var(--text-color);
            box-shadow: 0 0 0 0.2rem rgba(108, 92, 231, 0.25);
        }
        
        .form-control::placeholder {
            color: var(--text-secondary);
        }
        
        /* 按钮样式 */
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            border-radius: 4px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.4);
        }
        
        /* 上传区域样式 */
        .upload-area {
            border: 2px dashed var(--border-color);
            border-radius: 8px;
            padding: 40px 20px;
            text-align: center;
            transition: all 0.3s ease;
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .upload-area:hover {
            border-color: var(--primary-color);
            background-color: rgba(108, 92, 231, 0.1);
        }
        
        .upload-area i {
            font-size: 48px;
            color: var(--primary-color);
            margin-bottom: 15px;
        }
        
        /* 进度条样式 */
        .progress {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 4px;
            height: 8px;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, var(--primary-color), var(--secondary-color));
            border-radius: 4px;
        }
        
        /* 图片预览样式 */
        .image-preview {
            border: 1px solid var(--border-color);
            border-radius: 4px;
            padding: 10px;
            margin-bottom: 15px;
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .image-preview img {
            max-width: 100%;
            height: auto;
            border-radius: 4px;
        }
        
        /* 任务历史样式 */
        .task-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            margin-bottom: 10px;
            transition: all 0.3s ease;
        }
        
        .task-item:hover {
            border-color: var(--primary-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .task-info {
            flex-grow: 1;
        }
        
        .task-title {
            color: var(--text-color);
            font-weight: 600;
            margin-bottom: 5px;
        }
        
        .task-date {
            color: var(--text-secondary);
            font-size: 14px;
        }
        
        /* 响应式设计 */
        @media (max-width: 768px) {
            .app-container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                height: auto;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
            
            .sidebar-nav {
                display: flex;
                overflow-x: auto;
            }
            
            .sidebar-nav li {
                margin-bottom: 0;
                margin-right: 5px;
            }
            
            .sidebar-nav a {
                padding: 10px 15px;
                white-space: nowrap;
                border-left: none;
                border-bottom: 3px solid transparent;
            }
            
            .sidebar-nav a:hover,
            .sidebar-nav a.active {
                border-left: none;
                border-bottom-color: var(--primary-color);
            }
        }
        
        /* 错误/成功消息样式 */
        .alert {
            background-color: rgba(255, 255, 255, 0.1);
            border: 1px solid var(--border-color);
            color: var(--text-color);
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border-color: rgba(40, 167, 69, 0.5);
        }
        
        .alert-danger {
            background-color: rgba(220, 53, 69, 0.1);
            border-color: rgba(220, 53, 69, 0.5);
        }
        
        /* 快速模板样式 */
        .template-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .template-item {
            padding: 15px;
            background-color: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .template-item:hover {
            background-color: rgba(108, 92, 231, 0.1);
            border-color: var(--primary-color);
        }
        
        .template-item h4 {
            color: var(--text-color);
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        
        .template-item p {
            color: var(--text-secondary);
            font-size: 14px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- 侧边栏 -->
        <aside class="sidebar">
            <div class="sidebar-header">
                <h2>NeoAI GC</h2>
            </div>
            <ul class="sidebar-nav">
                <li><a href="#" class="active"><i class="fas fa-home"></i> 工作台</a></li>
                <li><a href="#"><i class="fas fa-font"></i> 文生图</a></li>
                <li><a href="#"><i class="fas fa-exchange-alt"></i> 图生图</a></li>
                <li><a href="#"><i class="fas fa-cut"></i> 批量抠图</a></li>
                <li><a href="#"><i class="fas fa-user-circle"></i> 智能换脸</a></li>
                <li><a href="#"><i class="fas fa-history"></i> 任务历史</a></li>
            </ul>
        </aside>
        
        <!-- 主内容区域 -->
        <main class="main-content">
            <!-- 成功/错误消息 -->
            <?php if (isset($_SESSION['success_msg'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['success_msg']; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['success_msg']); ?>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error_msg'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo $_SESSION['error_msg']; ?>
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <?php unset($_SESSION['error_msg']); ?>
            <?php endif; ?>
            
            <!-- 工作台内容 -->
            <section id="workspace" class="content-section active">
                <div class="content-header">
                    <h1>AI图片处理工作台</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h3>批量图片处理</h3>
                            </div>
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <!-- 图片上传 -->
                                    <div class="form-group">
                                        <label for="images">选择图片（支持批量上传）</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>拖拽图片到此处或点击上传</p>
                                            <input type="file" class="form-control-file" id="images" name="images[]" multiple accept="image/*" style="display: none;" required>
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('images').click()">选择图片</button>
                                        </div>
                                        <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过10MB</small>
                                    </div>
                                    
                                    <!-- 处理类型选择 -->
                                    <div class="form-group">
                                        <label>处理类型</label>
                                        <div class="row">
                                            <!-- 1. 批量去除瑕疵 -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="remove_defect" name="process_types[]" value="remove_defect">
                                                            <label class="form-check-label" for="remove_defect">批量去除瑕疵，调整亮度对比度</label>
                                                        </div>
                                                        <!-- 批量去除瑕疵参数 -->
                                                        <div id="remove_defect_params" class="mt-3">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="remove_defect_width">宽度（像素）</label>
                                                                        <input type="number" class="form-control" id="remove_defect_width" name="remove_defect_width" value="1200" min="100" max="4096">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="form-group">
                                                                        <label for="remove_defect_height">高度（像素）</label>
                                                                        <input type="number" class="form-control" id="remove_defect_height" name="remove_defect_height" value="1200" min="100" max="4096">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 2. 批量抠图 - 导出PNG -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="crop_png" name="process_types[]" value="crop_png">
                                                            <label class="form-check-label" for="crop_png">批量抠图 - 导出PNG</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 3. 批量抠图 - 导出白底图 -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="crop_white_bg" name="process_types[]" value="crop_white_bg">
                                                            <label class="form-check-label" for="crop_white_bg">批量抠图 - 导出白底图</label>
                                                        </div>
                                                        <!-- 批量抠图 - 导出白底图参数 -->
                                                        <div id="crop_white_bg_params" class="mt-3">
                                                            <div class="row">
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="crop_white_bg_width">宽度（像素）</label>
                                                                        <input type="number" class="form-control" id="crop_white_bg_width" name="crop_white_bg_width" value="800" min="100" max="4096">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="crop_white_bg_height">高度（像素）</label>
                                                                        <input type="number" class="form-control" id="crop_white_bg_height" name="crop_white_bg_height" value="800" min="100" max="4096">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-4">
                                                                    <div class="form-group">
                                                                        <label for="crop_white_bg_subject_ratio">主体占比（%）</label>
                                                                        <input type="number" class="form-control" id="crop_white_bg_subject_ratio" name="crop_white_bg_subject_ratio" value="80" min="50" max="95">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 4. 批量改尺寸 -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="resize" name="process_types[]" value="resize">
                                                            <label class="form-check-label" for="resize">批量改尺寸</label>
                                                        </div>
                                                        <!-- 批量改尺寸参数 -->
                                                        <div id="resize_params" class="mt-3">
                                                            <div class="form-group">
                                                                <label>改尺寸方式</label>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="resize_type" id="resize_ratio" value="ratio" checked>
                                                                    <label class="form-check-label" for="resize_ratio">按比例</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="resize_type" id="resize_pixel" value="pixel">
                                                                    <label class="form-check-label" for="resize_pixel">按像素</label>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- 比例选择 -->
                                                            <div id="ratio_options" class="mt-2">
                                                                <div class="form-group">
                                                                    <label for="resize_ratio_select">选择比例</label>
                                                                    <select class="form-control" id="resize_ratio_select" name="resize_ratio">
                                                                        <option value="4:3">4:3</option>
                                                                        <option value="3:4">3:4</option>
                                                                        <option value="16:9">16:9</option>
                                                                        <option value="9:16">9:16</option>
                                                                        <option value="1:1">1:1</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- 像素选择 -->
                                                            <div id="pixel_options" class="mt-2" style="display: none;">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="resize_width">宽度（像素）</label>
                                                                            <select class="form-control" id="resize_width" name="resize_width">
                                                                                <option value="1920">1920</option>
                                                                                <option value="1200">1200</option>
                                                                                <option value="800">800</option>
                                                                                <option value="400">400</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="form-group">
                                                                            <label for="resize_height">高度（像素）</label>
                                                                            <select class="form-control" id="resize_height" name="resize_height">
                                                                                <option value="1080">1080</option>
                                                                                <option value="1200">1200</option>
                                                                                <option value="800">800</option>
                                                                                <option value="400">400</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 5. 批量打水印 -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="watermark" name="process_types[]" value="watermark">
                                                            <label class="form-check-label" for="watermark">批量打水印</label>
                                                        </div>
                                                        <!-- 批量打水印参数 -->
                                                        <div id="watermark_params" class="mt-3">
                                                            <div class="form-group">
                                                                <label for="watermark_position">水印位置</label>
                                                                <select class="form-control" id="watermark_position" name="watermark_position">
                                                                    <option value="左上">左上</option>
                                                                    <option value="右上">右上</option>
                                                                    <option value="左下">左下</option>
                                                                    <option value="右下">右下</option>
                                                                    <option value="居中">居中</option>
                                                                </select>
                                                            </div>
                                                            
                                                            <div class="form-group">
                                                                <label>水印类型</label>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="watermark_type" id="text_watermark" value="text" checked>
                                                                    <label class="form-check-label" for="text_watermark">文字水印</label>
                                                                </div>
                                                                <div class="form-check">
                                                                    <input class="form-check-input" type="radio" name="watermark_type" id="image_watermark" value="image">
                                                                    <label class="form-check-label" for="image_watermark">图片水印</label>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- 文字水印参数 -->
                                                            <div id="text_watermark_params" class="mt-2">
                                                                <div class="form-group">
                                                                    <label for="watermark_text">水印文字</label>
                                                                    <input type="text" class="form-control" id="watermark_text" name="watermark_text" placeholder="请输入水印文字">
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- 图片水印参数 -->
                                                            <div id="image_watermark_params" class="mt-2" style="display: none;">
                                                                <div class="form-group">
                                                                    <label for="watermark_image">选择水印图片</label>
                                                                    <input type="file" class="form-control-file" id="watermark_image" name="watermark_image" accept="image/*">
                                                                    <small class="form-text text-muted">建议使用PNG格式的透明图片</small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 6. 批量模特换脸 -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="face_swap" name="process_types[]" value="face_swap">
                                                            <label class="form-check-label" for="face_swap">批量模特换脸</label>
                                                        </div>
                                                        <!-- 批量模特换脸参数 -->
                                                        <div id="face_swap_params" class="mt-3">
                                                            <div class="form-group">
                                                                <label for="model_image">选择模特图片</label>
                                                                <input type="file" class="form-control-file" id="model_image" name="model_image" accept="image/*">
                                                                <small class="form-text text-muted">请选择一张包含清晰人脸的模特图片</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 7. 生成多角度图片 -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="multi_angle" name="process_types[]" value="multi_angle">
                                                            <label class="form-check-label" for="multi_angle">生成多角度图片</label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 8. 图生图 -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="image_to_image" name="process_types[]" value="image_to_image">
                                                            <label class="form-check-label" for="image_to_image">图生图</label>
                                                        </div>
                                                        <!-- 图生图参数 -->
                                                        <div id="image_to_image_params" class="mt-3">
                                                            <div class="form-group">
                                                                <label for="image_prompt">图片描述</label>
                                                                <textarea class="form-control" id="image_prompt" name="image_prompt" rows="3" placeholder="请输入图片描述"></textarea>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="image_strength">图像相似度（0-1）</label>
                                                                <input type="number" class="form-control" id="image_strength" name="image_strength" value="0.5" min="0" max="1" step="0.1">
                                                                <small class="form-text text-muted">数值越大，生成的图片与原图越相似</small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- 9. 文生图 -->
                                            <div class="col-md-6 mb-3">
                                                <div class="card">
                                                    <div class="card-body">
                                                        <div class="form-check">
                                                            <input class="form-check-input" type="checkbox" id="text_to_image" name="process_types[]" value="text_to_image">
                                                            <label class="form-check-label" for="text_to_image">文生图</label>
                                                        </div>
                                                        <!-- 文生图参数 -->
                                                        <div id="text_to_image_params" class="mt-3">
                                                            <div class="form-group">
                                                                <label for="text_prompt">图片描述</label>
                                                                <textarea class="form-control" id="text_prompt" name="text_prompt" rows="3" placeholder="请输入图片描述"></textarea>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 提交按钮 -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">开始处理</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- 右侧信息栏 -->
                    <div class="col-md-4">
                        <!-- 快速模板 -->
                        <div class="card">
                            <div class="card-header">
                                <h3>快速模板</h3>
                            </div>
                            <div class="card-body">
                                <div class="template-grid">
                                    <div class="template-item" data-type="ecommerce">
                                        <h4>电商产品图</h4>
                                        <p>快速生成适合电商平台的产品图片</p>
                                    </div>
                                    <div class="template-item" data-type="social">
                                        <h4>社交媒体图</h4>
                                        <p>生成适合社交媒体分享的图片</p>
                                    </div>
                                    <div class="template-item" data-type="banner">
                                        <h4>广告横幅</h4>
                                        <p>创建吸引人的广告横幅图片</p>
                                    </div>
                                    <div class="template-item" data-type="illustration">
                                        <h4>插画创作</h4>
                                        <p>生成艺术风格的插画作品</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- 任务历史 -->
                        <div class="card">
                            <div class="card-header">
                                <h3>最近任务</h3>
                            </div>
                            <div class="card-body">
                                <div class="task-item">
                                    <div class="task-info">
                                        <div class="task-title">批量抠图任务</div>
                                        <div class="task-date">2024-01-15 14:30</div>
                                    </div>
                                    <div class="task-status">
                                        <span class="badge badge-success">已完成</span>
                                    </div>
                                </div>
                                <div class="task-item">
                                    <div class="task-info">
                                        <div class="task-title">文生图生成</div>
                                        <div class="task-date">2024-01-15 13:45</div>
                                    </div>
                                    <div class="task-status">
                                        <span class="badge badge-success">已完成</span>
                                    </div>
                                </div>
                                <div class="task-item">
                                    <div class="task-info">
                                        <div class="task-title">批量改尺寸</div>
                                        <div class="task-date">2024-01-15 12:20</div>
                                    </div>
                                    <div class="task-status">
                                        <span class="badge badge-success">已完成</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>
    </div>
    
    <!-- 引入JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // 侧边栏导航
        $(document).ready(function() {
            $('.sidebar-nav a').click(function(e) {
                e.preventDefault();
                
                // 移除所有active类
                $('.sidebar-nav a').removeClass('active');
                $('.content-section').removeClass('active');
                
                // 添加active类到当前点击的导航项
                $(this).addClass('active');
                
                // 显示对应的内容区域
                const target = $(this).attr('href');
                $(target).addClass('active');
            });
            
            // 改尺寸方式切换
            $('input[name="resize_type"]').change(function() {
                const ratioOptions = $('#ratio_options');
                const pixelOptions = $('#pixel_options');
                
                if (this.value === 'ratio') {
                    ratioOptions.show();
                    pixelOptions.hide();
                } else {
                    ratioOptions.hide();
                    pixelOptions.show();
                }
            });
            
            // 水印类型切换
            $('input[name="watermark_type"]').change(function() {
                const textParams = $('#text_watermark_params');
                const imageParams = $('#image_watermark_params');
                
                if (this.value === 'text') {
                    textParams.show();
                    imageParams.hide();
                } else {
                    textParams.hide();
                    imageParams.show();
                }
            });
            
            // 拖拽上传功能
            const uploadArea = $('.upload-area');
            const fileInput = $('#images');
            
            uploadArea.on('dragover', function(e) {
                e.preventDefault();
                $(this).css('border-color', '#6c5ce7');
            });
            
            uploadArea.on('dragleave', function(e) {
                e.preventDefault();
                $(this).css('border-color', '#2c3e50');
            });
            
            uploadArea.on('drop', function(e) {
                e.preventDefault();
                $(this).css('border-color', '#2c3e50');
                
                const files = e.originalEvent.dataTransfer.files;
                fileInput[0].files = files;
            });
            
            // 点击上传功能
            uploadArea.on('click', function() {
                fileInput.click();
            });
            
            // 快速模板选择
            $('.template-item').click(function() {
                const templateType = $(this).data('type');
                alert('模板功能开发中，敬请期待！');
            });
        });
    </script>
</body>
</html>