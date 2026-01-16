<?php
// 包含配置文件和控制器
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/AuthController.php';

$authController = new AuthController();
$authController->dashboard();
?>