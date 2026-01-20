# 开发日志：修改date字段为cost_date

## 修改时间
2026-01-16

## 修改内容
将shop_costs表中的`date`字段重命名为`cost_date`，以避免与SQL关键字冲突，并使字段名称更具描述性。

## 修改的文件

### 1. 数据库定义
**文件路径：** `admin-panel/database/init.sql`
- **修改字段名：** `date` → `cost_date`
- **位置：** 第602行
- **保持其他属性不变：** 类型仍为DATE，非空约束，备注信息不变

### 2. 模型层
**文件路径：** `admin-panel/models/ShopCosts.php`
- **SQL查询更新：**
  - 所有SELECT语句中的ORDER BY date → ORDER BY cost_date
  - INSERT语句中的字段列表：date → cost_date
  - UPDATE语句中的SET子句：date = ? → cost_date = ?
  - WHERE子句中的条件：date >= ? → cost_date >= ?
  - WHERE子句中的条件：date <= ? → cost_date <= ?
  - 批量插入语句中的字段列表：date → cost_date
- **数组索引更新：**
  - $data['date'] → $data['cost_date']
  - $row['date'] → $row['cost_date']

### 3. 控制层
**文件路径：** `admin-panel/controllers/ShopCostsController.php`
- **表单验证更新：**
  - empty($_POST['date']) → empty($_POST['cost_date'])
- **数据处理更新：**
  - $_POST['date'] → $_POST['cost_date']
  - 'date' => $_POST['date'] → 'cost_date' => $_POST['cost_date']
  - 'date' => trim($row[4]) → 'cost_date' => trim($row[4])
- **模型调用更新：**
  - 修复了一处模型变量名错误：$this->costsModel → $this->shopCostsModel

### 4. 视图层

**index.php**
- **字段显示：** $cost['date'] → $cost['cost_date']

**create.php**
- **表单字段：**
  - id="date" → id="cost_date"
  - name="date" → name="cost_date"

**edit.php**
- **表单字段：**
  - id="date" → id="cost_date"
  - name="date" → name="cost_date"
  - value="<?php echo $cost['date']; ?>" → value="<?php echo $cost['cost_date']; ?>"
- **记录概览：** $cost['date'] → $cost['cost_date']

**import.php**
- **CSV格式说明：**
  - "日期" → "费用日期"
  - date → cost_date
- **CSV示例数据：**
  - 表头：日期 → 费用日期
- **下载模板函数：**
  - CSV内容中的"日期" → "费用日期"

## 验证情况
- 模型查询语句已全部更新
- 控制器表单处理已更新
- 视图显示和输入已更新
- CSV导入模板已更新
- 模型变量名错误已修复

## 注意事项
- 已有数据库中的字段需要手动重命名，因为init.sql只在表创建时执行
- 新的CSV导入模板使用"费用日期"作为字段名
- 保持了与其他日期字段（如开始日期、结束日期）的一致性