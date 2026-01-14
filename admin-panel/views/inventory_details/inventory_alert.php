<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        
        <a href="<?php echo APP_URL; ?>/inventory_details.php?action=export_inventory_alert" class="btn btn-outline-primary">
            <i class="fa fa-download"></i> 导出数据
        </a>
        <a href="<?php echo APP_URL; ?>/inventory_details.php" class="btn btn-outline-secondary mr-2">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<!-- 批量查询表单 -->
<div class="card mb-3">
    <div class="card-body">
        <h5 class="card-title">批量查询SKU（支持模糊查询）</h5>
        <form method="post" action="<?php echo APP_URL; ?>/inventory_details.php?action=inventory_alert">
            <div class="form-group">
                <label for="batch_sku">输入SKU（每行一个，支持模糊查询）</label>
                <textarea class="form-control" id="batch_sku" name="batch_sku" rows="4" placeholder="例如：
 SKU001 
 SKU002 
 003"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary mr-2">批量查询</button>
                <button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('batch_sku').value = '';">清空</button>
            </div>
        </form>
    </div>
</div>

<?php if (!empty($inventoryAlerts)): ?>
    <div class="row mb-3">
       
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">总可用量（不含温州仓）</h5>
                    <h2 class="mb-0"><?php echo number_format($totalStats['product_valid_num_excluding_wenzhou']); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">总调拨在途（不含温州仓）</h5>
                    <h2 class="mb-0"><?php echo number_format($totalStats['product_onway_excluding_wenzhou']); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">总可用量（温州仓）</h5>
                    <h2 class="mb-0"><?php echo number_format($totalStats['product_valid_num_wenzhou']); ?></h2>
                </div>
            </div>
        </div>
         <div class="col-md-2">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">总待到货量（温州仓）</h5>
                    <h2 class="mb-0"><?php echo number_format($totalStats['quantity_receive_wenzhou']); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">最近30天总出库量</h5>
                    <h2 class="mb-0"><?php echo number_format($totalStats['outbound_30days']); ?></h2>
                </div>
            </div>
        </div>
         <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">SKU总数</h5>
                    <h2 class="mb-0"><?php echo $totalStats['sku_count']; ?></h2>
                </div>
            </div>
        </div>
        
       
    </div>

    <div class="card mb-3">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>序号</th>
                            <th>SKU</th>
                            <th>商品名称</th>
                            <th>商品图片</th>
                            <th>可用量<br>（不含温州仓）</th>
                            <th>调拨在途<br>（不含温州仓）</th>
                            <th>可用量<br>（温州仓）</th>
                            <th>待到货量<br>（温州仓）</th>
                            <th>最近30天出库量</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventoryAlerts as $index => $item): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/inventory_details.php?keyword=<?php echo $item['sku']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($item['sku']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars(mb_strlen($item['product_name'] ?? '') > 20 ? mb_substr($item['product_name'] ?? '', 0, 20) . '...' : ($item['product_name'] ?? '')); ?></td>
                                <td>
                                    <?php if (!empty($item['product_image'])): ?>
                                        <div class="product-image-container" style="position: relative; display: inline-block; max-width: 50px; max-height: 50px;">
                                            <img src="<?php echo $item['product_image']; ?>" alt="商品图片" style="max-width: 50px; max-height: 50px;">
                                            <div class="product-image-zoom" style="position: absolute; top: -100px; left: 0; z-index: 1000; display: none; max-width: 150px; max-height: 150px; box-shadow: 0 0 10px rgba(0, 0, 0, 0.3); background-color: white; padding: 5px;">
                                                <img src="<?php echo $item['product_image']; ?>" alt="商品图片放大" style="max-width: 100%; max-height: 100%;">
                                            </div>
                                            <style>
                                                .product-image-container:hover .product-image-zoom {
                                                    display: block;
                                                }
                                            </style>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $productValidNum = $item['product_valid_num_excluding_wenzhou'];
                                    $outbound30Days = $item['outbound_30days'];
                                    if ($productValidNum > 0 && $outbound30Days > 0) {
                                        $daysOfStock = round($productValidNum / $outbound30Days * 30, 1);
                                        if ($daysOfStock < 30) {
                                            echo '<span class="badge bg-danger">' . number_format($productValidNum) . '</span>';
                                        } elseif ($daysOfStock < 60) {
                                            echo '<span class="badge bg-warning text-dark">' . number_format($productValidNum) . '</span>';
                                        } else {
                                            echo '<span class="badge bg-success">' . number_format($productValidNum) . '</span>';
                                        }
                                    } else {
                                        echo number_format($productValidNum);
                                    }
                                    ?>
                                </td>

                                <td><?php echo number_format($item['product_onway_excluding_wenzhou']); ?></td>
                                <td><?php echo number_format($item['product_valid_num_wenzhou']); ?></td>
                                <td><?php echo number_format($item['quantity_receive_wenzhou']); ?></td>
                                <td><?php echo number_format($item['outbound_30days']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <?php if (isset($pagination) && $pagination['totalPages'] > 1): ?>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                共 <?php echo number_format($pagination['totalCount']); ?> 条记录，每页显示 <?php echo $pagination['pageSize']; ?> 条，共 <?php echo $pagination['totalPages']; ?> 页
            </div>
            <nav aria-label="Page navigation">
                <ul class="pagination">
                    <?php if ($pagination['prevPage']): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo APP_URL; ?>/inventory_details.php?action=inventory_alert&page=<?php echo $pagination['prevPage']; ?>&limit=<?php echo $pagination['pageSize']; ?>" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link" aria-label="Previous">
                                <span aria-hidden="true">&laquo;</span>
                            </span>
                        </li>
                    <?php endif; ?>
                    
                    <?php 
                    // 显示页码，最多显示10个页码
                    $startPage = max(1, $pagination['currentPage'] - 4);
                    $endPage = min($pagination['totalPages'], $startPage + 9);
                    if ($endPage - $startPage < 9) {
                        $startPage = max(1, $endPage - 9);
                    }
                    
                    for ($i = $startPage; $i <= $endPage; $i++): ?>
                        <li class="page-item <?php echo $i == $pagination['currentPage'] ? 'active' : ''; ?>">
                            <a class="page-link" href="<?php echo APP_URL; ?>/inventory_details.php?action=inventory_alert&page=<?php echo $i; ?>&limit=<?php echo $pagination['pageSize']; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['nextPage']): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo APP_URL; ?>/inventory_details.php?action=inventory_alert&page=<?php echo $pagination['nextPage']; ?>&limit=<?php echo $pagination['pageSize']; ?>" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </a>
                        </li>
                    <?php else: ?>
                        <li class="page-item disabled">
                            <span class="page-link" aria-label="Next">
                                <span aria-hidden="true">&raquo;</span>
                            </span>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    <?php endif; ?>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> 没有找到库存数据
    </div>
<?php endif; ?>
