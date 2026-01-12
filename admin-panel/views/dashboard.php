<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>欢迎回来，<?php echo $_SESSION['full_name']; ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h3>各个平台销售额占比（饼状图）</h3>
                            <canvas id="salesChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h3>各个平台订单总量占比（饼状图）</h3>
                            <canvas id="ordersChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h3>各个平台毛利润占比（饼状图）</h3>
                            <canvas id="profitsChart" width="400" height="300"></canvas>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h3>各个平台广告费占比（饼状图）</h3>
                            <canvas id="costsChart" width="400" height="300"></canvas>
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
        const radius = Math.min(centerX, centerY) - 20;
        
        // 计算总数
        const total = data.reduce((sum, item) => sum + item.value, 0);
        
        if (total === 0) {
            ctx.fillStyle = '#ccc';
            ctx.font = '16px Arial';
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
            
            // 绘制扇形标签
            const labelAngle = startAngle + sliceAngle / 2;
            const labelX = centerX + Math.cos(labelAngle) * (radius + 20);
            const labelY = centerY + Math.sin(labelAngle) * (radius + 20);
            
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'center';
            ctx.fillText(item.label, labelX, labelY);
            
            // 绘制百分比
            const percentage = ((item.value / total) * 100).toFixed(1) + '%';
            ctx.fillText(percentage, labelX, labelY + 15);
            
            startAngle += sliceAngle;
        });
        
        // 绘制图例
        const legendX = centerX - 80;
        const legendY = centerY + radius + 30;
        const legendItemHeight = 20;
        
        data.forEach((item, index) => {
            const itemX = legendX;
            const itemY = legendY + index * legendItemHeight;
            
            // 绘制颜色块
            ctx.fillStyle = colors[index % colors.length];
            ctx.fillRect(itemX, itemY, 15, 15);
            
            // 绘制图例文本
            ctx.fillStyle = '#333';
            ctx.font = '12px Arial';
            ctx.textAlign = 'left';
            ctx.fillText(item.label + ': ' + ((item.value / total) * 100).toFixed(1) + '%', itemX + 20, itemY + 12);
        });
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
    
    // 绘制饼图
    window.onload = function() {
        drawPieChart('salesChart', salesData, '平台销售额占比');
        drawPieChart('ordersChart', ordersData, '平台订单总量占比');
        drawPieChart('profitsChart', profitsData, '平台毛利润占比');
        drawPieChart('costsChart', costsData, '平台广告费占比');
    };
</script>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>