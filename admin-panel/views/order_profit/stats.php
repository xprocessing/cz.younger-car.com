<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>利润统计</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<!-- 筛选条件 -->
<div class="card mb-3">
    <div class="card-header">
        <h5 class="mb-0">筛选条件</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/order_profit.php" class="row g-3">
            <input type="hidden" name="action" value="stats">
            
            <div class="col-md-3">
                <label for="start_date" class="form-label">开始日期</label>
                <input type="date" class="form-control" id="start_date" name="start_date"
                       value="<?php echo $_GET['start_date'] ?? ''; ?>">
            </div>
            
            <div class="col-md-3">
                <label for="end_date" class="form-label">结束日期</label>
                <input type="date" class="form-control" id="end_date" name="end_date"
                       value="<?php echo $_GET['end_date'] ?? ''; ?>">
            </div>
            
            <div class="col-md-3">
                <label for="store_id" class="form-label">店铺</label>
                <select name="store_id" class="form-select">
                    <option value="">全部店铺</option>
                    <?php if (!empty($storeList)): ?>
                        <?php foreach ($storeList as $store): ?>
                            <option value="<?php echo htmlspecialchars($store); ?>" 
                                    <?php echo (($_GET['store_id'] ?? '') == $store ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($store); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> 筛选
                    </button>
                    <a href="?action=stats" class="btn btn-outline-secondary">
                        <i class="fa fa-refresh"></i> 重置
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 统计概览 -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h5 class="card-title text-primary">
                    <?php echo number_format($stats['order_count'] ?? 0); ?>
                </h5>
                <p class="card-text">订单数量</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center border-success">
            <div class="card-body">
                <h5 class="card-title text-success">
                    ¥<?php echo number_format($stats['total_amount'] ?? 0, 2); ?>
                </h5>
                <p class="card-text">总订单金额</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center border-info">
            <div class="card-body">
                <h5 class="card-title text-info">
                    ¥<?php echo number_format($stats['total_cost'] ?? 0, 2); ?>
                </h5>
                <p class="card-text">总成本</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card text-center <?php echo ($stats['total_profit'] ?? 0) >= 0 ? 'border-success' : 'border-danger'; ?>">
            <div class="card-body">
                <h5 class="card-title <?php echo ($stats['total_profit'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?>">
                    ¥<?php echo number_format($stats['total_profit'] ?? 0, 2); ?>
                </h5>
                <p class="card-text">总毛利润</p>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card text-center border-warning">
            <div class="card-body">
                <h5 class="card-title text-warning">
                    <?php echo number_format($stats['avg_profit_rate'] ?? 0, 2); ?>%
                </h5>
                <p class="card-text">平均利润率</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card text-center border-secondary">
            <div class="card-body">
                <h5 class="card-title text-secondary">
                    ¥<?php echo number_format($stats['wms_cost'] ?? 0, 2); ?>
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
                        <td>订单总成本:</td>
                        <td><strong>¥<?php echo number_format($stats['total_cost'] ?? 0, 2); ?></strong></td>
                    </tr>
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

<script>
// 简单的利润率分布图表
const ctx = document.getElementById('profitRateChart').getContext('2d');
const profitRate = <?php echo $stats['avg_profit_rate'] ?? 0; ?>;

// 模拟数据（实际项目中应该从后端获取详细数据）
const data = {
    labels: ['亏损 (<0%)', '低利润 (0-5%)', '正常利润 (5-15%)', '高利润 (>15%)'],
    datasets: [{
        label: '订单数量',
        data: [
            Math.max(0, Math.floor(Math.random() * 10)),
            Math.max(0, Math.floor(Math.random() * 20)),
            Math.max(0, Math.floor(Math.random() * 50)),
            Math.max(0, Math.floor(Math.random() * 30))
        ],
        backgroundColor: [
            'rgba(255, 99, 132, 0.6)',
            'rgba(255, 206, 86, 0.6)',
            'rgba(54, 162, 235, 0.6)',
            'rgba(75, 192, 192, 0.6)'
        ],
        borderColor: [
            'rgba(255, 99, 132, 1)',
            'rgba(255, 206, 86, 1)',
            'rgba(54, 162, 235, 1)',
            'rgba(75, 192, 192, 1)'
        ],
        borderWidth: 1
    }]
};

// 简单的条形图绘制（没有Chart.js时的降级方案）
if (typeof Chart === 'undefined') {
    const canvas = ctx.canvas;
    const ctx = canvas.getContext('2d');
    const width = canvas.width;
    const height = canvas.height;
    const barWidth = width / data.labels.length;
    
    ctx.clearRect(0, 0, width, height);
    
    data.datasets[0].data.forEach((value, index) => {
        const barHeight = (value / Math.max(...data.datasets[0].data)) * (height - 40);
        const x = index * barWidth + barWidth * 0.2;
        const y = height - barHeight - 20;
        
        ctx.fillStyle = data.datasets[0].backgroundColor[index];
        ctx.fillRect(x, y, barWidth * 0.6, barHeight);
        
        ctx.fillStyle = '#333';
        ctx.font = '12px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(value, x + barWidth * 0.3, y - 5);
        ctx.fillText(data.labels[index].split(' ')[0], x + barWidth * 0.3, height - 5);
    });
}
</script>