<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>编辑商品</h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/products.php" class="btn btn-secondary">
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
                <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/products.php?action=edit_post">
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
                                <label for="sku_identifier" class="form-label">SKU识别码</label>
                                <input type="text" class="form-control" id="sku_identifier" name="sku_identifier"
                                       value="<?php echo htmlspecialchars($product['sku_identifier'] ?? ''); ?>"
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
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="ps_id" class="form-label">SPU ID</label>
                                <input type="number" class="form-control" id="ps_id" name="ps_id"
                                       value="<?php echo htmlspecialchars($product['ps_id'] ?? ''); ?>"
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
                                            <option value="<?php echo htmlspecialchars($brand['brand_name']); ?>"
                                                    <?php echo ($brand['brand_name'] == ($product['brand_name'] ?? '')) ? 'selected' : ''; ?>>
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
                                            <option value="<?php echo htmlspecialchars($category['category_name']); ?>"
                                                    <?php echo ($category['category_name'] == ($product['category_name'] ?? '')) ? 'selected' : ''; ?>>
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
                               value="<?php echo htmlspecialchars($product['product_name'] ?? ''); ?>"
                               placeholder="请输入商品名称" maxlength="200">
                    </div>

                    <div class="mb-3">
                        <label for="pic_url" class="form-label">商品图片URL</label>
                        <input type="url" class="form-control" id="pic_url" name="pic_url"
                               value="<?php echo htmlspecialchars($product['pic_url'] ?? ''); ?>"
                               placeholder="请输入商品图片URL" maxlength="500">
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">采购信息</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cg_price" class="form-label">采购成本</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.0001" class="form-control" id="cg_price" 
                                           name="cg_price" placeholder="0.0000"
                                           value="<?php echo htmlspecialchars($product['cg_price'] ?? '0.0000'); ?>">
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cg_transport_costs" class="form-label">运输成本</label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="cg_transport_costs" 
                                           name="cg_transport_costs" placeholder="0.00"
                                           value="<?php echo htmlspecialchars($product['cg_transport_costs'] ?? '0.00'); ?>">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cg_delivery" class="form-label">采购交付</label>
                        <input type="text" class="form-control" id="cg_delivery" name="cg_delivery"
                               value="<?php echo htmlspecialchars($product['cg_delivery'] ?? ''); ?>"
                               placeholder="请输入采购交付信息" maxlength="200">
                    </div>

                    <div class="mb-3">
                        <label for="purchase_remark" class="form-label">采购备注</label>
                        <textarea class="form-control" id="purchase_remark" name="purchase_remark"
                                  rows="3" placeholder="请输入采购备注"><?php echo htmlspecialchars($product['purchase_remark'] ?? ''); ?></textarea>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">状态设置</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">销售状态</label>
                                <select class="form-control" id="status" name="status">
                                    <option value="1" <?php echo ($product['status'] ?? '') == '1' ? 'selected' : ''; ?>>在售</option>
                                    <option value="0" <?php echo ($product['status'] ?? '') == '0' ? 'selected' : ''; ?>>停售</option>
                                    <option value="2" <?php echo ($product['status'] ?? '') == '2' ? 'selected' : ''; ?>>开发中</option>
                                    <option value="3" <?php echo ($product['status'] ?? '') == '3' ? 'selected' : ''; ?>>清仓</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="open_status" class="form-label">启用状态</label>
                                <select class="form-control" id="open_status" name="open_status">
                                    <option value="1" <?php echo ($product['open_status'] ?? '') == '1' ? 'selected' : ''; ?>>启用</option>
                                    <option value="0" <?php echo ($product['open_status'] ?? '') == '0' ? 'selected' : ''; ?>>停用</option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="is_combo" class="form-label">是否组合产品</label>
                        <select class="form-control" id="is_combo" name="is_combo">
                            <option value="0" <?php echo ($product['is_combo'] ?? '') == '0' ? 'selected' : ''; ?>>否</option>
                            <option value="1" <?php echo ($product['is_combo'] ?? '') == '1' ? 'selected' : ''; ?>>是</option>
                        </select>
                    </div>

                    <h6 class="border-bottom pb-2 mb-3">人员信息</h6>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_developer" class="form-label">商品开发员</label>
                                <input type="text" class="form-control" id="product_developer" name="product_developer"
                                       value="<?php echo htmlspecialchars($product['product_developer'] ?? ''); ?>"
                                       placeholder="请输入商品开发员" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="product_developer_uid" class="form-label">开发员UID</label>
                                <input type="number" class="form-control" id="product_developer_uid" name="product_developer_uid"
                                       value="<?php echo htmlspecialchars($product['product_developer_uid'] ?? ''); ?>"
                                       placeholder="请输入开发员UID">
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cg_opt_username" class="form-label">采购员用户名</label>
                                <input type="text" class="form-control" id="cg_opt_username" name="cg_opt_username"
                                       value="<?php echo htmlspecialchars($product['cg_opt_username'] ?? ''); ?>"
                                       placeholder="请输入采购员用户名" maxlength="100">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cg_opt_uid" class="form-label">采购员UID</label>
                                <input type="number" class="form-control" id="cg_opt_uid" name="cg_opt_uid"
                                       value="<?php echo htmlspecialchars($product['cg_opt_uid'] ?? ''); ?>"
                                       placeholder="请输入采购员UID">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">创建时间</label>
                        <div class="form-control-plaintext">
                            <strong><?php echo htmlspecialchars($product['create_time'] ?? ''); ?></strong>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo ADMIN_PANEL_URL; ?>/products.php" class="btn btn-secondary me-md-2">
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
                    <?php if (!empty($product['sku_identifier'])): ?>
                    <tr>
                        <td><strong>SKU识别码:</strong></td>
                        <td><?php echo htmlspecialchars($product['sku_identifier'] ?? ''); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($product['spu'])): ?>
                    <tr>
                        <td><strong>SPU:</strong></td>
                        <td><?php echo htmlspecialchars($product['spu'] ?? ''); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($product['ps_id'])): ?>
                    <tr>
                        <td><strong>SPU ID:</strong></td>
                        <td><?php echo htmlspecialchars($product['ps_id'] ?? ''); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($product['brand_name'])): ?>
                    <tr>
                        <td><strong>品牌:</strong></td>
                        <td><?php echo htmlspecialchars($product['brand_name'] ?? ''); ?></td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($product['category_name'])): ?>
                    <tr>
                        <td><strong>分类:</strong></td>
                        <td><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></td>
                    </tr>
                    <?php endif; ?>
                </table>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">成本分析</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>采购成本:</span>
                        <strong class="text-danger">¥<?php echo number_format($product['cg_price'] ?? 0, 4); ?></strong>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>运输成本:</span>
                        <strong class="text-warning">¥<?php echo number_format($product['cg_transport_costs'] ?? 0, 2); ?></strong>
                    </div>
                </div>

                <?php 
                $cgPrice = floatval($product['cg_price'] ?? 0);
                $cgTransportCosts = floatval($product['cg_transport_costs'] ?? 0);
                $totalCost = $cgPrice + $cgTransportCosts;
                ?>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>总成本:</span>
                        <strong class="text-primary">
                            ¥<?php echo number_format($totalCost, 4); ?>
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">状态信息</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">销售状态:</small>
                    <div>
                        <?php 
                        $status = $product['status'] ?? '';
                        $statusText = '';
                        $badgeClass = '';
                        switch($status) {
                            case '0':
                                $statusText = '停售';
                                $badgeClass = 'bg-danger';
                                break;
                            case '1':
                                $statusText = '在售';
                                $badgeClass = 'bg-success';
                                break;
                            case '2':
                                $statusText = '开发中';
                                $badgeClass = 'bg-warning';
                                break;
                            case '3':
                                $statusText = '清仓';
                                $badgeClass = 'bg-secondary';
                                break;
                            default:
                                $statusText = '未知';
                                $badgeClass = 'bg-dark';
                        }
                        ?>
                        <span class="badge <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span>
                    </div>
                </div>
                
                <div class="mb-2">
                    <small class="text-muted">启用状态:</small>
                    <div>
                        <?php if (($product['open_status'] ?? '') == '1'): ?>
                            <span class="badge bg-success">启用</span>
                        <?php else: ?>
                            <span class="badge bg-danger">停用</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="mb-2">
                    <small class="text-muted">是否组合:</small>
                    <div>
                        <?php if (($product['is_combo'] ?? '') == '1'): ?>
                            <span class="badge bg-info">是</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">否</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">人员信息</h5>
            </div>
            <div class="card-body">
                <?php if (!empty($product['product_developer'])): ?>
                <div class="mb-2">
                    <small class="text-muted">商品开发员:</small>
                    <div><?php echo htmlspecialchars($product['product_developer']); ?></div>
                </div>
                <?php endif; ?>
                
                <?php if (!empty($product['cg_opt_username'])): ?>
                <div class="mb-2">
                    <small class="text-muted">采购员:</small>
                    <div><?php echo htmlspecialchars($product['cg_opt_username']); ?></div>
                </div>
                <?php endif; ?>
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
