<?php
require_once __DIR__ . '/controller.php';

$controller = new PublicQueryController();

$action = $_GET['action'] ?? 'index';

// 如果直接提供了order_no参数，自动跳转到搜索结果
if (!isset($_GET['action']) && isset($_GET['order_no'])) {
    $_GET['action'] = 'search';
    $action = 'search';
}

// 加载页面头部
include __DIR__ . '/views/header.php';

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'search':
        $controller->search();
        break;
    case 'api':
        $controller->api();
        break;
    default:
        $controller->index();
        break;
}

// 加载页面底部
include __DIR__ . '/views/footer.php';
?>