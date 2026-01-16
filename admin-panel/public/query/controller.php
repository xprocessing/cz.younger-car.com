<?php
// 定义必要的常量和配置
// 从public/query目录向上两级到达admin-panel根目录
define('APP_ROOT', dirname(dirname(__DIR__)));
define('APP_URL', 'https://cz.younger-car.com/admin-panel');

// 加载数据库配置
require_once __DIR__ . '/../../../config.php';

// 引入admin-panel的核心文件
require_once __DIR__ . '/../../models/Yunfei.php';
require_once __DIR__ . '/../../helpers/functions.php';

class PublicQueryController {
    private $yunfeiModel;
    
    public function __construct() {
        $this->yunfeiModel = new Yunfei();
        // 公共页面不需要会话管理
    }
    
    // 显示运费查询页面
    public function index() {
        include __DIR__ . '/views/index.php';
    }
    
    // 处理查询请求
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            $this->redirect('./');
        }
        
        $orderNo = trim($_GET['order_no'] ?? '');
        
        if (empty($orderNo)) {
            $this->showError('请输入订单号');
            return;
        }
        
        $result = $this->yunfeiModel->getByOrderNo($orderNo);
        
        if (!$result) {
            $this->showError('未找到该订单号的运费信息');
            return;
        }
        
        // 格式化运费数据
        $yunfeiData = json_decode($result['yunfei'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $yunfeiData = ['error' => '运费数据解析失败'];
        }
        
        include __DIR__ . '/views/result.php';
    }
    
    // API方式查询（返回JSON）
    public function api() {
        header('Content-Type: application/json');
        
        $orderNo = trim($_GET['order_no'] ?? '');
        $response = ['success' => false, 'data' => null, 'message' => ''];
        
        if (empty($orderNo)) {
            $response['message'] = '请输入订单号';
            echo json_encode($response, JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $result = $this->yunfeiModel->getByOrderNo($orderNo);
        
        if (!$result) {
            $response['message'] = '未找到该订单号的运费信息';
        } else {
            $yunfeiData = json_decode($result['yunfei'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $yunfeiData = ['error' => '运费数据解析失败'];
            }
            
            $response['success'] = true;
            $response['data'] = [
                'id' => $result['id'],
                'global_order_no' => $result['global_order_no'],
                'yunfei' => $yunfeiData,
                'create_at' => $result['create_at']
            ];
        }
        
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    // 显示错误信息
    private function showError($message) {
        include __DIR__ . '/views/error.php';
    }
    
    // 重定向
    private function redirect($url) {
        header("Location: $url");
        exit;
    }
}