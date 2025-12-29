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
            if (!empty($_GET['status'])) $exportParams[] = 'status=' . urlencode($_GET['status']);
            if (!empty($exportParams)) echo '&' . implode('&', $exportParams);
        ?>" class="btn btn-outline-primary me-2">
            <i class="fa fa-download"></i> 批量导出
        </a>
        <a href="<?php echo APP_URL; ?>/products.php?action=stats" class="btn btn-outline-info me-2">
            <i class="fa fa-chart-bar"></i> 商品统计
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
                            <option value="<?php echo htmlspecialchars($brand['brand_name']); ?>" 
                                    <?php echo (($_GET['brand'] ?? '') == $brand['brand_name'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($brand['brand_name']); ?>
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
                            <option value="<?php echo htmlspecialchars($category['category_name']); ?>" 
                                    <?php echo (($_GET['category'] ?? '') == $category['category_name'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($category['category_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">状态</label>
                <select name="status" class="form-select">
                    <option value="">全部状态</option>
                    <option value="0" <?php echo (($_GET['status'] ?? '') == '0' ? 'selected' : ''); ?>>停售</option>
                    <option value="1" <?php echo (($_GET['status'] ?? '') == '1' ? 'selected' : ''); ?>>在售</option>
                    <option value="2" <?php echo (($_GET['status'] ?? '') == '2' ? 'selected' : ''); ?>>开发中</option>
                    <option value="3" <?php echo (($_GET['status'] ?? '') == '3' ? 'selected' : ''); ?>>清仓</option>
                </select>
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
                            <th>图片</th>
                            <th>SKU</th>
                            <th>SPU</th>
                            <th>品牌</th>
                            <th>分类</th>
                            <th>商品名称</th>
                            <th>采购成本</th>
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
                                    <td><img src="<?php echo htmlspecialchars($product['pic_url'] ?? ''); ?>" alt="商品图片" class="product-img-zoom"></td>
                                    <td><a href="order_profit.php?keyword=<?php echo urlencode($product['sku'] ?? ''); ?>" target="_blank" title="查看订单利润"><?php echo htmlspecialchars($product['sku'] ?? ''); ?></a></td>
                                    <td><?php echo htmlspecialchars($product['spu'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($product['brand_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></td>
                                    <td><?php echo htmlspecialchars($product['product_name'] ?? ''); ?></td>
                                    <td><?php echo number_format($product['cg_price'] ?? 0, 4); ?></td>
                                    <td>
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
                                    </td>
                                    <td><?php echo $product['create_time'] ?? ''; ?></td>
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
                                    if (!empty($_GET['status'])) $params[] = 'status=' . urlencode($_GET['status']);
                                    if (!empty($params)) echo '&' . implode('&', $params);
                                ?>">上一页</a>
                            </li>
                        <?php endif; ?>
                        
                        <?php
                        $showPages = [];
                        $showPages[] = 1;
                        
                        if ($totalPages > 1) {
                            $startPage = max(2, $page - 2);
                            $endPage = min($totalPages - 1, $page + 2);
                            
                            if ($startPage > 2) {
                                $showPages[] = '...';
                            }
                            
                            for ($i = $startPage; $i <= $endPage; $i++) {
                                $showPages[] = $i;
                            }
                            
                            if ($endPage < $totalPages - 1) {
                                $showPages[] = '...';
                            }
                            
                            if ($totalPages > 1) {
                                $showPages[] = $totalPages;
                            }
                        }
                        
                        foreach ($showPages as $i):
                            if ($i === '...'):
                        ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php elseif ($i == $page): ?>
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
                                        if (!empty($_GET['status'])) $params[] = 'status=' . urlencode($_GET['status']);
                                        if (!empty($params)) echo '&' . implode('&', $params);
                                    ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $page + 1; ?><?php 
                                    $params = [];
                                    if (!empty($_GET['keyword'])) $params[] = 'keyword=' . urlencode($_GET['keyword']);
                                    if (!empty($_GET['brand'])) $params[] = 'brand=' . urlencode($_GET['brand']);
                                    if (!empty($_GET['category'])) $params[] = 'category=' . urlencode($_GET['category']);
                                    if (!empty($_GET['spu'])) $params[] = 'spu=' . urlencode($_GET['spu']);
                                    if (!empty($_GET['status'])) $params[] = 'status=' . urlencode($_GET['status']);
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

<style>
.product-img-zoom {
    width: 50px;
    height: 50px;
    object-fit: cover;
    cursor: pointer;
    transition: transform 0.3s ease;
}

.product-img-zoom:hover {
    transform: scale(5);
    z-index: 1000;
    position: relative;
    border: 2px solid #0066ffff;
}
</style>
