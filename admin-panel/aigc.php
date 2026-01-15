<?php
require_once 'config/config.php';
require_once 'controllers/AIGCController.php';

$controller = new AIGCController();
$action = $_GET['action'] ?? 'index';

switch ($action) {
    case 'index':
        $controller->index();
        break;
    case 'processImages':
        $controller->processImages();
        break;
    case 'taskHistory':
        $controller->taskHistory();
        break;
    case 'taskDetail':
        $controller->taskDetail();
        break;
    case 'getTaskDetail':
        $controller->getTaskDetail();
        break;
    default:
        // 默认重定向到首页
        header('Location: ' . APP_URL . '/aigc.php');
        exit;
}
