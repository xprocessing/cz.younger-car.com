# 2026-01-21 更新shop_costs统计方式

## 更新内容

### 1. 模型层更新 (TrackStatistics.php)

- **修改shop_costs统计方式**：
  - 原方式：通过关联store表，使用platform_name和store_name匹配来获取track_name
  - 新方式：直接使用shop_costs表中的track_name字段进行统计
  - 原因：shop_costs数据表已增加了track_name字段，用于对应store表中的track_name

- **具体更改**：
  - 更新`getTrackStatistics`方法中的shopCostSql查询
  - 移除了与store表的LEFT JOIN
  - 直接从shop_costs表中按track_name字段分组
  - 添加了track_name不为空的条件

### 2. 统计逻辑

- **费用统计**：
  - 直接从shop_costs表中按track_name分组统计total_shop_cost
  - 保留了货币转换逻辑：将人民币按汇率7转换为美元

- **净利润计算**：
  - 总净利润 = 总毛利润 - 总赛道费用cost - 赛道分摊公司成本 - 店铺费用
  - 净利润率 = 总净利润 / 总订单金额

### 3. 技术要点

- **数据关联**：直接使用shop_costs表中的track_name字段，避免了复杂的多表关联
- **性能优化**：减少了数据库查询的复杂度，提高了查询效率
- **数据一致性**：确保了shop_costs统计与其他赛道统计的逻辑一致性

### 4. 影响范围

- **统计结果**：店铺费用的统计方式更加直接和准确
- **性能**：查询性能得到优化
- **可维护性**：代码逻辑更加清晰，易于维护

## 测试建议

1. **数据验证**：确保shop_costs表中的track_name字段已正确填充
2. **统计验证**：验证店铺费用的统计结果是否与预期一致
3. **性能测试**：检查统计查询的执行时间是否有所改善
4. **集成测试**：确保整个赛道统计模块的功能正常