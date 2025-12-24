<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>创建订单利润</h4>
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
                <h5 class="mb-0">基本信息</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/order_profit.php?action=create_post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_id" class="form-label">店铺ID</label>
                                <input type="text" class="form-control" id="store_id" name="store_id"
                                       placeholder="请输入店铺ID" maxlength="50">
                                <div class="form-text">店铺的唯一标识</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="global_order_no" class="form-label">订单号 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="global_order_no" name="global_order_no" required
                                       placeholder="请输入订单号" maxlength="50">
                                <div class="form-text">唯一的订单编号</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="receiver_country" class="form-label">收货国家</label>
                                <input type="text" class="form-control" id="receiver_country" name="receiver_country"
                                       placeholder="请输入收货国家" maxlength="10">
                                <div class="form-text">如：美国、英国等</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="global_purchase_time" class="form-label">下单时间</label>
                                <input type="text" class="form-control" id="global_purchase_time" name="global_purchase_time"
                                       placeholder="请输入下单时间" maxlength="30">
                                <div class="form-text">格式：YYYY-MM-DD HH:MM:SS</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="local_sku" class="form-label">SKU</label>
                        <input type="text" class="form-control" id="local_sku" name="local_sku"
                               placeholder="请输入SKU" maxlength="50">
                        <div class="form-text">商品库存单位</div>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">金额信息</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="order_total_amount" class="form-label">订单总额</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="order_total_amount" 
                                           name="order_total_amount" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profit_amount" class="form-label">毛利润</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="profit_amount" 
                                           name="profit_amount" placeholder="0.00">
                                </div>
                                <div class="form-text">手动输入毛利润金额</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profit_rate" class="form-label">利润率</label>
                                <div class="input-group">
                                    <input type="number" step="0.01" class="form-control" id="profit_rate" 
                                           name="profit_rate" placeholder="0.00">
                                    <span class="input-group-text">%</span>
                                </div>
                                <div class="form-text">手动输入或自动计算利润率</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wms_outbound_cost_amount" class="form-label">实际出库成本（CNY）</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="wms_outbound_cost_amount" 
                                           name="wms_outbound_cost_amount" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wms_shipping_price_amount" class="form-label">实际运费（CNY）</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="wms_shipping_price_amount" 
                                           name="wms_shipping_price_amount" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-secondary me-md-2">
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
                <h5 class="mb-0">计算说明</h5>
            </div>
            <div class="card-body">
                <h6>利润计算公式：</h6>
                <div class="bg-light p-3 rounded mb-3">
                    <code>利润率 = 毛利润 / 订单总额 × 100%</code>
                </div>

                <h6 class="mt-3">字段说明：</h6>
                <ul class="list-unstyled">
                    <li><strong>订单总额</strong>: 客户支付的总金额</li>
                    <li><strong>毛利润</strong>: 订单毛利（手动输入）</li>
                    <li><strong>利润率</strong>: 毛利率百分比</li>
                    <li><strong>实际出库成本</strong>: WMS系统记录的实际成本</li>
                    <li><strong>实际运费</strong>: WMS系统记录的实际运费</li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">快速操作</h5>
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
                    <a href="<?php echo APP_URL; ?>/order_profit.php?action=import" class="btn btn-sm btn-outline-success w-100">
                        <i class="fa fa-upload"></i> 批量导入
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 自动计算利润率
function calculateProfitRate() {
    const totalAmount = parseFloat(document.getElementById('order_total_amount').value) || 0;
    const profitAmount = parseFloat(document.getElementById('profit_amount').value) || 0;
    
    const profitRate = totalAmount > 0 ? (profitAmount / totalAmount * 100) : 0;
    
    document.getElementById('profit_rate').value = profitRate.toFixed(2);
}

// 监听金额输入变化
document.getElementById('order_total_amount').addEventListener('input', calculateProfitRate);
document.getElementById('profit_amount').addEventListener('input', calculateProfitRate);

// 设置示例数据
function setSampleData() {
    document.getElementById('store_id').value = 'STORE001';
    document.getElementById('global_order_no').value = 'ORDER' + Date.now();
    document.getElementById('receiver_country').value = '美国';
    document.getElementById('global_purchase_time').value = new Date().toISOString().slice(0, 19).replace('T', ' ');
    document.getElementById('local_sku').value = 'SKU001';
    document.getElementById('order_total_amount').value = '299.00';
    document.getElementById('profit_amount').value = '149.00';
    document.getElementById('wms_outbound_cost_amount').value = '145.00';
    document.getElementById('wms_shipping_price_amount').value = '25.00';
    calculateProfitRate();
}

// 清空表单
function clearForm() {
    document.querySelectorAll('input[type="text"], input[type="number"]').forEach(input => {
        input.value = '';
    });
}

// 表单验证
document.querySelector('form').addEventListener('submit', function(e) {
    const orderNo = document.getElementById('global_order_no').value.trim();
    if (!orderNo) {
        e.preventDefault();
        alert('请输入订单号');
        document.getElementById('global_order_no').focus();
        return;
    }

    const totalAmount = parseFloat(document.getElementById('order_total_amount').value) || 0;
    if (totalAmount <= 0) {
        e.preventDefault();
        alert('订单总额必须大于0');
        document.getElementById('order_total_amount').focus();
        return;
    }
});
</script>