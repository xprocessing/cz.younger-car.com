<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/RoleController.php';

$roleController = new RoleController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'index':
        $roleController->index();
        break;
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roleController->createPost();
        } else {
            $roleController->create();
        }
        break;
    case 'edit':
        if (!$id) {
            showError('缺少角色ID');
            redirect(APP_URL . '/roles.php');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $roleController->editPost($id);
        } else {
            $roleController->edit($id);
        }
        break;
    case 'delete':
        if (!$id) {
            showError('缺少角色ID');
            redirect(APP_URL . '/roles.php');
        }
        $roleController->delete($id);
        break;
    default:
        $roleController->index();
        break;
}
?>