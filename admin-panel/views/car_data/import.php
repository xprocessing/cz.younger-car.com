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
                                <input type="file" class="form-control-file" id="import_file" name="import_file" accept=".csv" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-2 col-form-label">文件格式说明：</label>
                            <div class="col-sm-10">
                                <div class="alert alert-info">
                                    <p>CSV文件需包含以下列（顺序必须一致）：</p>
                                    <p>品牌(英文),品牌(中文),车型,年份,配置版本,配置描述,销售市场</p>
                                    <p>示例：Toyota,丰田,Corolla,2023,舒适版,1.8L CVT,中国大陆</p>
                                    <p>注意：文件编码为UTF-8，建议使用Excel或文本编辑器保存为CSV格式</p>
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