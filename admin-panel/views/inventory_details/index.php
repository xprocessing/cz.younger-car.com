<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>库存明细</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/inventory_details.php?action=overaged_stats" class="btn btn-warning me-2">
            <i class="fa fa-clock-o"></i> 库龄统计
        </a>
        <a href="<?php echo APP_URL; ?>/inventory_details.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增库存明细
        </a>
    </div>
</div>

<?php
function getSortUrl($field) {
    $currentSort = $_GET['sort'] ?? 'id';
    $currentOrder = $_GET['order'] ?? 'DESC';
    
    $newOrder = ($currentSort === $field && $currentOrder === 'ASC') ? 'DESC' : 'ASC';
    
    $params = ['sort' => $field, 'order' => $newOrder];
    
    if (!empty($_GET['keyword'])) {
        $params['keyword'] = $_GET['keyword'];
    }
    
    if (!empty($_GET['wid'])) {
        $params['wid'] = $_GET['wid'];
    }
    
    if (!empty($_GET['page'])) {
        $params['page'] = $_GET['page'];
    }
    
    return APP_URL . '/inventory_details.php?' . http_build_query($params);
}

function getSortIcon($field) {
    $currentSort = $_GET['sort'] ?? 'id';
    $currentOrder = $_GET['order'] ?? 'DESC';
    
    if ($currentSort === $field) {
        return $currentOrder === 'ASC' ? '<i class="fa fa-sort-asc"></i>' : '<i class="fa fa-sort-desc"></i>';
    }
    
    return '<i class="fa fa-sort text-muted"></i>';
}
?>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/inventory_details.php" class="row g-3">
            <div class="col-md-4">
                <label for="keyword" class="form-label">SKU搜索</label>
                <input type="text" name="keyword" class="form-control" placeholder="搜索SKU..." 
                       value="<?php echo $_GET['keyword'] ?? ''; ?>">
            </div>
            <div class="col-md-4">
                <label for="wid" class="form-label">仓库筛选</label>
                <input type="number" name="wid" class="form-control" placeholder="输入仓库ID..." 
                       value="<?php echo $_GET['wid'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="action_search" class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" name="action" value="search" class="btn btn-outline-primary">
                        <i class="fa fa-search"></i> 搜索
                    </button>
                    <a href="<?php echo APP_URL; ?>/inventory_details.php" class="btn btn-outline-secondary">
                        <i class="fa fa-refresh"></i> 重置
                    </a>
                </div>
            </div>
            <?php if (!empty($_GET['sort']) || !empty($_GET['order'])): ?>
            <div class="col-md-2">
                <label for="action_clear_sort" class="form-label">&nbsp;</label>
                <div>
                    <a href="<?php echo APP_URL; ?>/inventory_details.php<?php echo !empty($keyword) ? '?keyword=' . urlencode($keyword) : ''; ?><?php echo !empty($wid) ? '&wid=' . urlencode($wid) : ''; ?>" class="btn btn-outline-warning">
                        <i class="fa fa-times"></i> 清除排序
                    </a>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th><a href="<?php echo getSortUrl('id'); ?>" class="text-decoration-none text-dark">ID <?php echo getSortIcon('id'); ?></a></th>
                        <th><a href="<?php echo getSortUrl('wid'); ?>" class="text-decoration-none text-dark">仓库ID <?php echo getSortIcon('wid'); ?></a></th>
                        <th>仓库名称</th>
                        <th><a href="<?php echo getSortUrl('sku'); ?>" class="text-decoration-none text-dark">SKU <?php echo getSortIcon('sku'); ?></a></th>
                        <th><a href="<?php echo getSortUrl('product_valid_num'); ?>" class="text-decoration-none text-dark">可用量 <?php echo getSortIcon('product_valid_num'); ?></a></th>
                        <th><a href="<?php echo getSortUrl('quantity_receive'); ?>" class="text-decoration-none text-dark">待到货量 <?php echo getSortIcon('quantity_receive'); ?></a></th>
                        <th><a href="<?php echo getSortUrl('average_age'); ?>" class="text-decoration-none text-dark">平均库龄(天) <?php echo getSortIcon('average_age'); ?></a></th>
                        <th><a href="<?php echo getSortUrl('purchase_price'); ?>" class="text-decoration-none text-dark">采购单价 <?php echo getSortIcon('purchase_price'); ?></a></th>
                        <th><a href="<?php echo getSortUrl('head_stock_price'); ?>" class="text-decoration-none text-dark">单位头程费用 <?php echo getSortIcon('head_stock_price'); ?></a></th>
                        <th><a href="<?php echo getSortUrl('stock_price'); ?>" class="text-decoration-none text-dark">单位库存成本 <?php echo getSortIcon('stock_price'); ?></a></th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($inventoryDetails)): ?>
                        <?php foreach ($inventoryDetails as $detail): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($detail['id']); ?></td>
                                <td><?php echo htmlspecialchars($detail['wid']); ?></td>
                                <td><?php echo $detail['warehouse_name']; ?></td>
                                <td><a href="<?php echo APP_URL; ?>/products.php?keyword=<?php echo $detail['sku']; ?>" target="_blank"><?php echo htmlspecialchars($detail['sku']); ?> </a></td>
                                <td><?php echo number_format($detail['product_valid_num']); ?></td>
                                <td><?php echo htmlspecialchars($detail['quantity_receive']); ?></td>
                                <td><?php echo number_format($detail['average_age']); ?></td>
                                <td><?php echo number_format($detail['purchase_price'], 4); ?></td>
                                <td><?php echo number_format($detail['head_stock_price'], 4); ?></td>
                                <td><?php echo number_format($detail['stock_price'], 4); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo APP_URL; ?>/inventory_details.php?action=edit&id=<?php echo $detail['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="编辑">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete('<?php echo $detail['id']; ?>')" title="删除">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">暂无数据</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                $currentPage = $page;
                $delta = 2;
                $range = [];
                
                if ($currentPage - $delta > 1) {
                    $range[] = 1;
                    if ($currentPage - $delta > 2) {
                        $range[] = '...';
                    }
                }
                
                for ($i = max(1, $currentPage - $delta); $i <= min($totalPages, $currentPage + $delta); $i++) {
                    $range[] = $i;
                }
                
                if ($currentPage + $delta < $totalPages) {
                    if ($currentPage + $delta < $totalPages - 1) {
                        $range[] = '...';
                    }
                    $range[] = $totalPages;
                }
                
                foreach ($range as $p):
                    if ($p === '...'):
                ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php else: ?>
                    <li class="page-item <?php echo $p == $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo APP_URL; ?>/inventory_details.php?page=<?php echo $p; ?><?php echo !empty($keyword) ? '&keyword=' . urlencode($keyword) : ''; ?><?php echo !empty($wid) ? '&wid=' . urlencode($wid) : ''; ?><?php echo !empty($_GET['sort']) ? '&sort=' . urlencode($_GET['sort']) : ''; ?><?php echo !empty($_GET['order']) ? '&order=' . urlencode($_GET['order']) : ''; ?>">
                            <?php echo $p; ?>
                        </a>
                    </li>
                <?php
                    endif;
                endforeach;
                ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('确定要删除这条库存明细吗？')) {
        window.location.href = '<?php echo APP_URL; ?>/inventory_details.php?action=delete&id=' + id;
    }
}
</script>
