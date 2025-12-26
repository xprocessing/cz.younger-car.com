<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>商品管理</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/products.php?action=import" class="btn btn-outline-success me-2">
            <i class="fa fa-upload"></i> 批量导入
        </a>
        <a href="<?php echo APP_URL; ?>/products.php?action=export<?php 
            $exportParams = [];
            if (!empty($_GET['keyword'])) $exportParams[] = 'keyword=' . urlencode($_GET['keyword']);
            if (!empty($_GET['brand'])) $exportParams[] = 'brand=' . urlencode($_GET['brand']);
            if (!empty($_GET['category'])) $exportParams[] = 'category=' . urlencode($_GET['category']);
            if (!empty($_GET['spu'])) $exportParams[] = 'spu=' . urlencode($_GET['spu']);
            if (!empty($_GET['start_date'])) $exportParams[] = 'start_date=' . urlencode($_GET['start_date']);
            if (!empty($_GET['end_date'])) $exportParams[] = 'end_date=' . urlencode($_GET['end_date']);
            if (!empty($exportParams)) echo '&' . implode('&', $exportParams);
        ?>" class="btn btn-outline-primary me-2">
            <i class="fa fa-download"></i> 批量导出
        </a>
        <a href="<?php echo APP_URL; ?>/products.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增商品
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/products.php" class="row g-3">
            <div class="col-md-2">
                <label for="keyword" class="form-label">关键词搜索</label>
                <input type="text" name="keyword" class="form-control" placeholder="搜索SKU、商品名称..." 
                       value="<?php echo $_GET['keyword'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="brand" class="form-label">品牌</label>
                <select name="brand" class="form-select">
                    <option value="">全部品牌</option>
                    <?php if (!empty($brandList)): ?>
                        <?php foreach ($brandList as $brand): ?>
                            <option value="<?php echo htmlspecialchars($brand['brand']); ?>" 
                                    <?php echo (($_GET['brand'] ?? '') == $brand['brand'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($brand['brand']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="category" class="form-label">分类</label>
                <select name="category" class="form-select">
                    <option value="">全部分类</option>
                    <?php if (!empty($categoryList)): ?>
                        <?php foreach ($categoryList as $category): ?>
                            <option value="<?php echo htmlspecialchars($category['category']); ?>" 
                                    <?php echo (($_GET['category'] ?? '') == $category['category'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($category['category']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="spu" class="form-label">SPU</label>
                <select name="spu" class="form-select">
                    <option value="">全部SPU</option>
                    <?php if (!empty($spuList)): ?>
                        <?php foreach ($spuList as $spu): ?>
                            <option value="<?php echo htmlspecialchars($spu['spu']); ?>" 
                                    <?php echo (($_GET['spu'] ?? '') == $spu['spu'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($spu['spu']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="start_date" class="form-label">开始日期</label>
                <input type="date" name="start_date" class="form-control" 
                       value="<?php echo $_GET['start_date'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="end_date" class="form-label">结束日期</label>
                <input type="date" name="end_date" class="form-control" 
                       value="<?php echo $_GET['end_date'] ?? ''; ?>">
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> 搜索
                </button>
                <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> 重置
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/products.php?action=batchDelete" id="batchDeleteForm">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>ID</th>
                            <th>SKU</th>
                            <th>SPU</th>
                            <th>品牌</th>
                            <th>分类</th>
                            <th>商品名称</th>
                            <th>成本价</th>
                            <th>销售价</th>
                            <th>重量</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($products)): ?>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td><input type="checkbox" name="ids[]" value="<?php echo $product['id']; ?>"></td>
                                    <td><?php echo $product['id']; ?></td>
                                    <td><?php echo htmlspecialchars($product['sku'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($product['spu'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($product['brand'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($product['category'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_name'] ?? ''); ?></td>
                                    <td><?php echo number_format($product['cost_price'], 2); ?></td>
                                    <td><?php echo number_format($product['sale_price'], 2); ?></td>
                                    <td><?php echo number_format($product['weight'], 2); ?></td>
                                    <td>
                                        <?php if ($product['status'] == '1'): ?>
                                            <span class="badge bg-success">启用</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">禁用</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $product['create_time']; ?></td>
                                    <td>
                                        <a href="<?php echo APP_URL; ?>/products.php?action=edit&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-primary">
                                            <i class="fa fa-edit"></i> 编辑
                                        </a>
                                        <a href="<?php echo APP_URL; ?>/products.php?action=delete&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('确定要删除该商品吗？');">
                                            <i class="fa fa-trash"></i> 删除
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="13" class="text-center">暂无数据</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if ($totalPages > 1): ?>
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page - 1; ?><?php 
                                    $params = [];
                                    if (!empty($_GET['keyword'])) $params[] = 'keyword=' . urlencode($_GET['keyword']);
                                    if (!empty($_GET['brand'])) $params[] = 'brand=' . urlencode($_GET['brand']);
                                    if (!empty($_GET['category'])) $params[] = 'category=' . urlencode($_GET['category']);
                                    if (!empty($_GET['spu'])) $params[] = 'spu=' . urlencode($_GET['spu']);
                                    if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
                                    if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
                                    if (!empty($params)) echo '&' . implode('&', $params);
                                ?>">上一页</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <?php if ($i == $page): ?>
                                <li class="page-item active">
                                    <span class="page-link"><?php echo $i; ?></span>
                                </li>
                            <?php else: ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $i; ?><?php 
                                        $params = [];
                                        if (!empty($_GET['keyword'])) $params[] = 'keyword=' . urlencode($_GET['keyword']);
                                        if (!empty($_GET['brand'])) $params[] = 'brand=' . urlencode($_GET['brand']);
                                        if (!empty($_GET['category'])) $params[] = 'category=' . urlencode($_GET['category']);
                                        if (!empty($_GET['spu'])) $params[] = 'spu=' . urlencode($_GET['spu']);
                                        if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
                                        if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
                                        if (!empty($params)) echo '&' . implode('&', $params);
                                    ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php 
                                    $params = [];
                                    if (!empty($_GET['keyword'])) $params[] = 'keyword=' . urlencode($_GET['keyword']);
                                    if (!empty($_GET['brand'])) $params[] = 'brand=' . urlencode($_GET['brand']);
                                    if (!empty($_GET['category'])) $params[] = 'category=' . urlencode($_GET['category']);
                                    if (!empty($_GET['spu'])) $params[] = 'spu=' . urlencode($_GET['spu']);
                                    if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
                                    if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
                                    if (!empty($params)) echo '&' . implode('&', $params);
                                ?>">下一页</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </nav>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
document.getElementById('selectAll').addEventListener('change', function() {
    var checkboxes = document.querySelectorAll('input[name="ids[]"]');
    checkboxes.forEach(function(checkbox) {
        checkbox.checked = this.checked;
    }.bind(this));
});
</script>
