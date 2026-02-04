<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/inventory_details.php" class="btn btn-outline-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<?php if (!empty($inventoryStats)): ?>
    <div class="row mb-3">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">仓库总数</h5>
                    <h2 class="mb-0"><?php echo count($inventoryStats); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">总可用库存</h5>
                    <h2 class="mb-0"><?php echo number_format(array_sum(array_column($inventoryStats, 'total_valid'))); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-dark">
                <div class="card-body">
                    <h5 class="card-title">总调拨在途</h5>
                    <h2 class="mb-0"><?php echo number_format(array_sum(array_column($inventoryStats, 'total_onway'))); ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">总待到货</h5>
                    <h2 class="mb-0"><?php echo number_format(array_sum(array_column($inventoryStats, 'total_receive'))); ?></h2>
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
                            <th>仓库ID</th>
                            <th>仓库名称</th>
                            <th>可用数量</th>
                            <th>调拨在途数量</th>
                            <th>待到货数量</th>
                            <th>总数量</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inventoryStats as $index => $item): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($item['wid']); ?></td>
                                <td><?php echo htmlspecialchars($item['warehouse_name']); ?></td>
                                <td>
                                    <span class="badge bg-success"><?php echo number_format($item['total_valid']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark"><?php echo number_format($item['total_onway']); ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info"><?php echo number_format($item['total_receive']); ?></span>
                                </td>
                                <td>
                                    <strong><?php echo number_format($item['total_valid'] + $item['total_onway'] + $item['total_receive']); ?></strong>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="alert alert-info">
        <i class="fa fa-info-circle"></i> 没有找到库存记录
    </div>
<?php endif; ?>
