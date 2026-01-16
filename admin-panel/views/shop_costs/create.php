<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/shop_costs.php" class="btn btn-secondary">
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
                <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/shop_costs.php?action=create_post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="platform_name" class="form-label">平台名称 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="platform_name" name="platform_name" required
                                       placeholder="请输入平台名称（如Amazon-FBA，Amazon、eBay，Shopify�? maxlength="50">
                                <div class="form-text">平台名称，如Amazon-FBA，Amazon、eBay，Shopify</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_name" class="form-label">店铺名称 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="store_name" name="store_name" required
                                       placeholder="请输入店铺名" maxlength="50">
                                <div class="form-text">店铺名称</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost" class="form-label">日广告花费（美元�?<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="cost" 
                                           name="cost" required placeholder="0.00">
                                </div>
                                <div class="form-text">日广告花费金�?/div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_type" class="form-label">费用类型 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cost_type" name="cost_type" required
                                       placeholder="请输入费用类型（如广告费用、平台租金、其他费用）" maxlength="50">
                                <div class="form-text">费用类型，如广告费用、平台租金、其他费�?/div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_date" class="form-label">日期 <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="cost_date" name="cost_date" required
                                       placeholder="请选择日期">
                                <div class="form-text">数据日期，按天存�?/div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="remark" class="form-label">备注</label>
                                <textarea class="form-control" id="remark" name="remark" rows="3"
                                          placeholder="请输入备注信�? maxlength="255"></textarea>
                                <div class="form-text">可选，最�?55个字�?/div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo ADMIN_PANEL_URL; ?>/shop_costs.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> 创建
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">字段说明</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><strong>平台名称</strong>: 平台名称，如Amazon-FBA，Amazon、eBay，Shopify</li>
                    <li><strong>店铺名称</strong>: 店铺名称</li>
                    <li><strong>日广告花�?/strong>: 每日广告花费金额（美元）</li>
                    <li><strong>费用类型</strong>: 费用类型，如广告费用、平台租金、其他费�?/li>
                    <li><strong>日期</strong>: 数据日期，按天存�?/li>
                    <li>特别注意：平台名字和店铺名字必须大小写一致，具体参照<a href="<?php echo ADMIN_PANEL_URL; ?>/store.php" target="_blank">店铺列表</a></li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">快速操�?/h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="setSampleData()">
                        填充示例数据
                    </button>
                </div>
                <div class="mb-2">
                    <button class="btn btn-sm btn-outline-info w-100" onclick="clearForm()">
                        清空表单
                    </button>
                </div>
                <div class="mb-2">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/shop_costs.php?action=import" class="btn btn-sm btn-outline-success w-100">
                        <i class="fa fa-upload"></i> 批量导入
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 设置示例数据
function setSampleData() {
    document.getElementById('platform_name').value = 'Amazon-FBA';
    document.getElementById('store_name').value = 'US Store 1';
    document.getElementById('cost').value = '250.50';
    document.getElementById('cost_type').value = '广告费用';
    document.getElementById('date').value = new Date().toISOString().slice(0, 10);
    document.getElementById('remark').value = '示例备注信息';
}

// 清空表单
function clearForm() {
    document.querySelectorAll('input[type="text"], input[type="number"], input[type="date"], textarea').forEach(input => {
        input.value = '';
    });
}

// 表单验证
document.querySelector('form').addEventListener('submit', function(e) {
    const platformName = document.getElementById('platform_name').value.trim();
    if (!platformName) {
        e.preventDefault();
        alert('请输入平台名称');
        document.getElementById('platform_name').focus();
        return;
    }

    const storeName = document.getElementById('store_name').value.trim();
    if (!storeName) {
        e.preventDefault();
        alert('请输入店铺名称');
        document.getElementById('store_name').focus();
        return;
    }

    const cost = parseFloat(document.getElementById('cost').value) || 0;
    if (cost <= 0) {
        e.preventDefault();
        alert('日广告花费必须大于0');
        document.getElementById('cost').focus();
        return;
    }

    const date = document.getElementById('date').value;
    if (!date) {
        e.preventDefault();
        alert('请选择日期');
        document.getElementById('date').focus();
        return;
    }

    const costType = document.getElementById('cost_type').value.trim();
    if (!costType) {
        e.preventDefault();
        alert('请输入费用类型');
        document.getElementById('cost_type').focus();
        return;
    }
});
</script>
