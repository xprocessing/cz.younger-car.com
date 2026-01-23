<?php
// 引入配置文件
require_once __DIR__ . '/../config.php';

// 定义常量
if (!defined('ADMIN_PANEL_DIR')) {
    define('ADMIN_PANEL_DIR', dirname(__FILE__));
}
if (!defined('VIEWS_DIR')) {
    define('VIEWS_DIR', ADMIN_PANEL_DIR . '/views');
}
if (!defined('ADMIN_PANEL_URL')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    define('ADMIN_PANEL_URL', $protocol . '://' . $_SERVER['HTTP_HOST'] . '/admin-panel');
}

// 引入必要文件
require_once ADMIN_PANEL_DIR . '/includes/database.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';
require_once ADMIN_PANEL_DIR . '/controllers/LogisticsController.php';

// 检查登录状态
if (!isLoggedIn()) {
    redirect(ADMIN_PANEL_URL . '/login.php');
}

// 获取操作参数
$action = $_GET['action'] ?? 'index';

// 实例化控制器
$logisticsController = new LogisticsController();

// 路由处理
switch ($action) {
    case 'index':
        $logisticsController->index();
        break;
    case 'create':
        $logisticsController->create();
        break;
    case 'createPost':
        $logisticsController->createPost();
        break;
    case 'edit':
        $logisticsController->edit();
        break;
    case 'editPost':
        $logisticsController->editPost();
        break;
    case 'delete':
        $logisticsController->delete();
        break;
    default:
        $logisticsController->index();
        break;
}