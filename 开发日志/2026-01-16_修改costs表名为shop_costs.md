# 开发日志：修改costs表名为shop_costs

## 修改时间
2026-01-16

## 修改内容
将数据库表名从 `costs` 修改为 `shop_costs`，并更新所有相关引用。

## 修改的文件

### 1. 数据库定义文件
**文件路径：** `database/init.sql`
- 修改表名定义：`CREATE TABLE IF NOT EXISTS costs (` → `CREATE TABLE IF NOT EXISTS shop_costs (`

### 2. 模型文件
**文件路径：** `models/Costs.php`
- 更新12处SQL查询中的表名引用，将 `costs` 改为 `shop_costs`
  - SELECT 查询（5处）
  - INSERT 查询（2处）
  - UPDATE 查询（1处）
  - DELETE 查询（1处）
  - 分页查询（2处）
  - 去重查询（1处）

**文件路径：** `models/OrderProfit.php`
- 更新1处SQL查询中的表名引用：`FROM costs c` → `FROM shop_costs c`

## 验证情况
使用正则表达式搜索 `(FROM|INTO|UPDATE|DELETE)\s+\bcosts\b` 确认没有遗漏任何直接的SQL表名引用。

## 注意事项
- 控制器和视图文件中的URL和文件名引用（如 `costs.php`、`views/costs/`）未做修改，因为这些是路由和视图目录名称，不是数据库表名。
- 开发日志和README.md中的文档引用未做修改，因为这些是文档内容，不影响系统运行。