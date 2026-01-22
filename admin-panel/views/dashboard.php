<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>欢迎回来，<?php echo $_SESSION['full_name']; ?></h5>
            </div>
            <!-- 赛道统计模块 -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="mb-4">
                        <h3>赛道统计 - <?php echo date('Y年m月', strtotime('-1 month')); ?></h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th>赛道名称</th>
                                        <th>总订单数量</th>
                                        <th>总订单金额</th>
                                        <th>总毛利润</th>
                                        <th>总赛道费用</th>
                                        <th>公司成本分摊</th>
                                        <th>店铺费用</th>
                                        <th>总净利润</th>
                                        <th>净利润率</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($trackStatistics)): ?>
                                        <?php foreach ($trackStatistics as $stat): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($stat['track_name'] ?? '未知赛道'); ?></td>
                                                <td><?php echo number_format($stat['order_count']); ?></td>
                                                <td>$<?php echo number_format($stat['total_order_amount'], 2); ?></td>
                                                <td>$<?php echo number_format($stat['total_profit'], 2); ?></td>
                                                <td>$<?php echo number_format($stat['total_cost'], 2); ?></td>
                                                <td>$<?php echo number_format($stat['allocated_company_cost'], 2); ?></td>
                                                <td>$<?php echo number_format($stat['shop_cost'], 2); ?></td>
                                                <td>
                                                    <?php 
                                                        $netProfitClass = $stat['net_profit'] > 0 ? 'text-success' : ($stat['net_profit'] < 0 ? 'text-danger' : 'text-muted');
                                                        echo '<span class="' . $netProfitClass . '">';
                                                        echo '$' . number_format($stat['net_profit'], 2);
                                                        echo '</span>';
                                                    ?>
                                                </td>
                                                <td>
                                                    <?php 
                                                        $marginClass = $stat['net_profit_margin'] > 0 ? 'text-success' : ($stat['net_profit_margin'] < 0 ? 'text-danger' : 'text-muted');
                                                        echo '<span class="' . $marginClass . '">';
                                                        echo $stat['net_profit_margin'] > 0 ? '+' : '';
                                                        echo number_format($stat['net_profit_margin'], 2) . '%';
                                                        echo '</span>';
                                                    ?>
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
                    </div>
                </div>
            </div>

            <div class="card-body">
                <!-- 各平台月度销售额统计 -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="mb-4">
                            <h3>各平台月度销售额统计</h3>
                            <div class="table-responsive">
                                <?php 
                                    // 获取当前月份名称
                                    $currentMonth = date('m月');
                                    // 获取上月月份名称
                                    $lastMonth = date('m月', strtotime('-1 month'));
                                    // 获取上上月月份名称
                                    $lastLastMonth = date('m月', strtotime('-2 month'));
                                ?>
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>平台名称</th>
                                            <th><?php echo $currentMonth; ?>销售额(本月)</th>
                                            <th><?php echo $lastMonth; ?>销售额(上月)</th>
                                            <th><?php echo $lastLastMonth; ?>销售额(上上月)</th>
                                            <th><?php echo $lastMonth; ?>对比<?php echo $lastLastMonth; ?>增长率(上月对比上上月)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($platformMonthlyStats)): ?>
                                            <?php foreach ($platformMonthlyStats as $stat): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($stat['platform_name'] ?? '未知平台'); ?></td>
                                                    <td>$<?php echo number_format($stat['current_month_sales'], 2); ?></td>
                                                    <td>$<?php echo number_format($stat['last_month_sales'], 2); ?></td>
                                                    <td>$<?php echo number_format($stat['last_last_month_sales'], 2); ?></td>
                                                    <td>
                                                        <?php 
                                                            $growthRate = $stat['growth_rate']; 
                                                            $growthClass = $growthRate > 0 ? 'text-success' : ($growthRate < 0 ? 'text-danger' : 'text-muted');
                                                            echo '<span class="' . $growthClass . '">';
                                                            echo $growthRate > 0 ? '+' : '';
                                                            echo number_format($growthRate, 2) . '%';
                                                            echo '</span>';
                                                        ?>
                                                    </td>
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
                </div>

                <!-- 各平台月度订单量统计 -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="mb-4">
                            <h3>各平台月度订单量统计</h3>
                            <div class="table-responsive">
                                <?php 
                                    // 获取当前月份名称
                                    $currentMonth = date('m月');
                                    // 获取上月月份名称
                                    $lastMonth = date('m月', strtotime('-1 month'));
                                    // 获取上上月月份名称
                                    $lastLastMonth = date('m月', strtotime('-2 month'));
                                ?>
                                <table class="table table-bordered table-hover">
                                    <thead class="thead-light">
                                        <tr>
                                            <th>平台名称</th>
                                            <th><?php echo $currentMonth; ?>订单量(本月)</th>
                                            <th><?php echo $lastMonth; ?>订单量(上月)</th>
                                            <th><?php echo $lastLastMonth; ?>订单量(上上月)</th>
                                            <th><?php echo $lastMonth; ?>对比<?php echo $lastLastMonth; ?>增长率(上月对比上上月)</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($platformMonthlyOrderStats)): ?>
                                            <?php foreach ($platformMonthlyOrderStats as $stat): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($stat['platform_name'] ?? '未知平台'); ?></td>
                                                    <td><?php echo number_format($stat['current_month_orders']); ?></td>
                                                    <td><?php echo number_format($stat['last_month_orders']); ?></td>
                                                    <td><?php echo number_format($stat['last_last_month_orders']); ?></td>
                                                    <td>
                                                        <?php 
                                                            $growthRate = $stat['growth_rate']; 
                                                            $growthClass = $growthRate > 0 ? 'text-success' : ($growthRate < 0 ? 'text-danger' : 'text-muted');
                                                            echo '<span class="' . $growthClass . '">';
                                                            echo $growthRate > 0 ? '+' : '';
                                                            echo number_format($growthRate, 2) . '%';
                                                            echo '</span>';
                                                        ?>
                                                    </td>
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
                </div>

                <!-- 各平台月度销售额统计 -->


                <div class="row">
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h3>各平台销售额占比</h3>
                            <canvas id="salesChart" width="250" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h3>各平台订单总量占比</h3>
                            <canvas id="ordersChart" width="250" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h3>各平台毛利润占比</h3>
                            <canvas id="profitsChart" width="250" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h3>各平台广告费占比</h3>
                            <canvas id="costsChart" width="250" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h3>赛道销售占比</h3>
                            <canvas id="trackSalesChart" width="250" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="mb-4">
                            <h3>赛道利润占比</h3>
                            <canvas id="trackProfitChart" width="250" height="250"></canvas>
                        </div>
                    </div>
                </div>

                <!-- 各个平台销售额折线图（最近60天） -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-12">
                            <h3>各个平台销售额折线图（最近60天）</h3>
                            <canvas id="dailySalesChart" height="400" style="width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Amazon + Amazon-FBA 平台近60天日销售额柱状图 -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-12">
                            <h3>Amazon + Amazon-FBA 平台近60天日销售额柱状图</h3>
                            <canvas id="amazonSalesChart" height="300" style="width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- eBay 平台近60天日销售额柱状图 -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-12">
                            <h3>eBay 平台近60天日销售额柱状图</h3>
                            <canvas id="ebaySalesChart" height="300" style="width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                
                <!-- Shopify 平台近60天日销售额柱状图 -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="mb-12">
                            <h3>Shopify 平台近60天日销售额柱状图</h3>
                            <canvas id="shopifySalesChart" height="300" style="width: 100%;"></canvas>
                        </div>
                    </div>
                </div>
                <!-- 最近30天按平台统计 -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="mb-4">
                        <h3>最近30天按平台统计</h3>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>平台名称</th>
                                        <th>订单数量</th>
                                        <th>总订单金额</th>
                                        <th>总利润</th>
                                        <th>总出库成本</th>
                                        <th>总运费</th>
                                        <th>平均利润率</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($platformStats)): ?>
                                        <?php foreach ($platformStats as $platform): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($platform['platform_name']); ?></td>
                                                <td><?php echo number_format($platform['order_count']); ?></td>
                                                <td><strong>$<?php echo number_format($platform['total_amount'], 2); ?></strong></td>
                                                <td><strong>$<?php echo number_format($platform['total_profit'], 2); ?></strong></td>
                                                <td><strong>$<?php echo number_format($platform['total_wms_cost'], 2); ?></strong></td>
                                                <td><strong>$<?php echo number_format($platform['total_wms_shipping'], 2); ?></strong></td>
                                                <td><strong
                                                        class="<?php echo $platform['avg_profit_rate'] >= 0 ? 'text-success' : 'text-danger'; ?>">
                                                        <?php echo number_format($platform['avg_profit_rate'], 2); ?>%
                                                    </strong></td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="7" class="text-center">暂无数据</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

                
                


                
            </div>
        </div>
    </div>
