<?php include VIEWS_DIR . '/layouts/header.php'; ?>

<div class="container-fluid">
    <!-- 页面标题 -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">AI图片处理</h1>
        <a href="" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> 下载模板
        </a>
    </div>

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

    <div class="row">
        <!-- 图片上传和处理选项 -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow mb-4">
                <!-- 卡片标题 -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">批量图片处理</h6>
                </div>
                <!-- 卡片内容 -->
                <div class="card-body">
                    <form action="<?php echo APP_URL; ?>/aigc.php?action=processImages" method="POST" enctype="multipart/form-data">
                        <!-- 图片上传 -->
                        <div class="form-group">
                            <label for="images">选择图片（支持批量上传）</label>
                            <input type="file" class="form-control-file" id="images" name="images[]" multiple accept="image/*" required>
                            <small class="form-text text-muted">支持JPG、PNG、GIF、WebP格式，单张图片不超过10MB</small>
                        </div>

                        <!-- 处理类型选择 -->
                        <div class="form-group">
                            <label for="process_type">处理类型</label>
                            <select class="form-control" id="process_type" name="process_type" required>
                                <option value="">请选择处理类型</option>
                                <option value="remove_defect">1. 批量去除瑕疵，调整亮度对比度</option>
                                <option value="crop_png">2. 批量抠图 - 导出PNG</option>
                                <option value="crop_white_bg">3. 批量抠图 - 导出白底图</option>
                                <option value="resize">4. 批量改尺寸</option>
                                <option value="watermark">5. 批量打水印</option>
                                <option value="face_swap">6. 批量模特换脸</option>
                                <option value="multi_angle">7. 生成多角度图片</option>
                                <option value="use_template">8. 使用自定义模板</option>
                            </select>
                        </div>

                        <!-- 动态参数设置 -->
                        <div id="dynamic_params">
                            <!-- 1. 批量去除瑕疵参数 -->
                            <div id="remove_defect_params" style="display: none;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="width">宽度（像素）</label>
                                            <input type="number" class="form-control" id="width" name="width" value="1200" min="100" max="4096">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="height">高度（像素）</label>
                                            <input type="number" class="form-control" id="height" name="height" value="1200" min="100" max="4096">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 3. 批量抠图 - 导出白底图参数 -->
                            <div id="crop_white_bg_params" style="display: none;">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="width">宽度（像素）</label>
                                            <input type="number" class="form-control" id="width" name="width" value="800" min="100" max="4096">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="height">高度（像素）</label>
                                            <input type="number" class="form-control" id="height" name="height" value="800" min="100" max="4096">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="subject_ratio">主体占比（%）</label>
                                            <input type="number" class="form-control" id="subject_ratio" name="subject_ratio" value="80" min="50" max="95">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- 4. 批量改尺寸参数 -->
                            <div id="resize_params" style="display: none;">
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
                                <div id="ratio_options" style="display: block;">
                                    <div class="form-group">
                                        <label for="resize_ratio">选择比例</label>
                                        <select class="form-control" id="resize_ratio" name="resize_ratio">
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
                            </div>

                            <!-- 5. 批量打水印参数 -->
                            <div id="watermark_params" style="display: none;">
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
                                <div id="text_watermark_params" style="display: block;">
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
                            </div>

                            <!-- 7. 生成多角度图片参数 -->
                            <div id="multi_angle_params" style="display: none;">
                                <div class="form-group">
                                    <label for="angles">选择角度（逗号分隔）</label>
                                    <input type="text" class="form-control" id="angles" name="angles" value="30,60,90,120,150,180" placeholder="例如：30,60,90,120,150,180">
                                </div>
                            </div>

                            <!-- 8. 使用自定义模板 -->
                            <div id="use_template_params" style="display: none;">
                                <div class="form-group">
                                    <label for="template_id">选择模板</label>
                                    <select class="form-control" id="template_id" name="template_id">
                                        <option value="">请选择模板</option>
                                        <?php foreach ($templates as $template): ?>
                                            <option value="<?php echo $template['id']; ?>">
                                                <?php echo htmlspecialchars($template['name']); ?> (<?php echo $template['template_type']; ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- 提交按钮 -->
                        <button type="submit" class="btn btn-primary">开始处理</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- 自定义模板管理 -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow mb-4">
                <!-- 卡片标题 -->
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">自定义模板</h6>
                    <button class="btn btn-primary btn-sm" data-toggle="modal" data-target="#addTemplateModal">
                        <i class="fas fa-plus"></i> 添加模板
                    </button>
                </div>
                <!-- 卡片内容 -->
                <div class="card-body">
                    <?php if (empty($templates)): ?>
                        <p class="text-center text-muted">暂无自定义模板</p>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>名称</th>
                                        <th>类型</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($templates as $template): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($template['name']); ?></td>
                                            <td><?php echo htmlspecialchars($template['template_type']); ?></td>
                                            <td>
                                                <button class="btn btn-sm btn-info" onclick="editTemplate(<?php echo $template['id']; ?>)">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                <a href="<?php echo APP_URL; ?>/aigc.php?action=deleteTemplate&id=<?php echo $template['id']; ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('确定要删除这个模板吗？');">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 添加模板模态框 -->
<div class="modal fade" id="addTemplateModal" tabindex="-1" role="dialog" aria-labelledby="addTemplateModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addTemplateModalLabel">添加自定义模板</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="<?php echo APP_URL; ?>/aigc.php?action=createTemplate" method="POST">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="template_name">模板名称</label>
                        <input type="text" class="form-control" id="template_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="template_type">模板类型</label>
                        <select class="form-control" id="template_type" name="template_type" required>
                            <option value="remove_defect">1. 批量去除瑕疵</option>
                            <option value="crop">2. 批量抠图</option>
                            <option value="resize">3. 批量改尺寸</option>
                            <option value="watermark">4. 批量打水印</option>
                            <option value="face_swap">5. 批量模特换脸</option>
                            <option value="multi_angle">6. 生成多角度图片</option>
                            <option value="custom">7. 自定义</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="template_params">模板参数（JSON格式）</label>
                        <textarea class="form-control" id="template_params" name="params" rows="5" required placeholder="{\"width\": 1200, \"height\": 1200}"></textarea>
                    </div>
                    <div class="form-group">
                        <label for="template_description">模板描述</label>
                        <textarea class="form-control" id="template_description" name="description" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">取消</button>
                    <button type="submit" class="btn btn-primary">保存模板</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript 用于动态显示参数表单 -->
<script>
    // 处理类型切换
    document.getElementById('process_type').addEventListener('change', function() {
        const value = this.value;
        const dynamicParams = document.getElementById('dynamic_params');
        
        // 隐藏所有参数表单
        const paramsDivs = dynamicParams.querySelectorAll('div');
        paramsDivs.forEach(div => {
            div.style.display = 'none';
        });
        
        // 显示选中的参数表单
        const targetDiv = document.getElementById(value + '_params');
        if (targetDiv) {
            targetDiv.style.display = 'block';
        }
    });
    
    // 改尺寸方式切换
    const resizeTypeRadios = document.querySelectorAll('input[name="resize_type"]');
    resizeTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const ratioOptions = document.getElementById('ratio_options');
            const pixelOptions = document.getElementById('pixel_options');
            
            if (this.value === 'ratio') {
                ratioOptions.style.display = 'block';
                pixelOptions.style.display = 'none';
            } else {
                ratioOptions.style.display = 'none';
                pixelOptions.style.display = 'block';
            }
        });
    });
    
    // 水印类型切换
    const watermarkTypeRadios = document.querySelectorAll('input[name="watermark_type"]');
    watermarkTypeRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            const textParams = document.getElementById('text_watermark_params');
            const imageParams = document.getElementById('image_watermark_params');
            
            if (this.value === 'text') {
                textParams.style.display = 'block';
                imageParams.style.display = 'none';
            } else {
                textParams.style.display = 'none';
                imageParams.style.display = 'block';
            }
        });
    });
    
    // 编辑模板功能
    function editTemplate(id) {
        // 这里可以实现编辑模板的功能
        alert('编辑模板功能开发中...');
    }
</script>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>
