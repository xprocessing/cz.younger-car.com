# 2026-01-12 调整dashboard图表布局和尺寸

## 更新内容

将dashboard.php中的四个饼图从两行两列布局调整为一行四列布局，并减小每个图表的尺寸，以优化页面布局和显示效果。

## 修改文件

- `admin-panel/views/dashboard.php`

## 具体修改

1. **调整HTML结构，将两行两列改为一行四列**：
```html
<!-- 原代码 -->
<div class="row">
    <div class="col-md-6">
        <!-- 图表内容 -->
    </div>
    <div class="col-md-6">
        <!-- 图表内容 -->
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <!-- 图表内容 -->
    </div>
    <div class="col-md-6">
        <!-- 图表内容 -->
    </div>
</div>

<!-- 修改后 -->
<div class="row">
    <div class="col-md-3">
        <!-- 图表内容 -->
    </div>
    <div class="col-md-3">
        <!-- 图表内容 -->
    </div>
    <div class="col-md-3">
        <!-- 图表内容 -->
    </div>
    <div class="col-md-3">
        <!-- 图表内容 -->
    </div>
</div>
```

2. **减小每个饼图canvas的尺寸**：
```html
<!-- 原代码 -->
<canvas id="salesChart" width="400" height="300"></canvas>

<!-- 修改后 -->
<canvas id="salesChart" width="250" height="200"></canvas>
```

3. **优化饼图绘制函数，适配小尺寸画布**：
```javascript
// 减小字体大小
ctx.font = '12px Arial'; // 原：16px Arial

// 调整半径
const radius = Math.min(centerX, centerY) - 15; // 原：20

// 仅在画布足够大时显示扇形标签
if (width > 300) {
    // 绘制标签逻辑
}

// 调整图例位置和大小
const legendX = 10;
const legendY = centerY + radius - 20;
const legendItemHeight = 16; // 原：20
```

## 效果

- 四个饼图现在以一行四列的布局显示，节省了页面空间
- 每个图表尺寸减小，更适合在一行中展示
- 饼图绘制函数经过优化，确保在小尺寸画布上也能清晰显示
- 页面布局更加紧凑和专业，提高了信息密度

## 技术细节

- 使用Bootstrap的栅格系统（col-md-3）实现四列布局
- 根据画布宽度动态调整图表元素的显示
- 优化了图例位置，确保在小尺寸画布上不会溢出
- 保持了颜色系统的一致性，便于用户识别不同平台的数据