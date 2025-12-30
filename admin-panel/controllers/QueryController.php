<?php
require_once APP_ROOT . '/models/Yunfei.php';
require_once APP_ROOT . '/helpers/functions.php';

class QueryController {
    private $yunfeiModel;
    
    public function __construct() {
        $this->yunfeiModel = new Yunfei();
        session_start();
    }
    
    // 显示运费查询页面
    public function index() {
        $title = '运费查询';
        
        include VIEWS_DIR . '/query/header.php';
        include VIEWS_DIR . '/query/index.php';
        include VIEWS_DIR . '/query/footer.php';
    }
    
    // 处理查询请求
    public function search() {
        if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
            redirect(APP_URL . '/query.php');
        }
        
        $orderNo = trim($_GET['order_no'] ?? '');
        
        if (empty($orderNo)) {
            showError('请输入订单号');
            redirect(APP_URL . '/query.php');
        }
        
        $result = $this->yunfeiModel->getByOrderNo($orderNo);
        
        if (!$result) {
            showError('未找到该订单号的运费信息');
            redirect(APP_URL . '/query.php');
        }
        
        // 格式化运费数据
        $yunfeiData = [];
        if (!empty($result['shisuanyunfei'])) {
            $yunfeiData = json_decode($result['shisuanyunfei'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $yunfeiData = ['error' => '运费数据解析失败: ' . json_last_error_msg() . ' (原始数据: ' . substr($result['shisuanyunfei'], 0, 200) . '...)'];
            }
        } else {
            $yunfeiData = ['error' => '运费数据为空'];
        }
        
        $title = '运费查询结果';
        
        include VIEWS_DIR . '/query/header.php';
        include VIEWS_DIR . '/query/result.php';
        include VIEWS_DIR . '/query/footer.php';
    }
    
    // API方式查询（返回JSON）
    public function api() {
        if (!hasPermission('query.view')) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => '您没有权限查询运费'], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
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
            $yunfeiData = [];
            if (!empty($result['shisuanyunfei'])) {
                $yunfeiData = json_decode($result['shisuanyunfei'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $yunfeiData = ['error' => '运费数据解析失败: ' . json_last_error_msg() . ' (原始数据: ' . substr($result['shisuanyunfei'], 0, 200) . '...)'];
                }
            } else {
                $yunfeiData = ['error' => '运费数据为空'];
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
}