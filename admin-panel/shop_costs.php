<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/ShopCostsController.php';

$shopCostsController = new ShopCostsController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $shopCostsController->index();
        break;
    case 'create':
        $shopCostsController->create();
        break;
    case 'create_post':
        $shopCostsController->createPost();
        break;
    case 'edit':
        $shopCostsController->edit();
        break;
    case 'edit_post':
        $shopCostsController->editPost();
        break;
    case 'delete':
        $shopCostsController->delete();
        break;
    case 'import':
        $shopCostsController->import();
        break;
    case 'import_post':
        $shopCostsController->importPost();
        break;

    case 'export':
        $shopCostsController->export();
        break;
    default:
        $shopCostsController->index();
        break;
}
?>