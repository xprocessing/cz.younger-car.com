<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>批量导入广告费</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/costs.php" class="btn btn-secondary">
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
                <form method="POST" action="<?php echo APP_URL; ?>/costs.php?action=import_post" 
                      enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">选择CSV文件</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file"
                               accept=".csv,.txt" required>
                        <div class="form-text">支持CSV格式文件，最大10MB，最多1000条记录</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo APP_URL; ?>/costs.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-upload"></i> 开始导入
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">粘贴CSV内容导入</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/costs.php?action=import_post">
                    <div class="mb-3">
                        <label for="csv_content" class="form-label">CSV内容</label>
                        <textarea class="form-control" id="csv_content" name="csv_content" rows="8"
                                  placeholder="请粘贴CSV格式的内容，第一行为表头，将被跳过" required></textarea>
                        <div class="form-text">支持直接粘贴CSV内容，格式与文件导入相同</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo APP_URL; ?>/costs.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-clipboard"></i> 粘贴导入
                        </button>
                    </div>
                </form>
            </div>
        </div>
        
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
                        <li>同一平台、店铺、日期的记录将被视为重复，导入时会跳过</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fa fa-exclamation-triangle"></i> 注意事项</h6>
                    <ul class="mb-0">
                        <li>导入前请备份现有数据</li>
                        <li>请检查文件格式是否符合要求</li>
                        <li>金额字段请使用数字格式，如：299.00</li>
                        <li>日期格式：YYYY-MM-DD</li>
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
                <h6>字段顺序（共4列）：</h6>
                <ol class="list-unstyled">
                    <li><strong>平台名称</strong> - platform_name</li>
                    <li><strong>店铺名称</strong> - store_name</li>
                    <li><strong>日广告花费</strong> - cost</li>
                    <li><strong>日期</strong> - date</li>
                </ol>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">示例数据</h5>
            </div>
            <div class="card-body">
                <h6>CSV示例：</h6>
                <pre class="bg-light p-2 rounded small"><code>平台名称,店铺名称,日广告花费,日期
Amazon-FBA,Shop001,299.50,2024-01-15
eBay,Shop002,149.75,2024-01-15
Shopify,Shop003,89.99,2024-01-15</code></pre>
                
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
                    <a href="<?php echo APP_URL; ?>/costs.php?action=create" class="btn btn-sm btn-outline-info w-100">
                        <i class="fa fa-plus"></i> 手动添加记录
                    </a>
                </div>
                <div class="mb-2">
                    <a href="<?php echo APP_URL; ?>/costs.php" class="btn btn-sm btn-outline-secondary w-100">
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
    const csvContent = `平台名称,店铺名称,日广告花费,日期
Amazon-FBA,Shop001,299.50,2024-01-15
eBay,Shop002,149.75,2024-01-15
Shopify,Shop003,89.99,2024-01-15`;
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'costs_template.csv');
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
        if (headerColumns !== 4) {
            alert('文件格式不正确，应有4列，实际有' + headerColumns + '列');
            e.target.value = '';
            return;
        }
    };
    reader.readAsText(file);
});

// 表单提交确认
document.querySelectorAll('form').forEach(function(form) {
    form.addEventListener('submit', function(e) {
        if (form.querySelector('#excel_file')) {
            const fileInput = form.querySelector('#excel_file');
            if (!fileInput.files.length) {
                e.preventDefault();
                alert('请选择要导入的文件');
                return;
            }
        }
        
        if (form.querySelector('#csv_content')) {
            const csvContent = form.querySelector('#csv_content');
            if (!csvContent.value.trim()) {
                e.preventDefault();
                alert('请粘贴CSV内容');
                return;
            }
        }
        
        if (!confirm('确定要开始导入吗？请确保文件格式正确。')) {
            e.preventDefault();
            return;
        }
    });
});
</script>