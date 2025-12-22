<?php
require_once APP_ROOT . '/models/User.php';
require_once APP_ROOT . '/helpers/functions.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        session_start();
    }
    
    // 显示登录页面
    public function login() {
        if (isLoggedIn()) {
            redirect(APP_URL . '/dashboard.php');
        }
        
        $error = getError();
        include VIEWS_DIR . '/auth/login.php';
    }
    
    // 处理登录请求
    public function loginPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/login.php');
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            showError('请输入用户名和密码');
            redirect(APP_URL . '/login.php');
        }
        
        $user = $this->userModel->verifyLogin($username, $password);
        if (!$user) {
            showError('用户名或密码错误');
            redirect(APP_URL . '/login.php');
        }
        
        // 设置会话
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['last_activity'] = time();
        
        showSuccess('登录成功');
        redirect(APP_URL . '/dashboard.php');
    }
    
    // 处理登出请求
    public function logout() {
        session_destroy();
        redirect(APP_URL . '/login.php');
    }
    
    // 显示仪表盘
    public function dashboard() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        $success = getSuccess();
        $user = getCurrentUser();
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/dashboard.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
}
?>