</div>

<script>
    // 饼图绘制函数
    function drawPieChart(canvasId, data, title) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        const centerX = width / 2;
        const centerY = height / 2;
        const radius = Math.min(centerX, centerY) - 15;
        
        // 计算总数
        const total = data.reduce((sum, item) => sum + item.value, 0);
        
        if (total === 0) {
            ctx.fillStyle = '#ccc';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('暂无数据', centerX, centerY);
            return;
        }
        
        // 颜色数组
        const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#8ACB88', '#8884D8'];
        
        let startAngle = -Math.PI / 2; // 从12点位置开始
        
        // 绘制饼图
        data.forEach((item, index) => {
            const sliceAngle = (item.value / total) * 2 * Math.PI;
            
            // 绘制扇形
            ctx.fillStyle = colors[index % colors.length];
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, startAngle, startAngle + sliceAngle);
            ctx.closePath();
            ctx.fill();
            
            // 计算扇区中心点
            const labelAngle = startAngle + sliceAngle / 2;
            const textRadius = radius * 0.6; // 文本距离圆心的距离
            const textX = centerX + Math.cos(labelAngle) * textRadius;
            const textY = centerY + Math.sin(labelAngle) * textRadius;
            
            // 绘制扇区内平台名称和百分比
            const platformName = item.label;
            const percentage = ((item.value / total) * 100).toFixed(1) + '%';
            ctx.fillStyle = '#fff';
            ctx.textAlign = 'center';
            
            // 绘制平台名称
            ctx.font = 'bold 10px Arial';
            ctx.fillText(platformName, textX, textY - 4);
            
            // 绘制百分比
            ctx.font = 'bold 12px Arial';
            ctx.fillText(percentage, textX, textY + 10);
            
            // 不再需要外部标签，因为信息已在扇区内显示
            // if (width > 300) {
            //     const labelX = centerX + Math.cos(labelAngle) * (radius + 15);
            //     const labelY = centerY + Math.sin(labelAngle) * (radius + 15);
            //     
            //     ctx.fillStyle = '#333';
            //     ctx.font = '10px Arial';
            //     ctx.textAlign = 'center';
            //     ctx.fillText(item.label, labelX, labelY);
            // }
            
            startAngle += sliceAngle;
        });
        
        // 绘制图例
        const legendX = 10;
        const legendY = centerY + radius - 20;
        const legendItemHeight = 16;
        
        // 仅在小尺寸画布上显示图例
        if (width <= 300) {
            data.forEach((item, index) => {
                const itemX = legendX;
                const itemY = legendY + index * legendItemHeight;
                
                // 绘制颜色块
                ctx.fillStyle = colors[index % colors.length];
                ctx.fillRect(itemX, itemY, 12, 12);
                
                // 绘制图例文本
                ctx.fillStyle = '#333';
                ctx.font = '10px Arial';
                ctx.textAlign = 'left';
                ctx.fillText(item.label, itemX + 18, itemY + 10);
            });
        }
    }
    
    // 数据准备
    const salesData = <?php 
        $salesData = [];
        foreach ($platformSales as $sale) {
            $salesData[] = [
                'label' => $sale['platform_name'],
                'value' => floatval($sale['total_sales'])
            ];
        }
        echo json_encode($salesData);
    ?>;
    
    const ordersData = <?php 
        $ordersData = [];
        foreach ($platformOrders as $order) {
            $ordersData[] = [
                'label' => $order['platform_name'],
                'value' => intval($order['order_count'])
            ];
        }
        echo json_encode($ordersData);
    ?>;
    
    const profitsData = <?php 
        $profitsData = [];
        foreach ($platformProfits as $profit) {
            $profitsData[] = [
                'label' => $profit['platform_name'],
                'value' => floatval($profit['total_profit'])
            ];
        }
        echo json_encode($profitsData);
    ?>;
    
    const costsData = <?php 
        $costsData = [];
        foreach ($platformCosts as $cost) {
            $costsData[] = [
                'label' => $cost['platform_name'],
                'value' => floatval($cost['total_cost'])
            ];
        }
        echo json_encode($costsData);
    ?>;
    
    // 赛道销售数据
    const trackSalesData = <?php 
        echo json_encode($trackSalesData);
    ?>;
    
    // 赛道利润数据
    const trackProfitData = <?php 
        echo json_encode($trackProfitData);
    ?>;
    
    // 准备折线图数据
    const dailySalesData = <?php 
        echo json_encode($dailyPlatformSales);
    ?>;
    
    // 折线图绘制函数
    function drawLineChart(canvasId, data) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        // 动态设置canvas宽度以匹配显示宽度
        canvas.width = canvas.clientWidth;
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        const padding = { top: 30, right: 50, bottom: 60, left: 70 };
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        
        // 清空画布
        ctx.clearRect(0, 0, width, height);
        
        // 绘制背景
        ctx.fillStyle = '#f8f9fa';
        ctx.fillRect(0, 0, width, height);
        
        const dates = data.dates;
        const platforms = data.platforms;
        const sales = data.sales;
        
        if (dates.length === 0 || platforms.length === 0) {
            ctx.fillStyle = '#ccc';
            ctx.font = '16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('暂无数据', width / 2, height / 2);
            return;
        }
        
        // 颜色数组（与饼图保持一致）
        const colors = ['#FF6384', '#36A2EB', '#FFCE56', '#4BC0C0', '#9966FF', '#FF9F40', '#8ACB88', '#8884D8'];
        
        // 计算X轴和Y轴的刻度
        const maxSales = Math.max(...sales.flat());
        const yScale = chartHeight / maxSales;
        const xStep = chartWidth / (dates.length - 1);
        
        // 绘制网格线
        ctx.strokeStyle = '#e0e0e0';
        ctx.lineWidth = 1;
        
        // Y轴网格线
        for (let i = 0; i <= 10; i++) {
            const y = padding.top + (chartHeight / 10) * i;
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(padding.left + chartWidth, y);
            ctx.stroke();
            
            // Y轴刻度
            const value = (maxSales / 10) * (10 - i);
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'right';
            ctx.fillText('$' + value.toFixed(0), padding.left - 10, y + 4);
        }
        
        // X轴网格线（每7天显示一个日期）
        for (let i = 0; i < dates.length; i += Math.ceil(dates.length / 10)) {
            const x = padding.left + i * xStep;
            ctx.beginPath();
            ctx.moveTo(x, padding.top);
            ctx.lineTo(x, padding.top + chartHeight);
            ctx.stroke();
            
            // X轴日期标签
            const date = new Date(dates[i]);
            const dateLabel = (date.getMonth() + 1) + '/' + date.getDate();
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(dateLabel, x, padding.top + chartHeight + 20);
        }
        
        // 绘制折线
        platforms.forEach((platform, platformIndex) => {
            ctx.strokeStyle = colors[platformIndex % colors.length];
            ctx.fillStyle = colors[platformIndex % colors.length];
            ctx.lineWidth = 2;
            
            // 绘制折线
            ctx.beginPath();
            for (let i = 0; i < dates.length; i++) {
                const x = padding.left + i * xStep;
                const y = padding.top + chartHeight - (sales[i][platformIndex] * yScale);
                
                if (i === 0) {
                    ctx.moveTo(x, y);
                } else {
                    ctx.lineTo(x, y);
                }
            }
            ctx.stroke();
            
            // 绘制数据点
            for (let i = 0; i < dates.length; i += Math.ceil(dates.length / 20)) {
                const x = padding.left + i * xStep;
                const y = padding.top + chartHeight - (sales[i][platformIndex] * yScale);
                
                ctx.beginPath();
                ctx.arc(x, y, 4, 0, Math.PI * 2);
                ctx.fill();
                
                // 添加数值标签（每10天显示一个）
                if (i % Math.ceil(dates.length / 10) === 0) {
                    ctx.fillStyle = '#333';
                    ctx.font = '10px Arial';
                    ctx.textAlign = 'center';
                    ctx.fillText('$' + sales[i][platformIndex].toFixed(0), x, y - 10);
                }
            }
        });
        
        // 绘制图例
        const legendX = padding.left + chartWidth - 200;
        const legendY = padding.top + 10;
        const legendItemHeight = 20;
        
        platforms.forEach((platform, index) => {
            const itemY = legendY + index * legendItemHeight;
            
            // 绘制颜色块
            ctx.fillStyle = colors[index % colors.length];
            ctx.fillRect(legendX, itemY, 15, 15);
            
            // 绘制图例文本
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'left';
            ctx.fillText(platform, legendX + 20, itemY + 12);
        });
        
        // 绘制图表标题
        ctx.fillStyle = '#333';
        ctx.font = '16px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('各平台销售额趋势（最近60天）', width / 2, padding.top - 10);
        
        // 绘制X轴和Y轴标题
        ctx.fillStyle = '#666';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('日期', width / 2, height - 20);
        
        ctx.save();
        ctx.translate(padding.left / 2, height / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.fillStyle = '#666';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('销售额（美元）', 0, 0);
        ctx.restore();
    }
    
    // 柱状图绘制函数
    function drawBarChart(canvasId, data, platformsToShow, chartTitle) {
        const canvas = document.getElementById(canvasId);
        if (!canvas) return;
        
        // 动态设置canvas宽度以匹配显示宽度
        canvas.width = canvas.clientWidth;
        const ctx = canvas.getContext('2d');
        const width = canvas.width;
        const height = canvas.height;
        const padding = { top: 30, right: 50, bottom: 60, left: 70 };
        const chartWidth = width - padding.left - padding.right;
        const chartHeight = height - padding.top - padding.bottom;
        
        // 清空画布
        ctx.clearRect(0, 0, width, height);
        
        // 绘制背景
        ctx.fillStyle = '#f8f9fa';
        ctx.fillRect(0, 0, width, height);
        
        const dates = data.dates;
        const platforms = data.platforms;
        const sales = data.sales;
        
        if (dates.length === 0) {
            ctx.fillStyle = '#ccc';
            ctx.font = '16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('暂无数据', width / 2, height / 2);
            return;
        }
        
        // 颜色数组
        const colors = ['#36A2EB', '#FF6384', '#4BC0C0', '#FFCE56', '#9966FF', '#FF9F40'];
        
        // 准备要显示的数据
        const filteredPlatforms = platforms.filter(platform => platformsToShow.includes(platform));
        const filteredPlatformIndices = platformsToShow.map(platform => platforms.indexOf(platform)).filter(idx => idx !== -1);
        
        if (filteredPlatformIndices.length === 0) {
            ctx.fillStyle = '#ccc';
            ctx.font = '16px Arial';
            ctx.textAlign = 'center';
            ctx.fillText('暂无数据', width / 2, height / 2);
            return;
        }
        
        // 计算总销售额（每个平台的销售额相加）
        const combinedSales = sales.map(dateSales => {
            return filteredPlatformIndices.reduce((sum, idx) => sum + dateSales[idx], 0);
        });
        
        // 计算Y轴的刻度
        const maxSales = Math.max(...combinedSales);
        const yScale = chartHeight / maxSales;
        const barWidth = chartWidth / dates.length * 0.8;
        
        // 绘制网格线
        ctx.strokeStyle = '#e0e0e0';
        ctx.lineWidth = 1;
        
        // Y轴网格线
        for (let i = 0; i <= 10; i++) {
            const y = padding.top + (chartHeight / 10) * i;
            ctx.beginPath();
            ctx.moveTo(padding.left, y);
            ctx.lineTo(padding.left + chartWidth, y);
            ctx.stroke();
            
            // Y轴刻度
            const value = (maxSales / 10) * (10 - i);
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'right';
            ctx.fillText('$' + value.toFixed(0), padding.left - 10, y + 4);
        }
        
        // X轴网格线（每7天显示一个日期）
        for (let i = 0; i < dates.length; i += Math.ceil(dates.length / 10)) {
            const x = padding.left + i * (chartWidth / dates.length);
            ctx.beginPath();
            ctx.moveTo(x, padding.top);
            ctx.lineTo(x, padding.top + chartHeight);
            ctx.stroke();
            
            // X轴日期标签
            const date = new Date(dates[i]);
            const dateLabel = (date.getMonth() + 1) + '/' + date.getDate();
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(dateLabel, x, padding.top + chartHeight + 20);
        }
        
        // 绘制柱状图
        dates.forEach((date, dateIndex) => {
            const x = padding.left + dateIndex * (chartWidth / dates.length) + (chartWidth / dates.length - barWidth) / 2;
            let totalHeight = 0;
            
            // 计算该日期的总销售额
            const dateTotal = combinedSales[dateIndex];
            
            // 绘制总销售额柱状图
            const barHeight = dateTotal * yScale;
            const barX = x;
            const barY = padding.top + chartHeight - barHeight;
            
            ctx.fillStyle = '#36A2EB';
            ctx.fillRect(barX, barY, barWidth, barHeight);
            
            // 绘制边框
            ctx.strokeStyle = '#2196F3';
            ctx.lineWidth = 1;
            ctx.strokeRect(barX, barY, barWidth, barHeight);
            
            // 显示数值标签（每5天显示一个）
            if (dateIndex % Math.ceil(dates.length / 15) === 0) {
                ctx.fillStyle = '#333';
                ctx.font = '10px Arial';
                ctx.textAlign = 'center';
                ctx.fillText('$' + dateTotal.toFixed(0), barX + barWidth / 2, barY - 10);
            }
        });
        
        // 绘制图表标题
        ctx.fillStyle = '#333';
        ctx.font = '16px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(chartTitle, width / 2, padding.top - 10);
        
        // 绘制X轴和Y轴标题
        ctx.fillStyle = '#666';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('日期', width / 2, height - 20);
        
        ctx.save();
        ctx.translate(padding.left / 2, height / 2);
        ctx.rotate(-Math.PI / 2);
        ctx.fillStyle = '#666';
        ctx.font = '14px Arial';
        ctx.textAlign = 'center';
        ctx.fillText('销售额（美元）', 0, 0);
        ctx.restore();
    }
    
    // 绘制所有图表
    function drawAllCharts() {
        drawPieChart('salesChart', salesData, '平台销售额占比');
        drawPieChart('ordersChart', ordersData, '平台订单总量占比');
        drawPieChart('profitsChart', profitsData, '平台毛利润占比');
        drawPieChart('costsChart', costsData, '平台广告费占比');
        drawLineChart('dailySalesChart', dailySalesData);
        
        // 绘制Amazon + Amazon-FBA平台柱状图
        drawBarChart('amazonSalesChart', dailySalesData, ['Amazon', 'Amazon-FBA'], 'Amazon + Amazon-FBA 平台近60天日销售额');
        
        // 绘制eBay平台柱状图
        drawBarChart('ebaySalesChart', dailySalesData, ['eBay'], 'eBay 平台近60天日销售额');
        
        // 绘制Shopify平台柱状图
        drawBarChart('shopifySalesChart', dailySalesData, ['Shopify'], 'Shopify 平台近60天日销售额');
        
        // 绘制赛道销售占比饼图
        drawPieChart('trackSalesChart', trackSalesData, '赛道销售占比');
        
        // 绘制赛道利润占比饼图
        drawPieChart('trackProfitChart', trackProfitData, '赛道利润占比');
    }
    
    // 页面加载完成后绘制图表
    window.onload = drawAllCharts;
    
    // 窗口大小变化时重新绘制图表
    window.addEventListener('resize', drawAllCharts);
</script>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>