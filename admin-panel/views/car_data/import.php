<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?php echo htmlspecialchars($title); ?>
                <div class="float-right">
                    <a href="<?php echo APP_URL; ?>/car_data.php" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> 返回列表
                    </a>
                </div>
            </h1>
            
            <!-- 导入表单 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa fa-upload"></i> 导入车型数据
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo APP_URL; ?>/car_data.php?action=importPost" enctype="multipart/form-data" class="form-horizontal">
                        <div class="form-group row">
                            <label for="import_file" class="col-sm-2 col-form-label">选择CSV文件：</label>
                            <div class="col-sm-10">
                                <input type="file" class="form-control-file" id="import_file" name="import_file" accept=".csv">
                            </div>
                        </div>
                        <div class="form-group row">
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
                                    <p>我给你脏数据，请按照如下格式给我返回csv数据：</p>
                                </div>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-upload"></i> 开始导入
                                </button>
                                <a href="<?php echo APP_URL; ?>/car_data.php?action=export" class="btn btn-info ml-2">
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