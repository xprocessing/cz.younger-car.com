<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/QueryController.php';

$queryController = new QueryController();

$action = $_GET['action'] ?? 'index';

// 如果直接提供了order_no参数，自动跳转到搜索结果
if (!isset($_GET['action']) && isset($_GET['order_no'])) {
    $_GET['action'] = 'search';
    $action = 'search';
}

switch ($action) {
    case 'index':
        $queryController->index();
        break;
    case 'search':
        $queryController->search();
        break;
    case 'api':
        $queryController->api();
        break;
    default:
        $queryController->index();
        break;
}
?>