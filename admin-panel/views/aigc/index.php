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
        
        /* 内容区域显示/隐藏 */
        .content-section {
            display: none;
        }
        
        .content-section.active {
            display: block;
        }
        
        /* 快速操作项样式 */
        .quick-action-item {
            display: block;
            text-align: center;
            padding: 20px;
            margin-bottom: 15px;
            background-color: rgba(108, 92, 231, 0.1);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            color: var(--text-color);
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .quick-action-item:hover {
            background-color: rgba(108, 92, 231, 0.2);
            border-color: var(--primary-color);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(108, 92, 231, 0.3);
            color: var(--text-color);
        }
        
        .quick-action-item i {
            font-size: 32px;
            color: var(--primary-color);
            margin-bottom: 10px;
        }
        
        .quick-action-item span {
            font-size: 16px;
            font-weight: 600;
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
                <li><a href="#workspace" class="active"><i class="fas fa-home"></i> 工作台</a></li>
                <li><a href="result.php"><i class="fas fa-images"></i> 处理结果</a></li>
                <li><a href="#remove-defect"><i class="fas fa-magic"></i> 批量去瑕疵</a></li>
                <li><a href="#crop-png"><i class="fas fa-cut"></i> 批量抠图(PNG)</a></li>
                <li><a href="#crop-white"><i class="fas fa-image"></i> 批量抠图(白底)</a></li>
                <li><a href="#resize"><i class="fas fa-expand-arrows-alt"></i> 批量改尺寸</a></li>
                <li><a href="#watermark"><i class="fas fa-stamp"></i> 批量打水印</a></li>
                <li><a href="#face-swap"><i class="fas fa-user-circle"></i> 智能换脸</a></li>
                <li><a href="#multi-angle"><i class="fas fa-sync-alt"></i> 多角度图片</a></li>
                <li><a href="#image-to-image"><i class="fas fa-exchange-alt"></i> 图生图</a></li>
                <li><a href="#text-to-image"><i class="fas fa-font"></i> 文生图</a></li>

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
            
            <!-- 0. 工作台 -->
            <section id="workspace" class="content-section active">
                <div class="content-header">
                    <h1>工作台</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <h3>欢迎使用 NeoAI GC</h3>
                                        <p>您可以通过左侧导航选择需要的图片处理功能。</p>
                                        <p>NeoAI GC 提供了丰富的图片处理工具，帮助您快速完成各种图片编辑任务。</p>
                                    </div>
                                    <div class="col-md-6">
                                        <h3>快速操作</h3>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <a href="#remove-defect" class="quick-action-item">
                                                    <i class="fas fa-magic"></i>
                                                    <span>批量去瑕疵</span>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="#crop-png" class="quick-action-item">
                                                    <i class="fas fa-cut"></i>
                                                    <span>批量抠图</span>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="#resize" class="quick-action-item">
                                                    <i class="fas fa-expand-arrows-alt"></i>
                                                    <span>批量改尺寸</span>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="#watermark" class="quick-action-item">
                                                    <i class="fas fa-stamp"></i>
                                                    <span>批量打水印</span>
                                                </a>
                                            </div>
                                            <div class="col-md-6">
                                                <a href="result.php" class="quick-action-item">
                                                    <i class="fas fa-images"></i>
                                                    <span>处理结果</span>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 1. 批量去除瑕疵 -->
            <section id="remove-defect" class="content-section">
                <div class="content-header">
                    <h1>批量去除瑕疵</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="process_types[]" value="remove_defect">
                                    
                                    <!-- 图片上传 -->
                                    <div class="form-group">
                                        <label for="remove_defect_images">选择图片（支持批量上传）</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>拖拽图片到此处或点击上传</p>
                                            <input type="file" class="form-control-file" id="remove_defect_images" name="images[]" multiple accept="image/*" style="display: none;" required>
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('remove_defect_images').click()">选择图片</button>
                                        </div>
                                        <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过2MB</small>
                                    </div>
                                    
                                    <!-- 参数设置 -->
                                    <div class="form-group">
                                        <label>参数设置</label>
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
                                    
                                    <!-- 提交按钮 -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">开始处理</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>功能说明</h3>
                            </div>
                            <div class="card-body">
                                <p>自动检测并去除图片中的瑕疵、划痕、污渍等，同时调整亮度和对比度，使图片更加清晰美观。</p>
                                <p><strong>适用场景：</strong>产品图片修复、老照片修复、商品图片优化等。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 2. 批量抠图 - 导出PNG -->
            <section id="crop-png" class="content-section">
                <div class="content-header">
                    <h1>批量抠图(PNG)</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="process_types[]" value="crop_png">
                                    
                                    <!-- 图片上传 -->
                                    <div class="form-group">
                                        <label for="crop_png_images">选择图片（支持批量上传）</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>拖拽图片到此处或点击上传</p>
                                            <input type="file" class="form-control-file" id="crop_png_images" name="images[]" multiple accept="image/*" style="display: none;" required>
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('crop_png_images').click()">选择图片</button>
                                        </div>
                                        <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过2MB</small>
                                    </div>
                                    
                                    <!-- 提交按钮 -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">开始处理</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>功能说明</h3>
                            </div>
                            <div class="card-body">
                                <p>自动识别图片中的主体，将其从背景中分离出来，导出透明背景的PNG格式图片。</p>
                                <p><strong>适用场景：</strong>电商产品图片、设计素材制作、广告创意等。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 3. 批量抠图 - 导出白底图 -->
            <section id="crop-white" class="content-section">
                <div class="content-header">
                    <h1>批量抠图(白底)</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="process_types[]" value="crop_white_bg">
                                    
                                    <!-- 图片上传 -->
                                    <div class="form-group">
                                        <label for="crop_white_images">选择图片（支持批量上传）</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>拖拽图片到此处或点击上传</p>
                                            <input type="file" class="form-control-file" id="crop_white_images" name="images[]" multiple accept="image/*" style="display: none;" required>
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('crop_white_images').click()">选择图片</button>
                                        </div>
                                        <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过2MB</small>
                                    </div>
                                    
                                    <!-- 参数设置 -->
                                    <div class="form-group">
                                        <label>参数设置</label>
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
                                    
                                    <!-- 提交按钮 -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">开始处理</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>功能说明</h3>
                            </div>
                            <div class="card-body">
                                <p>自动识别图片中的主体，将其从背景中分离出来，并放置在白色背景上。</p>
                                <p><strong>适用场景：</strong>电商平台产品展示、产品目录制作等。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 4. 批量改尺寸 -->
            <section id="resize" class="content-section">
                <div class="content-header">
                    <h1>批量改尺寸</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="process_types[]" value="resize">
                                    
                                    <!-- 图片上传 -->
                                    <div class="form-group">
                                        <label for="resize_images">选择图片（支持批量上传）</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>拖拽图片到此处或点击上传</p>
                                            <input type="file" class="form-control-file" id="resize_images" name="images[]" multiple accept="image/*" style="display: none;" required>
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('resize_images').click()">选择图片</button>
                                        </div>
                                        <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过2MB</small>
                                    </div>
                                    
                                    <!-- 参数设置 -->
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
                                    <div id="ratio_options">
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
                                    <div id="pixel_options" style="display: none;">
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
                                    
                                    <!-- 提交按钮 -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">开始处理</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>功能说明</h3>
                            </div>
                            <div class="card-body">
                                <p>批量调整图片尺寸，可以选择按比例或按固定像素进行调整。</p>
                                <p><strong>适用场景：</strong>网站图片优化、社交媒体图片准备、印刷品制作等。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 5. 批量打水印 -->
            <section id="watermark" class="content-section">
                <div class="content-header">
                    <h1>批量打水印</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="process_types[]" value="watermark">
                                    
                                    <!-- 图片上传 -->
                                    <div class="form-group">
                                        <label for="watermark_images">选择图片（支持批量上传）</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>拖拽图片到此处或点击上传</p>
                                            <input type="file" class="form-control-file" id="watermark_images" name="images[]" multiple accept="image/*" style="display: none;" required>
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('watermark_images').click()">选择图片</button>
                                        </div>
                                        <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过2MB</small>
                                    </div>
                                    
                                    <!-- 参数设置 -->
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
                                    <div id="text_watermark_params">
                                        <div class="form-group">
                                            <label for="watermark_text">水印文字</label>
                                            <input type="text" class="form-control" id="watermark_text" name="watermark_text" placeholder="请输入水印文字">
                                        </div>
                                    </div>
                                    
                                    <!-- 图片水印参数 -->
                                    <div id="image_watermark_params" style="display: none;">
                                        <div class="form-group">
                                            <label for="watermark_image">选择水印图片</label>
                                            <input type="file" class="form-control-file" id="watermark_image" name="watermark_image" accept="image/*">
                                            <small class="form-text text-muted">建议使用PNG格式的透明图片</small>
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
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>功能说明</h3>
                            </div>
                            <div class="card-body">
                                <p>批量为图片添加文字或图片水印，保护图片版权或添加品牌标识。</p>
                                <p><strong>适用场景：</strong>图片版权保护、品牌推广、产品图片标识等。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 6. 批量模特换脸 -->
            <section id="face-swap" class="content-section">
                <div class="content-header">
                    <h1>智能换脸</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="process_types[]" value="face_swap">
                                    
                                    <!-- 图片上传 -->
                                    <div class="form-group">
                                        <label for="face_swap_images">选择图片（支持批量上传）</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>拖拽图片到此处或点击上传</p>
                                            <input type="file" class="form-control-file" id="face_swap_images" name="images[]" multiple accept="image/*" style="display: none;" required>
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('face_swap_images').click()">选择图片</button>
                                        </div>
                                        <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过2MB</small>
                                    </div>
                                    
                                    <!-- 模特图片 -->
                                    <div class="form-group">
                                        <label for="model_image">选择模特图片</label>
                                        <input type="file" class="form-control-file" id="model_image" name="model_image" accept="image/*" required>
                                        <small class="form-text text-muted">请选择一张包含清晰人脸的模特图片</small>
                                    </div>
                                    
                                    <!-- 提交按钮 -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">开始处理</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>功能说明</h3>
                            </div>
                            <div class="card-body">
                                <p>将图片中的人脸替换为指定模特的人脸，保持其他部分不变。</p>
                                <p><strong>适用场景：</strong>时尚电商产品展示、广告创意、虚拟试衣等。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 7. 生成多角度图片 -->
            <section id="multi-angle" class="content-section">
                <div class="content-header">
                    <h1>多角度图片</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="process_types[]" value="multi_angle">
                                    
                                    <!-- 图片上传 -->
                                    <div class="form-group">
                                        <label for="multi_angle_images">选择图片（支持批量上传）</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>拖拽图片到此处或点击上传</p>
                                            <input type="file" class="form-control-file" id="multi_angle_images" name="images[]" multiple accept="image/*" style="display: none;" required>
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('multi_angle_images').click()">选择图片</button>
                                        </div>
                                        <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过2MB</small>
                                    </div>
                                    
                                    <!-- 提交按钮 -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">开始处理</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>功能说明</h3>
                            </div>
                            <div class="card-body">
                                <p>根据一张图片生成物体的多角度视图，帮助用户更全面地了解产品。</p>
                                <p><strong>适用场景：</strong>电商产品展示、产品设计、3D建模辅助等。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 8. 图生图 -->
            <section id="image-to-image" class="content-section">
                <div class="content-header">
                    <h1>图生图</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="process_types[]" value="image_to_image">
                                    
                                    <!-- 图片上传 -->
                                    <div class="form-group">
                                        <label for="image_to_image_images">选择图片（支持批量上传）</label>
                                        <div class="upload-area">
                                            <i class="fas fa-cloud-upload-alt"></i>
                                            <p>拖拽图片到此处或点击上传</p>
                                            <input type="file" class="form-control-file" id="image_to_image_images" name="images[]" multiple accept="image/*" style="display: none;" required>
                                            <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('image_to_image_images').click()">选择图片</button>
                                        </div>
                                        <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过2MB</small>
                                    </div>
                                    
                                    <!-- 参数设置 -->
                                    <div class="form-group">
                                        <label for="image_prompt">图片描述</label>
                                        <textarea class="form-control" id="image_prompt" name="image_prompt" rows="4" placeholder="请输入图片描述" required></textarea>
                                        <small class="form-text text-muted">描述您希望图片如何变化，例如：将这只猫变成一只狗</small>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="image_strength">图像相似度（0-1）</label>
                                        <input type="number" step="0.1" class="form-control" id="image_strength" name="image_strength" value="0.5" min="0" max="1">
                                        <small class="form-text text-muted">数值越大，生成的图片与原图越相似</small>
                                    </div>
                                    
                                    <!-- 提交按钮 -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">开始处理</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h3>功能说明</h3>
                            </div>
                            <div class="card-body">
                                <p>根据上传的图片和文字描述，生成新的图片。您可以控制生成图片与原图的相似度。</p>
                                <p><strong>适用场景：</strong>图片风格转换、创意设计、图像修复等。</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            
            <!-- 9. 文生图 -->
            <section id="text-to-image" class="content-section">
                <div class="content-header">
                    <h1>文生图</h1>
                </div>
                
                <div class="row">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-body">
                                <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                                    <input type="hidden" name="process_types[]" value="text_to_image">
                                    
                                    <!-- 参数设置 -->
                                    <div class="form-group">
                                        <label for="text_prompt">输入提示词</label>
                                        <textarea class="form-control" id="text_prompt" name="text_prompt" rows="4" placeholder="描述你想要生成的图片，例如：一只可爱的猫咪坐在窗台上..." required></textarea>
                                        <small class="form-text text-muted">越详细的描述会得到越准确的图片结果</small>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="image_width">图片宽度（像素）</label>
                                                <input type="number" class="form-control" id="image_width" name="image_width" value="1024" min="256" max="2048">
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label for="image_height">图片高度（像素）</label>
                                                <input type="number" class="form-control" id="image_height" name="image_height" value="1024" min="256" max="2048">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- 提交按钮 -->
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-primary">生成图片</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
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
                    </div>
                </div>
            </section>
            


        </main>
    </div>
    
    <!-- 引入JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // 侧边栏导航和快速操作
        $(document).ready(function() {
            // 导航点击处理函数
            function handleNavigationClick(e) {
                e.preventDefault();
                
                // 移除所有active类
                $('.sidebar-nav a').removeClass('active');
                $('.content-section').removeClass('active');
                
                // 显示对应的内容区域
                const target = $(this).attr('href');
                $(target).addClass('active');
                
                // 如果是侧边栏导航项，添加active类
                if ($(this).parents('.sidebar-nav').length > 0) {
                    $(this).addClass('active');
                } else {
                    // 如果是快速操作项，同步更新侧边栏导航的active状态
                    $('.sidebar-nav a[href="' + target + '"]').addClass('active');
                }
            }
            
            // 侧边栏导航点击事件
            $('.sidebar-nav a').click(handleNavigationClick);
            
            // 快速操作项点击事件
            $('.quick-action-item').click(handleNavigationClick);
            
            // 改尺寸方式切换
            $('input[name="resize_type"]').change(function() {
                const ratioOptions = $(this).closest('.content-section').find('#ratio_options');
                const pixelOptions = $(this).closest('.content-section').find('#pixel_options');
                
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
                const textParams = $(this).closest('.content-section').find('#text_watermark_params');
                const imageParams = $(this).closest('.content-section').find('#image_watermark_params');
                
                if (this.value === 'text') {
                    textParams.show();
                    imageParams.hide();
                } else {
                    textParams.hide();
                    imageParams.show();
                }
            });
            
            // 拖拽上传功能
            $('.upload-area').each(function() {
                const uploadArea = $(this);
                const fileInput = uploadArea.find('input[type="file"]');
                
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
                    if (fileInput.length > 0) {
                        fileInput[0].files = files;
                    }
                });
                
                // 点击上传功能
                uploadArea.on('click', function() {
                    fileInput.click();
                });
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