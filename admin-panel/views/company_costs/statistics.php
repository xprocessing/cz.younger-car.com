<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php" class="btn btn-primary">
            <i class="fa fa-arrow-left"></i> 返回费用列表
        </a>
    </div>
</div>

<!-- 统计图表 -->
<div class="row mb-3">
    <!-- 月度统计图表 -->
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">过去12个月费用总额统计</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="200"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row mb-3">
    <!-- 本月费用统计 -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">本月费用统计</h5>
            </div>
            <div class="card-body">
                <canvas id="currentMonthChart"></canvas>
                <?php 
                    $currentMonthTotal = 0;
                    foreach ($currentMonthStats as $stat) {
                        $currentMonthTotal += $stat['total_cost'];
                    }
                ?>
                <p class="text-end mt-2"><strong>本月总费用：$<?php echo number_format($currentMonthTotal, 2); ?></strong></p>
            </div>
        </div>
    </div>
    
    <!-- 上月费用统计 -->
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">上月费用统计</h5>
            </div>
            <div class="card-body">
                <canvas id="previousMonthChart"></canvas>
                <?php 
                    $previousMonthTotal = 0;
                    foreach ($previousMonthStats as $stat) {
                        $previousMonthTotal += $stat['total_cost'];
                    }
                ?>
                <p class="text-end mt-2"><strong>上月总费用：$<?php echo number_format($previousMonthTotal, 2); ?></strong></p>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js 脚本 -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // 月度统计图表
    const monthlyData = <?php echo json_encode($monthlyStatistics); ?>;
    const monthlyLabels = monthlyData.map(item => item.month);
    const monthlyValues = monthlyData.map(item => item.total_cost);
    
    const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
    const monthlyChart = new Chart(monthlyCtx, {
        type: 'line',
        data: {
            labels: monthlyLabels,
            datasets: [{
                label: '费用总额 ($)',
                data: monthlyValues,
                borderColor: '#4e73df',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4,
                fill: true,
                pointRadius: 4,
                pointHoverRadius: 6
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return '$' + context.parsed.y.toFixed(2);
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        callback: function(value) {
                            return '$' + value;
                        }
                    }
                }
            }
        }
    });
    
    // 本月费用统计图表
    const currentMonthData = <?php echo json_encode($currentMonthStats); ?>;
    const currentMonthLabels = currentMonthData.map(item => item.cost_type);
    const currentMonthValues = currentMonthData.map(item => item.total_cost);
    
    const currentMonthCtx = document.getElementById('currentMonthChart').getContext('2d');
    const currentMonthChart = new Chart(currentMonthCtx, {
        type: 'pie',
        data: {
            labels: currentMonthLabels,
            datasets: [{
                data: currentMonthValues,
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#e74a3b',
                    '#858796',
                    '#6f42c1',
                    '#f8f9fc'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = '$' + context.parsed.toFixed(2);
                            const total = currentMonthValues.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) + '%' : '0.0%';
                            return label + ': ' + value + ' (' + percentage + ')';
                        }
                    }
                }
            }
        }
    });
    
    // 上月费用统计图表
    const previousMonthData = <?php echo json_encode($previousMonthStats); ?>;
    const previousMonthLabels = previousMonthData.map(item => item.cost_type);
    const previousMonthValues = previousMonthData.map(item => item.total_cost);
    
    const previousMonthCtx = document.getElementById('previousMonthChart').getContext('2d');
    const previousMonthChart = new Chart(previousMonthCtx, {
        type: 'pie',
        data: {
            labels: previousMonthLabels,
            datasets: [{
                data: previousMonthValues,
                backgroundColor: [
                    '#4e73df',
                    '#1cc88a',
                    '#36b9cc',
                    '#f6c23e',
                    '#e74a3b',
                    '#858796',
                    '#6f42c1',
                    '#f8f9fc'
                ],
                borderWidth: 2
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = '$' + context.parsed.toFixed(2);
                            const total = previousMonthValues.reduce((a, b) => a + b, 0);
                            const percentage = total > 0 ? ((context.parsed / total) * 100).toFixed(1) + '%' : '0.0%';
                            return label + ': ' + value + ' (' + percentage + ')';
                        }
                    }
                }
            }
        }
    });
</script>