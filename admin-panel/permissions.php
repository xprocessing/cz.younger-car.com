<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/PermissionController.php';

$permissionController = new PermissionController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'index':
        $permissionController->index();
        break;
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $permissionController->createPost();
        } else {
            $permissionController->create();
        }
        break;
    case 'edit':
        if (!$id) {
            showError('缺少权限ID');
            redirect(APP_URL . '/permissions.php');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $permissionController->editPost($id);
        } else {
            $permissionController->edit($id);
        }
        break;
    case 'delete':
        if (!$id) {
            showError('缺少权限ID');
            redirect(APP_URL . '/permissions.php');
        }
        $permissionController->delete($id);
        break;
    default:
        $permissionController->index();
        break;
}
?>