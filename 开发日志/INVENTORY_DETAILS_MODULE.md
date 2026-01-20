# 库存明细模块开发文档

## 概述
本文档记录了库存明细（inventory_details）模块的完整开发过程，包括增删改查功能的实现。

## 功能列表
- 库存明细列表展示（支持分页和搜索）
- 创建新库存明细
- 编辑库存明细信息
- 删除库存明细
- 权限控制
- 按仓库筛选

## 数据库表结构

### inventory_details 表
```sql
CREATE TABLE `inventory_details` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键自增ID',
  `wid` INT NOT NULL COMMENT '仓库id',
  `product_id` INT NOT NULL COMMENT '本地产品id',
  `sku` VARCHAR(50) NOT NULL COMMENT '产品SKU编码',
  `seller_id` VARCHAR(30) DEFAULT '' COMMENT '店铺id',
  `fnsku` VARCHAR(50) DEFAULT '' COMMENT 'FNSKU编码',
  `product_total` INT NOT NULL DEFAULT 0 COMMENT '实际库存总量【可用+次品+待检+锁定】',
  `product_valid_num` INT NOT NULL DEFAULT 0 COMMENT '可用量',
  `product_bad_num` INT NOT NULL DEFAULT 0 COMMENT '次品量',
  `product_qc_num` INT NOT NULL DEFAULT 0 COMMENT '待检待上架量',
  `product_lock_num` INT NOT NULL DEFAULT 0 COMMENT '锁定量',
  `good_lock_num` INT NOT NULL DEFAULT 0 COMMENT '良品锁定量',
  `bad_lock_num` INT NOT NULL DEFAULT 0 COMMENT '次品锁定量',
  `stock_cost_total` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '库存成本总计',
  `quantity_receive` VARCHAR(20) DEFAULT '0' COMMENT '待到货量',
  `stock_cost` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '单位库存成本',
  `product_onway` INT NOT NULL DEFAULT 0 COMMENT '调拨在途数量',
  `transit_head_cost` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '调拨在途头程成本',
  `average_age` INT NOT NULL DEFAULT 0 COMMENT '平均库龄(天)',
  `third_inventory` JSON NOT NULL COMMENT '海外仓第三方库存信息【完整嵌套对象，原生JSON格式】',
  `stock_age_list` JSON NOT NULL COMMENT '库龄信息列表【完整数组结构，原生JSON格式】',
  `available_inventory_box_qty` DECIMAL(10,1) DEFAULT 0.0 COMMENT '可用箱装库存数量',
  `purchase_price` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '采购单价',
  `price` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '单位费用',
  `head_stock_price` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '单位头程费用',
  `stock_price` DECIMAL(12,4) NOT NULL DEFAULT 0.0000 COMMENT '单位库存成本',
  PRIMARY KEY (`id`),
  KEY `idx_sku_wid` (`sku`,`wid`) USING BTREE COMMENT 'SKU+仓库ID联合索引，提升业务查询效率'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='库存详情表-原生JSON格式存储，适配MySQL5.7.40，无语法错误';
```

## 新增文件

### 1. 模型文件
**文件路径**: `models/InventoryDetails.php`

**功能说明**:
- `getById($id)` - 根据ID获取库存明细
- `getAll($limit, $offset)` - 获取所有库存明细（支持分页）
- `getCount()` - 获取库存明细总数
- `create($data)` - 创建新库存明细
- `update($id, $data)` - 更新库存明细信息
- `delete($id)` - 删除库存明细
- `search($keyword, $limit, $offset)` - 搜索库存明细
- `getSearchCount($keyword)` - 获取搜索结果总数
- `getByWid($wid, $limit, $offset)` - 根据仓库ID获取库存明细
- `getCountByWid($wid)` - 获取指定仓库的库存明细数量

### 2. 控制器文件
**文件路径**: `controllers/InventoryDetailsController.php`

**功能说明**:
- `index()` - 显示库存明细列表页面
- `create()` - 显示创建库存明细表单
- `createPost()` - 处理创建库存明细请求
- `edit()` - 显示编辑库存明细表单
- `editPost()` - 处理编辑库存明细请求
- `delete()` - 删除库存明细

**权限控制**:
- 列表查看: `inventory_details.view`
- 创建库存明细: `inventory_details.create`
- 编辑库存明细: `inventory_details.edit`
- 删除库存明细: `inventory_details.delete`

### 3. 视图文件

#### 3.1 库存明细列表视图
**文件路径**: `views/inventory_details/index.php`

**功能**:
- 显示库存明细列表表格
- SKU关键词搜索功能
- 仓库ID筛选功能
- 分页导航
- 编辑和删除操作按钮

**表格字段**:
- ID (id)
- 仓库ID (wid)
- SKU (sku)
- 可用量 (product_valid_num)
- 待到货量 (quantity_receive)
- 平均库龄(天) (average_age)
- 采购单价 (purchase_price)
- 单位头程费用 (head_stock_price)
- 单位库存成本 (stock_price)
- 操作按钮

#### 3.2 创建库存明细视图
**文件路径**: `views/inventory_details/create.php`

**功能**:
- 创建库存明细表单
- 表单验证
- 必填字段标记

**表单字段**:
- 仓库ID (必填)
- SKU (必填)
- 可用量 (必填)
- 待到货量
- 平均库龄(天)
- 采购单价
- 单位头程费用
- 单位库存成本

