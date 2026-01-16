<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_other_costs.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/order_other_costs.php?action=edit_post">
            <input type="hidden" name="id" value="<?php echo $cost['id']; ?>">
            
            <div class="mb-3">
                <label for="cost_date" class="form-label">费用日期 <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="cost_date" name="cost_date" 
                       value="<?php echo $cost['cost_date']; ?>" required>
                <div class="form-text">请选择费用发生的日期，格式为YYYY-MM-DD</div>
            </div>
            
            <div class="mb-3">
                <label for="order_id" class="form-label">订单号 <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="order_id" name="order_id" 
                       value="<?php echo htmlspecialchars($cost['order_id']); ?>" required 
                       placeholder="请输入订单号" maxlength="50">
                <div class="form-text">请输入订单号，最大50个字符</div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="platform_name" class="form-label">平台名称 <span class="text-danger">*</span></label>
                    <select class="form-select" id="platform_name" name="platform_name" required>
                        <option value="">请选择平台名称</option>
                        <option value="Amazon-FBA" <?php echo ($cost['platform_name'] == 'Amazon-FBA' ? 'selected' : ''); ?>>Amazon-FBA</option>
                        <option value="Amazon" <?php echo ($cost['platform_name'] == 'Amazon' ? 'selected' : ''); ?>>Amazon</option>
                        <option value="eBay" <?php echo ($cost['platform_name'] == 'eBay' ? 'selected' : ''); ?>>eBay</option>
                        <option value="Shopify" <?php echo ($cost['platform_name'] == 'Shopify' ? 'selected' : ''); ?>>Shopify</option>
                        <option value="其他平台" <?php echo ($cost['platform_name'] == '其他平台' ? 'selected' : ''); ?>>其他平台</option>
                    </select>
                    <div class="form-text">请选择订单所属的平台</div>
                </div>
                
                <div class="col-md-6">
                    <label for="store_name" class="form-label">店铺名称 <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="store_name" name="store_name" 
                           value="<?php echo htmlspecialchars($cost['store_name']); ?>" required 
                           placeholder="请输入店铺名称" maxlength="50">
                    <div class="form-text">请输入店铺名称，最大50个字符</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="cost_type" class="form-label">费用类型 <span class="text-danger">*</span></label>
                <select class="form-select" id="cost_type" name="cost_type" required>
                    <option value="">请选择费用类型</option>
                    <option value="退货运费" <?php echo ($cost['cost_type'] == '退货运费' ? 'selected' : ''); ?>>退货运费</option>
                    <option value="运费补收" <?php echo ($cost['cost_type'] == '运费补收' ? 'selected' : ''); ?>>运费补收</option>
                    <option value="运输索赔" <?php echo ($cost['cost_type'] == '运输索赔' ? 'selected' : ''); ?>>运输索赔</option>
                    <option value="其他费用" <?php echo ($cost['cost_type'] == '其他费用' ? 'selected' : ''); ?>>其他费用</option>
                </select>
                <div class="form-text">请选择费用的类型</div>
            </div>
            
            <div class="mb-3">
                <label for="cost" class="form-label">费用金额（美元） <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="cost" name="cost" 
                       value="<?php echo $cost['cost']; ?>" required 
                       step="0.01" placeholder="请输入费用金额，如29.99">
                <div class="form-text">请输入费用金额，最多支持两位小数，支持负数（如退款）</div>
            </div>
            
            <div class="mb-3">
                <label for="remark" class="form-label">备注</label>
                <textarea class="form-control" id="remark" name="remark" rows="3" 
                          placeholder="请输入备注信息（可选）"><?php echo htmlspecialchars($cost['remark'] ?? ''); ?></textarea>
                <div class="form-text">请输入备注信息（可选）</div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo ADMIN_PANEL_URL; ?>/order_other_costs.php" class="btn btn-secondary me-md-2">
                    <i class="fa fa-times"></i> 取消
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> 更新
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // 表单验证
    document.querySelector('form').addEventListener('submit', function(e) {
        const costDate = document.getElementById('cost_date').value;
        const orderId = document.getElementById('order_id').value;
        const platformName = document.getElementById('platform_name').value;
        const storeName = document.getElementById('store_name').value;
        const costType = document.getElementById('cost_type').value;
        const cost = document.getElementById('cost').value;
        
        // 验证必填字段
        if (!costDate) {
            e.preventDefault();
            alert('请选择日期');
            document.getElementById('cost_date').focus();
            return;
        }
        
        if (!orderId) {
            e.preventDefault();
            alert('请输入订单号');
            document.getElementById('order_id').focus();
            return;
        }
        
        if (!platformName) {
            e.preventDefault();
            alert('请选择平台名称');
            document.getElementById('platform_name').focus();
            return;
        }
        
        if (!storeName) {
            e.preventDefault();
            alert('请输入店铺名称');
            document.getElementById('store_name').focus();
            return;
        }
        
        if (!costType) {
            e.preventDefault();
            alert('请选择费用类型');
            document.getElementById('cost_type').focus();
            return;
        }
        
        if (!cost) {
            e.preventDefault();
            alert('请输入费用金额');
            document.getElementById('cost').focus();
            return;
        }
        
        // 验证费用金额
        if (isNaN(cost)) {
            e.preventDefault();
            alert('费用金额必须是有效的数字');
            document.getElementById('cost').focus();
            return;
        }
    });
</script>