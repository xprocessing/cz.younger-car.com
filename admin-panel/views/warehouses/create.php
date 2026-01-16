<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>创建仓库</h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/warehouses.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">基本信息</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/warehouses.php?action=create_post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wid" class="form-label">仓库ID <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="wid" name="wid" required
                                       placeholder="请输入仓库ID">
                                <div class="form-text">仓库的唯一标识</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">仓库名称 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" required
                                       placeholder="请输入仓库名称" maxlength="100">
                                <div class="form-text">仓库的显示名称</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="type" class="form-label">仓库类型 <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="type" name="type" required
                                       placeholder="请输入仓库类型">
                                <div class="form-text">仓库的类型编码</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sub_type" class="form-label">仓库子类型 <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="sub_type" name="sub_type" required
                                       placeholder="请输入仓库子类型">
                                <div class="form-text">仓库的子类型编码</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="country_code" class="form-label">国家/地区编码</label>
                                <input type="text" class="form-control" id="country_code" name="country_code"
                                       placeholder="请输入国家/地区编码" maxlength="10">
                                <div class="form-text">如：CN、US、GB等</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wp_id" class="form-label">WP ID</label>
                                <input type="number" class="form-control" id="wp_id" name="wp_id"
                                       placeholder="请输入WP ID">
                                <div class="form-text">服务商ID</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wp_name" class="form-label">WP名称</label>
                                <input type="text" class="form-control" id="wp_name" name="wp_name"
                                       placeholder="请输入WP名称" maxlength="50">
                                <div class="form-text">服务商平台名称</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="t_warehouse_name" class="form-label">T仓库名称</label>
                                <input type="text" class="form-control" id="t_warehouse_name" name="t_warehouse_name"
                                       placeholder="请输入T仓库名称" maxlength="50">
                                <div class="form-text">第三方平台仓库名称</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="t_warehouse_code" class="form-label">T仓库代码</label>
                                <input type="text" class="form-control" id="t_warehouse_code" name="t_warehouse_code"
                                       placeholder="请输入T仓库代码" maxlength="50">
                                <div class="form-text">第三方平台仓库代码</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="t_country_area_name" class="form-label">T国家/地区</label>
                                <input type="text" class="form-control" id="t_country_area_name" name="t_country_area_name"
                                       placeholder="请输入T国家/地区" maxlength="50">
                                <div class="form-text">第三方平台国家/地区名称</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="t_status" class="form-label">T状态</label>
                                <input type="text" class="form-control" id="t_status" name="t_status"
                                       placeholder="请输入T状态" maxlength="10">
                                <div class="form-text">第三方平台状态</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="is_delete" class="form-label">是否删除</label>
                                <select class="form-select" id="is_delete" name="is_delete">
                                    <option value="0">否</option>
                                    <option value="1">是</option>
                                </select>
                                <div class="form-text">标记为删除状态</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check"></i> 保存
                        </button>
                        <a href="<?php echo ADMIN_PANEL_URL; ?>/warehouses.php" class="btn btn-outline-secondary ms-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">提示信息</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        带 <span class="text-danger">*</span> 的字段为必填项
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        仓库ID是唯一的，不可重复
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        国家/地区编码请使用标准的ISO代码
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        T前缀字段表示第三方平台数据
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
