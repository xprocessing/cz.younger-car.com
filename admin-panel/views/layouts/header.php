<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? APP_NAME; ?></title>
    <link href="<?php echo ADMIN_PANEL_URL; ?>/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ADMIN_PANEL_URL; ?>/public/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }

        .sidebar {
            height: 100vh;
            background-color: #343a40;
            color: white;
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            overflow-y: auto;
        }

        .sidebar a {
            color: #adb5bd;
            text-decoration: none;
            display: block;
            padding: 10px 15px;
        }

        .sidebar a:hover {
            background-color: #495057;
            color: white;
        }

        .sidebar .active {
            background-color: #0d6efd;
            color: white;
        }

        .content {
            margin-left: 250px;
            padding: 20px;
        }

        .header {
            background-color: white;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-left: 250px;
        }

        .nav {
            display: flex;
            flex-direction: column;
        }

        .nav-item {
            margin-bottom: 5px;
            display: block;

        }

        .nav-item .sub-nav {
            background-color: #495057;
            padding-left: 20px;
        }
    </style>
</head>

<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3 bg-dark text-center">
            <h4><?php echo APP_NAME; ?></h4>
        </div>
        <div class="nav">
            <div class="nav-item">
                <a href="<?php echo ADMIN_PANEL_URL; ?>/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : ''; ?>">
                    <i class="fa fa-dashboard"></i> 仪表盘
                </a>
            </div>



            <?php if (isLoggedIn()): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/new_products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'new_products.php' ? 'active' : ''; ?>">
                        <i class="fa fa-cube"></i> 新产品管理
                    </a>
                </div>
            <?php endif; ?>

            <?php if (isLoggedIn()):  ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/products.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'products.php' ? 'active' : ''; ?>">
                        <i class="fa fa-shopping-cart"></i> 商品管理
                    </a>
                </div>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/order_profit.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'order_profit.php' ? 'active' : ''; ?>">
                        <i class="fa fa-money"></i> 订单利润
                    </a>
                </div>
            <?php endif; ?>


            <?php if (hasPermission('inventory_details.view')): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/inventory_details.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'inventory_details.php' ? 'active' : ''; ?>">
                        <i class="fa fa-list"></i> 库存明细
                    </a>
                </div>
            <?php endif; ?>
            <?php if (hasPermission('inventory_details.view')): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/inventory_details.php?action=inventory_alert" class="<?php echo basename($_SERVER['PHP_SELF']) == 'inventory_details.php?action=inventory_alert' ? 'active' : ''; ?>">
                        <i class="fa fa-list"></i> 库存预警
                    </a>
                </div>
            <?php endif; ?>
            <?php if (isLoggedIn()): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/inventory_details_fba.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'inventory_details_fba.php' ? 'active' : ''; ?>">
                        <i class="fa fa-list"></i> fba库存明细
                    </a>
                </div>
            <?php endif; ?>
             <?php if (isLoggedIn()): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/shop_costs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'shop_costs.php' ? 'active' : ''; ?>">
                        <i class="fa fa-list"></i> 店铺费用
                    </a>
                </div>
            <?php endif; ?>
             <?php if (isLoggedIn()): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/track_costs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'track_costs.php' ? 'active' : ''; ?>">
                        <i class="fa fa-list"></i> 赛道费用
                    </a>
                </div>
            <?php endif; ?>
             <?php if (isLoggedIn()): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/order_other_costs.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'order_other_costs.php' ? 'active' : ''; ?>">
                        <i class="fa fa-list"></i> 订单其他费用
                    </a>
                </div>
            <?php endif; ?>

            <?php if (isLoggedIn()): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/aigc.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'aigc.php' ? 'active' : ''; ?>">
                        <i class="fa fa-image"></i> AI图片处理
                    </a>
                </div>
            <?php endif; ?>
            <?php if (isLoggedIn()): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/store.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'store.php' ? 'active' : ''; ?>">
                        <i class="fa fa-money"></i> 店铺管理
                    </a>
                </div>
            <?php endif; ?>



          

            <?php if (hasPermission('warehouses.view')): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/warehouses.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'warehouses.php' ? 'active' : ''; ?>">
                        <i class="fa fa-list"></i> 仓库管理
                    </a>
                </div>
            <?php endif; ?>
            <?php if (hasPermission('car_data.view')): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/car_data.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'car_data.php' ? 'active' : ''; ?>">
                        <i class="fa fa-car"></i> 车型数据管理
                    </a>
                </div>
            <?php endif; ?>
             <?php if (hasPermission('yunfei.view')): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/yunfei.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'yunfei.php' ? 'active' : ''; ?>">
                        <i class="fa fa-truck"></i> 运费管理
                    </a>
                </div>
            <?php endif; ?>

            <?php if (hasPermission('users.view')): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/users.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                        <i class="fa fa-users"></i> 用户管理
                    </a>
                </div>
            <?php endif; ?>

            <?php if (hasPermission('roles.view')): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/roles.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'roles.php' ? 'active' : ''; ?>">
                        <i class="fa fa-user-circle"></i> 角色管理
                    </a>
                </div>
            <?php endif; ?>

            <?php if (hasPermission('permissions.view')): ?>
                <div class="nav-item">
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/permissions.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'permissions.php' ? 'active' : ''; ?>">
                        <i class="fa fa-key"></i> 权限管理
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Header -->
    <div class="header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><?php echo $title ?? '欢迎使用'; ?></h5>
            <div class="dropdown">
                <button class="btn btn-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-user"></i> <?php echo $_SESSION['full_name']; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                    <li><a class="dropdown-item" href="#"><i class="fa fa-cog"></i> 设置</a></li>
                    <li>
                        <hr class="dropdown-divider">
                    </li>
                    <li><a class="dropdown-item" href="<?php echo ADMIN_PANEL_URL; ?>/logout.php"><i class="fa fa-sign-out"></i> 退出登录</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="content">
        <?php if ($success = getSuccess()): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>

        <?php if ($error = getError()): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>