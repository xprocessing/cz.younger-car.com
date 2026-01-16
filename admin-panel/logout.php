<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/controllers/AuthController.php';

$authController = new AuthController();
$authController->logout();
?>