# 2026-01-16 修复AuthController路径和APP_ROOT重复定义问题

## 问题概述

修复了AIGC模块中出现的两个问题：

1. **APP_ROOT常量重复定义警告**：
   ```
   Warning: Constant APP_ROOT already defined in /www/wwwroot/cz.younger-car.com/admin-panel/config/config.php on line 11
   ```

2. **User模型文件路径错误**：
   ```
   Warning: require_once(/www/wwwroot/cz.younger-car.com/models/User.php): Failed to open stream: No such file or directory in /www/wwwroot/cz.younger-car.com/admin-panel/controllers/AuthController.php on line 2
   ```

## 修复内容

### 1. 修复AuthController.php中的路径

**修改文件**：`admin-panel/controllers/AuthController.php`

修正了User模型和helpers/functions.php的路径：

```php
// 原代码
require_once APP_ROOT . '/models/User.php';
require_once APP_ROOT . '/helpers/functions.php';

// 修复后的代码
require_once APP_ROOT . '/admin-panel/models/User.php';
require_once APP_ROOT . '/admin-panel/helpers/functions.php';
```

### 2. 修复dashboard.php中的APP_ROOT重复定义

**修改文件**：`admin-panel/dashboard.php`

移除了dashboard.php中重复的APP_ROOT定义，直接使用__DIR__包含配置文件：

```php
// 原代码
// 设置APP_ROOT为网站根目录
define('APP_ROOT', realpath(__DIR__ . '/..'));

// 包含配置文件和控制器
require_once APP_ROOT . '/admin-panel/config/config.php';
require_once APP_ROOT . '/admin-panel/controllers/AuthController.php';

// 修复后的代码
// 包含配置文件和控制器
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/AuthController.php';
```

## 修复结果

通过这两个修复，解决了以下问题：

1. **APP_ROOT重复定义警告**：移除了dashboard.php中重复的定义，避免了警告
2. **User模型文件路径错误**：AuthController.php现在可以正确找到User模型文件

系统现在应该能够正常处理登录和仪表盘页面请求，并且不会出现路径相关的错误。