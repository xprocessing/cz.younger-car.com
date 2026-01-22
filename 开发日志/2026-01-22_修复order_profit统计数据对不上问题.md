# 2026-01-22 修复order_profit统计数据对不上问题

## 问题描述

- **问题位置**：order_profit/stats.php页面
- **问题现象**：统计数据对不上，具体表现为页面显示的是"最近30天利润统计"和"30天订单数量"，但实际统计的是最近60天的数据
- **问题原因**：控制器代码中设置的日期范围与视图显示的统计范围不一致

## 修复方案

### 1. 控制器层更新 (OrderProfitController.php)

- **修改日期范围设置**：
  - 原代码：只设置了一个60天的日期范围
  - 修改后：分别设置30天和60天的日期范围
- **具体更改**：
  ```php
  $endDate = date('Y-m-d');
  $startDate30 = date('Y-m-d', strtotime('-30 days'));
  $startDate60 = date('Y-m-d', strtotime('-60 days'));
  ```

- **更新方法调用**：
  - 对于30天统计的方法，使用`$startDate30`
  - 对于60天统计的方法，使用`$startDate60`
- **具体更改**：
  ```php
  $platformStats = $this->orderProfitModel->getPlatformStats($startDate30, $endDate);
  $storeStats = $this->orderProfitModel->getStoreStats($startDate30, $endDate);
  $brandStats = $this->orderProfitModel->getBrandStats($startDate30, $endDate);
  $skuStats = $this->orderProfitModel->getSkuStats($startDate30, $endDate);
  $stats = $this->orderProfitModel->getProfitStats($startDate30, $endDate, $storeId);
  $profitRateDistribution = $this->orderProfitModel->getProfitRateDistribution($startDate30, $endDate);
  $dailySalesStats = $this->orderProfitModel->getDailySalesStats($startDate60, $endDate);
  ```

### 2. 技术要点

- **数据一致性**：确保控制器中设置的统计范围与视图中显示的统计范围一致
- **代码清晰**：使用明确的变量名（如`startDate30`、`startDate60`）来区分不同的统计范围
- **功能分离**：将30天统计和60天统计的逻辑分开，确保各自的数据范围正确
- **用户体验**：提供准确的统计数据，避免用户对数据产生困惑

### 3. 影响范围

- **order_profit/stats.php**：所有统计数据现在与显示的统计范围一致
  - "最近30天利润统计"：现在确实统计的是最近30天的数据
  - "30天订单数量"：现在确实统计的是最近30天的订单数量
  - "最近60天每日销量统计"：现在确实统计的是最近60天的数据
- **数据准确性**：所有统计数据现在基于正确的日期范围计算
- **用户体验**：用户现在可以看到与显示范围一致的统计数据，避免了混淆

## 测试建议

1. **功能测试**：
   - 验证order_profit/stats.php页面中的所有统计数据是否与显示的统计范围一致
   - 确认"最近30天利润统计"的数据是否基于最近30天
   - 确认"最近60天每日销量统计"的数据是否基于最近60天

2. **数据验证**：
   - 手动计算最近30天的订单数量，与页面显示的"30天订单数量"进行对比
   - 手动计算最近60天的每日销量，与页面显示的"最近60天每日销量统计"图表进行对比

3. **边界测试**：
   - 测试当天的数据是否被正确统计
   - 测试日期范围的边界情况（如第30天、第60天的数据）

## 总结

本次修复通过以下步骤解决了统计数据对不上的问题：

1. **分别设置日期范围**：为30天和60天的统计分别设置了正确的日期范围
2. **更新方法调用**：确保每个统计方法都使用了正确的日期范围
3. **保持数据一致性**：确保控制器中设置的统计范围与视图中显示的统计范围一致

这样，用户现在可以在order_profit/stats.php页面上看到准确的统计数据，避免了因统计范围不一致导致的数据对不上的问题。