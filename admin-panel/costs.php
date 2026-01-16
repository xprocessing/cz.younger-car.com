<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/CostsController.php';

$costsController = new CostsController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $costsController->index();
        break;
    case 'create':
        $costsController->create();
        break;
    case 'create_post':
        $costsController->createPost();
        break;
    case 'edit':
        $costsController->edit();
        break;
    case 'edit_post':
        $costsController->editPost();
        break;
    case 'delete':
        $costsController->delete();
        break;
    case 'import':
        $costsController->import();
        break;
    case 'import_post':
        $costsController->importPost();
        break;

    case 'export':
        $costsController->export();
        break;
    default:
        $costsController->index();
        break;
}
?>