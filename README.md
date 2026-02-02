# cz.younger-car.com 项目

## 项目简介

这是一个汽车配件电商管理系统，包含订单利润管理、库存预警、ERP集成、数据统计、订单审核等功能模块，旨在提高汽车配件电商的运营效率和管理水平。

## 主要模块

### 1. 管理后台 (admin-panel)
基于PHP的MVC架构实现的管理后台，包含以下功能：

#### 核心功能
- **订单利润管理**: 订单利润明细展示、利润多维度统计分析、订单数据导入
- **库存管理**: 库存详情管理、库存预警、库龄统计分析、FBA库存管理
- **产品管理**: 产品信息管理、新产品开发进度跟踪、产品数据统计
- **订单审核**: 批量导入/导出、订单审核记录管理、运费试算对比
- **费用管理**: 广告费用管理、店铺费用管理、公司费用管理、物流费用管理
- **数据仪表盘**: 关键指标可视化、业务数据概览

#### 系统管理
- **用户管理**: 用户创建、编辑、删除、权限分配
- **角色管理**: 角色创建、编辑、删除、权限分配
- **权限管理**: 权限创建、编辑、删除、RBAC基于角色的访问控制
- **店铺管理**: 店铺信息管理
- **仓库管理**: 仓库信息管理
- **车型数据**: 车型数据库管理

#### 辅助功能
- **AI生成**: AI辅助内容生成
- **运费查询**: 运费在线查询和管理
- **数据查询**: 综合数据查询工具

#### 外部集成
- **领星ERP**: 订单数据同步、产品数据同步、库存数据同步
- **钉钉通知**: 库存预警、利润异常等通知提醒

### 2. eBay工具 (ebayfast)
提供eBay平台的辅助工具，用于快速显示销售数据，支持搜索页面、类目页面和产品页面。

#### 功能特性
- 查找页面中所有有属性 `data-item-id` 的span元素
- 在元素父级添加"点击查看"按钮，跳转到eBay购买历史页面
- 鼠标悬停按钮时在页面右侧预览页面内容

### 3. 批量审核订单系统 (review_order)
基于Python Tkinter开发的图形界面应用程序，用于自动化处理订单审核流程。

#### 核心功能
- 自动获取店铺列表、物流渠道列表、订单列表
- 根据订单SKU查询库存信息
- 智能匹配物流渠道（支持Amazon、eBay、Shopify平台）
- 自动计算中邮和运德运费
- 运德渠道代码分批查询（每批最多10个，间隔10秒）
- 自动选择最优运费方案
- 批量修改订单物流配置
- 实时日志输出和进度监控
- 结果统计和展示

#### 技术架构
- **GUI框架**: Python Tkinter + ttk
- **多线程**: threading模块（处理线程、日志线程、监控线程）
- **日志系统**: queue.Queue实现异步日志
- **外部接口**: review_order_func模块

### 4. 领星ERP集成 (xlingxing)
提供领星ERP的PHP SDK和数据同步脚本。

#### PHP SDK
- 基于OpenAPI的PHP SDK
- 支持AccessToken自动刷新
- 提供GET/POST请求封装
- 支持订单、产品、库存等数据接口

#### 数据同步脚本
- 订单数据同步
- 产品数据同步
- 库存数据同步
- 运费批量更新
- 批量审核订单

## 技术栈

### 后端
- **PHP**: 原生MVC架构
- **Python**: 数据同步脚本、GUI应用
- **MySQL**: 数据库

### 前端
- **HTML5, CSS3, JavaScript**
- **Bootstrap 5**: UI框架
- **jQuery**: JavaScript库
- **Font Awesome**: 图标库
- **Chart.js**: 图表库

### 集成
- **领星ERP API**: 订单、产品、库存数据同步
- **钉钉通知**: 消息推送
- **eBay API**: 销售数据获取

## 项目结构

