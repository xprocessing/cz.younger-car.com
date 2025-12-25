<?php
session_start();
$_SESSION['user_id'] = 1;
$_SESSION['full_name'] = '测试用户';
$_SESSION['role_id'] = 1;

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/models/OrderProfit.php';

$model = new OrderProfit();
$startDate = date('Y-m-d', strtotime('-30 days'));
$endDate = date('Y-m-d');

$profitRateDistribution = $model->getProfitRateDistribution($startDate, $endDate);

echo "<h2>利润率分布数据</h2>";
echo "<pre>";
print_r($profitRateDistribution);
echo "</pre>";

echo "<h2>Canvas图表测试</h2>";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>利润率分布测试</title>
    <style>
        canvas {
            border: 1px solid #ccc;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <canvas id="profitRateChart" width="400" height="200"></canvas>
    
    <script>
    const canvas = document.getElementById('profitRateChart');
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
    
    console.log('Canvas:', canvas);
    console.log('Context:', ctx);
    console.log('Data:', data);
    
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
    </script>
</body>
</html>
