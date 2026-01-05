<?php
require_once APP_ROOT . '/models/InventoryDetails.php';
require_once APP_ROOT . '/helpers/functions.php';

class InventoryDetailsController {
    private $inventoryDetailsModel;
    
    public function __construct() {
        $this->inventoryDetailsModel = new InventoryDetails();
        session_start();
    }
    
    public function index() {
        if (!hasPermission('inventory_details.view')) {
            showError('您没有权限访问此页面');
            redirect(APP_URL . '/dashboard.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        $wid = $_GET['wid'] ?? '';
        $sortField = $_GET['sort'] ?? 'id';
        $sortOrder = $_GET['order'] ?? 'DESC';
        
        if ($keyword) {
            $inventoryDetails = $this->inventoryDetailsModel->search($keyword, $limit, $offset, $sortField, $sortOrder);
            $totalCount = $this->inventoryDetailsModel->getSearchCount($keyword);
        } elseif ($wid) {
            $inventoryDetails = $this->inventoryDetailsModel->getByWid($wid, $limit, $offset, $sortField, $sortOrder);
            $totalCount = $this->inventoryDetailsModel->getCountByWid($wid);
        } else {
            $inventoryDetails = $this->inventoryDetailsModel->getAll($limit, $offset, $sortField, $sortOrder);
            $totalCount = $this->inventoryDetailsModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $title = '库存明细';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/inventory_details/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function create() {
        if (!hasPermission('inventory_details.create')) {
            showError('您没有权限创建库存明细');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        $title = '创建库存明细';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/inventory_details/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function createPost() {
        if (!hasPermission('inventory_details.create')) {
            showError('您没有权限创建库存明细');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/inventory_details.php');
        }
        
        if (empty($_POST['wid'])) {
            showError('仓库ID不能为空');
            redirect(APP_URL . '/inventory_details.php?action=create');
        }
        
        if (empty($_POST['sku'])) {
            showError('SKU不能为空');
            redirect(APP_URL . '/inventory_details.php?action=create');
        }
        
        if (empty($_POST['product_valid_num'])) {
            showError('可用量不能为空');
            redirect(APP_URL . '/inventory_details.php?action=create');
        }
        
        $data = [
            'wid' => $_POST['wid'],
            'sku' => $_POST['sku'],
            'product_valid_num' => $_POST['product_valid_num'],
            'quantity_receive' => $_POST['quantity_receive'] ?? '0',
            'average_age' => $_POST['average_age'] ?? 0,
            'purchase_price' => $_POST['purchase_price'] ?? 0,
            'head_stock_price' => $_POST['head_stock_price'] ?? 0,
            'stock_price' => $_POST['stock_price'] ?? 0
        ];
        
        if ($this->inventoryDetailsModel->create($data)) {
            setSuccess('库存明细创建成功');
            redirect(APP_URL . '/inventory_details.php');
        } else {
            showError('库存明细创建失败');
            redirect(APP_URL . '/inventory_details.php?action=create');
        }
    }
    
    public function edit() {
        if (!hasPermission('inventory_details.edit')) {
            showError('您没有权限编辑库存明细');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            showError('库存明细ID不能为空');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        $inventoryDetail = $this->inventoryDetailsModel->getById($id);
        if (!$inventoryDetail) {
            showError('库存明细不存在');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        $title = '编辑库存明细';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/inventory_details/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function editPost() {
        if (!hasPermission('inventory_details.edit')) {
            showError('您没有权限编辑库存明细');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/inventory_details.php');
        }
        
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            showError('库存明细ID不能为空');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        if (empty($_POST['wid'])) {
            showError('仓库ID不能为空');
            redirect(APP_URL . '/inventory_details.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['sku'])) {
            showError('SKU不能为空');
            redirect(APP_URL . '/inventory_details.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['product_valid_num'])) {
            showError('可用量不能为空');
            redirect(APP_URL . '/inventory_details.php?action=edit&id=' . $id);
        }
        
        $data = [
            'wid' => $_POST['wid'],
            'sku' => $_POST['sku'],
            'product_valid_num' => $_POST['product_valid_num'],
            'quantity_receive' => $_POST['quantity_receive'] ?? '0',
            'average_age' => $_POST['average_age'] ?? 0,
            'purchase_price' => $_POST['purchase_price'] ?? 0,
            'head_stock_price' => $_POST['head_stock_price'] ?? 0,
            'stock_price' => $_POST['stock_price'] ?? 0
        ];
        
        if ($this->inventoryDetailsModel->update($id, $data)) {
            setSuccess('库存明细更新成功');
            redirect(APP_URL . '/inventory_details.php');
        } else {
            showError('库存明细更新失败');
            redirect(APP_URL . '/inventory_details.php?action=edit&id=' . $id);
        }
    }
    
    public function delete() {
        if (!hasPermission('inventory_details.delete')) {
            showError('您没有权限删除库存明细');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            showError('库存明细ID不能为空');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        $inventoryDetail = $this->inventoryDetailsModel->getById($id);
        if (!$inventoryDetail) {
            showError('库存明细不存在');
            redirect(APP_URL . '/inventory_details.php');
        }
        
        if ($this->inventoryDetailsModel->delete($id)) {
            setSuccess('库存明细删除成功');
        } else {
            showError('库存明细删除失败');
        }
        
        redirect(APP_URL . '/inventory_details.php');
    }
    
    public function overagedStats() {
        if (!hasPermission('inventory_details.view')) {
            showError('您没有权限访问此页面');
            redirect(APP_URL . '/dashboard.php');
        }
        
        $thresholdDays = $_GET['threshold'] ?? 180;
        $overagedInventory = $this->inventoryDetailsModel->getOveragedInventory($thresholdDays);
        
        $title = '库龄统计（超过' . $thresholdDays . '天）';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/inventory_details/overaged_stats.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function inventoryAlert() {
        if (!hasPermission('inventory_details.view')) {
            showError('您没有权限访问此页面');
            redirect(APP_URL . '/dashboard.php');
        }
        
        $inventoryAlerts = $this->inventoryDetailsModel->getInventoryAlert();
        
        $title = '库存预警（海外仓）';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/inventory_details/inventory_alert.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
}
