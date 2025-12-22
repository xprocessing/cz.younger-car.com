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
├── config/          # 配置文件
├── controllers/     # 控制器
├── models/          # 模型
├── views/           # 视图
│   ├── auth/        # 认证相关视图
│   ├── users/       # 用户管理视图
│   ├── roles/       # 角色管理视图
│   ├── permissions/ # 权限管理视图
│   ├── data/        # 数据管理视图
│   └── layouts/     # 布局文件
├── public/          # 公共资源
│   ├── css/         # CSS 文件
│   └── js/          # JavaScript 文件
├── includes/        # 包含文件
├── middleware/      # 中间件
├── helpers/         # 辅助函数
└── database/        # 数据库脚本
```

## 功能特性

1. **用户认证**
   - 登录/登出
   - 密码重置
   - 会话管理

2. **角色管理**
   - 角色创建、编辑、删除
   - 角色权限分配

3. **权限管理**
   - 权限创建、编辑、删除
   - 权限分组

4. **数据管理**
   - 数据列表展示
   - 数据添加、编辑、删除
   - 数据搜索和过滤

5. **RBAC 权限控制**
   - 基于角色的访问控制
   - 细粒度权限管理

## 安装说明

1. 创建数据库
2. 导入 `database/init.sql` 脚本
3. 配置 `config/config.php` 文件
4. 将项目部署到 Web 服务器
5. 访问 `http://your-domain/admin-panel`

## 默认账号

- **用户名**: admin
- **密码**: password
