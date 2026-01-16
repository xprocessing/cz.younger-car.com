<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/NewProductController.php';

$newProductController = new NewProductController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $newProductController->index();
        break;
    case 'create':
        $newProductController->create();
        break;
    case 'create_post':
        $newProductController->createPost();
        break;
    case 'edit':
        $newProductController->edit();
        break;
    case 'edit_post':
        $newProductController->editPost();
        break;
    case 'delete':
        $newProductController->delete();
        break;
    case 'search':
        $newProductController->search();
        break;
    default:
        $newProductController->index();
        break;
}
?>