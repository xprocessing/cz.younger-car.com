<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/InventoryDetailsController.php';

$inventoryDetailsController = new InventoryDetailsController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $inventoryDetailsController->index();
        break;
    case 'create':
        $inventoryDetailsController->create();
        break;
    case 'create_post':
        $inventoryDetailsController->createPost();
        break;
    case 'edit':
        $inventoryDetailsController->edit();
        break;
    case 'edit_post':
        $inventoryDetailsController->editPost();
        break;
    case 'delete':
        $inventoryDetailsController->delete();
        break;
    case 'search':
        $inventoryDetailsController->index();
        break;
    case 'overaged_stats':
        $inventoryDetailsController->overagedStats();
        break;
    case 'inventory_alert':
        $inventoryDetailsController->inventoryAlert();
        break;
    case 'export_inventory_alert':
        $inventoryDetailsController->exportInventoryAlert();
        break;
    default:
        $inventoryDetailsController->index();
        break;
}
?>
