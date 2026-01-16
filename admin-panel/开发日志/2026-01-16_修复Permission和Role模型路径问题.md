# 2026-01-16 修复Permission和Role模型路径问题

## 问题概述

修复了AIGC模块中出现的Permission和Role模型文件加载错误：

```
Warning: require_once(/www/wwwroot/cz.younger-car.com/models/Permission.php): Failed to open stream: No such file or directory in /www/wwwroot/cz.younger-car.com/admin-panel/helpers/functions.php on line 81
```

## 修复内容

**修改文件**：
- `admin-panel/helpers/functions.php`

**修复方案**：
修正了hasPermission和hasRole函数中Permission和Role模型的路径：

### 1. hasPermission函数修复

```php
// 原代码
require_once APP_ROOT . '/models/Permission.php';

// 修复后的代码
require_once APP_ROOT . '/admin-panel/models/Permission.php';
```

### 2. hasRole函数修复

```php
// 原代码
require_once APP_ROOT . '/models/Role.php';

// 修复后的代码
require_once APP_ROOT . '/admin-panel/models/Role.php';
```

## 验证结果

修复后，AIGC模块能够正常加载Permission和Role模型，不再出现路径错误。系统现在可以正常检查用户权限，并根据权限显示相应的导航菜单。

## 影响范围

此修复影响整个系统的权限检查功能，确保用户能够正常访问具有相应权限的页面和功能。