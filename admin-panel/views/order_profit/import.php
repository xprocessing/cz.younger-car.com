<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>批量导入订单利润</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">文件上传</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/order_profit.php?action=import_post" 
                      enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">选择CSV文件</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" 
                               accept=".csv,.txt" required>
                        <div class="form-text">支持CSV格式文件，最大10MB，最多1000条记录</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-upload"></i> 开始导入
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- 导入历史记录 -->
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">导入说明</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fa fa-info-circle"></i> 导入要求</h6>
                    <ul class="mb-0">
                        <li>文件格式：CSV（逗号分隔）</li>
                        <li>文件大小：不超过10MB</li>
                        <li>记录数量：不超过1000条</li>
                        <li>第一行为表头，将被跳过</li>
                        <li>订单号必须唯一，重复记录将被跳过</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fa fa-exclamation-triangle"></i> 注意事项</h6>
                    <ul class="mb-0">
                        <li>导入前请备份现有数据</li>
                        <li>请检查文件格式是否符合要求</li>
                        <li>金额字段请使用数字格式，如：299.00</li>
                        <li>日期时间格式：YYYY-MM-DD HH:MM:SS</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">CSV格式说明</h5>
            </div>
            <div class="card-body">
                <h6>字段顺序（共11列）：</h6>
                <ol class="list-unstyled">
                    <li><strong>店铺ID</strong> - store_id</li>
                    <li><strong>订单号</strong> - global_order_no</li>
                    <li><strong>收货国家</strong> - receiver_country</li>
                    <li><strong>下单时间</strong> - global_purchase_time</li>
                    <li><strong>SKU</strong> - local_sku</li>
                    <li><strong>订单总额</strong> - order_total_amount</li>
                    <li><strong>出库成本</strong> - outbound_cost_amount</li>
                    <li><strong>毛利润</strong> - profit_amount</li>
                    <li><strong>利润率</strong> - profit_rate</li>
                    <li><strong>实际出库成本</strong> - wms_outbound_cost_amount</li>
                    <li><strong>实际运费</strong> - wms_shipping_price_amount</li>
                </ol>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">示例数据</h5>
            </div>
            <div class="card-body">
                <h6>CSV示例：</h6>
                <pre class="bg-light p-2 rounded small"><code>STORE001,ORDER2024001,美国,2024-01-15 10:30:00,SKU001,299.00,150.00,149.00,49.83,145.00,25.00
STORE002,ORDER2024002,英国,2024-01-15 11:45:00,SKU002,199.00,80.00,119.00,59.80,78.00,18.00
STORE001,ORDER2024003,美国,2024-01-15 14:20:00,SKU003,599.00,320.00,279.00,46.58,315.00,45.00</code></pre>
                
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="downloadTemplate()">
                        <i class="fa fa-download"></i> 下载模板文件
                    </button>
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">快速操作</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <a href="<?php echo APP_URL; ?>/order_profit.php?action=create" class="btn btn-sm btn-outline-info w-100">
                        <i class="fa fa-plus"></i> 手动添加记录
                    </a>
                </div>
                <div class="mb-2">
                    <a href="<?php echo APP_URL; ?>/order_profit.php?action=stats" class="btn btn-sm btn-outline-success w-100">
                        <i class="fa fa-bar-chart"></i> 查看利润统计
                    </a>
                </div>
                <div class="mb-2">
                    <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fa fa-list"></i> 查看所有记录
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 下载模板文件
function downloadTemplate() {
    const csvContent = `store_id,global_order_no,receiver_country,global_purchase_time,local_sku,order_total_amount,outbound_cost_amount,profit_amount,profit_rate,wms_outbound_cost_amount,wms_shipping_price_amount
STORE001,ORDER2024001,美国,2024-01-15 10:30:00,SKU001,299.00,150.00,149.00,49.83,145.00,25.00
STORE002,ORDER2024002,英国,2024-01-15 11:45:00,SKU002,199.00,80.00,119.00,59.80,78.00,18.00`;
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'order_profit_template.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// 文件验证
document.getElementById('excel_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    // 检查文件大小
    if (file.size > 10 * 1024 * 1024) {
        alert('文件大小不能超过10MB');
        e.target.value = '';
        return;
    }
    
    // 检查文件类型
    if (!file.name.match(/\.(csv|txt)$/i)) {
        alert('请选择CSV或TXT格式的文件');
        e.target.value = '';
        return;
    }
    
    // 读取文件头进行简单验证
    const reader = new FileReader();
    reader.onload = function(e) {
        const content = e.target.result;
        const lines = content.split('\n');
        
        if (lines.length < 2) {
            alert('文件内容不完整，请检查文件格式');
            e.target.value = '';
            return;
        }
        
        const headerColumns = lines[0].split(',').length;
        if (headerColumns !== 11) {
            alert('文件格式不正确，应有11列，实际有' + headerColumns + '列');
            e.target.value = '';
            return;
        }
    };
    reader.readAsText(file);
});

// 表单提交确认
document.querySelector('form').addEventListener('submit', function(e) {
    const fileInput = document.getElementById('excel_file');
    if (!fileInput.files.length) {
        e.preventDefault();
        alert('请选择要导入的文件');
        return;
    }
    
    if (!confirm('确定要开始导入吗？请确保文件格式正确。')) {
        e.preventDefault();
        return;
    }
});
</script>