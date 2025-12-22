<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/controllers/DataController.php';

$dataController = new DataController();

$action = $_GET['action'] ?? 'index';
$id = $_GET['id'] ?? null;

switch ($action) {
    case 'index':
        $dataController->index();
        break;
    case 'create':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dataController->createPost();
        } else {
            $dataController->create();
        }
        break;
    case 'edit':
        if (!$id) {
            showError('缺少产品ID');
            redirect(APP_URL . '/data.php');
        }
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $dataController->editPost($id);
        } else {
            $dataController->edit($id);
        }
        break;
    case 'delete':
        if (!$id) {
            showError('缺少产品ID');
            redirect(APP_URL . '/data.php');
        }
        $dataController->delete($id);
        break;
    default:
        $dataController->index();
        break;
}
?>