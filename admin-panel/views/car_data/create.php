<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?php echo htmlspecialchars($title); ?>
                <div class="float-right">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/car_data.php" class="btn btn-default">
                        <i class="fa fa-arrow-left"></i> 返回列表
                    </a>
                </div>
            </h1>
            
            <!-- 创建表单 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa fa-plus"></i> 车型数据信息
                </div>
                <div class="card-body">
                    <form method="post" action="<?php echo ADMIN_PANEL_URL; ?>/car_data.php?action=createPost" class="form-horizontal">
                        <div class="form-group row">
                            <label for="make" class="col-sm-2 col-form-label">品牌(英文)：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="make" name="make" placeholder="请输入品牌英文名称">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="make_cn" class="col-sm-2 col-form-label">品牌(中文)：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="make_cn" name="make_cn" placeholder="请输入品牌中文名称">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="model" class="col-sm-2 col-form-label">车型：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="model" name="model" placeholder="请输入车型名称" required>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="year" class="col-sm-2 col-form-label">年份：</label>
                            <div class="col-sm-10">
                                <input type="number" class="form-control" id="year" name="year" placeholder="请输入生产年份" min="1900" max="2100">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="trim" class="col-sm-2 col-form-label">配置版本：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="trim" name="trim" placeholder="请输入配置版本（如：舒适版/豪华版）">
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="trim_description" class="col-sm-2 col-form-label">配置描述：</label>
                            <div class="col-sm-10">
                                <textarea class="form-control" id="trim_description" name="trim_description" rows="3" placeholder="请输入配置版本详细描述"></textarea>
                            </div>
                        </div>
                        <div class="form-group row">
                            <label for="market" class="col-sm-2 col-form-label">销售市场：</label>
                            <div class="col-sm-10">
                                <input type="text" class="form-control" id="market" name="market" placeholder="请输入销售市场（如：中国大陆/北美/欧洲）">
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-offset-2 col-sm-10">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-save"></i> 保存
                                </button>
                                <a href="<?php echo ADMIN_PANEL_URL; ?>/car_data.php" class="btn btn-default ml-2">
                                    <i class="fa fa-times"></i> 取消
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>