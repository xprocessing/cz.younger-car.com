# 开发日志：为shop_costs表增加cost_type字段

## 修改时间
2026-01-16

## 修改内容
为数据库表`shop_costs`增加`cost_type`字段，用于记录费用类型（如广告费用、平台租金、其他费用），并更新相关的模型、控制器和视图文件以支持该字段。

## 修改的文件

### 1. 数据库定义文件
**文件路径：** `admin-panel/database/init.sql`
- 为`shop_costs`表添加`cost_type`字段：
  ```sql
  cost_type VARCHAR(50) NOT NULL COMMENT '费用类型（如广告费用、平台租金、其他费用）'
  ```

### 2. 模型文件
**文件路径：** `admin-panel/models/Costs.php`
- 更新了所有CRUD方法，添加对`cost_type`字段的支持：
  - `create()`：INSERT语句添加`cost_type`字段
  - `update()`：UPDATE语句添加`cost_type`字段
  - `batchInsert()`：批量插入添加`cost_type`字段
  - `searchWithFilters()`：添加`cost_type`筛选条件
  - `getSearchWithFiltersCount()`：添加`cost_type`筛选条件
  - `getAllWithFilters()`：添加`cost_type`筛选条件
- 新增`getCostTypeList()`方法：获取费用类型列表用于筛选

### 3. 控制器文件
**文件路径：** `admin-panel/controllers/CostsController.php`
- 更新了所有调用模型方法的地方，传递`cost_type`参数：
  - `index()`：添加`cost_type`参数处理和视图传递
  - `createPost()`：添加`cost_type`字段验证和数据传递
  - `editPost()`：添加`cost_type`字段验证和数据传递
  - `search()`：添加`cost_type`参数处理
  - `processImportRow()`：更新CSV导入逻辑，支持`cost_type`字段
  - `export()`：更新导出逻辑，包含`cost_type`字段

### 4. 视图文件
**文件路径：** `admin-panel/views/costs/index.php`
- 添加`cost_type`筛选条件到搜索表单
- 表格中新增费用类型列
- 更新分页和导出链接，包含`cost_type`参数

**文件路径：** `admin-panel/views/costs/create.php`
- 表单中新增费用类型字段
- 更新字段说明和示例数据
- 添加前端验证

**文件路径：** `admin-panel/views/costs/edit.php`
- 表单中新增费用类型字段
- 更新记录概览表格，显示费用类型
- 更新字段说明
- 添加前端验证

**文件路径：** `admin-panel/views/costs/import.php`
- 更新CSV格式说明，添加费用类型字段
- 更新示例数据和模板文件
- 修改文件验证逻辑，支持6列数据

## 验证情况
- 模型方法已更新，支持`cost_type`字段的CRUD操作
- 控制器能够正确处理`cost_type`参数和验证
- 视图文件包含`cost_type`字段的显示和输入
- 导入导出功能支持`cost_type`字段

## 注意事项
- 已有数据记录的`cost_type`字段将为空，需要手动补充
- CSV导入时需要确保数据格式正确，包含费用类型字段
- 前端表单添加了必填验证，确保费用类型不为空