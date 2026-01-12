# 2026-01-12 更新dashboard折线图宽度自适应

## 更新内容

修复dashboard.php中各个平台销售额折线图（最近60天）的自适应宽度问题，确保图表不会拉伸失真。

## 修改文件

- `admin-panel/views/dashboard.php`

## 具体修改

1. 移除canvas元素的固定width属性：
```html
<!-- 原代码 -->
<canvas id="dailySalesChart" width="800" height="400"></canvas>

<!-- 修改后 -->
<canvas id="dailySalesChart" height="400"></canvas>
```

2. 在折线图绘制函数中动态设置canvas宽度以匹配显示宽度：
```javascript
// 动态设置canvas宽度以匹配显示宽度
canvas.width = canvas.clientWidth;
```

3. 优化图表绘制逻辑，添加窗口大小变化事件监听器：
```javascript
// 绘制所有图表
function drawAllCharts() {
    drawPieChart('salesChart', salesData, '平台销售额占比');
    drawPieChart('ordersChart', ordersData, '平台订单总量占比');
    drawPieChart('profitsChart', profitsData, '平台毛利润占比');
    drawPieChart('costsChart', costsData, '平台广告费占比');
    drawLineChart('dailySalesChart', dailySalesData);
}

// 页面加载完成后绘制图表
window.onload = drawAllCharts;

// 窗口大小变化时重新绘制图表
window.addEventListener('resize', drawAllCharts);
```

## 效果

折线图现在会自适应父容器宽度，铺满整个网页，同时保持图表比例正确，避免拉伸失真，提升了页面的响应式表现和视觉效果。