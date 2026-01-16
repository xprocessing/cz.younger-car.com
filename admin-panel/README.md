# PHP 后端管理平台

## 技术栈

- **后端框架**: 原生 PHP (无框架)
- **数据库**: MySQL
- **前端**: HTML5, CSS3, JavaScript, Bootstrap 5
- **认证**: 基于会话的认证
- **权限管理**: RBAC (角色基于访问控制)

## 项目结构

```
admin-panel/
├── controllers/     # 控制器
├── models/          # 模型
├── views/           # 视图
│   ├── auth/        # 认证相关视图
│   ├── users/       # 用户管理视图
│   ├── roles/       # 角色管理视图
│   ├── permissions/ # 权限管理视图
│   ├── order_profit/ # 订单利润视图
│   ├── inventory_details/ # 库存详情视图
│   ├── products/    # 产品管理视图
│   ├── costs/       # 广告费用视图
│   ├── car_data/    # 车型数据视图
│   ├── new_products/ # 新产品视图
│   ├── store/       # 店铺管理视图
│   ├── warehouses/  # 仓库管理视图
│   ├── yunfei/      # 运费管理视图
│   ├── layouts/     # 布局文件
│   └── aigc/        # AI生成视图
├── public/          # 公共资源
│   ├── css/         # CSS 文件
│   ├── js/          # JavaScript 文件
│   ├── fonts/       # 字体文件
│   └── temp/        # 临时文件
├── includes/        # 包含文件
├── helpers/         # 辅助函数
├── database/        # 数据库脚本
├── dingding/        # 钉钉通知
├── scripts/         # 脚本文件
└── 开发日志/        # 开发日志
```

## 功能特性

1. **用户认证**
   - 登录/登出
   - 会话管理

2. **角色与权限管理**
   - 角色创建、编辑、删除
   - 角色权限分配
   - 权限创建、编辑、删除
   - RBAC 基于角色的访问控制

3. **订单利润管理**
   - 订单利润明细展示
   - 利润多维度统计分析
   - 订单数据导入

4. **库存管理**
   - 库存详情管理
   - 库存预警
   - 库龄统计分析
   - FBA库存管理

5. **产品管理**
   - 产品信息管理
   - 新产品开发进度跟踪
   - 产品数据统计

6. **广告费用管理**
   - 广告费用导入
   - 费用统计分析

7. **数据仪表盘**
   - 关键指标可视化
   - 业务数据概览

8. **外部集成**
   - 领星ERP数据同步
   - 钉钉通知提醒

9. **辅助功能**
   - 车型数据库
   - 运费管理
   - 店铺管理
   - 仓库管理
   - AI生成功能

## 安装说明

1. 创建MySQL数据库
2. 导入 `database/init.sql` 脚本
3. 配置 `includes/database.php` 数据库连接信息
4. 将项目部署到 Web 服务器
5. 访问 `http://your-domain/admin-panel/login.php`

## 默认账号

- **用户名**: admin
- **密码**: password
