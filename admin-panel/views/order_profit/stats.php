<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>利润统计</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<!-- 统计概览 -->
<div class="row mb-4">
    <div class="col-md-1">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h5 class="card-title text-primary">
                    <?php echo number_format($stats['order_count'] ?? 0); ?>
                </h5>
                <p class="card-text">订单数量</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-1">
        <div class="card text-center border-success">
            <div class="card-body">
                <h5 class="card-title text-success">
                    多种货币
                </h5>
                <p class="card-text">总订单金额</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-1">
        <div class="card text-center border-secondary">
            <div class="card-body">
                <h5 class="card-title text-secondary">
                    ¥<?php 
                    $totalCost = ($stats['wms_cost'] ?? 0) + ($stats['wms_shipping'] ?? 0);
                    echo number_format($totalCost, 2); 
                    ?>
                </h5>
                <p class="card-text">总成本</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-1">
        <div class="card text-center border-info">
            <div class="card-body">
                <h5 class="card-title text-info">
                    盈亏统计
                </h5>
                <p class="card-text">盈利: <?php echo $stats['positive_orders'] ?? 0; ?> 单</p>
                <p class="card-text">亏损: <?php echo $stats['negative_orders'] ?? 0; ?> 单</p>
            </div>
        </div>
    </div>
</div>

                <div class="row mb-2">
    <div class="col-md-2">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning">
                    <?php echo number_format($stats['avg_profit_rate'] ?? 0, 2); ?>%
                </h5>
                <p class="card-text">平均利润率</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-2">
        <div class="card text-center border-info">
            <div class="card-body">
                <h5 class="card-title text-info">
                    ¥<?php 
                    $totalWmsCost = ($stats['wms_cost'] ?? 0) + ($stats['wms_shipping'] ?? 0);
                    echo number_format($totalWmsCost, 2); 
                    ?>
                </h5>
                <p class="card-text">WMS总成本</p>
            </div>
        </div>
    </div>
</div>

<!-- 利润率分布 -->
<div class="card mb-4">
    <div class="card-header">
        <h5 class="mb-0">利润率分布</h5>
    </div>
    <div class="card-body">
        <canvas id="profitRateChart" width="400" height="200"></canvas>
    </div>
</div>

<!-- 详细的利润分析 -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">详细分析</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6>成本构成分析</h6>
                <table class="table table-sm">
                    <tr>
                        <td>WMS出库成本:</td>
                        <td><strong>¥<?php echo number_format($stats['wms_cost'] ?? 0, 2); ?></strong></td>
                    </tr>
                    <tr>
                        <td>WMS运费成本:</td>
                        <td><strong>¥<?php echo number_format($stats['wms_shipping'] ?? 0, 2); ?></strong></td>
                    </tr>
                    <tr class="table-primary">
                        <td>WMS总成本:</td>
                        <td><strong>¥<?php 
                            $wmsTotal = ($stats['wms_cost'] ?? 0) + ($stats['wms_shipping'] ?? 0);
                            echo number_format($wmsTotal, 2); 
                        ?></strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <h6>关键指标</h6>
                <table class="table table-sm">
                    <tr>
                        <td>平均订单金额:</td>
                        <td><strong>¥<?php 
                            $avgOrderAmount = ($stats['order_count'] ?? 0) > 0 ? (($stats['total_amount'] ?? 0) / ($stats['order_count'] ?? 1)) : 0;
                            echo number_format($avgOrderAmount, 2); 
                        ?></strong></td>
                    </tr>
                    <tr>
                        <td>平均毛利润:</td>
                        <td><strong>¥<?php 
                            $avgProfit = ($stats['order_count'] ?? 0) > 0 ? (($stats['total_profit'] ?? 0) / ($stats['order_count'] ?? 1)) : 0;
                            echo number_format($avgProfit, 2); 
                        ?></strong></td>
                    </tr>
                    <tr>
                        <td>最高利润率:</td>
                        <td><strong class="text-success"><?php echo number_format($stats['avg_profit_rate'] ?? 0, 2); ?>%</strong></td>
                    </tr>
                    <tr>
                        <td>利润有效率:</td>
                        <td><strong class="text-info"><?php 
                            $profitRate = ($stats['avg_profit_rate'] ?? 0);
                            echo $profitRate > 0 ? '盈利' : ($profitRate < 0 ? '亏损' : '平衡'); 
                        ?></strong></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- 最近30天按平台统计 -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">最近30天按平台统计</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>平台名称</th>
                        <th>订单数量</th>
                        <th>总利润</th>
                        <th>平均利润率</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($platformStats)): ?>
                        <?php foreach ($platformStats as $platform): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($platform['platform_name']); ?></td>
                                <td><?php echo number_format($platform['order_count']); ?></td>
                                <td><strong>¥<?php echo number_format($platform['total_profit'], 2); ?></strong></td>
                                <td><strong class="<?php echo $platform['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo number_format($platform['avg_profit_rate'], 2); ?>%
                                </strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4" class="text-center">暂无数据</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 最近30天按店铺统计 -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">最近30天按店铺统计</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>平台名称</th>
                        <th>店铺名称</th>
                        <th>订单数量</th>
                        <th>总利润</th>
                        <th>平均利润率</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($storeStats)): ?>
                        <?php foreach ($storeStats as $store): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($store['platform_name']); ?></td>
                                <td><?php echo htmlspecialchars($store['store_name']); ?></td>
                                <td><?php echo number_format($store['order_count']); ?></td>
                                <td><strong>¥<?php echo number_format($store['total_profit'], 2); ?></strong></td>
                                <td><strong class="<?php echo $store['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                    <?php echo number_format($store['avg_profit_rate'], 2); ?>%
                                </strong></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">暂无数据</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
