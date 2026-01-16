<div class="container-fluid">
    <!-- 页面标题 -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">AI图片处理</h1>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/aigc.php?action=taskHistory" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-history fa-sm text-white-50"></i> 任务历史
        </a>
    </div>

    <!-- 提示消息 -->
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

    <!-- 主要内容区域 -->
    <div class="row">
        <!-- 左侧导航栏 -->
        <div class="col-lg-3 col-md-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">处理类型</h6>
                </div>
                <div class="card-body">
                    <div class="list-group">
                        <a href="#" class="list-group-item list-group-item-action active" data-task-type="remove_defect">
                            <i class="fas fa-eraser mr-2"></i>批量去瑕疵
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-task-type="crop_png">
                            <i class="fas fa-crop-alt mr-2"></i>批量抠图(PNG)
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-task-type="crop_white_bg">
                            <i class="fas fa-crop mr-2"></i>批量抠图(白底)
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-task-type="resize">
                            <i class="fas fa-expand-arrows-alt mr-2"></i>批量改尺寸
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-task-type="watermark">
                            <i class="fas fa-water mr-2"></i>批量打水印
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-task-type="face_swap">
                            <i class="fas fa-user-circle mr-2"></i>智能换脸
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-task-type="image_to_image">
                            <i class="fas fa-image mr-2"></i>图生图
                        </a>
                        <a href="#" class="list-group-item list-group-item-action" data-task-type="text_to_image">
                            <i class="fas fa-file-alt mr-2"></i>文生图
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- 右侧操作区域 -->
        <div class="col-lg-9 col-md-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">操作面板</h6>
                </div>
                <div class="card-body">
                    <form action="<?php echo ADMIN_PANEL_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                        <!-- 任务类型选择 -->
                        <input type="hidden" id="task_type" name="process_types[]" value="remove_defect">

                        <!-- 通用图片上传区域 -->
                        <div id="image_upload_section" class="form-group">
                            <label for="images">选择图片（支持批量上传）</label>
                            <div class="upload-area" style="border: 2px dashed #dee2e6; border-radius: 8px; padding: 20px; text-align: center;">
                                <i class="fas fa-cloud-upload-alt" style="font-size: 48px; color: #6c757d; margin-bottom: 10px;"></i>
                                <p>拖拽图片到此处或点击上传</p>
                                <input type="file" class="form-control-file" id="images" name="images[]" multiple accept="image/*" style="display: none;">
                                <button type="button" class="btn btn-primary mt-3" onclick="document.getElementById('images').click()">选择图片</button>
                            </div>
                            <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过2MB</small>
                        </div>

                        <!-- 参数设置区域 -->
                        <div id="params_section" class="form-group">
                            <!-- 参数设置将根据选择的任务类型动态加载 -->
                            <h5>参数设置</h5>
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
                        <button type="submit" class="btn btn-primary">开始处理</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript 用于动态加载参数设置 -->
