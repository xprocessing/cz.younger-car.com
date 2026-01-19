<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?action=import" class="btn btn-outline-success me-2">
            <i class="fa fa-upload"></i> 批量导入
        </a>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?action=export<?php 
            $exportParams = [];
            if (!empty($_GET['cost_type'])) $exportParams[] = 'cost_type=' . urlencode($_GET['cost_type']);
            if (!empty($_GET['start_date'])) $exportParams[] = 'start_date=' . urlencode($_GET['start_date']);
            if (!empty($_GET['end_date'])) $exportParams[] = 'end_date=' . urlencode($_GET['end_date']);
            if (!empty($exportParams)) echo '&' . implode('&', $exportParams);
        ?>" class="btn btn-outline-primary me-2">
            <i class="fa fa-download"></i> 批量导出
        </a>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增费用
        </a>
    </div>
</div>

<!-- 搜索和筛选框 -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php" class="row g-3">
            <div class="col-md-3">
                <label for="cost_type" class="form-label">费用类型</label>
                <select name="cost_type" class="form-select">
                    <option value="">全部类型</option>
                    <?php if (!empty($costTypeList)): ?>
                        <?php foreach ($costTypeList as $costType): ?>
                            <option value="<?php echo htmlspecialchars($costType); ?>" 
                                    <?php echo (($_GET['cost_type'] ?? '') == $costType ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($costType); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">开始日期</label>
                <input type="date" name="start_date" class="form-control" 
                       value="<?php echo $_GET['start_date'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">结束日期</label>
                <input type="date" name="end_date" class="form-control" 
                       value="<?php echo $_GET['end_date'] ?? ''; ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fa fa-search"></i> 筛选
                </button>
                <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php" class="btn btn-outline-secondary">
                    <i class="fa fa-refresh"></i> 重置
                </a>
            </div>
        </form>
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

<!-- 数据表格 -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>费用日期</th>
                        <th>费用类型</th>
                        <th>费用金额（美元）</th>
                        <th>备注</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($costs)): ?>
                        <?php foreach ($costs as $cost): ?>
                            <tr>
                                <td><?php echo $cost['id']; ?></td>
                                <td><?php echo $cost['cost_date']; ?></td>
                                <td><?php echo htmlspecialchars($cost['cost_type']); ?></td>
                                <td><?php echo $cost['cost']; ?></td>
                                <td><?php echo htmlspecialchars($cost['remark'] ?? ''); ?></td>
                                <td><?php echo $cost['create_at']; ?></td>
                                <td><?php echo $cost['update_at']; ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?action=edit&id=<?php echo $cost['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?action=delete&id=<?php echo $cost['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('确定要删除这条记录吗？');">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">暂无数据</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- 分页 -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?page=<?php echo $page - 1; ?><?php 
                                $params = [];
                                if (!empty($_GET['cost_type'])) $params[] = 'cost_type=' . urlencode($_GET['cost_type']);
                                if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
                                if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                上一页
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo ($i == $page ? 'active' : ''); ?>">
                            <a class="page-link" href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?page=<?php echo $i; ?><?php 
                                $params = [];
                                if (!empty($_GET['cost_type'])) $params[] = 'cost_type=' . urlencode($_GET['cost_type']);
                                if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
                                if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo ADMIN_PANEL_URL; ?>/company_costs.php?page=<?php echo $page + 1; ?><?php 
                                $params = [];
                                if (!empty($_GET['cost_type'])) $params[] = 'cost_type=' . urlencode($_GET['cost_type']);
                                if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
                                if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                下一页
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
    <?php endif; ?>
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