// 等待DOM加载完成
document.addEventListener('DOMContentLoaded', function() {
    const canvas = document.getElementById('profitRateChart');
    if (!canvas) {
        console.error('Canvas element not found');
        return;
    }
    
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    
    // 使用真实的利润率分布数据
    const data = {
        labels: ['亏损 (<0%)', '低利润 (0-5%)', '正常利润 (5-15%)', '高利润 (>15%)'],
        values: [
            <?php echo $profitRateDistribution['negative'] ?? 0; ?>,
            <?php echo $profitRateDistribution['low'] ?? 0; ?>,
            <?php echo $profitRateDistribution['normal'] ?? 0; ?>,
            <?php echo $profitRateDistribution['high'] ?? 0; ?>
        ],
        colors: [
            'rgba(255, 99, 132, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ]
    };
    
    console.log('Canvas data:', data);
    
    // 清空画布
    ctx.clearRect(0, 0, width, height);
    
    // 绘制背景
    ctx.fillStyle = '#f8f9fa';
    ctx.fillRect(0, 0, width, height);
    
    const barWidth = width / data.labels.length;
    const maxValue = Math.max(...data.values);
    console.log('Max value:', maxValue);
    
    data.values.forEach((value, index) => {
        const barHeight = maxValue > 0 ? (value / maxValue) * (height - 60) : 0;
        const x = index * barWidth + barWidth * 0.15;
        const y = height - barHeight - 40;
        
        console.log(`Bar ${index}: value=${value}, barHeight=${barHeight}, x=${x}, y=${y}`);
        
        // 绘制柱状图
        ctx.fillStyle = data.colors[index];
        ctx.fillRect(x, y, barWidth * 0.7, barHeight);
        
        // 绘制数值
        ctx.fillStyle = '#333';
        ctx.font = 'bold 14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(value, x + barWidth * 0.35, y - 5);
        
        // 绘制标签
        ctx.font = '12px Arial';
        const labelParts = data.labels[index].split(' ');
        ctx.fillText(labelParts[0], x + barWidth * 0.35, height - 25);
        if (labelParts[1]) {
            ctx.fillText(labelParts[1], x + barWidth * 0.35, height - 10);
        }
    });
    
    console.log('Chart drawing completed');
});
</script>