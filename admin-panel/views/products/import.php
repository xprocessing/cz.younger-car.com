<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>批量导入商品</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-secondary">
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
                <form method="POST" action="<?php echo APP_URL; ?>/products.php?action=import_post" 
                      enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">选择CSV文件</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file" 
                               accept=".csv,.txt" required>
                        <div class="form-text">支持CSV格式文件，最大10MB，最多1000条记录</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-secondary me-md-2">
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
                        <li>SKU必须唯一，重复记录将被跳过</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fa fa-exclamation-triangle"></i> 注意事项</h6>
                    <ul class="mb-0">
                        <li>导入前请备份现有数据</li>
                        <li>请检查文件格式是否符合要求</li>
                        <li>金额字段请使用数字格式，如：99.00</li>
                        <li>重量字段请使用数字格式，如：0.5</li>
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
                <h6>字段顺序（共17列）：</h6>
                <ol class="list-unstyled">
                    <li><strong>SKU</strong> - sku (必填)</li>
                    <li><strong>SKU识别码</strong> - sku_identifier</li>
                    <li><strong>SPU</strong> - spu</li>
                    <li><strong>SPU ID</strong> - ps_id</li>
                    <li><strong>品牌名称</strong> - brand_name</li>
                    <li><strong>分类名称</strong> - category_name</li>
                    <li><strong>商品名称</strong> - product_name</li>
                    <li><strong>商品图片URL</strong> - pic_url</li>
                    <li><strong>采购成本</strong> - cg_price</li>
                    <li><strong>运输成本</strong> - cg_transport_costs</li>
                    <li><strong>采购交付</strong> - cg_delivery</li>
                    <li><strong>采购备注</strong> - purchase_remark</li>
                    <li><strong>销售状态</strong> - status (1=在售, 0=停售, 2=开发中, 3=清仓)</li>
                    <li><strong>启用状态</strong> - open_status (1=启用, 0=停用)</li>
                    <li><strong>是否组合产品</strong> - is_combo (1=是, 0=否)</li>
                    <li><strong>商品开发员</strong> - product_developer</li>
                    <li><strong>采购员用户名</strong> - cg_opt_username</li>
                </ol>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">示例数据</h5>
            </div>
            <div class="card-body">
                <h6>CSV示例：</h6>
                <pre class="bg-light p-2 rounded small"><code>SKU001,SKU001-001,SPU001,1,品牌A,分类A,示例商品,http://example.com/img.jpg,99.0000,5.00,7天,备注,1,1,0,张三,李四
SKU002,SKU002-001,SPU001,1,品牌A,分类A,示例商品2,http://example.com/img2.jpg,149.0000,8.00,7天,备注2,1,1,0,张三,李四
SKU003,SKU003-001,SPU002,2,品牌B,分类B,示例商品3,http://example.com/img3.jpg,199.0000,10.00,10天,备注3,1,1,0,王五,赵六</code></pre>
                
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
                    <a href="<?php echo APP_URL; ?>/products.php?action=create" class="btn btn-sm btn-outline-info w-100">
                        <i class="fa fa-plus"></i> 手动添加商品
                    </a>
                </div>
                <div class="mb-2">
                    <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-sm btn-outline-secondary w-100">
                        <i class="fa fa-list"></i> 查看所有商品
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function downloadTemplate() {
    const csvContent = `sku,sku_identifier,spu,ps_id,brand_name,category_name,product_name,pic_url,cg_price,cg_transport_costs,cg_delivery,purchase_remark,status,open_status,is_combo,product_developer,cg_opt_username
SKU001,SKU001-001,SPU001,1,品牌A,分类A,示例商品,http://example.com/img.jpg,99.0000,5.00,7天,备注,1,1,0,张三,李四
SKU002,SKU002-001,SPU001,1,品牌A,分类A,示例商品2,http://example.com/img2.jpg,149.0000,8.00,7天,备注2,1,1,0,张三,李四`;
    
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', 'products_template.csv');
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

document.getElementById('excel_file').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;
    
    if (file.size > 10 * 1024 * 1024) {
        alert('文件大小不能超过10MB');
        e.target.value = '';
        return;
    }
    
    if (!file.name.match(/\.(csv|txt)$/i)) {
        alert('请选择CSV或TXT格式的文件');
        e.target.value = '';
        return;
    }
    
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
        if (headerColumns !== 17) {
            alert('文件格式不正确，应有17列，实际有' + headerColumns + '列');
            e.target.value = '';
            return;
        }
    };
    reader.readAsText(file);
});

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
