# 2026-01-12 更新dashboard折线图宽度自适应

## 更新内容

将dashboard.php中各个平台销售额折线图（最近60天）的canvas元素宽度修改为100%自适应铺满网页。

## 修改文件

- `admin-panel/views/dashboard.php`

## 具体修改

将第42行的canvas元素：
```html
<canvas id="dailySalesChart" width="800" height="400"></canvas>
```

修改为：
```html
<canvas id="dailySalesChart" style="width: 100%;" height="400"></canvas>
```

## 效果

折线图现在会自适应父容器宽度，铺满整个网页，提升了页面的响应式表现和视觉效果。