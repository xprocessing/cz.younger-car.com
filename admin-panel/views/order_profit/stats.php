<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>最近30天利润统计</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<!-- 统计概览 -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="card text-center border-primary">
            <div class="card-body">
                <h5 class="card-title text-primary">
                    <?php echo number_format($stats['order_count'] ?? 0); ?>
                </h5>
                <p class="card-text">30天订单数量</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="card text-center border-info">
            <div class="card-body">                
                <p class="card-text">盈利: <?php echo $stats['positive_orders'] ?? 0; ?> 单</p>
                <p class="card-text">亏损: <?php echo $stats['negative_orders'] ?? 0; ?> 单</p>
            </div>
        </div>
    </div>
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
        <div class="card text-center border-success">
            <div class="card-body">
                <h5 class="card-title text-success">
                    多种货币
                </h5>
                <p class="card-text">总订单金额</p>
            </div>
        </div>
    </div>

    <div class="col-md-2">
        <div class="card text-center border-secondary">
            <div class="card-body">
                <h5 class="card-title text-secondary">
                    $<?php
                        $totalCost = ($stats['wms_cost'] ?? 0) + ($stats['wms_shipping'] ?? 0);
                        echo number_format($totalCost, 2);
                        ?>
                </h5>
                <p class="card-text">总货品成本（待统一货币）</p>
            </div>
        </div>
    </div>



    <div class="col-md-2">
        <div class="card text-center border-info">
            <div class="card-body">
                <h5 class="card-title text-info">
                    $<?php
                        $totalWmsCost = ($stats['wms_cost'] ?? 0) + ($stats['wms_shipping'] ?? 0);
                        echo number_format($totalWmsCost, 2);
                        ?>
                </h5>
                <p class="card-text">总运费成本（待统一货币）</p>
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
        <canvas id="profitRateChart" width="600" height="200"></canvas>
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
                                <td><strong>$<?php echo number_format($platform['total_profit'], 2); ?></strong></td>
                                <td><strong
                                        class="<?php echo $platform['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger'; ?>">
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
                                <td><strong>$<?php echo number_format($store['total_profit'], 2); ?></strong></td>
                                <td><strong
                                        class="<?php echo $store['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger'; ?>">
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

<!-- 最近30天按品牌统计 -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">最近30天按品牌统计</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>品牌名称</th>
                        <th>订单数量</th>
                        <th>总利润</th>
                        <th>平均利润率</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($brandStats)): ?>
                        <?php foreach ($brandStats as $brand): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($brand['brand_name']); ?></td>
                                <td><?php echo number_format($brand['order_count']); ?></td>
                                <td><strong>$<?php echo number_format($brand['total_profit'], 2); ?></strong></td>
                                <td><strong
                                        class="<?php echo $brand['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo number_format($brand['avg_profit_rate'], 2); ?>%
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

<!-- 最近30天按SKU统计（前100名） -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="mb-0">最近30天按SKU统计（销量前100名）</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>SKU</th>
                        <th>总销量</th>
                        <th>总利润</th>
                        <th>平均利润率</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($skuStats)): ?>
                        <?php foreach ($skuStats as $sku): ?>
                            <tr>
                                <td><a href="products.php?keyword=<?php echo urlencode($sku['sku']); ?>" target="_blank"><?php echo htmlspecialchars($sku['sku']); ?></a></td>
                                <td><strong><?php echo number_format($sku['order_count']); ?></strong></td>
                                <td><strong>$<?php echo number_format($sku['total_profit'], 2); ?></strong></td>
                                <td><strong
                                        class="<?php echo $sku['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                        <?php echo number_format($sku['avg_profit_rate'], 2); ?>%
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
            labels: ['<0%', '0-10%', '10-20%', '20-30%', '30-40%', '40-50%', '50-60%', '60-70%', '70-80%', '80-90%', '>90%'],
            values: [
                <?php echo $profitRateDistribution['negative'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_0_10'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_10_20'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_20_30'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_30_40'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_40_50'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_50_60'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_60_70'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_70_80'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_80_90'] ?? 0; ?>,
                <?php echo $profitRateDistribution['range_90_plus'] ?? 0; ?>
            ],
            colors: [
                'rgba(255, 99, 132, 0.6)',
                'rgba(255, 159, 64, 0.6)',
                'rgba(255, 205, 86, 0.6)',
                'rgba(75, 192, 192, 0.6)',
                'rgba(54, 162, 235, 0.6)',
                'rgba(153, 102, 255, 0.6)',
                'rgba(255, 99, 255, 0.6)',
                'rgba(201, 203, 207, 0.6)',
                'rgba(255, 128, 0, 0.6)',
                'rgba(128, 128, 0, 0.6)',
                'rgba(0, 128, 0, 0.6)'
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