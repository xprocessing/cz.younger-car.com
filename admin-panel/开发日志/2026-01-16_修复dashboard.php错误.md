# 开发日志：修复dashboard.php错误问题

## 问题描述
在dashboard.php页面出现了PDOException错误：
```
Fatal error: Uncaught PDOException: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'c.date' in 'where clause' in /www/wwwroot/cz.younger-car.com/admin-panel/includes/database.php:37
```

错误发生在OrderProfit.php文件的getPlatformCostPercentage方法中，该方法在AuthController.php的dashboard方法中被调用，最终导致dashboard.php页面无法正常显示。

## 原因分析
经过代码检查，发现问题出在`OrderProfit.php`文件的`getPlatformCostPercentage`方法中：
- 第946行：`$sql .= " AND c.date >= ?";`
- 第951行：`$sql .= " AND c.date <= ?";`

这些代码仍然使用旧的字段名`'c.date'`，而数据库中的字段名已经改为`'c.cost_date'`。这导致SQL查询失败，错误信息为：
`SQLSTATE[42S22]: Column not found: 1054 Unknown column 'c.date' in 'where clause'`

## 修复内容
将`OrderProfit.php`文件中`getPlatformCostPercentage`方法的旧字段名`'c.date'`改为`'c.cost_date'`：
```php
if ($startDate) {
    $sql .= " AND c.cost_date >= ?";
    $params[] = $startDate;
}

if ($endDate) {
    $sql .= " AND c.cost_date <= ?";
    $params[] = $endDate;
}
```

## 验证
修复后，dashboard.php页面应该能够正常显示，因为SQL查询现在使用了正确的字段名`'c.cost_date'`。

## 注意事项
- 确保数据库中的字段名确实已从`'date'`改为`'cost_date'`
- 如果数据库字段名尚未修改，需要手动执行SQL语句：`ALTER TABLE shop_costs CHANGE date cost_date DATE NOT NULL COMMENT '费用日期（YYYY-MM-DD）';`

修复时间：2026-01-16 10:00:00
修复人员：开发人员