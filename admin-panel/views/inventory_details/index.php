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
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>仓库ID</th>
                        <th>仓库名称</th>
                        <th>SKU</th>
                        <th>可用量</th>
                        <th>待到货量</th>
                        <th>平均库龄(天)</th>
                        <th>采购单价</th>
                        <th>单位头程费用</th>
                        <th>单位库存成本</th>
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
                        <a class="page-link" href="<?php echo APP_URL; ?>/inventory_details.php?page=<?php echo $p; ?><?php echo !empty($keyword) ? '&keyword=' . urlencode($keyword) : ''; ?><?php echo !empty($wid) ? '&wid=' . urlencode($wid) : ''; ?>">
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