<script>
    // 确保jQuery加载完成后执行
    document.addEventListener('DOMContentLoaded', function() {
        // 处理类型导航点击事件
        $('.list-group-item').click(function(e) {
            e.preventDefault();
            
            // 移除所有激活状态
            $('.list-group-item').removeClass('active');
            
            // 添加当前点击项的激活状态
            $(this).addClass('active');
            
            // 获取选择的任务类型
            var taskType = $(this).data('task-type');
            
            // 更新隐藏字段
            $('#task_type').val(taskType);
            
            // 加载对应的参数设置
            loadParams(taskType);
        });
        
        // 加载参数设置的函数
        function loadParams(taskType) {
            var paramsSection = $('#params_section');
            var imageUploadSection = $('#image_upload_section');
            
            // 清空参数设置区域
            paramsSection.empty();
            
            // 显示或隐藏图片上传区域
            if (taskType === 'text_to_image') {
                imageUploadSection.hide();
            } else {
                imageUploadSection.show();
            }
            
            // 根据任务类型加载参数设置
            switch (taskType) {
                case 'remove_defect':
                    loadRemoveDefectParams(paramsSection);
                    break;
                case 'crop_png':
                    loadCropPngParams(paramsSection);
                    break;
                case 'crop_white_bg':
                    loadCropWhiteBgParams(paramsSection);
                    break;
                case 'resize':
                    loadResizeParams(paramsSection);
                    break;
                case 'watermark':
                    loadWatermarkParams(paramsSection);
                    break;
                case 'face_swap':
                    loadFaceSwapParams(paramsSection);
                    break;
                case 'image_to_image':
                    loadImageToImageParams(paramsSection);
                    break;
                case 'text_to_image':
                    loadTextToImageParams(paramsSection);
                    break;
            }
        }
        
        // 批量去瑕疵参数
        function loadRemoveDefectParams(container) {
            container.html(`
                <h5>参数设置</h5>
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
            `);
        }
        
        // 批量抠图(PNG)参数
        function loadCropPngParams(container) {
            container.html(`
                <h5>参数设置</h5>
                <div class="form-group">
                    <p>将自动去除背景，生成PNG格式图片</p>
                </div>
            `);
        }
        
        // 批量抠图(白底)参数
        function loadCropWhiteBgParams(container) {
            container.html(`
                <h5>参数设置</h5>
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
            `);
        }
        
        // 批量改尺寸参数
        function loadResizeParams(container) {
            container.html(`
                <h5>参数设置</h5>
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
            `);
            
            // 绑定事件
            $('input[name="resize_type"]').change(function() {
                if (this.value === 'ratio') {
                    $('#ratio_options').show();
                    $('#pixel_options').hide();
                } else {
                    $('#ratio_options').hide();
                    $('#pixel_options').show();
                }
            });
        }
        
        // 批量打水印参数
        function loadWatermarkParams(container) {
            container.html(`
                <h5>参数设置</h5>
                <div class="form-group">
                    <label for="watermark_position">水印位置</label>
                    <select class="form-control" id="watermark_position" name="watermark_position">
                        <option value="左上">左上</option>
                        <option value="右上">右上</option>
                        <option value="左下">左下</option>
                        <option value="右下" selected>右下</option>
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
                
                <div id="text_watermark_params">
                    <div class="form-group">
                        <label for="watermark_text">水印文字</label>
                        <input type="text" class="form-control" id="watermark_text" name="watermark_text" placeholder="请输入水印文字">
                    </div>
                </div>
                
                <div id="image_watermark_params" style="display: none;">
                    <div class="form-group">
                        <label for="watermark_image">选择水印图片</label>
                        <input type="file" class="form-control-file" id="watermark_image" name="watermark_image" accept="image/*">
                        <small class="form-text text-muted">建议使用PNG格式的透明图片</small>
                    </div>
                </div>
            `);
            
            // 绑定事件
            $('input[name="watermark_type"]').change(function() {
                if (this.value === 'text') {
                    $('#text_watermark_params').show();
                    $('#image_watermark_params').hide();
                } else {
                    $('#text_watermark_params').hide();
                    $('#image_watermark_params').show();
                }
            });
        }
        
        // 智能换脸参数
        function loadFaceSwapParams(container) {
            container.html(`
                <h5>参数设置</h5>
                <div class="form-group">
                    <label for="model_image">选择模特图片</label>
                    <input type="file" class="form-control-file" id="model_image" name="model_image" accept="image/*" required>
                    <small class="form-text text-muted">请选择一张包含清晰人脸的模特图片</small>
                </div>
            `);
        }
        
        // 图生图参数
        function loadImageToImageParams(container) {
            container.html(`
                <h5>参数设置</h5>
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
            `);
        }
        
        // 文生图参数
        function loadTextToImageParams(container) {
            container.html(`
                <h5>参数设置</h5>
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
            `);
        }
        
        // 拖拽上传功能
        $('.upload-area').on('dragover', function(e) {
            e.preventDefault();
            $(this).css('border-color', '#6c5ce7');
        });
        
        $('.upload-area').on('dragleave', function(e) {
            e.preventDefault();
            $(this).css('border-color', '#dee2e6');
        });
        
        $('.upload-area').on('drop', function(e) {
            e.preventDefault();
            $(this).css('border-color', '#dee2e6');
            
            const files = e.originalEvent.dataTransfer.files;
            const fileInput = $(this).find('input[type="file"]');
            if (fileInput.length > 0) {
                fileInput[0].files = files;
            }
        });
        
        // 点击上传功能
        $('.upload-area').on('click', function() {
            $(this).find('input[type="file"]').click();
        });
    });
</script>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>