# 2026-01-22 修复dashboard页面图表不显示问题

## 问题描述

- **问题位置**：dashboard.php页面
- **问题现象**：页面上的所有图表都没有显示
- **问题类型**：数据获取和处理问题

## 原因分析

经过检查，发现问题出在OrderProfit.php模型的getDailySalesStats方法中：

1. **字段名不匹配**：
   - 方法返回的字段名是`sale_date`和`order_count`
   - 但dashboard.php页面中使用的字段名是`date`和`total_sales`

2. **数据类型不匹配**：
   - 方法返回的是订单数量（order_count）
   - 但dashboard.php页面期望的是销售额（total_sales）

3. **SQL查询问题**：
   - 原SQL查询使用COUNT(*)统计订单数量
   - 没有计算总销售额

## 解决方案

修改OrderProfit.php模型中的getDailySalesStats方法：

1. **修改SQL查询**：
   - 将字段名从`sale_date`改为`date`
   - 将统计方式从`COUNT(*) as order_count`改为`SUM(CAST(REPLACE(REPLACE(REPLACE(order_total_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2))) as total_sales`
   - 这样就能正确计算每日总销售额

2. **保持参数和返回结构**：
   - 保持方法的参数不变，确保与调用处兼容
   - 保持返回结构为数组，确保与dashboard.php页面的数据处理逻辑兼容

## 修复代码

### 修改前：

```php
public function getDailySalesStats($last30DaysStart, $endDate) {
    $sql = "SELECT 
                DATE(global_purchase_time) as sale_date,
                COUNT(*) as order_count
            FROM order_profit
            WHERE global_purchase_time >= ? 
            AND global_purchase_time <= ?
            GROUP BY DATE(global_purchase_time)
            ORDER BY sale_date ASC";
    
    $stmt = $this->db->query($sql, [$last30DaysStart, $endDate]);
    return $stmt->fetchAll();
}
```

### 修改后：

```php
public function getDailySalesStats($last30DaysStart, $endDate) {
    $sql = "SELECT 
                DATE(global_purchase_time) as date,
                SUM(CAST(REPLACE(REPLACE(REPLACE(order_total_amount, '$', ''), ',', ''), '%', '') AS DECIMAL(10,2))) as total_sales
            FROM order_profit
            WHERE global_purchase_time >= ? 
            AND global_purchase_time <= ?
            GROUP BY DATE(global_purchase_time)
            ORDER BY date ASC";
    
    $stmt = $this->db->query($sql, [$last30DaysStart, $endDate]);
    return $stmt->fetchAll();
}
```

## 技术要点

1. **数据类型转换**：
   - 使用CAST和REPLACE函数处理order_total_amount字段，将其从字符串转换为数值类型
   - 确保正确计算总销售额

2. **字段名一致性**：
   - 确保模型方法返回的字段名与视图页面使用的字段名一致
   - 避免因字段名不匹配导致的数据获取失败

3. **SQL聚合函数**：
   - 使用SUM函数计算总销售额
   - 使用GROUP BY按日期分组
   - 使用ORDER BY确保数据按日期排序

## 影响范围

- **OrderProfit.php**：修改了getDailySalesStats方法的实现
- **dashboard.php**：
  - 最近60天每日销量统计图表现在可以正确显示
  - 其他依赖此方法的图表也可能受益

## 测试建议

1. **功能测试**：
   - 验证dashboard.php页面的所有图表是否都能正确显示
   - 确认最近60天每日销量统计图表的数据是否正确
   - 检查其他图表是否也能正常显示

2. **数据验证**：
   - 手动计算某几天的销售额，与图表显示的数据进行对比
   - 验证图表中的日期范围是否正确（最近60天）

3. **性能测试**：
   - 验证页面加载速度是否受到影响
   - 测试图表渲染是否流畅

## 总结

本次修复通过修改OrderProfit.php模型中的getDailySalesStats方法，解决了dashboard.php页面图表不显示的问题。具体修改包括：

1. **修正字段名**：将`sale_date`改为`date`，确保与dashboard.php页面中的字段名匹配
2. **修正数据类型**：将订单数量统计改为销售额统计，确保与dashboard.php页面中的数据处理逻辑兼容
3. **修正SQL查询**：使用SUM函数和类型转换计算每日总销售额

这样，dashboard.php页面的所有图表现在都应该能够正确显示了。