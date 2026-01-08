<?php
// 简单的测试脚本，用于验证import.php的功能

define('APP_URL', 'http://localhost');
$title = '测试导入页面';
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <h1 class="page-header">
                    <?php echo htmlspecialchars($title); ?>
                </h1>
                
                <!-- 导入表单 -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fa fa-upload"></i> 导入车型数据
                    </div>
                    <div class="card-body">
                        <form method="post" action="#" enctype="multipart/form-data" class="form-horizontal">
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">导入方式：</label>
                                <div class="col-sm-10">
                                    <div class="btn-group btn-group-toggle" data-toggle="buttons">
                                        <label class="btn btn-primary active">
                                            <input type="radio" name="import_type" id="import_type_file" value="file" autocomplete="off" checked> 文件上传
                                        </label>
                                        <label class="btn btn-primary">
                                            <input type="radio" name="import_type" id="import_type_text" value="text" autocomplete="off"> 粘贴CSV文本
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row" id="file_import_div">
                                <label for="import_file" class="col-sm-2 col-form-label">选择CSV文件：</label>
                                <div class="col-sm-10">
                                    <input type="file" class="form-control-file" id="import_file" name="import_file" accept=".csv" required>
                                </div>
                            </div>
                            <div class="form-group row" id="text_import_div" style="display: none;">
                                <label for="import_text" class="col-sm-2 col-form-label">粘贴CSV文本：</label>
                                <div class="col-sm-10">
                                    <textarea class="form-control" id="import_text" name="import_text" rows="10" placeholder="请粘贴CSV格式的文本内容，包括表头"></textarea>
                                </div>
                            </div>
                            <div class="form-group row">
                                <label class="col-sm-2 col-form-label">格式说明：</label>
                                <div class="col-sm-10">
                                    <div class="alert alert-info">
                                        <p>CSV内容需包含以下列（顺序必须一致）：</p>
                                        <p>品牌(英文),品牌(中文),车型,年份,配置版本,配置描述,销售市场</p>
                                        <p>示例：Toyota,丰田,Corolla,2023,舒适版,1.8L CVT,中国大陆</p>
                                        <p>注意：使用UTF-8编码，包含表头</p>
                                        <p>导入前请确认文件内容是否符合格式要求，避免导入错误数据</p>
                                    </div>
                                </div>
                            </div>
                            <script>
                                // 切换导入方式
                                $(function() {
                                    console.log('jQuery loaded successfully');
                                    $('input[name="import_type"]').change(function() {
                                        console.log('Import type changed to: ' + $(this).val());
                                        if ($(this).val() === 'file') {
                                            $('#file_import_div').show();
                                            $('#import_file').prop('required', true);
                                            $('#text_import_div').hide();
                                            $('#import_text').prop('required', false);
                                        } else {
                                            $('#file_import_div').hide();
                                            $('#import_file').prop('required', false);
                                            $('#text_import_div').show();
                                            $('#import_text').prop('required', true);
                                        }
                                    });
                                });
                            </script>
                            <div class="form-group row">
                                <div class="col-sm-offset-2 col-sm-10">
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-upload"></i> 开始导入
                                    </button>
                                    <a href="#" class="btn btn-info ml-2">
                                        <i class="fa fa-download"></i> 下载模板
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- jQuery库 -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>