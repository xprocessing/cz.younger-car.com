<?php
// AIGC图片处理模块入口文件

// 设置应用根目录
if (!defined('APP_ROOT')) {
    define('APP_ROOT', realpath(dirname(__FILE__) . '/../'));
}

// 加载配置文件
require_once APP_ROOT . '/admin-panel/config/config.php';


// 加载控制器
require_once APP_ROOT . '/admin-panel/controllers/AIGCController.php';

// 创建控制器实例
$aigcController = new AIGCController();

// 获取操作类型
$action = $_GET['action'] ?? 'index';

// 根据操作类型调用相应的方法
switch ($action) {
    case 'index':
        $aigcController->index();
        break;
    case 'processImages':
        $aigcController->processImages();
        break;
    case 'taskHistory':
        $aigcController->taskHistory();
        break;
    case 'taskDetail':
        $aigcController->taskDetail();
        break;
    case 'getTaskDetail':
        $aigcController->getTaskDetail();
        break;
    default:
        // 默认显示主页面
        $aigcController->index();
        break;
}
