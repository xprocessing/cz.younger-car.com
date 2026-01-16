<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/YunfeiController.php';

$yunfeiController = new YunfeiController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'index':
        $yunfeiController->index();
        break;
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $yunfeiController->createPost();
        } else {
            $yunfeiController->create();
        }
        break;
    case 'edit':
        if (!$id) {
            showError('缺少记录ID');
            redirect(ADMIN_PANEL_URL . '/yunfei.php');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $yunfeiController->editPost($id);
        } else {
            $yunfeiController->edit($id);
        }
        break;
    case 'delete':
        if (!$id) {
            showError('缺少记录ID');
            redirect(ADMIN_PANEL_URL . '/yunfei.php');
        }
        $yunfeiController->delete($id);
        break;
    default:
        $yunfeiController->index();
        break;
}
?>