<?php

// 密码加密
function hashPassword($password) {
    return password_hash($password, HASH_ALGO, ['cost' => HASH_COST]);
}

// 密码验证
function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

// 生成随机字符串
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// 重定向函数
function redirect($url) {
    header("Location: $url");
    exit;
}

// 显示错误信息
function showError($message) {
    $_SESSION['error_msg'] = $message;
}

// 显示成功信息
function showSuccess($message) {
    $_SESSION['success_msg'] = $message;
}

// 获取错误信息
function getError() {
    $error = $_SESSION['error_msg'] ?? '';
    unset($_SESSION['error_msg']);
    return $error;
}

// 获取成功信息
function getSuccess() {
    $success = $_SESSION['success_msg'] ?? '';
    unset($_SESSION['success_msg']);
    return $success;
}

// 检查用户是否登录
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// 获取当前登录用户ID
function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

// 获取当前登录用户信息
function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    require_once APP_ROOT . '/models/User.php';
    $userModel = new User();
    return $userModel->getById(getCurrentUserId());
}

// 检查用户是否有指定权限
function hasPermission($permissionSlug) {
    if (!isLoggedIn()) {
        return false;
    }
    
    require_once APP_ROOT . '/models/Permission.php';
    $permissionModel = new Permission();
    return $permissionModel->checkUserPermission(getCurrentUserId(), $permissionSlug);
}

// 检查用户是否有指定角色
function hasRole($roleName) {
    if (!isLoggedIn()) {
        return false;
    }
    
    require_once APP_ROOT . '/models/Role.php';
    $roleModel = new Role();
    return $roleModel->checkUserRole(getCurrentUserId(), $roleName);
}

// 渲染视图
function renderView($view, $data = []) {
    extract($data);
    $viewPath = VIEWS_DIR . '/' . $view . '.php';
    
    if (file_exists($viewPath)) {
        require $viewPath;
    } else {
        die("视图文件不存在: $viewPath");
    }
}

// 渲染布局
function renderLayout($layout, $content, $data = []) {
    extract($data);
    $layoutPath = VIEWS_DIR . '/layouts/' . $layout . '.php';
    
    if (file_exists($layoutPath)) {
        require $layoutPath;
    } else {
        die("布局文件不存在: $layoutPath");
    }
}

// 解析带货币符号的字符串为浮点数
function parseCurrencyAmount($amountString) {
    if (empty($amountString)) {
        return 0.0;
    }
    
    // 移除货币符号（$、¥、€等）、百分号和空格，但保留负号和小数点
    $cleanAmount = preg_replace('/[^\d\.-]/', '', (string)$amountString);
    
    // 转换为浮点数
    $amount = (float)$cleanAmount;
    
    return $amount;
}
?>