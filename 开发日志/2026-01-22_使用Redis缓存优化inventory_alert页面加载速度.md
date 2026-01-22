# 使用Redis缓存优化inventory_alert页面加载速度

## 优化目标
- 解决inventory_alert.php页面打开慢的问题
- 使用Redis缓存提升页面加载速度
- 设置缓存过期时间为1天

## 技术方案
1. **引入Redis缓存**：在InventoryDetailsController.php中引入RedisCache类
2. **缓存策略设计**：
   - 缓存前缀：`inventory_alert:2026-01-22`（包含日期）
   - 缓存过期时间：86400秒（1天）
   - 缓存键设计：
     - 分页数据：`inventory_alert:2026-01-22:list:{page}:{limit}`
     - 总记录数：`inventory_alert:2026-01-22:total_count`
     - 统计数据：`inventory_alert:2026-01-22:stats`
3. **缓存范围**：
   - 对GET请求查询所有数据的情况进行缓存
   - 批量查询（POST请求）的情况不缓存

## 代码修改

### 1. 引入RedisCache类
**文件**：`admin-panel/controllers/InventoryDetailsController.php`
**修改**：在文件开头添加RedisCache.php的引入
```php
require_once ADMIN_PANEL_DIR . '/includes/RedisCache.php';
```

### 2. 添加Redis缓存初始化
**文件**：`admin-panel/controllers/InventoryDetailsController.php`
**修改**：在inventoryAlert方法中添加Redis缓存初始化代码
```php
// 初始化Redis缓存
$redisCache = RedisCache::getInstance();
$cachePrefix = 'inventory_alert:' . date('Y-m-d');
$cacheExpire = 86400; // 1天缓存
```

### 3. 为GET请求添加缓存逻辑
**文件**：`admin-panel/controllers/InventoryDetailsController.php`
**修改**：在GET请求处理部分添加缓存逻辑
```php
// 生成缓存键
$cacheKey = $cachePrefix . ':list:' . $page . ':' . $limit;
$totalCountCacheKey = $cachePrefix . ':total_count';

// 尝试从缓存获取数据
$cachedData = $redisCache->get($cacheKey);
$totalCount = $redisCache->get($totalCountCacheKey);

if ($cachedData && $totalCount) {
    $inventoryAlerts = $cachedData;
} else {
    // 查询所有数据
    $totalCount = 0;
    $inventoryAlerts = $this->inventoryDetailsModel->getInventoryAlert($limit, $offset, $totalCount);
    
    // 缓存数据
    $redisCache->set($cacheKey, $inventoryAlerts, $cacheExpire);
    $redisCache->set($totalCountCacheKey, $totalCount, $cacheExpire);
}
```

### 4. 为统计数据添加缓存逻辑
**文件**：`admin-panel/controllers/InventoryDetailsController.php`
**修改**：在统计数据处理部分添加缓存逻辑
```php
// 生成统计数据缓存键
$statsCacheKey = $cachePrefix . ':stats';

// 尝试从缓存获取统计数据
$cachedStats = $redisCache->get($statsCacheKey);

if ($cachedStats) {
    $totalStats = $cachedStats;
} else {
    // 否则获取所有库存预警数据的统计信息
    $allInventoryAlerts = $this->inventoryDetailsModel->getInventoryAlert();
    
    // 计算所有数据的总和
    $totalStats['product_valid_num_excluding_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_valid_num_excluding_wenzhou'));
    $totalStats['product_onway_excluding_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_onway_excluding_wenzhou'));
    $totalStats['product_valid_num_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_valid_num_wenzhou'));
    $totalStats['quantity_receive_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'quantity_receive_wenzhou'));
    $totalStats['outbound_30days'] = array_sum(array_column($allInventoryAlerts, 'outbound_30days'));
    $totalStats['sku_count'] = count($allInventoryAlerts);
    
    // 缓存统计数据
    $redisCache->set($statsCacheKey, $totalStats, $cacheExpire);
}
```

## 预期效果
- **页面加载速度**：从慢加载变为秒开
- **缓存有效期**：1天，每天自动更新缓存
- **兼容性**：批量查询功能不受影响
- **可靠性**：Redis连接失败时自动降级为直接查询数据库

## 注意事项
1. 缓存键包含日期前缀，确保每天自动生成新的缓存
2. 批量查询的数据不缓存，因为每次查询的SKU列表可能不同
3. 缓存过期时间设置为1天，平衡了实时性和性能需求
4. 使用RedisCache类的单例模式，确保Redis连接的高效管理

## 验证方法
1. 打开inventory_alert.php页面，观察加载速度
2. 检查Redis缓存是否生效（可通过Redis命令查看缓存键）
3. 验证批量查询功能是否正常工作
4. 确认页面数据是否正确显示