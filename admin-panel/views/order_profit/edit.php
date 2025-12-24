<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>编辑订单利润</h4>
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
                <h5 class="mb-0">编辑基本信息</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/order_profit.php?action=edit_post">
                    <input type="hidden" name="id" value="<?php echo $profit['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="store_id" class="form-label">店铺</label>
                                <select class="form-control" id="store_id" name="store_id">
                                    <option value="">请选择店铺</option>
                                    <?php if (!empty($storeList)): ?>
                                        <?php foreach ($storeList as $store): ?>
                                            <option value="<?php echo htmlspecialchars($store['store_id']); ?>"
                                                    <?php echo ($store['store_id'] == ($profit['store_id'] ?? '')) ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($store['platform_name'] . ' - ' . $store['store_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text">选择对应的店铺</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="global_order_no" class="form-label">订单号 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="global_order_no" name="global_order_no" required
                                       value="<?php echo htmlspecialchars($profit['global_order_no'] ?? ''); ?>"
                                       placeholder="请输入订单号" maxlength="50">
                                <div class="form-text">唯一的订单编号</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="receiver_country" class="form-label">收货国家</label>
                                <input type="text" class="form-control" id="receiver_country" name="receiver_country"
                                       value="<?php echo htmlspecialchars($profit['receiver_country'] ?? ''); ?>"
                                       placeholder="请输入收货国家" maxlength="10">
                                <div class="form-text">如：美国、英国等</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="warehouse_name" class="form-label">发货仓库</label>
                                <input type="text" class="form-control" id="warehouse_name" name="warehouse_name"
                                       value="<?php echo htmlspecialchars($profit['warehouse_name'] ?? ''); ?>"
                                       placeholder="请输入发货仓库名称" maxlength="50">
                                <div class="form-text">如：中国仓库、美国仓库等</div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="global_purchase_time" class="form-label">下单时间</label>
                                <input type="text" class="form-control" id="global_purchase_time" name="global_purchase_time"
                                       value="<?php echo htmlspecialchars($profit['global_purchase_time'] ?? ''); ?>"
                                       placeholder="请输入下单时间" maxlength="30">
                                <div class="form-text">格式：YYYY-MM-DD HH:MM:SS</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="local_sku" class="form-label">SKU</label>
                        <input type="text" class="form-control" id="local_sku" name="local_sku"
                               value="<?php echo htmlspecialchars($profit['local_sku'] ?? ''); ?>"
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
                                           name="order_total_amount" placeholder="0.00"
                                           value="<?php echo htmlspecialchars($profit['order_total_amount'] ?? '0.00'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profit_amount" class="form-label">毛利润</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="profit_amount" 
                                           name="profit_amount" placeholder="0.00"
                                           value="<?php echo htmlspecialchars($profit['profit_amount'] ?? '0.00'); ?>">
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
                                           name="profit_rate" placeholder="0.00"
                                           value="<?php echo htmlspecialchars($profit['profit_rate'] ?? '0.00'); ?>">
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
                                           name="wms_outbound_cost_amount" placeholder="0.00"
                                           value="<?php echo htmlspecialchars($profit['wms_outbound_cost_amount'] ?? '0.00'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wms_shipping_price_amount" class="form-label">实际运费（CNY）</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="wms_shipping_price_amount" 
                                           name="wms_shipping_price_amount" placeholder="0.00"
                                           value="<?php echo htmlspecialchars($profit['wms_shipping_price_amount'] ?? '0.00'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">更新时间</label>
                        <div class="form-control-plaintext">
                            <strong><?php echo htmlspecialchars($profit['update_time'] ?? ''); ?></strong>
                            <small class="text-muted">（保存后自动更新为当前时间）</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                        <button type="button" class="btn btn-outline-info me-md-2" onclick="recalculateProfitRate()">
                            <i class="fa fa-calculator"></i> 重新计算
                        </button>
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
                <h5 class="mb-0">订单概览</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td><?php echo $profit['id']; ?></td>
                    </tr>
                    <?php if (!empty($profit['store_id'])): ?>
                    <tr>
                        <td><strong>店铺:</strong></td>
                        <td><?php echo htmlspecialchars(($profit['platform_name'] ?? '') . ' - ' . ($profit['store_name'] ?? $profit['store_id'])); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($profit['local_sku'])): ?>
                    <tr>
                        <td><strong>SKU:</strong></td>
                        <td><?php echo htmlspecialchars($profit['local_sku']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><strong>订单号:</strong></td>
                        <td><code><?php echo htmlspecialchars($profit['global_order_no'] ?? ''); ?></code></td>
                    </tr>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">利润分析</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>订单总额:</span>
                        <strong class="text-success"><?php echo htmlspecialchars($profit['order_total_amount'] ?? ''); ?></strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>毛利润:</span>
                        <strong class="<?php echo ($profit['profit_amount'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo htmlspecialchars($profit['profit_amount'] ?? ''); ?>
                        </strong>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>利润率:</span>
                        <strong class="<?php echo ($profit['profit_rate'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo number_format($profit['profit_rate'] ?? 0, 2); ?>%
                        </strong>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-2">
                    <small class="text-muted">实际成本合计:</small>
                    <div class="d-flex justify-content-between">
                        <span>出库成本 + 运费:</span>
                        <strong>¥<?php 
                            $totalActualCost = ($profit['wms_outbound_cost_amount'] ?? 0) + ($profit['wms_shipping_price_amount'] ?? 0);
                            echo number_format($totalActualCost, 2); 
                        ?></strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">计算说明</h5>
            </div>
            <div class="card-body">
                <h6>利润计算公式：</h6>
                <div class="bg-light p-2 rounded mb-2">
                    <small><code>利润率 = 毛利润 / 订单总额 × 100%</code></small>
                </div>

                <div class="alert alert-info">
                    <small><strong>提示：</strong>点击"重新计算"按钮可以根据当前订单总额和出库成本自动重新计算毛利润和利润率。</small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 重新计算利润率
function recalculateProfitRate() {
    const totalAmount = parseFloat(document.getElementById('order_total_amount').value) || 0;
    const profitAmount = parseFloat(document.getElementById('profit_amount').value) || 0;
    
    const profitRate = totalAmount > 0 ? (profitAmount / totalAmount * 100) : 0;
    
    document.getElementById('profit_rate').value = profitRate.toFixed(2);
}

// 监听金额输入变化（可选自动计算）
document.getElementById('order_total_amount').addEventListener('input', function() {
    if (confirm('是否自动重新计算利润率？')) {
        recalculateProfitRate();
    }
});

document.getElementById('profit_amount').addEventListener('input', function() {
    if (confirm('是否自动重新计算利润率？')) {
        recalculateProfitRate();
    }
});

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