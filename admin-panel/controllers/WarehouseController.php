<?php
require_once ADMIN_PANEL_DIR . '/models/Warehouse.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';

class WarehouseController {
    private $warehouseModel;
    
    public function __construct() {
        $this->warehouseModel = new Warehouse();
        session_start();
    }
    
    public function index() {
        if (!hasPermission('warehouses.view')) {
            showError('您没有权限访问此页面');
            redirect(ADMIN_PANEL_URL . '/dashboard.php');
        }
        
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = 50;
        $offset = ($page - 1) * $limit;
        
        $keyword = $_GET['keyword'] ?? '';
        
        if ($keyword) {
            $warehouses = $this->warehouseModel->search($keyword, $limit, $offset);
            $totalCount = $this->warehouseModel->getSearchCount($keyword);
        } else {
            $warehouses = $this->warehouseModel->getAll($limit, $offset);
            $totalCount = $this->warehouseModel->getCount();
        }
        
        $totalPages = ceil($totalCount / $limit);
        $title = '仓库管理';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/warehouses/index.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function create() {
        if (!hasPermission('warehouses.create')) {
            showError('您没有权限创建仓库');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        $title = '创建仓库';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/warehouses/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function createPost() {
        if (!hasPermission('warehouses.create')) {
            showError('您没有权限创建仓库');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        if (empty($_POST['wid'])) {
            showError('仓库ID不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=create');
        }
        
        if (empty($_POST['name'])) {
            showError('仓库名称不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=create');
        }
        
        if (empty($_POST['type'])) {
            showError('仓库类型不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=create');
        }
        
        if (empty($_POST['sub_type'])) {
            showError('仓库子类型不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=create');
        }
        
        $existingWarehouse = $this->warehouseModel->getById($_POST['wid']);
        if ($existingWarehouse) {
            showError('仓库ID已存在');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=create');
        }
        
        $data = [
            'wid' => $_POST['wid'],
            'type' => $_POST['type'],
            'sub_type' => $_POST['sub_type'],
            'name' => $_POST['name'],
            'is_delete' => $_POST['is_delete'] ?? 0,
            'country_code' => $_POST['country_code'] ?? '',
            'wp_id' => $_POST['wp_id'] ?? '',
            'wp_name' => $_POST['wp_name'] ?? '',
            't_warehouse_name' => $_POST['t_warehouse_name'] ?? '',
            't_warehouse_code' => $_POST['t_warehouse_code'] ?? '',
            't_country_area_name' => $_POST['t_country_area_name'] ?? '',
            't_status' => $_POST['t_status'] ?? ''
        ];
        
        if ($this->warehouseModel->create($data)) {
            setSuccess('仓库创建成功');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        } else {
            showError('仓库创建失败');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=create');
        }
    }
    
    public function edit() {
        if (!hasPermission('warehouses.edit')) {
            showError('您没有权限编辑仓库');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        $wid = $_GET['wid'] ?? '';
        if (empty($wid)) {
            showError('仓库ID不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        $warehouse = $this->warehouseModel->getById($wid);
        if (!$warehouse) {
            showError('仓库不存在');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        $title = '编辑仓库';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/warehouses/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function editPost() {
        if (!hasPermission('warehouses.edit')) {
            showError('您没有权限编辑仓库');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        $wid = $_POST['wid'] ?? '';
        if (empty($wid)) {
            showError('仓库ID不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        if (empty($_POST['name'])) {
            showError('仓库名称不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=edit&wid=' . $wid);
        }
        
        if (empty($_POST['type'])) {
            showError('仓库类型不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=edit&wid=' . $wid);
        }
        
        if (empty($_POST['sub_type'])) {
            showError('仓库子类型不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=edit&wid=' . $wid);
        }
        
        $data = [
            'type' => $_POST['type'],
            'sub_type' => $_POST['sub_type'],
            'name' => $_POST['name'],
            'is_delete' => $_POST['is_delete'] ?? 0,
            'country_code' => $_POST['country_code'] ?? '',
            'wp_id' => $_POST['wp_id'] ?? '',
            'wp_name' => $_POST['wp_name'] ?? '',
            't_warehouse_name' => $_POST['t_warehouse_name'] ?? '',
            't_warehouse_code' => $_POST['t_warehouse_code'] ?? '',
            't_country_area_name' => $_POST['t_country_area_name'] ?? '',
            't_status' => $_POST['t_status'] ?? ''
        ];
        
        if ($this->warehouseModel->update($wid, $data)) {
            setSuccess('仓库更新成功');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        } else {
            showError('仓库更新失败');
            redirect(ADMIN_PANEL_URL . '/warehouses.php?action=edit&wid=' . $wid);
        }
    }
    
    public function delete() {
        if (!hasPermission('warehouses.delete')) {
            showError('您没有权限删除仓库');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        $wid = $_GET['wid'] ?? '';
        if (empty($wid)) {
            showError('仓库ID不能为空');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        $warehouse = $this->warehouseModel->getById($wid);
        if (!$warehouse) {
            showError('仓库不存在');
            redirect(ADMIN_PANEL_URL . '/warehouses.php');
        }
        
        if ($this->warehouseModel->delete($wid)) {
            setSuccess('仓库删除成功');
        } else {
            showError('仓库删除失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/warehouses.php');
    }
}
