<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>编辑库存明细</h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/inventory_details.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">库存明细信息</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/inventory_details.php?action=edit_post">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($inventoryDetail['id']); ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="wid" class="form-label">仓库ID <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="wid" name="wid" required
                                       value="<?php echo htmlspecialchars($inventoryDetail['wid']); ?>">
                                <div class="form-text">仓库的唯一标识</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sku" name="sku" required
                                       value="<?php echo htmlspecialchars($inventoryDetail['sku']); ?>"
                                       placeholder="请输入SKU" maxlength="50">
                                <div class="form-text">产品SKU编码</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_valid_num" class="form-label">可用量 <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="product_valid_num" name="product_valid_num" required
                                       value="<?php echo htmlspecialchars($inventoryDetail['product_valid_num']); ?>"
                                       placeholder="请输入可用量">
                                <div class="form-text">可用库存数量</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="quantity_receive" class="form-label">待到货量</label>
                                <input type="text" class="form-control" id="quantity_receive" name="quantity_receive"
                                       value="<?php echo htmlspecialchars($inventoryDetail['quantity_receive'] ?? ''); ?>"
                                       placeholder="请输入待到货量" maxlength="20">
                                <div class="form-text">待到货数量</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_onway" class="form-label">调拨在途</label>
                                <input type="number" class="form-control" id="product_onway" name="product_onway"
                                       value="<?php echo htmlspecialchars($inventoryDetail['product_onway'] ?? 0); ?>"
                                       placeholder="请输入调拨在途数量">
                                <div class="form-text">调拨在途的库存数量</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="average_age" class="form-label">平均库龄(天)</label>
                                <input type="number" class="form-control" id="average_age" name="average_age"
                                       value="<?php echo htmlspecialchars($inventoryDetail['average_age'] ?? 0); ?>"
                                       placeholder="请输入平均库龄">
                                <div class="form-text">平均库存天数</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="purchase_price" class="form-label">采购单价</label>
                                <input type="number" step="0.0001" class="form-control" id="purchase_price" name="purchase_price"
                                       value="<?php echo htmlspecialchars($inventoryDetail['purchase_price'] ?? 0); ?>"
                                       placeholder="请输入采购单价">
                                <div class="form-text">单位采购价格</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="head_stock_price" class="form-label">单位头程费用</label>
                                <input type="number" step="0.0001" class="form-control" id="head_stock_price" name="head_stock_price"
                                       value="<?php echo htmlspecialchars($inventoryDetail['head_stock_price'] ?? 0); ?>"
                                       placeholder="请输入单位头程费用">
                                <div class="form-text">单位头程费用</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="stock_price" class="form-label">单位库存成本</label>
                                <input type="number" step="0.0001" class="form-control" id="stock_price" name="stock_price"
                                       value="<?php echo htmlspecialchars($inventoryDetail['stock_price'] ?? 0); ?>"
                                       placeholder="请输入单位库存成本">
                                <div class="form-text">单位库存成本</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-check"></i> 保存
                        </button>
                        <a href="<?php echo ADMIN_PANEL_URL; ?>/inventory_details.php" class="btn btn-outline-secondary ms-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">提示信息</h5>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        带 <span class="text-danger">*</span> 的字段为必填项
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        ID不可修改
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        SKU是产品的唯一标识
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        金额字段支持4位小数
                    </li>
                    <li class="list-group-item">
                        <i class="fa fa-info-circle text-info me-2"></i>
                        库龄单位为天
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
