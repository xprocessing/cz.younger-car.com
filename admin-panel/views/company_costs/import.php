<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>批量导入公司运营费用</h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php" class="btn btn-secondary">
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
                <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?action=import_post" 
                      enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="excel_file" class="form-label">选择CSV文件</label>
                        <input type="file" class="form-control" id="excel_file" name="excel_file"
                               accept=".csv,.txt" required>
                        <div class="form-text">支持CSV格式文件，最大0MB，最多000条记录</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fa fa-upload"></i> 开始导入</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">粘贴CSV内容导入</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?action=import_post">
                    <div class="mb-3">
                        <label for="csv_content" class="form-label">CSV内容</label>
                        <textarea class="form-control" id="csv_content" name="csv_content" rows="8"
                                  placeholder="请粘贴CSV格式的内容，第一行为表头，将被跳过" required></textarea>
                        <div class="form-text">支持直接粘贴CSV内容，格式与文件导入相同</div>
                    </div>
                    
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php" class="btn btn-secondary me-md-2">
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
                        <li>同一费用类型、日期的记录将被视为重复，导入时会跳过</li>
                    </ul>
                </div>
                
                <div class="alert alert-warning">
                    <h6><i class="fa fa-exclamation-triangle"></i> 注意事项</h6>
                    <ul class="mb-0">
                        <li>导入前请备份现有数据</li>
                        <li>请检查文件格式是否符合要求</li>
                        <li>金额字段请使用数字格式，如：299.00</li>
                        <li>日期格式：YYYY-MM-DD （如：2024-01-15）</li>
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
                <h6>字段顺序（共4列）</h6>
                <ol class="list-unstyled">
                    <li><strong>费用日期</strong> - cost_date</li>
                    <li><strong>费用类型</strong> - cost_type</li>
                    <li><strong>费用金额</strong> - cost</li>
                    <li><strong>备注</strong> - remark（可选）</li>
                </ol>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">示例数据</h5>
            </div>
            <div class="card-body">
                <h6>CSV示例</h6>
                <pre class="bg-light p-2 rounded small"><code>费用日期,费用类型,费用金额,备注
2024-01-15,租赁费用,5000.00,办公室租金
2024-01-15,人员工资,25000.00,1月份工资
2024-01-15,网络通信费,200.00,宽带费用
2024-01-15,软件订阅/系统服务费,500.00,Shopify订阅费</code></pre>
                
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
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?action=create" class="btn btn-sm btn-outline-info w-100">
                        <i class="fa fa-plus"></i> 手动添加记录
                    </a>
                </div>
                <div class="mb-2">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php" class="btn btn-sm btn-outline-secondary w-100">
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
        const csvContent = "data:text/csv;charset=utf-8," + 
                           "费用日期,费用类型,费用金额,备注\n" +
                           "2024-01-15,租赁费用,5000.00,办公室租金\n" +
                           "2024-01-15,人员工资,25000.00,1月份工资\n" +
                           "2024-01-15,网络通信费,200.00,宽带费用\n" +
                           "2024-01-15,软件订阅/系统服务费,500.00,Shopify订阅费\n" +
                           "2024-01-15,其他费用,100.00,办公用品费用\n";
        
        const encodedUri = encodeURI(csvContent);
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", "company_costs_template.csv");
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }
</script>