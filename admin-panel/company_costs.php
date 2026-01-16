<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/CompanyCostsController.php';

$companyCostsController = new CompanyCostsController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $companyCostsController->index();
        break;
    case 'create':
        $companyCostsController->create();
        break;
    case 'create_post':
        $companyCostsController->createPost();
        break;
    case 'edit':
        $companyCostsController->edit();
        break;
    case 'edit_post':
        $companyCostsController->editPost();
        break;
    case 'delete':
        $companyCostsController->delete();
        break;
    case 'import':
        $companyCostsController->import();
        break;
    case 'import_post':
        $companyCostsController->importPost();
        break;

    case 'export':
        $companyCostsController->export();
        break;
    default:
        $companyCostsController->index();
        break;
}