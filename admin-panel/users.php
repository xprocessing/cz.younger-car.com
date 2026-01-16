<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/UserController.php';

$userController = new UserController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'index':
        $userController->index();
        break;
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->createPost();
        } else {
            $userController->create();
        }
        break;
    case 'edit':
        if (!$id) {
            showError('缺少用户ID');
            redirect(APP_URL . '/users.php');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userController->editPost($id);
        } else {
            $userController->edit($id);
        }
        break;
    case 'delete':
        if (!$id) {
            showError('缺少用户ID');
            redirect(APP_URL . '/users.php');
        }
        $userController->delete($id);
        break;
    default:
        $userController->index();
        break;
}
?>