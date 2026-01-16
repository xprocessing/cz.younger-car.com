<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/CarDataController.php';

$carDataController = new CarDataController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $carDataController->index();
        break;
    case 'create':
        $carDataController->create();
        break;
    case 'createPost':
        $carDataController->createPost();
        break;
    case 'edit':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $carDataController->edit($id);
        } else {
            $carDataController->index();
        }
        break;
    case 'editPost':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $carDataController->editPost($id);
        } else {
            $carDataController->index();
        }
        break;
    case 'delete':
        $id = $_GET['id'] ?? null;
        if ($id) {
            $carDataController->delete($id);
        } else {
            $carDataController->index();
        }
        break;
    case 'import':
        $carDataController->import();
        break;
    case 'importPost':
        $carDataController->importPost();
        break;
    case 'export':
        $carDataController->export();
        break;
    // 动态筛选API接口
    case 'getModelsByMake':
        $carDataController->getModelsByMake();
        break;
    case 'getModelsByMakeCn':
        $carDataController->getModelsByMakeCn();
        break;
    case 'getYearsByMakeAndModel':
        $carDataController->getYearsByMakeAndModel();
        break;
    case 'getYearsByMakeCnAndModel':
        $carDataController->getYearsByMakeCnAndModel();
        break;
    case 'getMarketsByMakeModelAndYear':
        $carDataController->getMarketsByMakeModelAndYear();
        break;
    case 'getMarketsByMakeCnModelAndYear':
        $carDataController->getMarketsByMakeCnModelAndYear();
        break;
    default:
        $carDataController->index();
        break;
}
?>