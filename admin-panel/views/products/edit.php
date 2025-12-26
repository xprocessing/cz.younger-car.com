<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>编辑商品</h4>
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
                <h5 class="mb-0">编辑基本信息</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo APP_URL; ?>/products.php?action=edit_post">
                    <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="sku" name="sku" required
                                       value="<?php echo htmlspecialchars($product['sku'] ?? ''); ?>"
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
                                            <option value="<?php echo htmlspecialchars($spu['spu']); ?>"
                                                    <?php echo ($spu['spu'] == ($product['spu'] ?? '')) ? 'selected' : ''; ?>>
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
                                            <option value="<?php echo htmlspecialchars($brand['brand']); ?>"
                                                    <?php echo ($brand['brand'] == ($product['brand'] ?? '')) ? 'selected' : ''; ?>>
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
                                            <option value="<?php echo htmlspecialchars($category['category']); ?>"
                                                    <?php echo ($category['category'] == ($product['category'] ?? '')) ? 'selected' : ''; ?>>
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
                               value="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>"
                               placeholder="请输入商品名称" maxlength="200">
                    </div>

                    <div class="mb-3">
                        <label for="product_name_en" class="form-label">商品名称（英文）</label>
                        <input type="text" class="form-control" id="product_name_en" name="product_name_en"
                               value="<?php echo htmlspecialchars($product['product_name_en'] ?? ''); ?>"
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
                                           name="cost_price" placeholder="0.00"
                                           value="<?php echo htmlspecialchars($product['cost_price'] ?? '0.00'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sale_price" class="form-label">销售价</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="sale_price" 
                                           name="sale_price" placeholder="0.00"
                                           value="<?php echo htmlspecialchars($product['sale_price'] ?? '0.00'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="weight" class="form-label">重量（kg）</label>
                        <input type="number" step="0.01" class="form-control" id="weight" 
                               name="weight" placeholder="0.00"
                               value="<?php echo htmlspecialchars($product['weight'] ?? '0.00'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">状态</label>
                        <select class="form-control" id="status" name="status">
                            <option value="1" <?php echo ($product['status'] ?? '') == '1' ? 'selected' : ''; ?>>启用</option>
                            <option value="0" <?php echo ($product['status'] ?? '') == '0' ? 'selected' : ''; ?>>禁用</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">创建时间</label>
                        <div class="form-control-plaintext">
                            <strong><?php echo htmlspecialchars($product['create_time'] ?? ''); ?></strong>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">更新时间</label>
                        <div class="form-control-plaintext">
                            <strong><?php echo htmlspecialchars($product['update_time'] ?? ''); ?></strong>
                            <small class="text-muted">（保存后自动更新为当前时间）</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
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
                <h5 class="mb-0">商品概览</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <td><strong>ID:</strong></td>
                        <td><?php echo $product['id']; ?></td>
                    </tr>
                    <tr>
                        <td><strong>SKU:</strong></td>
                        <td><code><?php echo htmlspecialchars($product['sku'] ?? ''); ?></code></td>
                    </tr>
                    <?php if (!empty($product['spu'])): ?>
                    <tr>
                        <td><strong>SPU:</strong></td>
                        <td><?php echo htmlspecialchars($product['spu']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($product['brand'])): ?>
                    <tr>
                        <td><strong>品牌:</strong></td>
                        <td><?php echo htmlspecialchars($product['brand']); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($product['category'])): ?>
                    <tr>
                        <td><strong>分类:</strong></td>
                        <td><?php echo htmlspecialchars($product['category']); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">价格分析</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>成本价:</span>
                        <strong class="text-danger">¥<?php echo number_format($product['cost_price'] ?? 0, 2); ?></strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>销售价:</span>
                        <strong class="text-success">¥<?php echo number_format($product['sale_price'] ?? 0, 2); ?></strong>
                    </div>
                </div>

                <?php 
                $costPrice = floatval($product['cost_price'] ?? 0);
                $salePrice = floatval($product['sale_price'] ?? 0);
                $profit = $salePrice - $costPrice;
                $profitRate = $costPrice > 0 ? ($profit / $costPrice * 100) : 0;
                ?>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>毛利润:</span>
                        <strong class="<?php echo $profit >= 0 ? 'text-success' : 'text-danger'; ?>">
                            ¥<?php echo number_format($profit, 2); ?>
                        </strong>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>毛利率:</span>
                        <strong class="<?php echo $profitRate >= 0 ? 'text-success' : 'text-danger'; ?>">
                            <?php echo number_format($profitRate, 2); ?>%
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">其他信息</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">重量:</small>
                    <div class="d-flex justify-content-between">
                        <span><?php echo number_format($product['weight'] ?? 0, 2); ?> kg</span>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">状态:</small>
                    <div>
                        <?php if (($product['status'] ?? '') == '1'): ?>
                            <span class="badge bg-success">启用</span>
                        <?php else: ?>
                            <span class="badge bg-danger">禁用</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
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
