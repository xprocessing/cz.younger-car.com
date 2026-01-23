<?php
// 引入配置文件
require_once __DIR__ . '/../config.php';

// 启动会话
session_start();

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
require_once ADMIN_PANEL_DIR . '/controllers/OrderReviewController.php';

// 检查登录状态
if (!isLoggedIn()) {
    redirect(ADMIN_PANEL_URL . '/login.php');
}

// 获取操作参数
$action = $_GET['action'] ?? 'index';

// 实例化控制器
$orderReviewController = new OrderReviewController();

// 路由处理
switch ($action) {
    case 'index':
        $orderReviewController->index();
        break;
    case 'create':
        $orderReviewController->create();
        break;
    case 'createPost':
        $orderReviewController->createPost();
        break;
    case 'edit':
        $orderReviewController->edit();
        break;
    case 'editPost':
        $orderReviewController->editPost();
        break;
    case 'delete':
        $orderReviewController->delete();
        break;
    case 'import':
        $orderReviewController->import();
        break;
    case 'importPost':
        $orderReviewController->importPost();
        break;
    case 'export':
        $orderReviewController->export();
        break;
    default:
        $orderReviewController->index();
        break;
}