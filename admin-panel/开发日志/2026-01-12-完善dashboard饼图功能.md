# 2026-01-12 - 完善dashboard饼图功能

## 功能描述
完善dashboard.php页面中的四个饼图功能，展示各平台的销售数据统计：
1. 各个平台销售额占比（饼状图）
2. 各个平台订单总量占比（饼状图）
3. 各个平台毛利润占比（饼状图）
4. 各个广告费占比（饼状图）

## 实现步骤

### 1. 数据库模型扩展
在`OrderProfit`模型中添加了四个统计方法：

- **getPlatformSalesPercentage($startDate, $endDate)**
  - 功能：获取各平台销售额占比数据
  - SQL：使用`SUM`聚合函数计算各平台的总销售额
  - 数据处理：移除货币符号并转换为DECIMAL类型

- **getPlatformOrderCountPercentage($startDate, $endDate)**
  - 功能：获取各平台订单总量占比数据
  - SQL：使用`COUNT`函数统计各平台的订单数量

- **getPlatformProfitPercentage($startDate, $endDate)**
  - 功能：获取各平台毛利润占比数据
  - SQL：使用`SUM`聚合函数计算各平台的总毛利润

- **getPlatformCostPercentage($startDate, $endDate)**
  - 功能：获取各平台广告费占比数据
  - SQL：关联`costs`表，统计各平台的广告费用

### 2. 控制器更新
在`AuthController`的`dashboard`方法中：
- 引入`OrderProfit`模型
- 设置默认时间范围（最近30天）
- 获取四种饼图所需的数据

### 3. 视图完善
在`dashboard.php`视图中：
- 添加了四个Canvas元素，分别对应四个饼图
- 使用网格布局（row-col-md-6）实现响应式显示
- 实现了`drawPieChart`函数用于绘制饼图

### 4. 饼图绘制逻辑
`drawPieChart`函数的主要功能：
- 计算数据总和
- 绘制饼图扇形区域，根据比例分配角度
- 为每个扇形添加标签和百分比
- 绘制图例，显示各平台名称和占比
- 处理无数据的情况

## 技术特点

1. **数据处理**：
   - 使用SQL聚合函数高效计算统计数据
   - 移除货币符号和特殊字符，确保数据准确性
   - 转换为合适的数据类型进行计算

2. **可视化**：
   - 使用Canvas API绘制饼图
   - 提供多种颜色区分不同平台
   - 显示平台名称、百分比和图例
   - 支持响应式布局

3. **性能优化**：
   - 限制数据范围为最近30天
   - 使用SQL索引优化查询性能
   - 前端使用高效的Canvas渲染

## 依赖关系

- 数据库表：`order_profit`、`store`、`costs`
- PHP模型：`OrderProfit`
- 前端技术：HTML5 Canvas

## 使用说明

1. 用户登录后自动访问dashboard页面
2. 页面将显示四个饼图，展示最近30天的数据统计
3. 每个饼图显示平台名称、占比百分比和图例
4. 当没有数据时，显示"暂无数据"提示

## 后续优化建议

1. 添加日期范围选择器，允许用户自定义统计周期
2. 增加悬停效果，显示详细的数值信息
3. 支持图表导出功能（PNG、PDF）
4. 优化大数据量下的渲染性能
5. 添加动画效果，提升用户体验