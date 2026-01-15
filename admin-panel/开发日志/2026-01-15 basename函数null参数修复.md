# 2026-01-15 basename函数null参数修复

## 修复内容

### 1. 问题描述

系统显示警告信息：
"Deprecated: basename(): Passing null to parameter #1 ($path) of type string is deprecated in /www/wwwroot/cz.younger-car.com/admin-panel/views/aigc/result.php on line 33"

### 2. 原因分析

在使用文生图功能时，`original_image`字段为null，但代码中直接调用了`basename($result['original_image'])`函数，导致PHP警告。

### 3. 修复位置

#### 3.1 views/aigc/result.php

**第33行**：修复卡片标题处的basename调用
```php
// 修复前
<?php echo basename($result['original_image']); ?>

// 修复后
<?php echo $result['original_image'] ? basename($result['original_image']) : '文生图'; ?>
```

**第50行**：修复原图显示处的basename调用（已添加条件判断）
```php
<?php if ($result['original_image']): ?>
    <img src="<?php echo APP_URL; ?>/public/temp/<?php echo basename($result['original_image']); ?>" ...>
<?php else: ?>
    <div class="text-center" ...>
        <span class="text-muted">文生图（无原图）</span>
    </div>
<?php endif; ?>
```

**第71行**：修复下载按钮处的basename调用
```php
// 修复前
download="processed_<?php echo basename($result['original_image']); ?>">

// 修复后
download="processed_<?php echo $result['original_image'] ? basename($result['original_image']) : 'text_to_image_' . date('YmdHis') . '_' . $index; ?>">
```

#### 3.2 controllers/AIGCController.php

**第293行**：修复保存任务结果时的basename调用
```php
// 修复前
basename($result['original_image']),

// 修复后
$result['original_image'] ? basename($result['original_image']) : 'text_to_image',
```

### 4. 修复效果

- 消除了basename()函数接收null参数的警告
- 为文生图提供了更好的用户体验
- 确保了系统的稳定性和兼容性

### 5. 测试建议

1. 测试文生图功能，确保不再显示警告
2. 测试其他图片处理功能，确保仍能正常工作
3. 检查数据库中的任务结果记录，确保文件名正确

## 总结

本次修复解决了basename()函数接收null参数的问题，通过添加null检查和默认值处理，确保了文生图功能的正常使用。修复涉及了结果页面显示和任务结果保存两个关键部分，提高了系统的稳定性和用户体验。