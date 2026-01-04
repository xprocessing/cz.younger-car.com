<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/WarehouseController.php';

$warehouseController = new WarehouseController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $warehouseController->index();
        break;
    case 'create':
        $warehouseController->create();
        break;
    case 'create_post':
        $warehouseController->createPost();
        break;
    case 'edit':
        $warehouseController->edit();
        break;
    case 'edit_post':
        $warehouseController->editPost();
        break;
    case 'delete':
        $warehouseController->delete();
        break;
    case 'search':
        $warehouseController->index();
        break;
    default:
        $warehouseController->index();
        break;
}
?>
