<?php
require_once ADMIN_PANEL_DIR . '/models/Yunfei.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

class YunfeiController {
    private $yunfeiModel;
    
    public function __construct() {
        $this->yunfeiModel = new Yunfei();
        session_start();
    }
    
    // 显示列表
    public function index() {
        if (!hasPermission('yunfei.view')) {
            showError('您没有权限访问此页面');
            redirect(APP_URL . '/dashboard.php');
        }
        
        $page = max(1, intval($_GET['page'] ?? 1));
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        $search = $_GET['search'] ?? '';
        
        if ($search) {
            $yunfeiList = $this->yunfeiModel->search($search, $limit, $offset);
            $totalRecords = $this->yunfeiModel->getSearchCount($search);
        } else {
            $yunfeiList = $this->yunfeiModel->getAll($limit, $offset);
            $totalRecords = $this->yunfeiModel->getCount();
        }
        
        $totalPages = ceil($totalRecords / $limit);
        
        $title = '运费管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/yunfei/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 显示创建页面
    public function create() {
        if (!hasPermission('yunfei.create')) {
            showError('您没有权限创建运费记录');
            redirect(APP_URL . '/yunfei.php');
        }
        
        $title = '创建运费记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/yunfei/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理创建请求
    public function createPost() {
        if (!hasPermission('yunfei.create')) {
            showError('您没有权限创建运费记录');
            redirect(APP_URL . '/yunfei.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/yunfei.php');
        }
        
        $globalOrderNo = $_POST['global_order_no'] ?? '';
        $yunfeiData = $_POST['yunfei_data'] ?? '';
        
        if (empty($globalOrderNo)) {
            showError('订单号不能为空');
            redirect(APP_URL . '/yunfei.php?action=create');
        }
        
        // 检查订单号是否已存在
        if ($this->yunfeiModel->getByOrderNo($globalOrderNo)) {
            showError('订单号已存在');
            redirect(APP_URL . '/yunfei.php?action=create');
        }
        
        // 解析运费数据
        $shisuanyunfeiArray = null;
        if (!empty($yunfeiData)) {
            // 尝试解析JSON
            $shisuanyunfeiArray = json_decode($yunfeiData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                showError('运费数据格式错误，请输入有效的JSON格式。错误信息：' . json_last_error_msg());
                redirect(APP_URL . '/yunfei.php?action=create');
            }
            
            // 验证数据结构
            if (!isset($shisuanyunfeiArray['ems']) || !isset($shisuanyunfeiArray['ems']['results'])) {
                showError('运费数据结构错误，缺少必要的字段');
                redirect(APP_URL . '/yunfei.php?action=create');
            }
        }
        
        // 创建记录
        $this->yunfeiModel->create([
            'global_order_no' => $globalOrderNo,
            'shisuanyunfei' => $shisuanyunfeiArray
        ]);
        
        showSuccess('运费记录创建成功');
        redirect(APP_URL . '/yunfei.php');
    }
    
    // 显示编辑页面
    public function edit($id) {
        if (!hasPermission('yunfei.edit')) {
            showError('您没有权限编辑运费记录');
            redirect(APP_URL . '/yunfei.php');
        }
        
        $yunfei = $this->yunfeiModel->getById($id);
        if (!$yunfei) {
            showError('记录不存在');
            redirect(APP_URL . '/yunfei.php');
        }
        
        $title = '编辑运费记录';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/yunfei/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 处理编辑请求
    public function editPost($id) {
        if (!hasPermission('yunfei.edit')) {
            showError('您没有权限编辑运费记录');
            redirect(APP_URL . '/yunfei.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/yunfei.php');
        }
        
        $yunfei = $this->yunfeiModel->getById($id);
        if (!$yunfei) {
            showError('记录不存在');
            redirect(APP_URL . '/yunfei.php');
        }
        
        $globalOrderNo = $_POST['global_order_no'] ?? '';
        $yunfeiData = $_POST['yunfei_data'] ?? '';
        
        if (empty($globalOrderNo)) {
            showError('订单号不能为空');
            redirect(APP_URL . '/yunfei.php?action=edit&id=' . $id);
        }
        
        // 检查订单号是否已存在（排除当前记录）
        $existingRecord = $this->yunfeiModel->getByOrderNo($globalOrderNo);
        if ($existingRecord && $existingRecord['id'] != $id) {
            showError('订单号已存在');
            redirect(APP_URL . '/yunfei.php?action=edit&id=' . $id);
        }
        
        // 解析运费数据
        $shisuanyunfeiArray = null;
        if (!empty($yunfeiData)) {
            // 尝试解析JSON
            $shisuanyunfeiArray = json_decode($yunfeiData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                showError('运费数据格式错误，请输入有效的JSON格式。错误信息：' . json_last_error_msg());
                redirect(APP_URL . '/yunfei.php?action=edit&id=' . $id);
            }
            
            // 验证数据结构
            if (!isset($shisuanyunfeiArray['ems']) || !isset($shisuanyunfeiArray['ems']['results'])) {
                showError('运费数据结构错误，缺少必要的字段');
                redirect(APP_URL . '/yunfei.php?action=edit&id=' . $id);
            }
        }
        
        // 更新记录
        $this->yunfeiModel->update($id, [
            'global_order_no' => $globalOrderNo,
            'shisuanyunfei' => $shisuanyunfeiArray
        ]);
        
        showSuccess('运费记录更新成功');
        redirect(APP_URL . '/yunfei.php');
    }
    
    // 处理删除请求
    public function delete($id) {
        if (!hasPermission('yunfei.delete')) {
            showError('您没有权限删除运费记录');
            redirect(APP_URL . '/yunfei.php');
        }
        
        $yunfei = $this->yunfeiModel->getById($id);
        if (!$yunfei) {
            showError('记录不存在');
            redirect(APP_URL . '/yunfei.php');
        }
        
        $this->yunfeiModel->delete($id);
        showSuccess('运费记录删除成功');
        redirect(APP_URL . '/yunfei.php');
    }
}