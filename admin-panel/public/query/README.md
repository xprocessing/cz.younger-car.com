# 运费查询系统

这是CZ管理系统的公共运费查询功能，允许用户通过订单号查询运费信息。

## 文件结构

```
public/query/
├── index.php          # 主查询入口文件
├── controller.php      # 查询控制器
├── track.php          # 简化移动端查询页面
├── views/             # 视图文件目录
│   ├── header.php     # 页面头部模板
│   ├── footer.php     # 页面底部模板
│   ├── index.php      # 主查询页面
│   ├── result.php     # 查询结果页面
│   └── error.php     # 错误页面
└── README.md         # 说明文档
```

## 访问方式

### 1. 完整查询页面
- **URL**: `/public/query/` 或 `/public/query/index.php`
- **功能**: 完整的查询界面，支持传统表单提交和AJAX快速查询
- **特点**: 
  - 美观的渐变背景设计
  - 支持直接URL查询: `?order_no=订单号`
  - API接口: `?action=api&order_no=订单号`

### 2. 移动端优化页面
- **URL**: `/public/track.php`
- **功能**: 完全AJAX驱动的查询体验
- **特点**:
  - 移动端优化设计
  - 实时加载动画
  - 键盘快捷键支持

### 3. API接口
- **URL**: `/public/query/index.php?action=api&order_no=订单号`
- **方法**: GET
- **返回**: JSON格式数据

## 功能特性

### 查询功能
- ✅ 通过订单号查询运费信息
- ✅ JSON格式运费数据展示
- ✅ 错误处理和友好提示
- ✅ 支持直接URL查询

### 用户体验
- ✅ 响应式设计，支持各种设备
- ✅ 实时查询状态指示
- ✅ 订单号复制功能
- ✅ 打印功能支持
- ✅ 键盘快捷键支持

### 数据展示
- ✅ 智能格式化运费数据
- ✅ 货币金额格式化显示
- ✅ JSON数据美化展示
- ✅ 错误信息友好提示

## 安全特性

- 输入数据验证
- SQL注入防护
- XSS攻击防护
- 合理的错误处理

## 使用示例

### 直接查询
```
https://yourdomain.com/public/query/?order_no=ORDER123456
```

### API调用
```javascript
fetch('/public/query/?action=api&order_no=ORDER123456')
  .then(response => response.json())
  .then(data => {
    if (data.success) {
      console.log(data.data);
    } else {
      console.error(data.message);
    }
  });
```

## 自定义配置

### 样式定制
可以修改 `views/header.php` 中的CSS样式来自定义外观。

### 数据字段
运费数据以JSON格式存储，系统会自动解析并美化显示各种数据类型。

## 技术要求

- PHP 7.0+
- MySQL 5.7+
- 现代浏览器支持

## 部署注意事项

### 路径配置
确保以下路径正确：
- `../../config/config.php` - 数据库配置文件
- `../../models/Yunfei.php` - 运费数据模型
- `../../helpers/functions.php` - 辅助函数

### 常量定义
系统会自动定义以下常量：
- `APP_ROOT` - 应用根目录
- `APP_URL` - 应用URL
- 所有数据库配置常量

### 错误排查
如果遇到路径错误，请检查：
1. 文件权限设置
2. 相对路径是否正确
3. 数据库配置是否可用

## 更新日志

- **v1.0**: 基础查询功能
- **v1.1**: 添加移动端优化页面
- **v1.2**: 改进API接口，GET请求支持

---

如有问题请联系系统管理员。