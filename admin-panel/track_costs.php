<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/TrackCostsController.php';

$trackCostsController = new TrackCostsController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $trackCostsController->index();
        break;
    case 'create':
        $trackCostsController->create();
        break;
    case 'create_post':
        $trackCostsController->createPost();
        break;
    case 'edit':
        $trackCostsController->edit();
        break;
    case 'edit_post':
        $trackCostsController->editPost();
        break;
    case 'delete':
        $trackCostsController->delete();
        break;
    case 'import':
        $trackCostsController->import();
        break;
    case 'import_post':
        $trackCostsController->importPost();
        break;
    case 'export':
        $trackCostsController->export();
        break;
    case 'statistics':
        $trackCostsController->statistics();
        break;
    default:
        $trackCostsController->index();
        break;
}
?>
