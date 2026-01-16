<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/inventory_details.php" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<?php if (!empty($overagedInventory)): ?>
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">SKU总数</h5>
                    <h2 class="mb-0"><?php echo count($overagedInventory); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">总库存量</h5>
                    <h2 class="mb-0"><?php echo number_format(array_sum(array_column($overagedInventory, 'product_valid_num'))); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">平均库龄</h5>
                    <h2 class="mb-0"><?php echo number_format(array_sum(array_column($overagedInventory, 'average_age')) / count($overagedInventory), 1); ?> 天</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">涉及仓库数</h5>
                    <h2 class="mb-0"><?php echo count(array_unique(array_column($overagedInventory, 'wid'))); ?></h2>
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
                            <th>平均库龄(天)</th>
                            <th>仓库ID</th>
                            <th>仓库名称</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($overagedInventory as $index => $item): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <a href="<?php echo ADMIN_PANEL_URL; ?>/products.php?keyword=<?php echo $item['sku']; ?>" target="_blank">
                                        <?php echo htmlspecialchars($item['sku']); ?>
                                    </a>
                                </td>
                                <td><?php echo htmlspecialchars(mb_strlen($item['product_name'] ?? '') > 20 ? mb_substr($item['product_name'] ?? '', 0, 20) . '...' : ($item['product_name'] ?? '')); ?></td>
                                <td>
                                    <?php if (!empty($item['product_image'])): ?>
                                        <img src="<?php echo $item['product_image']; ?>" alt="商品图片" style="max-width: 50px; max-height: 50px;">
                                    <?php endif; ?>
                                </td>
                                <td><?php echo number_format($item['product_valid_num']); ?></td>
                                <td>
                                    <span class="badge bg-danger"><?php echo number_format($item['average_age']); ?></span>
                                </td>
                                <td><?php echo htmlspecialchars($item['wid']); ?></td>
                                <td><?php echo htmlspecialchars($item['warehouse_name']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> 没有找到平均库龄超过<?php echo $thresholdDays; ?>天的库存记录
    </div>
<?php endif; ?>
