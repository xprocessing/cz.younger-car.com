# 修复getInventoryAlertBySkuList中的UNION查询错误开发日志

## 1. 错误描述
系统出现了`PDOException: SQLSTATE[21000]: Cardinality violation: 1222 The used SELECT statements have a different number of columns`错误，错误发生在`getInventoryAlertBySkuList()`方法的第422行附近。

## 2. 错误原因
在`getInventoryAlertBySkuList()`方法的主查询中，UNION ALL操作的两个SELECT语句列数不一致：

1. **从inventory_details获取数据的SELECT语句**：只选择了4个列（sku, wid, product_valid_num, product_onway）
2. **从order_profit获取数据的SELECT语句**：选择了5个列（local_sku, 0, 0, 0, 0）

此外，总记录数查询中的HAVING子句仍然使用了旧的字段名`product_onway`，而不是正确的`quantity_receive`。

## 3. 修复内容

### 3.1 模型层更新

#### InventoryDetails.php
1. **修复getInventoryAlertBySkuList()方法的主查询**
   - 在从inventory_details获取数据的SELECT语句中添加`quantity_receive`列
   - 确保两个SELECT语句都选择5个列，保持列数一致

2. **修复getInventoryAlertBySkuList()方法的总记录数查询**
   - 更新HAVING子句，将温州仓的条件从`product_onway`改为`quantity_receive`
   - 确保过滤条件与主查询保持一致

## 4. 技术要点

1. **UNION操作的严格要求**
   - UNION和UNION ALL操作对参与的SELECT语句有严格的列数要求
   - 即使添加的列只是为了填充（使用默认值），也必须确保列数一致
   - 修复时需要检查所有相关的UNION查询，包括总记录数查询和主查询

2. **逻辑一致性**
   - 确保过滤条件（如HAVING子句）与实际的字段名保持一致
   - 保持不同查询部分（如总记录数查询和主查询）之间的逻辑一致性
   - 避免在修复一个问题时引入新的逻辑错误

## 5. 实现效果

- 修复了`getInventoryAlertBySkuList()`方法中的UNION查询列数不匹配错误
- 确保了总记录数查询和主查询的过滤条件一致
- 库存预警功能的批量查询和模糊查询功能恢复正常

## 6. 文件变更列表

- `models/InventoryDetails.php`: 修复了getInventoryAlertBySkuList()方法中的UNION查询错误和HAVING子句