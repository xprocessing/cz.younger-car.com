# 2026-01-19 修复shop_costs和order_other_costs模块日期导入问题

## 更新内容

### 1. 问题描述
- **现象**：在导入店铺费用（shop_costs）和订单其他费用（order_other_costs）数据时，日期小于10的记录（如2024-12-9）无法成功导入
- **原因**：日期格式验证正则表达式要求月份和日期必须为两位数（如2024-12-09），导致单日日期无法通过验证

### 2. 修复方案

#### 2.1 ShopCostsController修复
- **文件**：`controllers/ShopCostsController.php`
- **方法**：`processImportRow()`

##### 2.1.1 修改正则表达式
- **原正则**：`/^\d{4}-\d{2}-\d{2}$/`（要求月份和日期必须为两位数）
- **新正则**：`/^\d{4}-\d{1,2}-\d{1,2}$/`（允许月份和日期为1-2位数）

##### 2.1.2 添加日期格式化功能
- 在日期验证通过后，将日期格式化为标准的YYYY-MM-DD格式
- 使用`str_pad()`函数确保月份和日期为两位数
- 示例：将"2024-12-9"转换为"2024-12-09"

#### 2.2 OrderOtherCostsController修复
- **文件**：`controllers/OrderOtherCostsController.php`
- **方法**：`processImportRow()`

##### 2.2.1 修改正则表达式
- **原正则**：`/^\d{4}-\d{2}-\d{2}$/`（要求月份和日期必须为两位数）
- **新正则**：`/^\d{4}-\d{1,2}-\d{1,2}$/`（允许月份和日期为1-2位数）

##### 2.2.2 添加日期格式化功能
- 与ShopCostsController相同，添加日期格式化功能

### 3. 技术细节

#### 3.1 ShopCostsController修复代码
```php
// 验证日期格式
if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $row[4])) {
    $errors[] = "第 {$rowCount} 行：日期格式不正确，应为YYYY-MM-DD";
    $errorCount++;
    return;
}

// 将日期格式化为标准的YYYY-MM-DD格式（确保月份和日期为两位数）
$dateParts = explode('-', $row[4]);
if (count($dateParts) === 3) {
    $year = $dateParts[0];
    $month = str_pad($dateParts[1], 2, '0', STR_PAD_LEFT);
    $day = str_pad($dateParts[2], 2, '0', STR_PAD_LEFT);
    $row[4] = "{$year}-{$month}-{$day}";
}
```

#### 3.2 OrderOtherCostsController修复代码
```php
// 验证日期格式
if (!preg_match('/^\d{4}-\d{1,2}-\d{1,2}$/', $row[0])) {
    $errors[] = "第 {$rowCount} 行：日期格式不正确，应为YYYY-MM-DD";
    $errorCount++;
    return;
}

// 将日期格式化为标准的YYYY-MM-DD格式（确保月份和日期为两位数）
$dateParts = explode('-', $row[0]);
if (count($dateParts) === 3) {
    $year = $dateParts[0];
    $month = str_pad($dateParts[1], 2, '0', STR_PAD_LEFT);
    $day = str_pad($dateParts[2], 2, '0', STR_PAD_LEFT);
    $row[0] = "{$year}-{$month}-{$day}";
}
```

### 4. 修复效果

- **兼容性提升**：支持更多格式的日期输入，包括单日日期
- **用户体验**：减少了因日期格式问题导致的导入失败
- **数据一致性**：确保数据库中日期格式的统一性
- **问题解决**：包含单日日期的记录现在可以成功导入

### 5. 测试验证

- 进行了PHP语法检查，确保代码正确性
- 验证了修复后的导入功能可以处理单日日期
- 保持了原有的导入逻辑和验证功能不变

## 总结

本次更新成功修复了shop_costs和order_other_costs模块中单日日期无法导入的问题。通过修改日期验证正则表达式和添加日期格式化功能，提高了导入功能的兼容性和用户体验。所有修改都经过了PHP语法检查，确保代码的正确性。