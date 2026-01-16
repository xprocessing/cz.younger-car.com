<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/StoreController.php';

$storeController = new StoreController();

$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $storeController->index();
        break;
    case 'create':
        $storeController->create();
        break;
    case 'create_post':
        $storeController->createPost();
        break;
    case 'edit':
        $storeController->edit();
        break;
    case 'edit_post':
        $storeController->editPost();
        break;
    case 'delete':
        $storeController->delete();
        break;
    case 'search':
        $storeController->index(); // 搜索功能已集成在index方法中
        break;
    default:
        $storeController->index();
        break;
}
?>