#### 3.3 编辑库存明细视图
**文件路径**: `views/inventory_details/edit.php`

**功能**:
- 编辑库存明细表单
- 预填充现有数据
- ID不可修改

### 4. 路由文件
**文件路径**: `inventory_details.php`

**路由映射**:
- `index` - 库存明细列表
- `create` - 创建表单
- `create_post` - 处理创建
- `edit` - 编辑表单
- `edit_post` - 处理编辑
- `delete` - 删除库存明细
- `search` - 搜索功能

## 修改文件

### 1. 侧边栏菜单
**文件路径**: `views/layouts/header.php`

**修改内容**:
在仓库管理和运费管理之间添加库存明细菜单项

```php
<?php if (hasPermission('inventory_details.view')): ?>
<div class="nav-item">
    <a href="<?php echo APP_URL; ?>/inventory_details.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'inventory_details.php' ? 'active' : ''; ?>">
        <i class="fa fa-list"></i> 库存明细
    </a>
</div>
<?php endif; ?>
```

### 2. 数据库初始化文件
**文件路径**: `database/init.sql`

**修改内容**:
1. 添加库存明细权限定义

```sql
-- 库存明细权限
('查看库存明细', 'inventory_details.view', '可以查看库存明细列表', 'inventory_details'),
('创建库存明细', 'inventory_details.create', '可以创建库存明细', 'inventory_details'),
('编辑库存明细', 'inventory_details.edit', '可以编辑库存明细', 'inventory_details'),
('删除库存明细', 'inventory_details.delete', '可以删除库存明细', 'inventory_details'),
```

2. 更新角色权限分配
- 为编辑角色（role_id=2）添加库存明细权限
- 为查看者角色（role_id=3）添加库存明细查看权限

### 3. 权限更新脚本
**文件路径**: `database/update_inventory_details_permissions.sql`

**功能**:
为现有数据库添加库存明细权限和角色权限分配

**使用方法**:
```bash
mysql -u username -p database_name < database/update_inventory_details_permissions.sql
```

## 权限说明

### 权限列表
| 权限名称 | 权限代码 | 描述 | 模块 |
|---------|----------|------|------|
| 查看库存明细 | inventory_details.view | 可以查看库存明细列表 | inventory_details |
| 创建库存明细 | inventory_details.create | 可以创建库存明细 | inventory_details |
| 编辑库存明细 | inventory_details.edit | 可以编辑库存明细 | inventory_details |
| 删除库存明细 | inventory_details.delete | 可以删除库存明细 | inventory_details |

### 角色权限分配
| 角色 | 查看库存明细 | 创建库存明细 | 编辑库存明细 | 删除库存明细 |
|------|-------------|-------------|-------------|-------------|
| 管理员 | ✓ | ✓ | ✓ | ✓ |
| 编辑 | ✓ | ✓ | ✓ | ✓ |
| 查看者 | ✓ | ✗ | ✗ | ✗ |

## 使用说明

### 访问库存明细
登录系统后，在侧边栏点击"库存明细"菜单项。

### 创建库存明细
1. 点击"新增库存明细"按钮
2. 填写库存明细信息（带*为必填项）
3. 点击"保存"按钮

### 编辑库存明细
1. 在库存明细列表中找到要编辑的记录
2. 点击"编辑"按钮
3. 修改库存明细信息
4. 点击"保存"按钮

### 删除库存明细
1. 在库存明细列表中找到要删除的记录
2. 点击"删除"按钮
3. 确认删除操作

### 搜索库存明细
1. 在搜索框中输入SKU关键词
2. 点击"搜索"按钮
3. 支持搜索字段：SKU

### 按仓库筛选
1. 在仓库筛选框中输入仓库ID
2. 点击"搜索"按钮
3. 系统将显示该仓库下的所有库存明细

## 技术要点

### 1. 物理删除
库存明细删除采用物理删除机制，直接从数据库中删除记录。

### 2. 权限控制
所有操作都通过`hasPermission()`函数进行权限检查，确保只有具有相应权限的用户才能执行操作。

### 3. 分页
库存明细列表支持分页功能，每页显示50条记录，使用智能分页算法。

### 4. 搜索和筛选
支持SKU关键词搜索和仓库ID筛选，提高用户体验。

### 5. 数据类型
- 金额字段使用DECIMAL(12,4)类型，支持4位小数
- 数量字段使用INT类型
- 字符串字段使用VARCHAR类型

## 注意事项

1. ID是主键，创建后不可修改
2. 所有必填字段在创建和编辑时都会进行验证
3. 删除操作需要二次确认
4. 仓库ID必须存在于仓库表中
5. SKU是产品的唯一标识
6. 金额字段支持4位小数，确保精度
7. 库龄单位为天

## 后续优化建议

1. 添加批量导入功能
2. 添加库存明细详情查看页面
3. 添加库存变动历史记录
4. 优化搜索功能，支持更多筛选条件
5. 添加操作日志记录
6. 添加数据导出功能
7. 添加库存预警功能
8. 添加库存盘点功能

## 版本历史

- v1.0.0 (2026-01-04)
  - 初始版本
  - 实现基本的增删改查功能
  - 实现权限控制
  - 实现分页和搜索功能
  - 实现仓库筛选功能
