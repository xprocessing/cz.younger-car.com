<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/ProductsController.php';

$productsController = new ProductsController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $productsController->index();
        break;
    case 'create':
        $productsController->create();
        break;
    case 'create_post':
        $productsController->createPost();
        break;
    case 'edit':
        $productsController->edit();
        break;
    case 'edit_post':
        $productsController->editPost();
        break;
    case 'delete':
        $productsController->delete();
        break;
    case 'batchDelete':
        $productsController->batchDelete();
        break;
    case 'search':
        $productsController->search();
        break;
    case 'import':
        $productsController->import();
        break;
    case 'import_post':
        $productsController->importPost();
        break;
    case 'export':
        $productsController->export();
        break;
    case 'stats':
        $productsController->stats();
        break;
    default:
        $productsController->index();
        break;
}
?>
