# cz.younger-car.com 项目

## 项目简介

这是一个汽车配件电商管理系统，包含订单利润管理、库存预警、ERP集成、数据统计等功能模块，旨在提高汽车配件电商的运营效率和管理水平。

## 主要模块

### 1. 管理后台 (admin-panel)
基于PHP的MVC架构实现的管理后台，包含以下功能：
- 订单利润管理与统计
- 库存预警与库龄统计
- 产品数据管理
- 广告费用管理
- 用户权限管理
- 数据仪表盘

### 2. eBay工具 (ebayfast)
提供eBay平台的辅助工具，用于快速显示销售数据，支持搜索页面、类目页面和产品页面。

### 3. 领星ERP集成 (lingxing.com & xlingxing)
- ERP API文档和示例
- 订单数据同步脚本
- 产品数据同步脚本
- 库存数据同步脚本

### 4. 运费工具
提供运费在线试算和订单审核辅助功能。

## 技术栈

### 后端
- PHP (原生MVC架构)
- Python (数据同步脚本)

### 前端
- HTML5, CSS3, JavaScript
- Bootstrap 5
- jQuery

### 数据库
- MySQL

### 集成
- 领星ERP API
- 钉钉通知

## 项目结构

```
cz.younger-car.com/
├── admin-panel/          # 管理后台
│   ├── controllers/     # 控制器
│   ├── models/          # 模型
│   ├── views/           # 视图
│   ├── public/          # 公共资源
│   ├── includes/        # 包含文件
│   ├── helpers/         # 辅助函数
│   ├── database/        # 数据库脚本
│   └── dingding/        # 钉钉通知
├── ebayfast/            # eBay辅助工具
├── lingxing.com/        # 领星ERP API文档和示例
├── xlingxing/           # ERP数据同步脚本
└── index.html           # 项目入口页面
```

## 常用入口

- **订单利润**: `admin-panel/order_profit.php`
- **利润统计**: `admin-panel/order_profit.php?action=stats`
- **库存预警**: `admin-panel/inventory_details.php?action=inventory_alert`
- **库龄统计**: `admin-panel/inventory_details.php?action=overaged_stats`
- **数据仪表盘**: `admin-panel/dashboard.php`

## 安装与配置

1. 配置Web服务器指向项目根目录
2. 导入数据库脚本 `admin-panel/database/init.sql`
3. 配置数据库连接 `admin-panel/includes/database.php`
4. 配置应用URL `admin-panel/includes/config.php`

## 默认账号

- **用户名**: admin
- **密码**: password

## 开发日志

开发日志存放在 `admin-panel/开发日志/` 目录下，记录项目的开发和维护历史。
