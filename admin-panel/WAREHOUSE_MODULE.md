# 仓库管理模块开发文档

## 概述
本文档记录了仓库管理（warehouses）模块的完整开发过程，包括增删改查功能的实现。

## 功能列表
- 仓库列表展示（支持分页和搜索）
- 创建新仓库
- 编辑仓库信息
- 删除仓库（软删除）
- 权限控制

## 数据库表结构

### warehouses 表
```sql
CREATE TABLE IF NOT EXISTS warehouses (
    wid INT PRIMARY KEY,
    type INT NOT NULL,
    sub_type INT NOT NULL,
    name VARCHAR(100) NOT NULL,
    is_delete INT NOT NULL,
    country_code VARCHAR(10),
    wp_id INT,
    wp_name VARCHAR(50),
    t_warehouse_name VARCHAR(50),
    t_warehouse_code VARCHAR(50),
    t_country_area_name VARCHAR(50),
    t_status VARCHAR(10)
);
```

## 新增文件

### 1. 模型文件
**文件路径**: `models/Warehouse.php`

**功能说明**:
- `getById($wid)` - 根据ID获取仓库信息
- `getAll($limit, $offset)` - 获取所有仓库（支持分页）
- `getCount()` - 获取仓库总数
- `create($data)` - 创建新仓库
- `update($wid, $data)` - 更新仓库信息
- `delete($wid)` - 软删除仓库（设置is_delete=1）
- `forceDelete($wid)` - 强制删除仓库
- `search($keyword, $limit, $offset)` - 搜索仓库
- `getSearchCount($keyword)` - 获取搜索结果总数

### 2. 控制器文件
**文件路径**: `controllers/WarehouseController.php`

**功能说明**:
- `index()` - 显示仓库列表页面
- `create()` - 显示创建仓库表单
- `createPost()` - 处理创建仓库请求
- `edit()` - 显示编辑仓库表单
- `editPost()` - 处理编辑仓库请求
- `delete()` - 删除仓库

**权限控制**:
- 列表查看: `warehouses.view`
- 创建仓库: `warehouses.create`
- 编辑仓库: `warehouses.edit`
- 删除仓库: `warehouses.delete`

### 3. 视图文件

#### 3.1 仓库列表视图
**文件路径**: `views/warehouses/index.php`

**功能**:
- 显示仓库列表表格
- 关键词搜索功能
- 分页导航
- 编辑和删除操作按钮

**表格字段**:
- 仓库ID (wid)
- 仓库名称 (name)
- 类型 (type)
- 子类型 (sub_type)
- 国家/地区编码 (country_code)
- WP ID (wp_id)
- WP名称 (wp_name)
- T仓库名称 (t_warehouse_name)
- T仓库代码 (t_warehouse_code)
- T国家/地区 (t_country_area_name)
- T状态 (t_status)
- 操作按钮

#### 3.2 创建仓库视图
**文件路径**: `views/warehouses/create.php`

**功能**:
- 创建仓库表单
- 表单验证
- 必填字段标记

**表单字段**:
- 仓库ID (必填)
- 仓库名称 (必填)
- 仓库类型 (必填)
- 仓库子类型 (必填)
- 国家/地区编码
- WP ID
- WP名称
- T仓库名称
- T仓库代码
- T国家/地区
- T状态
- 是否删除

#### 3.3 编辑仓库视图
**文件路径**: `views/warehouses/edit.php`

**功能**:
- 编辑仓库表单
- 预填充现有数据
- 仓库ID不可修改

### 4. 路由文件
**文件路径**: `warehouses.php`

**路由映射**:
- `index` - 仓库列表
- `create` - 创建表单
- `create_post` - 处理创建
- `edit` - 编辑表单
- `edit_post` - 处理编辑
- `delete` - 删除仓库
- `search` - 搜索功能

## 修改文件

### 1. 侧边栏菜单
**文件路径**: `views/layouts/header.php`

**修改内容**:
在店铺管理和运费管理之间添加仓库管理菜单项

