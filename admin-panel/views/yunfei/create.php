<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>创建运费记录</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo ADMIN_PANEL_URL; ?>/yunfei.php?action=create" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="global_order_no" class="form-label">订单号 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="global_order_no" name="global_order_no" required>
                                <div class="form-text">请输入全局订单号</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="yunfei_data" class="form-label">运费数据</label>
                                <textarea class="form-control" id="yunfei_data" name="yunfei_data" rows="10" placeholder='请输入JSON格式的运费数据，例如：
{
  "weight": 2.5,
  "distance": 100,
  "base_fee": 10.00,
  "total_fee": 25.50,
  "shipping_method": "express",
  "delivery_time": "2-3天"
}'></textarea>
                                <div class="form-text">
                                    请输入有效的JSON格式数据，可以为空
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <button type="button" class="btn btn-secondary" onclick="validateJSON()">
                                    <i class="fa fa-check"></i> 验证JSON格式
                                </button>
                                <button type="button" class="btn btn-info" onclick="formatJSON()">
                                    <i class="fa fa-indent"></i> 格式化JSON
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-save"></i> 创建
                            </button>
                            <a href="<?php echo ADMIN_PANEL_URL; ?>/yunfei.php" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> 返回
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function validateJSON() {
    const textarea = document.getElementById('yunfei_data');
    const value = textarea.value.trim();
    
    if (!value) {
        alert('JSON数据为空，无需验证');
        return;
    }
    
    try {
        JSON.parse(value);
        alert('JSON格式正确！');
        textarea.classList.remove('is-invalid');
        textarea.classList.add('is-valid');
    } catch (e) {
        alert('JSON格式错误：' + e.message);
        textarea.classList.remove('is-valid');
        textarea.classList.add('is-invalid');
    }
}

function formatJSON() {
    const textarea = document.getElementById('yunfei_data');
    const value = textarea.value.trim();
    
    if (!value) {
        alert('JSON数据为空，无需格式化');
        return;
    }
    
    try {
        const parsed = JSON.parse(value);
        textarea.value = JSON.stringify(parsed, null, 2);
        textarea.classList.remove('is-invalid');
        textarea.classList.add('is-valid');
    } catch (e) {
        alert('JSON格式错误，无法格式化：' + e.message);
        textarea.classList.remove('is-valid');
        textarea.classList.add('is-invalid');
    }
}
</script>