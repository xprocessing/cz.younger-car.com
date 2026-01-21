<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/track_costs.php" class="btn btn-outline-primary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <!-- 按赛道统计 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">上个月按赛道名称费用统计</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>总费用：</strong>¥<?php echo number_format($totalByTrack, 2); ?>
                </div>
                <?php if (!empty($lastMonthByTrack)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>赛道名称</th>
                                    <th>费用金额（人民币）</th>
                                    <th>占比</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lastMonthByTrack as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['track_name']); ?></td>
                                        <td><?php echo number_format($item['total_cost'], 2); ?></td>
                                        <td><?php echo $totalByTrack > 0 ? number_format(($item['total_cost'] / $totalByTrack) * 100, 2) . '%' : '0.00%'; ?></td>
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
    
    <!-- 按费用类型统计 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">上个月按费用类型统计</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <strong>总费用：</strong>¥<?php echo number_format($totalByType, 2); ?>
                </div>
                <?php if (!empty($lastMonthByType)): ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>费用类型</th>
                                    <th>费用金额（人民币）</th>
                                    <th>占比</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($lastMonthByType as $item): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($item['cost_type']); ?></td>
                                        <td><?php echo number_format($item['total_cost'], 2); ?></td>
                                        <td><?php echo $totalByType > 0 ? number_format(($item['total_cost'] / $totalByType) * 100, 2) . '%' : '0.00%'; ?></td>
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
    <!-- 赛道费用饼图 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">赛道费用占比图</h5>
            </div>
            <div class="card-body">
                <canvas id="trackChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- 费用类型饼图 -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">费用类型占比图</h5>
            </div>
            <div class="card-body">
                <canvas id="typeChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo ADMIN_PANEL_URL; ?>/public/js/chart.js"></script>
<script>
    // 赛道费用饼图
    const trackCtx = document.getElementById('trackChart').getContext('2d');
    const trackLabels = <?php echo json_encode(array_column($lastMonthByTrack, 'track_name')); ?>;
    const trackValues = <?php echo json_encode(array_map('floatval', array_column($lastMonthByTrack, 'total_cost'))); ?>;
    
    // 生成随机颜色
    const generateColors = (count) => {
        const colors = [];
        for (let i = 0; i < count; i++) {
            colors.push(`rgba(${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, ${Math.floor(Math.random() * 255)}, 0.8)`);
        }
        return colors;
    };
    
    new Chart(trackCtx, {
        type: 'pie',
        data: {
            labels: trackLabels,
            datasets: [{
                data: trackValues,
                backgroundColor: generateColors(trackLabels.length),
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
                            return `${label}: ¥${value.toFixed(2)} (${percentage})`;
                        }
                    }
                }
            }
        }
    });
    
    // 费用类型饼图
    const typeCtx = document.getElementById('typeChart').getContext('2d');
    const typeLabels = <?php echo json_encode(array_column($lastMonthByType, 'cost_type')); ?>;
    const typeValues = <?php echo json_encode(array_map('floatval', array_column($lastMonthByType, 'total_cost'))); ?>;
    
    new Chart(typeCtx, {
        type: 'pie',
        data: {
            labels: typeLabels,
            datasets: [{
                data: typeValues,
                backgroundColor: generateColors(typeLabels.length),
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
                            return `${label}: ¥${value.toFixed(2)} (${percentage})`;
                        }
                    }
                }
            }
        }
    });
</script>
