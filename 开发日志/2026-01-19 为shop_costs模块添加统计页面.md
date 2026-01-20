# 2026-01-19 为shop_costs模块添加统计页面

## 更新内容

### 1. 新增统计功能描述
- **功能**：为shop_costs模块添加一个统计页面，展示上个月的费用统计数据
- **统计维度**：
  - 按店铺名称的费用金额统计
  - 按平台名称的费用金额统计
  - 包含总费用、明细列表和占比信息
  - 提供可视化图表展示

### 2. 技术实现

#### 2.1 模型层 (ShopCosts.php)
- **新增方法**：
  - `getLastMonthByStore()`：获取上个月按店铺名称的费用统计
  - `getLastMonthByPlatform()`：获取上个月按平台名称的费用统计
- **实现**：使用SQL查询获取上个月数据并按指定维度分组求和

#### 2.2 控制器层 (ShopCostsController.php)
- **新增方法**：`statistics()`
- **功能**：
  - 调用模型获取统计数据
  - 计算总费用和占比
  - 渲染统计页面

#### 2.3 视图层 (statistics.php)
- **页面结构**：
  - 顶部导航栏：包含标题和返回按钮
  - 统计内容区：分为两个主要部分
    - 按店铺名称统计（表格形式展示）
    - 按平台名称统计（表格形式展示）
  - 图表展示区：
    - 店铺费用占比饼图
    - 平台费用占比饼图
- **技术**：使用Chart.js绘制交互式图表，Bootstrap实现响应式布局

#### 2.4 路由配置 (shop_costs.php)
- **新增路由**：`statistics`，指向`ShopCostsController->statistics()`方法

#### 2.5 索引页面更新 (index.php)
- **新增按钮**：在页面顶部添加"费用统计"按钮，链接到统计页面

### 3. 关键代码示例

#### 3.1 模型层统计方法
```php
// 获取上个月按店铺名称的费用统计
public function getLastMonthByStore() {
    $sql = "SELECT store_name, SUM(cost) as total_cost FROM shop_costs 
            WHERE cost_date >= DATE_FORMAT(NOW() - INTERVAL 1 MONTH, '%Y-%m-01') 
            AND cost_date < DATE_FORMAT(NOW(), '%Y-%m-01') 
            GROUP BY store_name 
            ORDER BY total_cost DESC";
    $stmt = $this->db->query($sql);
    return $stmt->fetchAll();
}
```

#### 3.2 控制器层统计方法
```php// 显示统计页面
public function statistics() {
    if (!isLoggedIn()) {
        redirect(ADMIN_PANEL_URL . '/login.php');
    }
    
    // 获取上个月按店铺名称的费用统计
    $lastMonthByStore = $this->shopCostsModel->getLastMonthByStore();
    
    // 获取上个月按平台名称的费用统计
    $lastMonthByPlatform = $this->shopCostsModel->getLastMonthByPlatform();
    
    // 计算总费用
    $totalByStore = array_reduce($lastMonthByStore, function($sum, $item) {
        return $sum + $item['total_cost'];
    }, 0);
    
    $totalByPlatform = array_reduce($lastMonthByPlatform, function($sum, $item) {
        return $sum + $item['total_cost'];
    }, 0);
    
    $title = '广告费统计';
    
    include VIEWS_DIR . '/layouts/header.php';
    include VIEWS_DIR . '/shop_costs/statistics.php';
    include VIEWS_DIR . '/layouts/footer.php';
}
```

### 4. 功能特点

- **数据展示**：清晰展示上个月的费用统计数据，包括店铺和平台维度
- **可视化图表**：使用饼图直观展示费用占比
- **响应式设计**：适配不同屏幕尺寸
- **用户体验**：提供返回按钮，方便用户在列表和统计页面间切换
- **数据准确性**：自动计算总费用和占比，确保数据准确

### 5. 测试验证

- 进行了PHP语法检查，确保代码正确性
- 验证了统计数据的准确性和完整性
- 确保图表能够正常显示
- 测试了页面的响应式设计

## 总结

本次更新成功为shop_costs模块添加了统计页面，实现了按店铺名称和平台名称的费用统计功能。通过表格和图表的结合，提供了清晰直观的数据展示，帮助用户更好地了解上个月的费用分布情况。所有修改都经过了PHP语法检查，确保代码的正确性和稳定性。