<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>创建商品</h4>
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
                <h5 class="mb-0">基本信息</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/products.php?action=create_post">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sku" name="sku" required
                                       placeholder="请输入SKU" maxlength="50">
                                <div class="form-text">唯一的商品库存单位</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku_identifier" class="form-label">SKU识别码</label>
                                <input type="text" class="form-control" id="sku_identifier" name="sku_identifier"
                                       placeholder="请输入SKU识别码" maxlength="100">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="spu" class="form-label">SPU</label>
                                <select class="form-control" id="spu" name="spu">
                                    <option value="">请选择SPU</option>
                                    <?php if (!empty($spuList)): ?>
                                        <?php foreach ($spuList as $spu): ?>
                                            <option value="<?php echo htmlspecialchars($spu['spu']); ?>">
                                                <?php echo htmlspecialchars($spu['spu']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <div class="form-text">标准产品单位</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ps_id" class="form-label">SPU ID</label>
                                <input type="number" class="form-control" id="ps_id" name="ps_id"
                                       placeholder="请输入SPU ID">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brand_name" class="form-label">品牌名称</label>
                                <select class="form-control" id="brand_name" name="brand_name">
                                    <option value="">请选择品牌</option>
                                    <?php if (!empty($brandList)): ?>
                                        <?php foreach ($brandList as $brand): ?>
                                            <option value="<?php echo htmlspecialchars($brand['brand_name']); ?>">
                                                <?php echo htmlspecialchars($brand['brand_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">分类名称</label>
                                <select class="form-control" id="category_name" name="category_name">
                                    <option value="">请选择分类</option>
                                    <?php if (!empty($categoryList)): ?>
                                        <?php foreach ($categoryList as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['category_name']); ?>">
                                                <?php echo htmlspecialchars($category['category_name']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="product_name" class="form-label">商品名称</label>
                        <input type="text" class="form-control" id="product_name" name="product_name"
                               placeholder="请输入商品名称" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="pic_url" class="form-label">产品图片URL</label>
                        <input type="url" class="form-control" id="pic_url" name="pic_url"
                               placeholder="请输入图片URL" maxlength="255">
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">采购信息</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cg_price" class="form-label">采购成本</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.0001" class="form-control" id="cg_price" 
                                           name="cg_price" placeholder="0.0000">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cg_transport_costs" class="form-label">运输成本</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="cg_transport_costs" 
                                           name="cg_transport_costs" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cg_delivery" class="form-label">采购交期</label>
                        <input type="text" class="form-control" id="cg_delivery" name="cg_delivery"
                               placeholder="请输入采购交期" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="purchase_remark" class="form-label">采购备注</label>
                        <textarea class="form-control" id="purchase_remark" name="purchase_remark"
                                  rows="3" placeholder="请输入采购备注"></textarea>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">状态设置</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">销售状态</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1">在售</option>
                                    <option value="0">停售</option>
                                    <option value="2">开发中</option>
                                    <option value="3">清仓</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="open_status" class="form-label">启用状态</label>
                                <select class="form-control" id="open_status" name="open_status">
                                    <option value="1">启用</option>
                                    <option value="0">停用</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="is_combo" class="form-label">是否组合产品</label>
                        <select class="form-control" id="is_combo" name="is_combo">
                            <option value="0">否</option>
                            <option value="1">是</option>
                        </select>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">人员信息</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_developer" class="form-label">开发人员</label>
                                <input type="text" class="form-control" id="product_developer" name="product_developer"
                                       placeholder="请输入开发人员姓名" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_developer_uid" class="form-label">开发人员ID</label>
                                <input type="number" class="form-control" id="product_developer_uid" name="product_developer_uid"
                                       placeholder="请输入开发人员ID">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cg_opt_username" class="form-label">采购人员</label>
                                <input type="text" class="form-control" id="cg_opt_username" name="cg_opt_username"
                                       placeholder="请输入采购人员姓名" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cg_opt_uid" class="form-label">采购人员ID</label>
                                <input type="number" class="form-control" id="cg_opt_uid" name="cg_opt_uid"
                                       placeholder="请输入采购人员ID">
                            </div>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-secondary me-md-2">
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
                <h5 class="mb-0">字段说明</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><strong>SKU</strong>: 唯一的商品库存单位（必填）</li>
                    <li><strong>SPU</strong>: 标准产品单位</li>
                    <li><strong>品牌名称</strong>: 商品所属品牌</li>
                    <li><strong>分类名称</strong>: 商品所属分类</li>
                    <li><strong>采购成本</strong>: 商品采购成本</li>
                    <li><strong>运输成本</strong>: 商品运输成本</li>
                    <li><strong>销售状态</strong>: 0停售 1在售 2开发中 3清仓</li>
                    <li><strong>启用状态</strong>: 0停用 1启用</li>
                    <li><strong>组合产品</strong>: 0否 1是</li>
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
                    <a href="<?php echo APP_URL; ?>/products.php?action=import" class="btn btn-sm btn-outline-success w-100">
                        <i class="fa fa-upload"></i> 批量导入
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function setSampleData() {
    document.getElementById('sku').value = 'SKU' + Date.now();
    document.getElementById('product_name').value = '示例商品';
    document.getElementById('cg_price').value = '99.0000';
    document.getElementById('cg_transport_costs').value = '10.00';
    document.getElementById('status').value = '1';
    document.getElementById('open_status').value = '1';
}

function clearForm() {
    document.querySelectorAll('input[type="text"], input[type="number"], input[type="url"], textarea').forEach(input => {
        input.value = '';
    });
    document.querySelectorAll('select').forEach(select => {
        if (select.id === 'status') {
            select.value = '1';
        } else if (select.id === 'open_status') {
            select.value = '1';
        } else if (select.id === 'is_combo') {
            select.value = '0';
        } else {
            select.value = '';
        }
    });
}

document.querySelector('form').addEventListener('submit', function(e) {
    const sku = document.getElementById('sku').value.trim();
    if (!sku) {
        e.preventDefault();
        alert('请输入SKU');
        document.getElementById('sku').focus();
        return;
    }

    const cgPrice = parseFloat(document.getElementById('cg_price').value) || 0;
    const cgTransportCosts = parseFloat(document.getElementById('cg_transport_costs').value) || 0;
    
    if (cgPrice < 0) {
        e.preventDefault();
        alert('采购成本不能为负数');
        document.getElementById('cg_price').focus();
        return;
    }
    
    if (cgTransportCosts < 0) {
        e.preventDefault();
        alert('运输成本不能为负数');
        document.getElementById('cg_transport_costs').focus();
        return;
    }
});
</script>
