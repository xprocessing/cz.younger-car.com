<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo APP_URL; ?>/inventory_details.php" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<?php if (!empty($inventoryAlerts)): ?>
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">SKU总数</h5>
                    <h2 class="mb-0"><?php echo count($inventoryAlerts); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">总可用量</h5>
                    <h2 class="mb-0"><?php echo number_format(array_sum(array_column($inventoryAlerts, 'product_valid_num'))); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">最近30天总出库量</h5>
                    <h2 class="mb-0"><?php echo number_format(array_sum(array_column($inventoryAlerts, 'outbound_30days'))); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">总调拨在途</h5>
                    <h2 class="mb-0"><?php echo number_format(array_sum(array_column($inventoryAlerts, 'product_onway'))); ?></h2>
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
                            <th>可用量</th>
                            <th>待到货量</th>
                            <th>调拨在途</th>
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
                                <td><?php echo htmlspecialchars(mb_strlen($item['product_name'] ?? '') > 10 ? mb_substr($item['product_name'] ?? '', 0, 10) . '...' : ($item['product_name'] ?? '')); ?></td>
                                <td>
                                    <?php if (!empty($item['product_image'])): ?>
                                        <img src="<?php echo $item['product_image']; ?>" alt="商品图片" style="max-width: 50px; max-height: 50px;">
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    $productValidNum = $item['product_valid_num'];
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
                                <td><?php echo number_format($item['quantity_receive']); ?></td>
                                <td><?php echo number_format($item['product_onway']); ?></td>
                                <td><?php echo number_format($item['outbound_30days']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> 没有找到库存数据
    </div>
<?php endif; ?>
