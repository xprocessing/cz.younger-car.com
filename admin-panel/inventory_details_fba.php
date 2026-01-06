<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/InventoryDetailsFbaController.php';

$inventoryDetailsFbaController = new InventoryDetailsFbaController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $inventoryDetailsFbaController->index();
        break;
    case 'delete':
        $inventoryDetailsFbaController->delete();
        break;
    case 'batchDelete':
        $inventoryDetailsFbaController->batchDelete();
        break;
    default:
        $inventoryDetailsFbaController->index();
        break;
}
?>