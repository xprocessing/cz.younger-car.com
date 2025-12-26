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
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="brand" class="form-label">品牌</label>
                                <select class="form-control" id="brand" name="brand">
                                    <option value="">请选择品牌</option>
                                    <?php if (!empty($brandList)): ?>
                                        <?php foreach ($brandList as $brand): ?>
                                            <option value="<?php echo htmlspecialchars($brand['brand']); ?>">
                                                <?php echo htmlspecialchars($brand['brand']); ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">分类</label>
                                <select class="form-control" id="category" name="category">
                                    <option value="">请选择分类</option>
                                    <?php if (!empty($categoryList)): ?>
                                        <?php foreach ($categoryList as $category): ?>
                                            <option value="<?php echo htmlspecialchars($category['category']); ?>">
                                                <?php echo htmlspecialchars($category['category']); ?>
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
                               placeholder="请输入商品名称" maxlength="200">
                    </div>

                    <div class="mb-3">
                        <label for="product_name_en" class="form-label">商品名称（英文）</label>
                        <input type="text" class="form-control" id="product_name_en" name="product_name_en"
                               placeholder="请输入商品英文名称" maxlength="200">
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">价格信息</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost_price" class="form-label">成本价</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="cost_price" 
                                           name="cost_price" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_price" class="form-label">销售价</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="sale_price" 
                                           name="sale_price" placeholder="0.00">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="weight" class="form-label">重量（kg）</label>
                        <input type="number" step="0.01" class="form-control" id="weight" 
                               name="weight" placeholder="0.00">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">状态</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1">启用</option>
                            <option value="0">禁用</option>
                        </select>
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
                    <li><strong>SKU</strong>: 唯一的商品库存单位</li>
                    <li><strong>SPU</strong>: 标准产品单位</li>
                    <li><strong>品牌</strong>: 商品所属品牌</li>
                    <li><strong>分类</strong>: 商品所属分类</li>
                    <li><strong>成本价</strong>: 商品采购成本</li>
                    <li><strong>销售价</strong>: 商品销售价格</li>
                    <li><strong>重量</strong>: 商品重量（千克）</li>
                    <li><strong>状态</strong>: 启用或禁用商品</li>
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
    document.getElementById('product_name_en').value = 'Sample Product';
    document.getElementById('cost_price').value = '99.00';
    document.getElementById('sale_price').value = '199.00';
    document.getElementById('weight').value = '0.5';
}

function clearForm() {
    document.querySelectorAll('input[type="text"], input[type="number"], select').forEach(input => {
        input.value = '';
    });
    document.getElementById('status').value = '1';
}

document.querySelector('form').addEventListener('submit', function(e) {
    const sku = document.getElementById('sku').value.trim();
    if (!sku) {
        e.preventDefault();
        alert('请输入SKU');
        document.getElementById('sku').focus();
        return;
    }

    const costPrice = parseFloat(document.getElementById('cost_price').value) || 0;
    const salePrice = parseFloat(document.getElementById('sale_price').value) || 0;
    
    if (costPrice < 0) {
        e.preventDefault();
        alert('成本价不能为负数');
        document.getElementById('cost_price').focus();
        return;
    }
    
    if (salePrice < 0) {
        e.preventDefault();
        alert('销售价不能为负数');
        document.getElementById('sale_price').focus();
        return;
    }
});
</script>
