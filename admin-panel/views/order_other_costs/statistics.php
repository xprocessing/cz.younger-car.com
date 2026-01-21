<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_other_costs.php" class="btn btn-outline-primary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <!-- 按店铺统计 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">上个月按店铺名称费用统计</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>总费用：</strong>￥<?php echo number_format($totalByStore, 2); ?>
                </div>
                <?php if (!empty($lastMonthByStore)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>店铺名称</th>
                                    <th>费用金额（人民币）</th>
                                    <th>占比</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lastMonthByStore as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['store_name']); ?></td>
                                        <td><?php echo number_format($item['total_cost'], 2); ?></td>
                                        <td><?php echo $totalByStore > 0 ? number_format(($item['total_cost'] / $totalByStore) * 100, 2) . '%' : '0.00%'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted">
                        暂无数据
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- 按平台统计 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">上个月按平台名称费用统计</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>总费用：</strong>￥<?php echo number_format($totalByPlatform, 2); ?>
                </div>
                <?php if (!empty($lastMonthByPlatform)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>平台名称</th>
                                    <th>费用金额（人民币）</th>
                                    <th>占比</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lastMonthByPlatform as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['platform_name']); ?></td>
                                        <td><?php echo number_format($item['total_cost'], 2); ?></td>
                                        <td><?php echo $totalByPlatform > 0 ? number_format(($item['total_cost'] / $totalByPlatform) * 100, 2) . '%' : '0.00%'; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-muted">
                        暂无数据
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 图表展示 -->
<div class="row">
    <!-- 店铺费用饼图 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">店铺费用占比图</h5>
            </div>
            <div class="card-body">
                <canvas id="storeChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- 平台费用饼图 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">平台费用占比图</h5>
            </div>
            <div class="card-body">
                <canvas id="platformChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo ADMIN_PANEL_URL; ?>/public/js/chart.js"></script>
<script>
    // 店铺费用饼图
    const storeCtx = document.getElementById('storeChart').getContext('2d');
    const storeLabels = <?php echo json_encode(array_column($lastMonthByStore, 'store_name')); ?>;
    // 确保所有值都转换为浮点数
    const storeValues = <?php 
        $values = array_map('floatval', array_column($lastMonthByStore, 'total_cost'));
        echo json_encode($values);
    ?>;
    
    // 生成随机颜色
    const generateColors = (count) => {
        const colors = [];
        for (let i = 0; i < count; i++) {
            colors.push(`rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.8)`);
        }
        return colors;
    };
    
    new Chart(storeCtx, {
        type: 'pie',
        data: {
            labels: storeLabels,
            datasets: [{
                data: storeValues,
                backgroundColor: generateColors(storeLabels.length),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0.0%';
                            return `${label}: ￥${value.toFixed(2)} (${percentage})`;
                        }
                    }
                }
            }
        }
    });
    
    // 平台费用饼图
    const platformCtx = document.getElementById('platformChart').getContext('2d');
    const platformLabels = <?php echo json_encode(array_column($lastMonthByPlatform, 'platform_name')); ?>;
    // 确保所有值都转换为浮点数
    const platformValues = <?php 
        $values = array_map('floatval', array_column($lastMonthByPlatform, 'total_cost'));
        echo json_encode($values);
    ?>;
    
    new Chart(platformCtx, {
        type: 'pie',
        data: {
            labels: platformLabels,
            datasets: [{
                data: platformValues,
                backgroundColor: generateColors(platformLabels.length),
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((value / total) * 100).toFixed(1) + '%' : '0.0%';
                            return `${label}: ￥${value.toFixed(2)} (${percentage})`;
                        }
                    }
                }
            }
        }
    });
</script>