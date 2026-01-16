# 2026-01-16 修复AIGC模块路径和APP_ROOT重复定义问题

## 问题概述

修复了AIGC模块中出现的两个关键错误：

1. **APP_ROOT常量重复定义错误**：`Warning: Constant APP_ROOT already defined in /www/wwwroot/cz.younger-car.com/admin-panel/config/config.php on line 11`
2. **模型文件路径错误**：`Warning: require_once(/www/wwwroot/cz.younger-car.com/models/AIGC.php): Failed to open stream: No such file or directory`

## 修复内容

### 1. 修复APP_ROOT重复定义问题

**修改文件**：
- `admin-panel/aigc.php`
- `admin-panel/scripts/process_images_worker.php`

**修复方案**：
在定义APP_ROOT常量之前添加检查，确保只有在未定义时才会定义：

```php
if (!defined('APP_ROOT')) {
    define('APP_ROOT', realpath(dirname(__FILE__) . '/../'));
}
```

### 2. 修复模型文件路径错误

**修改文件**：
- `admin-panel/controllers/AIGCController.php`

**修复方案**：
修正了AIGC模型和其他辅助文件的路径：

```php
// 原代码
require_once APP_ROOT . '/models/AIGC.php';
require_once APP_ROOT . '/helpers/functions.php';
require_once APP_ROOT . '/includes/Logger.php';

// 修复后的代码
require_once APP_ROOT . '/admin-panel/models/AIGC.php';
require_once APP_ROOT . '/admin-panel/helpers/functions.php';
require_once APP_ROOT . '/admin-panel/includes/Logger.php';
```

## 验证结果

修复后，AIGC模块能够正常加载，不再出现以下错误：
- APP_ROOT常量重复定义警告
- 模型文件无法找到的错误

系统现在可以正常处理AI图片处理任务，包括批量去瑕疵、抠图、改尺寸、打水印、文生图和图生图等功能。

## 影响范围

此修复仅影响AIGC模块的功能，不会对系统其他模块产生影响。