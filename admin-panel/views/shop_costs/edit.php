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
                <h5 class="mb-0">编辑广告费记录</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/shop_costs.php?action=edit_post">
                    <input type="hidden" name="id" value="<?php echo $cost['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="platform_name" class="form-label">平台名称 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="platform_name" name="platform_name" required
                                       value="<?php echo htmlspecialchars($cost['platform_name']); ?>"
                                       placeholder="请输入平台名称（如Amazon-FBA，Amazon、eBay，Shopify）" maxlength="50">
                                <div class="form-text">平台名称，如Amazon-FBA，Amazon、eBay，Shopify</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="store_name" class="form-label">店铺名称 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="store_name" name="store_name" required
                                       value="<?php echo htmlspecialchars($cost['store_name']); ?>"
                                       placeholder="请输入店铺名称" maxlength="50">
                                <div class="form-text">店铺名称</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="track_name" class="form-label">赛道名称 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="track_name" name="track_name" required
                                       value="<?php echo htmlspecialchars($cost['track_name'] ?? ''); ?>"
                                       placeholder="请输入赛道名称" maxlength="50">
                                <div class="form-text">赛道名称，如汽车用品、电子产品等</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost" class="form-label">日广告花费（美元）<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control" id="cost" 
                                           name="cost" required placeholder="0.00"
                                           value="<?php echo htmlspecialchars($cost['cost']); ?>">
                                </div>
                                <div class="form-text">日广告花费金额</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_type" class="form-label">费用类型 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cost_type" name="cost_type" required
                                       value="<?php echo htmlspecialchars($cost['cost_type']); ?>"
                                       placeholder="请输入费用类型（如广告费用、平台租金、其他费用）" maxlength="50">
                                <div class="form-text">费用类型，如广告费用、平台租金、其他费用</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="cost_date" class="form-label">日期 <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="cost_date" name="cost_date" required
                                       value="<?php echo $cost['cost_date']; ?>">
                                <div class="form-text">数据日期，按天存储</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="remark" class="form-label">备注</label>
                                <textarea class="form-control" id="remark" name="remark" rows="3"
                                          placeholder="请输入备注信息" maxlength="255"><?php echo htmlspecialchars($cost['remark'] ?? ''); ?></textarea>
                                <div class="form-text">可选，最多255个字符</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">更新时间</label>
                        <div class="form-control-plaintext">
                            <strong><?php echo $cost['update_at']; ?></strong>
                            <small class="text-muted">（保存后自动更新为当前时间）</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo ADMIN_PANEL_URL; ?>/shop_costs.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> 保存更改
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">记录概览</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td><?php echo $cost['id']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>平台名称:</strong></td>
                        <td><?php echo htmlspecialchars($cost['platform_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>店铺名称:</strong></td>
                        <td><?php echo htmlspecialchars($cost['store_name']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>费用类型:</strong></td>
                        <td><?php echo htmlspecialchars($cost['cost_type']); ?></td>
                    </tr>
                    <tr>
                        <td><strong>日期:</strong></td>
                        <td><?php echo $cost['cost_date']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>创建时间:</strong></td>
                        <td><?php echo $cost['create_at']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>更新时间:</strong></td>
                        <td><?php echo $cost['update_at']; ?></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">字段说明</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><strong>平台名称</strong>: 平台名称，如Amazon-FBA，Amazon、eBay，Shopify</li>
                    <li><strong>店铺名称</strong>: 店铺名称</li>
                    <li><strong>日广告花费</strong>: 每日广告花费金额（美元）</li>
                    <li><strong>费用类型</strong>: 费用类型，如广告费用、平台租金、其他费用</li>
                    <li><strong>日期</strong>: 数据日期，按天存储</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
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

    const date = document.getElementById('cost_date').value;
    if (!date) {
        e.preventDefault();
        alert('请选择日期');
        document.getElementById('cost_date').focus();
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