```
cz.younger-car.com/
├── admin-panel/              # 管理后台
│   ├── controllers/         # 控制器
│   │   ├── AuthController.php
│   │   ├── OrderProfitController.php
│   │   ├── OrderReviewController.php
│   │   ├── InventoryDetailsController.php
│   │   ├── ProductsController.php
│   │   ├── UserController.php
│   │   └── ...
│   ├── models/              # 模型
│   │   ├── OrderProfit.php
│   │   ├── OrderReview.php
│   │   ├── InventoryDetails.php
│   │   ├── Product.php
│   │   ├── User.php
│   │   └── ...
│   ├── views/               # 视图
│   │   ├── auth/            # 认证视图
│   │   ├── order_profit/    # 订单利润视图
│   │   ├── order_review/    # 订单审核视图
│   │   ├── inventory_details/ # 库存视图
│   │   ├── products/        # 产品视图
│   │   ├── users/           # 用户视图
│   │   ├── roles/           # 角色视图
│   │   ├── permissions/     # 权限视图
│   │   ├── aigc/            # AI生成视图
│   │   └── layouts/         # 布局文件
│   ├── public/              # 公共资源
│   │   ├── css/             # CSS文件
│   │   ├── js/              # JavaScript文件
│   │   ├── fonts/           # 字体文件
│   │   └── temp/            # 临时文件
│   ├── includes/            # 包含文件
│   │   ├── database.php     # 数据库连接
│   │   ├── Logger.php       # 日志类
│   │   └── RedisCache.php   # Redis缓存
│   ├── helpers/             # 辅助函数
│   ├── database/            # 数据库脚本
│   │   ├── init.sql         # 数据库初始化
│   │   └── create_tables.php
│   ├── dingding/            # 钉钉通知
│   │   ├── ding_class.php
│   │   ├── ding_profit.php
│   │   └── ding_inventory.php
│   ├── scripts/             # 脚本文件
│   ├── 开发日志/            # 开发日志
│   └── *.php                # 入口文件
├── ebayfast/                # eBay辅助工具
│   ├── ebayfast.js
│   ├── ebayfastpre.*.js     # 各版本脚本
│   └── ebay快捷显示数据.md
├── review_order/            # 批量审核订单系统
│   ├── batch_review_orde_gui.py  # GUI主程序
│   ├── review_order_func.py      # 业务逻辑
│   ├── 开发日志/                 # 开发日志
│   └── *.json                    # 数据文件
├── xlingxing/               # 领星ERP集成
│   ├── php/                 # PHP SDK
│   │   ├── ak_openapi/      # SDK源码
│   │   ├── batchyunfei2db*.py  # 运费同步脚本
│   │   ├── get_inventoryDetails*.py  # 库存同步脚本
│   │   ├── batch_review_order.php   # 批量审核订单
│   │   └── ...
│   └── ...
├── index.html               # 项目入口页面
├── README.md                # 项目说明文档
└── .gitignore               # Git忽略文件
```

## 常用入口

### 管理后台入口
- **登录页面**: `admin-panel/login.php`
- **数据仪表盘**: `admin-panel/dashboard.php`
- **订单利润**: `admin-panel/order_profit.php`
- **利润统计**: `admin-panel/order_profit.php?action=stats`
- **订单审核**: `admin-panel/order_review.php`
- **库存预警**: `admin-panel/inventory_details.php?action=inventory_alert`
- **库龄统计**: `admin-panel/inventory_details.php?action=overaged_stats`
- **FBA库存**: `admin-panel/inventory_details_fba.php`
- **产品管理**: `admin-panel/products.php`
- **新产品**: `admin-panel/new_products.php`
- **广告费用**: `admin-panel/order_other_costs.php`
- **店铺费用**: `admin-panel/shop_costs.php`
- **公司费用**: `admin-panel/company_costs.php`
- **物流费用**: `admin-panel/track_costs.php`
- **运费管理**: `admin-panel/yunfei.php`
- **物流管理**: `admin-panel/logistics.php`
- **用户管理**: `admin-panel/users.php`
- **角色管理**: `admin-panel/roles.php`
- **权限管理**: `admin-panel/permissions.php`
- **店铺管理**: `admin-panel/store.php`
- **仓库管理**: `admin-panel/warehouses.php`
- **车型数据**: `admin-panel/car_data.php`
- **AI生成**: `admin-panel/aigc.php`
- **数据查询**: `admin-panel/query.php`

### 批量审核订单系统入口
- **GUI程序**: `review_order/batch_review_orde_gui.v2.py`

## 安装与配置

### 环境要求
- PHP 7.4+
- MySQL 5.7+
- Python 3.8+
- Composer
- Redis (可选，用于缓存)

### 管理后台安装
1. 配置Web服务器指向项目根目录
2. 导入数据库脚本 `admin-panel/database/init.sql`
3. 配置数据库连接 `admin-panel/includes/database.php`
4. 配置应用URL `admin-panel/includes/config.php`
5. 安装PHP依赖: `composer install`
6. 访问 `http://your-domain/admin-panel/login.php`

### 批量审核订单系统安装
1. 安装Python 3.8+
2. 安装依赖: `pip install requests tkinter`
3. 运行程序: `python review_order/batch_review_orde_gui.v2.py`

### 领星ERP集成配置
1. 复制SDK代码到项目目录
2. 在 `composer.json` 中添加自动加载配置
3. 执行 `composer dump-autoload`
4. 配置API密钥和域名

## 默认账号

- **用户名**: admin
- **密码**: password

## 开发日志

开发日志存放在 `admin-panel/开发日志/` 目录下，记录项目的开发和维护历史。

## 更新记录

### 2026-02-02
- 更新项目README.md，完善项目结构和功能说明
- 添加批量审核订单系统模块说明
- 添加领星ERP集成模块说明
- 完善技术栈和项目结构文档
