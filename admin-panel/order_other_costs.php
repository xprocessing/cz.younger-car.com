<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/OrderOtherCostsController.php';

$orderOtherCostsController = new OrderOtherCostsController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $orderOtherCostsController->index();
        break;
    case 'create':
        $orderOtherCostsController->create();
        break;
    case 'create_post':
        $orderOtherCostsController->createPost();
        break;
    case 'edit':
        $orderOtherCostsController->edit();
        break;
    case 'edit_post':
        $orderOtherCostsController->editPost();
        break;
    case 'delete':
        $orderOtherCostsController->delete();
        break;
    case 'import':
        $orderOtherCostsController->import();
        break;
    case 'import_post':
        $orderOtherCostsController->importPost();
        break;

    case 'export':
        $orderOtherCostsController->export();
        break;
    default:
        $orderOtherCostsController->index();
        break;
}