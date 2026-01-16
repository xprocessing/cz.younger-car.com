# 开发日志：修复ShopCostsController错误

## 修改时间
2026-01-16

## 错误信息
```
Warning: Undefined property: ShopCostsController::$costsModel in /www/wwwroot/cz.younger-car.com/admin-panel/controllers/ShopCostsController.php on line 33 

Fatal error: Uncaught Error: Call to a member function getAll() on null in /www/wwwroot/cz.younger-car.com/admin-panel/controllers/ShopCostsController.php:33 Stack trace: #0 /www/wwwroot/cz.younger-car.com/admin-panel/shop_costs.php(11): ShopCostsController->index() #1 {main} thrown in /www/wwwroot/cz.younger-car.com/admin-panel/controllers/ShopCostsController.php on line 33
```

## 错误原因
1. **模型变量名不匹配**：控制器中定义的模型变量是 `$shopCostsModel`，但在方法中使用的是 `$costsModel`
2. **视图目录路径错误**：视图文件路径仍然使用旧的 `/costs/` 目录，而实际上目录已重命名为 `/shop_costs/`
3. **导出方法字段名错误**：CSV导出时仍然使用旧的 `date` 字段名，而实际上数据库字段已改为 `cost_date`

## 修复内容

### 1. 修复模型变量名
**文件路径：** `admin-panel/controllers/ShopCostsController.php`
- **替换所有**：`$this->costsModel` → `$this->shopCostsModel`
- **涉及行数**：20行

### 2. 修复视图目录路径
**文件路径：** `admin-panel/controllers/ShopCostsController.php`
- **替换所有**：`/costs/` → `/shop_costs/`
- **涉及行数**：5行
- **具体文件**：index.php, create.php, edit.php, import.php

### 3. 修复导出方法字段名
**文件路径：** `admin-panel/controllers/ShopCostsController.php`
- **替换**：`$cost['date']` → `$cost['cost_date']`
- **位置**：导出方法中的CSV数据行

## 验证情况
- 控制器中的模型变量名已统一
- 视图文件路径已更新
- 导出功能使用了正确的字段名
- 所有引用ShopCostsController的地方已修复

## 注意事项
- 确保数据库中的字段名确实已从date改为cost_date
- 如果数据库字段名尚未修改，需要手动执行SQL语句：`ALTER TABLE shop_costs CHANGE date cost_date DATE NOT NULL COMMENT '费用日期（YYYY-MM-DD）';`