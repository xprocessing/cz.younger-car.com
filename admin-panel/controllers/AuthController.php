<?php
require_once ADMIN_PANEL_DIR . '/models/User.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

class AuthController {
    private $userModel;
    
    public function __construct() {
        $this->userModel = new User();
        session_start();
    }
    
    // 显示登录页面
    public function login() {
        if (isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/dashboard.php');
        }
        
        $error = getError();
        include VIEWS_DIR . '/auth/login.php';
    }
    
    // 处理登录请求
    public function loginPost() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        
        if (empty($username) || empty($password)) {
            showError('请输入用户名和密码');
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        $user = $this->userModel->verifyLogin($username, $password);
        if (!$user) {
            showError('用户名或密码错误');
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        // 设置会话
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['last_activity'] = time();
        
        showSuccess('登录成功');
        redirect(ADMIN_PANEL_URL . '/dashboard.php');
    }
    
    // 处理登出请求
    public function logout() {
        session_destroy();
        redirect(ADMIN_PANEL_URL . '/login.php');
    }
    
    // 显示仪表盘
    public function dashboard() {
        // 检查用户是否登录
        if (!isLoggedIn()) {
            redirect(ADMIN_PANEL_URL . '/login.php');
        }
        
        require_once ADMIN_PANEL_DIR . '/models/OrderProfit.php';
        $orderProfitModel = new OrderProfit();
        
        // 获取最近30天的数据
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days'));
        
        // 获取各平台销售额占比
        $platformSales = $orderProfitModel->getPlatformSalesPercentage($startDate, $endDate);
        
        // 获取各平台订单总量占比
        $platformOrders = $orderProfitModel->getPlatformOrderCountPercentage($startDate, $endDate);
        
        // 获取各平台毛利润占比
        $platformProfits = $orderProfitModel->getPlatformProfitPercentage($startDate, $endDate);
        
        // 获取各平台广告费占比
        $platformCosts = $orderProfitModel->getPlatformCostPercentage($startDate, $endDate);
        
        // 获取最近60天各平台每日销售额数据
        $dailyPlatformSales = $orderProfitModel->getDailyPlatformSales(60);
        
        // 获取各平台月度销售额统计数据
        $platformMonthlyStats = $orderProfitModel->getPlatformMonthlySalesStats();
        
        // 获取赛道统计数据
        require_once ADMIN_PANEL_DIR . '/models/TrackStatistics.php';
        $trackStatisticsModel = new TrackStatistics();
        $trackStatistics = $trackStatisticsModel->getTrackStatistics();
        $trackSalesData = $trackStatisticsModel->getTrackSalesData();
        $trackProfitData = $trackStatisticsModel->getTrackProfitData();
        
        $success = getSuccess();
        $user = getCurrentUser();
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/dashboard.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
}
?>