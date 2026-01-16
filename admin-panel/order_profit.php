<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/OrderProfitController.php';

$orderProfitController = new OrderProfitController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $orderProfitController->index();
        break;
    case 'create':
        $orderProfitController->create();
        break;
    case 'create_post':
        $orderProfitController->createPost();
        break;
    case 'edit':
        $orderProfitController->edit();
        break;
    case 'edit_post':
        $orderProfitController->editPost();
        break;
    case 'delete':
        $orderProfitController->delete();
        break;
    case 'search':
        $orderProfitController->search();
        break;
    case 'stats':
        $orderProfitController->stats();
        break;
    case 'import':
        $orderProfitController->import();
        break;
    case 'import_post':
        $orderProfitController->importPost();
        break;
    case 'export':
        $orderProfitController->export();
        break;
    default:
        $orderProfitController->index();
        break;
}
?>