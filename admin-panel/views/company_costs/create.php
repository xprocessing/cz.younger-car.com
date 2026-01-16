<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?action=create_post">
            <div class="mb-3">
                <label for="cost_date" class="form-label">费用日期 <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="cost_date" name="cost_date" required>
                <div class="form-text">请选择费用发生的日期，格式为YYYY-MM-DD</div>
            </div>
            
            <div class="mb-3">
                <label for="cost_type" class="form-label">费用类型 <span class="text-danger">*</span></label>
                <select class="form-select" id="cost_type" name="cost_type" required>
                    <option value="">请选择费用类型</option>
                    <option value="租赁费用">租赁费用</option>
                    <option value="人员工资">人员工资</option>
                    <option value="物业费">物业费</option>
                    <option value="网络通信费">网络通信费</option>
                    <option value="软件订阅/系统服务费">软件订阅/系统服务费</option>
                    <option value="其他费用">其他费用</option>
                </select>
                <div class="form-text">请选择费用的类型</div>
            </div>
            
            <div class="mb-3">
                <label for="cost" class="form-label">费用金额（美元） <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="cost" name="cost" required 
                       min="0" step="0.01" placeholder="请输入费用金额，如299.00">
                <div class="form-text">请输入费用金额，最多支持两位小数</div>
            </div>
            
            <div class="mb-3">
                <label for="remark" class="form-label">备注</label>
                <textarea class="form-control" id="remark" name="remark" rows="3" placeholder="请输入备注信息（可选）"></textarea>
                <div class="form-text">请输入备注信息（可选）</div>
            </div>
            
            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php" class="btn btn-secondary me-md-2">
                    <i class="fa fa-times"></i> 取消
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-save"></i> 保存
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    // 表单验证
    document.querySelector('form').addEventListener('submit', function(e) {
        const costDate = document.getElementById('cost_date').value;
        const costType = document.getElementById('cost_type').value;
        const cost = document.getElementById('cost').value;
        
        // 验证必填字段
        if (!costDate) {
            e.preventDefault();
            alert('请选择日期');
            document.getElementById('cost_date').focus();
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
        if (isNaN(cost) || parseFloat(cost) < 0) {
            e.preventDefault();
            alert('费用金额必须是有效的正数');
            document.getElementById('cost').focus();
            return;
        }
    });
</script>