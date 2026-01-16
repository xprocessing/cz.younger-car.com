<?php
// 设置APP_ROOT为网站根目录
define('APP_ROOT', realpath(__DIR__ . '/..'));

// 包含配置文件和控制器
require_once APP_ROOT . '/admin-panel/config/config.php';
require_once APP_ROOT . '/admin-panel/controllers/AuthController.php';

$authController = new AuthController();
$authController->dashboard();
?>