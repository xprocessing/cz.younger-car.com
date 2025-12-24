<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>编辑店铺</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/store.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">店铺信息</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/store.php?action=edit_post">
                    <input type="hidden" name="store_id" value="<?php echo htmlspecialchars($store['store_id']); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_id" class="form-label">店铺ID <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="store_id" value="<?php echo htmlspecialchars($store['store_id']); ?>" disabled>
                                <div class="form-text">店铺ID不可修改</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sid" class="form-label">店铺编号</label>
                                <input type="text" class="form-control" id="sid" name="sid"
                                       value="<?php echo htmlspecialchars($store['sid'] ?? ''); ?>"
                                       placeholder="请输入店铺编号" maxlength="50">
                                <div class="form-text">店铺的内部编号</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_name" class="form-label">店铺名称 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="store_name" name="store_name" required
                                       value="<?php echo htmlspecialchars($store['store_name']); ?>"
                                       placeholder="请输入店铺名称" maxlength="100">
                                <div class="form-text">店铺的显示名称</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="platform_code" class="form-label">平台编码 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="platform_code" name="platform_code" required
                                       value="<?php echo htmlspecialchars($store['platform_code']); ?>"
                                       placeholder="请输入平台编码" maxlength="20">
                                <div class="form-text">平台的唯一编码</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="platform_name" class="form-label">平台名称 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="platform_name" name="platform_name" required
                                       value="<?php echo htmlspecialchars($store['platform_name']); ?>"
                                       placeholder="请输入平台名称" maxlength="50">
                                <div class="form-text">平台的显示名称</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency" class="form-label">货币类型 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="currency" name="currency" required
                                       value="<?php echo htmlspecialchars($store['currency']); ?>"
                                       placeholder="请输入货币类型" maxlength="10">
                                <div class="form-text">如：CNY、USD、EUR等</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="is_sync" class="form-label">是否同步</label>
                                <select class="form-select" id="is_sync" name="is_sync">
                                    <option value="1" <?php echo $store['is_sync'] == 1 ? 'selected' : ''; ?>>是</option>
                                    <option value="0" <?php echo $store['is_sync'] == 0 ? 'selected' : ''; ?>>否</option>
                                </select>
                                <div class="form-text">是否与外部系统同步</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="status" class="form-label">状态</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="1" <?php echo $store['status'] == 1 ? 'selected' : ''; ?>>正常</option>
                                    <option value="0" <?php echo $store['status'] != 1 ? 'selected' : ''; ?>>异常</option>
                                </select>
                                <div class="form-text">店铺的运营状态</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="country_code" class="form-label">国家/地区编码</label>
                                <input type="text" class="form-control" id="country_code" name="country_code"
                                       value="<?php echo htmlspecialchars($store['country_code'] ?? ''); ?>"
                                       placeholder="请输入国家/地区编码" maxlength="20">
                                <div class="form-text">如：CN、US、GB等</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check"></i> 保存修改
                        </button>
                        <a href="<?php echo APP_URL; ?>/store.php" class="btn btn-outline-secondary ms-2">
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
                        店铺ID不可修改
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        修改后请点击保存按钮
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