```php
<?php if (hasPermission('warehouses.view')): ?>
<div class="nav-item">
    <a href="<?php echo APP_URL; ?>/warehouses.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'warehouses.php' ? 'active' : ''; ?>">
        <i class="fa fa-warehouse"></i> 仓库管理
    </a>
</div>
<?php endif; ?>
```

### 2. 数据库初始化文件
**文件路径**: `database/init.sql`

**修改内容**:
1. 添加仓库管理权限定义

```sql
-- 仓库管理权限
('查看仓库', 'warehouses.view', '可以查看仓库列表', 'warehouses'),
('创建仓库', 'warehouses.create', '可以创建新仓库', 'warehouses'),
('编辑仓库', 'warehouses.edit', '可以编辑现有仓库', 'warehouses'),
('删除仓库', 'warehouses.delete', '可以删除仓库', 'warehouses'),
```

2. 更新角色权限分配
- 为编辑角色（role_id=2）添加仓库管理权限
- 为查看者角色（role_id=3）添加仓库查看权限

### 3. 权限更新脚本
**文件路径**: `database/update_warehouse_permissions.sql`

**功能**:
为现有数据库添加仓库管理权限和角色权限分配

**使用方法**:
```bash
mysql -u username -p database_name < database/update_warehouse_permissions.sql
```

## 权限说明

### 权限列表
| 权限名称 | 权限代码 | 描述 | 模块 |
|---------|----------|------|------|
| 查看仓库 | warehouses.view | 可以查看仓库列表 | warehouses |
| 创建仓库 | warehouses.create | 可以创建新仓库 | warehouses |
| 编辑仓库 | warehouses.edit | 可以编辑现有仓库 | warehouses |
| 删除仓库 | warehouses.delete | 可以删除仓库 | warehouses |

### 角色权限分配
| 角色 | 查看仓库 | 创建仓库 | 编辑仓库 | 删除仓库 |
|------|---------|---------|---------|---------|
| 管理员 | ✓ | ✓ | ✓ | ✓ |
| 编辑 | ✓ | ✓ | ✓ | ✓ |
| 查看者 | ✓ | ✗ | ✗ | ✗ |

## 使用说明

### 访问仓库管理
登录系统后，在侧边栏点击"仓库管理"菜单项。

### 创建仓库
1. 点击"新增仓库"按钮
2. 填写仓库信息（带*为必填项）
3. 点击"保存"按钮

### 编辑仓库
1. 在仓库列表中找到要编辑的仓库
2. 点击"编辑"按钮
3. 修改仓库信息
4. 点击"保存"按钮

### 删除仓库
1. 在仓库列表中找到要删除的仓库
2. 点击"删除"按钮
3. 确认删除操作

### 搜索仓库
1. 在搜索框中输入关键词
2. 点击"搜索"按钮
3. 支持搜索字段：仓库名称、T仓库名称、T仓库代码

## 技术要点

### 1. 软删除
仓库删除采用软删除机制，通过设置`is_delete`字段为1来标记删除，而非物理删除。

### 2. 权限控制
所有操作都通过`hasPermission()`函数进行权限检查，确保只有具有相应权限的用户才能执行操作。

### 3. 分页
仓库列表支持分页功能，每页显示50条记录，使用智能分页算法。

### 4. 搜索
支持多字段模糊搜索，提高用户体验。

## 注意事项

1. 仓库ID（wid）是主键，必须唯一且不可修改
2. 所有必填字段在创建和编辑时都会进行验证
3. 删除操作需要二次确认
4. 国家/地区编码建议使用标准ISO代码（如CN、US、GB等）
5. T前缀字段表示第三方平台数据

## 后续优化建议

1. 添加批量导入功能
2. 添加仓库详情查看页面
3. 添加仓库使用统计
4. 优化搜索功能，支持更多筛选条件
5. 添加操作日志记录
6. 添加数据导出功能

## 版本历史

- v1.0.0 (2026-01-04)
  - 初始版本
  - 实现基本的增删改查功能
  - 实现权限控制
  - 实现分页和搜索功能
