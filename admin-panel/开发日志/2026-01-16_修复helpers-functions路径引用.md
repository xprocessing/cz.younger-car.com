# 2026-01-16 修复helpers/functions.php路径引用

## 问题概述

修复了系统中出现的helpers/functions.php路径错误，导致系统出现以下错误：

```
Warning: require_once(/www/wwwroot/cz.younger-car.com/helpers/functions.php): Failed to open stream: No such file or directory in /www/wwwroot/cz.younger-car.com/admin-panel/models/OrderProfit.php on line 3
```

## 核心问题

模型文件中使用了错误的路径引用，将helpers/functions.php文件的路径指向了项目根目录下的helpers目录，而实际上该文件位于admin-panel/helpers目录下。

## 修复内容

### 1. 更新模型文件中的路径引用

**修改了2个模型文件**，包括：
- OrderProfit.php
- Products.php

**修改内容**：将所有使用`APP_ROOT`引用helpers目录的代码改为使用`ADMIN_PANEL_DIR`，例如：

```php
// 原代码
require_once APP_ROOT . '/helpers/functions.php';

// 修改后
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';
```

## 验证结果

✅ **已修复所有模型文件**：确保所有模型文件都能正确引用helpers/functions.php文件
✅ **解决了路径错误**：不再出现"Failed to open stream"的错误
✅ **保持了代码一致性**：所有模型文件现在使用相同的路径引用方式

## 检查范围

- 检查了admin-panel/models目录下的所有模型文件
- 验证了helpers/functions.php的所有引用都使用正确的路径
- 确保没有遗漏任何引用错误

## 后续建议

1. 在添加新的文件引用时，确保使用正确的路径常量（ADMIN_PANEL_DIR而不是APP_ROOT）
2. 定期检查代码中的路径引用，确保它们与项目结构一致
3. 考虑使用自动加载机制来避免手动路径引用问题