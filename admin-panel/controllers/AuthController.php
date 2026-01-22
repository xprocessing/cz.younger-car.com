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
        
        // 引入Redis缓存
        require_once ADMIN_PANEL_DIR . '/includes/RedisCache.php';
        $redisCache = RedisCache::getInstance();
        
        require_once ADMIN_PANEL_DIR . '/models/OrderProfit.php';
        $orderProfitModel = new OrderProfit();
        
        // 获取最近30天的数据
        $endDate = date('Y-m-d');
        $startDate = date('Y-m-d', strtotime('-30 days'));
        
        // 生成缓存键
        $cachePrefix = 'dashboard:' . date('Y-m-d');
        $cacheExpire = 36000; // 10小时缓存  
        
        // 获取各平台销售额占比
        $salesCacheKey = $cachePrefix . ':platform:sales';
        $platformSales = $redisCache->get($salesCacheKey);
        if (!$platformSales) {
            $platformSales = $orderProfitModel->getPlatformSalesPercentage($startDate, $endDate);
            $redisCache->set($salesCacheKey, $platformSales, $cacheExpire);
        }
        
        // 获取各平台订单总量占比
        $ordersCacheKey = $cachePrefix . ':platform:orders';
        $platformOrders = $redisCache->get($ordersCacheKey);
        if (!$platformOrders) {
            $platformOrders = $orderProfitModel->getPlatformOrderCountPercentage($startDate, $endDate);
            $redisCache->set($ordersCacheKey, $platformOrders, $cacheExpire);
        }
        
        // 获取各平台毛利润占比
        $profitsCacheKey = $cachePrefix . ':platform:profits';
        $platformProfits = $redisCache->get($profitsCacheKey);
        if (!$platformProfits) {
            $platformProfits = $orderProfitModel->getPlatformProfitPercentage($startDate, $endDate);
            $redisCache->set($profitsCacheKey, $platformProfits, $cacheExpire);
        }
        
        // 获取各平台广告费占比
        $costsCacheKey = $cachePrefix . ':platform:costs';
        $platformCosts = $redisCache->get($costsCacheKey);
        if (!$platformCosts) {
            $platformCosts = $orderProfitModel->getPlatformCostPercentage($startDate, $endDate);
            $redisCache->set($costsCacheKey, $platformCosts, $cacheExpire);
        }
        
        // 获取最近60天各平台每日销售额数据
        $dailySalesCacheKey = $cachePrefix . ':daily:platform:sales';
        $dailyPlatformSales = $redisCache->get($dailySalesCacheKey);
        if (!$dailyPlatformSales) {
            $dailyPlatformSales = $orderProfitModel->getDailyPlatformSales(60);
            $redisCache->set($dailySalesCacheKey, $dailyPlatformSales, $cacheExpire);
        }
        
        // 获取各平台月度销售额统计数据
        $monthlySalesCacheKey = $cachePrefix . ':monthly:sales';
        $platformMonthlyStats = $redisCache->get($monthlySalesCacheKey);
        if (!$platformMonthlyStats) {
            $platformMonthlyStats = $orderProfitModel->getPlatformMonthlySalesStats();
            $redisCache->set($monthlySalesCacheKey, $platformMonthlyStats, $cacheExpire);
        }
        
        // 获取各平台月度订单量统计数据
        $monthlyOrdersCacheKey = $cachePrefix . ':monthly:orders';
        $platformMonthlyOrderStats = $redisCache->get($monthlyOrdersCacheKey);
        if (!$platformMonthlyOrderStats) {
            $platformMonthlyOrderStats = $orderProfitModel->getPlatformMonthlyOrderStats();
            $redisCache->set($monthlyOrdersCacheKey, $platformMonthlyOrderStats, $cacheExpire);
        }
        
        // 获取最近30天按平台统计数据
        $platformStatsCacheKey = $cachePrefix . ':platform:stats';
        $platformStats = $redisCache->get($platformStatsCacheKey);
        if (!$platformStats) {
            $platformStats = $orderProfitModel->getPlatformStats($startDate, $endDate);
            $redisCache->set($platformStatsCacheKey, $platformStats, $cacheExpire);
        }
        
        // 获取最近60天每日销量统计数据
        $startDate60 = date('Y-m-d', strtotime('-60 days'));
        $dailySalesStatsCacheKey = $cachePrefix . ':daily:sales:stats';
        $dailySalesStats = $redisCache->get($dailySalesStatsCacheKey);
        if (!$dailySalesStats) {
            $dailySalesStats = $orderProfitModel->getDailySalesStats($startDate60, $endDate);
            $redisCache->set($dailySalesStatsCacheKey, $dailySalesStats, $cacheExpire);
        }
        
        // 获取库存统计数据
        $inventoryCacheKey = $cachePrefix . ':inventory:stats';
        $totalStats = $redisCache->get($inventoryCacheKey);
        if (!$totalStats) {
            require_once ADMIN_PANEL_DIR . '/models/InventoryDetails.php';
            $inventoryDetailsModel = new InventoryDetails();
            $allInventoryAlerts = $inventoryDetailsModel->getInventoryAlert();
            $totalStats = [];
            $totalStats['product_valid_num_excluding_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_valid_num_excluding_wenzhou'));
            $totalStats['product_onway_excluding_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_onway_excluding_wenzhou'));
            $totalStats['product_valid_num_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_valid_num_wenzhou'));
            $totalStats['quantity_receive_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'quantity_receive_wenzhou'));
            $totalStats['outbound_30days'] = array_sum(array_column($allInventoryAlerts, 'outbound_30days'));
            $totalStats['sku_count'] = count($allInventoryAlerts);
            $redisCache->set($inventoryCacheKey, $totalStats, $cacheExpire);
        }
        
        // 获取赛道统计数据
        $trackStatsCacheKey = $cachePrefix . ':track:stats';
        $trackSalesCacheKey = $cachePrefix . ':track:sales';
        $trackProfitCacheKey = $cachePrefix . ':track:profit';
        
        $trackStatistics = $redisCache->get($trackStatsCacheKey);
        $trackSalesData = $redisCache->get($trackSalesCacheKey);
        $trackProfitData = $redisCache->get($trackProfitCacheKey);
        
        if (!$trackStatistics || !$trackSalesData || !$trackProfitData) {
            require_once ADMIN_PANEL_DIR . '/models/TrackStatistics.php';
            $trackStatisticsModel = new TrackStatistics();
            $trackStatistics = $trackStatisticsModel->getTrackStatistics();
            $trackSalesData = $trackStatisticsModel->getTrackSalesData();
            $trackProfitData = $trackStatisticsModel->getTrackProfitData();
            
            $redisCache->set($trackStatsCacheKey, $trackStatistics, $cacheExpire);
            $redisCache->set($trackSalesCacheKey, $trackSalesData, $cacheExpire);
            $redisCache->set($trackProfitCacheKey, $trackProfitData, $cacheExpire);
        }
        
        $success = getSuccess();
        $user = getCurrentUser();
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/dashboard.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
}
?>