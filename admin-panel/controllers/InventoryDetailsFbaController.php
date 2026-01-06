<?php
require_once APP_ROOT . '/models/InventoryDetailsFba.php';
require_once APP_ROOT . '/helpers/functions.php';

class InventoryDetailsFbaController {
    private $inventoryDetailsFbaModel;
    
    public function __construct() {
        $this->inventoryDetailsFbaModel = new InventoryDetailsFba();
        session_start();
    }
    
    public function index() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        // 检查权限
        if (!hasPermission('inventory_details_fba.view')) {
            showError('您没有权限查看FBA库存详情');
            redirect(APP_URL . '/dashboard.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $search = $_GET['search'] ?? '';
        $name = $_GET['name'] ?? '';
        $sku = $_GET['sku'] ?? '';
        
        $filters = [
            'search' => $search,
            'name' => $name,
            'sku' => $sku
        ];
        
        // 由于模型没有实现分页查询，先获取所有数据再手动分页
        $allInventoryDetails = $this->inventoryDetailsFbaModel->getAll($filters);
        $totalCount = count($allInventoryDetails);
        $inventoryDetails = array_slice($allInventoryDetails, $offset, $limit);
        $totalPages = ceil($totalCount / $limit);
        $warehouseNames = $this->inventoryDetailsFbaModel->getAllWarehouseNames();
        $title = 'FBA库存详情管理';
        
        // 使用extract函数将变量导入当前作用域
        $data = [
            'inventoryDetails' => $inventoryDetails,
            'totalPages' => $totalPages,
            'page' => $page,
            'warehouseNames' => $warehouseNames,
            'title' => $title
        ];
        extract($data);
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/inventory_details_fba/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function delete() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        // 检查权限
        if (!hasPermission('inventory_details_fba.delete')) {
            showError('您没有权限删除FBA库存详情');
            redirect(APP_URL . '/inventory_details_fba.php');
        }
        
        $name = $_GET['name'] ?? '';
        $sku = $_GET['sku'] ?? '';
        
        if (empty($name) || empty($sku)) {
            showError('无效的参数');
            redirect(APP_URL . '/inventory_details_fba.php');
        }
        
        $inventoryDetail = $this->inventoryDetailsFbaModel->getByNameAndSku($name, $sku);
        if (!$inventoryDetail) {
            showError('FBA库存详情记录不存在');
            redirect(APP_URL . '/inventory_details_fba.php');
        }
        
        if ($this->inventoryDetailsFbaModel->delete($name, $sku)) {
            showSuccess('FBA库存详情删除成功');
        } else {
            showError('FBA库存详情删除失败');
        }
        
        redirect(APP_URL . '/inventory_details_fba.php');
    }
    
    public function batchDelete() {
        if (!isLoggedIn()) {
            redirect(APP_URL . '/login.php');
        }
        
        // 检查权限
        if (!hasPermission('inventory_details_fba.delete')) {
            showError('您没有权限批量删除FBA库存详情');
            redirect(APP_URL . '/inventory_details_fba.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(APP_URL . '/inventory_details_fba.php');
        }
        
        $records = $_POST['records'] ?? [];
        if (empty($records)) {
            showError('请选择要删除的记录');
            redirect(APP_URL . '/inventory_details_fba.php');
        }
        
        $successCount = 0;
        foreach ($records as $record) {
            $parts = explode('|', $record);
            if (count($parts) == 2) {
                list($name, $sku) = $parts;
                if ($this->inventoryDetailsFbaModel->delete($name, $sku)) {
                    $successCount++;
                }
            }
        }
        
        showSuccess("成功删除 {$successCount} 条记录");
        redirect(APP_URL . '/inventory_details_fba.php');
    }
}
?>