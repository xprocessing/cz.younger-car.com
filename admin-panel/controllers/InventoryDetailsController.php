<?php
require_once ADMIN_PANEL_DIR . '/models/InventoryDetails.php';
require_once ADMIN_PANEL_DIR . '/helpers/functions.php';
require_once ADMIN_PANEL_DIR . '/includes/RedisCache.php';

class InventoryDetailsController {
    private $inventoryDetailsModel;
    
    public function __construct() {
        $this->inventoryDetailsModel = new InventoryDetails();
        session_start();
    }
    
    public function index() {
        if (!hasPermission('inventory_details.view')) {
            showError('您没有权限访问此页面');
            redirect(ADMIN_PANEL_URL . '/dashboard.php');
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
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        $title = '创建库存明细';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/inventory_details/create.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function createPost() {
        if (!hasPermission('inventory_details.create')) {
            showError('您没有权限创建库存明细');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        if (empty($_POST['wid'])) {
            showError('仓库ID不能为空');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php?action=create');
        }
        
        if (empty($_POST['sku'])) {
            showError('SKU不能为空');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php?action=create');
        }
        
        if (empty($_POST['product_valid_num'])) {
            showError('可用量不能为空');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php?action=create');
        }
        
        $data = [
            'wid' => $_POST['wid'],
            'sku' => $_POST['sku'],
            'product_valid_num' => $_POST['product_valid_num'],
            'quantity_receive' => $_POST['quantity_receive'] ?? '0',
            'average_age' => $_POST['average_age'] ?? 0,
            'purchase_price' => $_POST['purchase_price'] ?? 0,
            'head_stock_price' => $_POST['head_stock_price'] ?? 0,
            'stock_price' => $_POST['stock_price'] ?? 0,
            'product_onway' => $_POST['product_onway'] ?? 0
        ];
        
        if ($this->inventoryDetailsModel->create($data)) {
            setSuccess('库存明细创建成功');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        } else {
            showError('库存明细创建失败');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php?action=create');
        }
    }
    
    public function edit() {
        if (!hasPermission('inventory_details.edit')) {
            showError('您没有权限编辑库存明细');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            showError('库存明细ID不能为空');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        $inventoryDetail = $this->inventoryDetailsModel->getById($id);
        if (!$inventoryDetail) {
            showError('库存明细不存在');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        $title = '编辑库存明细';
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/inventory_details/edit.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    public function editPost() {
        if (!hasPermission('inventory_details.edit')) {
            showError('您没有权限编辑库存明细');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        $id = $_POST['id'] ?? '';
        if (empty($id)) {
            showError('库存明细ID不能为空');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        if (empty($_POST['wid'])) {
            showError('仓库ID不能为空');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['sku'])) {
            showError('SKU不能为空');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php?action=edit&id=' . $id);
        }
        
        if (empty($_POST['product_valid_num'])) {
            showError('可用量不能为空');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php?action=edit&id=' . $id);
        }
        
        $data = [
            'wid' => $_POST['wid'],
            'sku' => $_POST['sku'],
            'product_valid_num' => $_POST['product_valid_num'],
            'quantity_receive' => $_POST['quantity_receive'] ?? '0',
            'average_age' => $_POST['average_age'] ?? 0,
            'purchase_price' => $_POST['purchase_price'] ?? 0,
            'head_stock_price' => $_POST['head_stock_price'] ?? 0,
            'stock_price' => $_POST['stock_price'] ?? 0,
            'product_onway' => $_POST['product_onway'] ?? 0
        ];
        
        if ($this->inventoryDetailsModel->update($id, $data)) {
            setSuccess('库存明细更新成功');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        } else {
            showError('库存明细更新失败');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php?action=edit&id=' . $id);
        }
    }
    
    public function delete() {
        if (!hasPermission('inventory_details.delete')) {
            showError('您没有权限删除库存明细');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        $id = $_GET['id'] ?? '';
        if (empty($id)) {
            showError('库存明细ID不能为空');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        $inventoryDetail = $this->inventoryDetailsModel->getById($id);
        if (!$inventoryDetail) {
            showError('库存明细不存在');
            redirect(ADMIN_PANEL_URL . '/inventory_details.php');
        }
        
        if ($this->inventoryDetailsModel->delete($id)) {
            setSuccess('库存明细删除成功');
        } else {
            showError('库存明细删除失败');
        }
        
        redirect(ADMIN_PANEL_URL . '/inventory_details.php');
    }
    
    public function overagedStats() {
        if (!hasPermission('inventory_details.view')) {
            showError('您没有权限访问此页面');
            redirect(ADMIN_PANEL_URL . '/dashboard.php');
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
            redirect(ADMIN_PANEL_URL . '/dashboard.php');
        }
        
        // 添加不缓存头部
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Cache-Control: post-check=0, pre-check=0', false);
        header('Pragma: no-cache');
        
        // 初始化Redis缓存
        $redisCache = RedisCache::getInstance();
        $cachePrefix = 'inventory_alert:' . date('Y-m-d');
        $cacheExpire = 86400; // 1天缓存
        
        // 分页参数处理
        $page = max(1, (int)($_GET['page'] ?? 1));
        $limit = max(1, min(500, (int)($_GET['limit'] ?? 100))); // 每页最多500条
        $offset = ($page - 1) * $limit;
        
        // 检查是否提交了批量查询表单
        $batchSku = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['batch_sku'])) {
            // 获取SKU列表（每行一个）
            $batchSku = trim($_POST['batch_sku']);
            // 按行分割，并过滤掉空行
            $skuList = array_filter(explode("\n", $batchSku), function($item) {
                return !empty(trim($item));
            });
            // 去除每个SKU的前后空格
            $skuList = array_map('trim', $skuList);
            
            // 只有当SKU列表不为空时才进行批量查询
                if (!empty($skuList)) {
                    // 调用批量查询方法，添加分页支持
                    $totalCount = 0;
                    $inventoryAlerts = $this->inventoryDetailsModel->getInventoryAlertBySkuList($skuList, $limit, $offset, $totalCount);
                    
                    // 保存批量查询的SKU列表到会话中，用于导出功能和分页
                    $_SESSION['batch_sku_list'] = $skuList;
                    
                    // 设置批量查询标记为true，用于后续统计数据的计算
                    $hasBatchQuery = true;
                } else {
                    // 如果SKU列表为空，清除会话中的批量查询数据，并查询所有数据
                    unset($_SESSION['batch_sku_list']);
                    $totalCount = 0;
                    $inventoryAlerts = $this->inventoryDetailsModel->getInventoryAlert($limit, $offset, $totalCount);
                }
        } else {
            // 当用户直接打开或刷新页面（GET请求）时，总是查询所有数据，不使用会话中的批量查询条件
            // 清除会话中的批量查询数据，确保下次打开页面时显示所有数据
            unset($_SESSION['batch_sku_list']);
            
            // 生成缓存键
            $cacheKey = $cachePrefix . ':list:' . $page . ':' . $limit;
            $totalCountCacheKey = $cachePrefix . ':total_count';
            
            // 尝试从缓存获取数据
            $cachedData = $redisCache->get($cacheKey);
            $totalCount = $redisCache->get($totalCountCacheKey);
            
            if ($cachedData && $totalCount) {
                $inventoryAlerts = $cachedData;
            } else {
                // 查询所有数据
                $totalCount = 0;
                $inventoryAlerts = $this->inventoryDetailsModel->getInventoryAlert($limit, $offset, $totalCount);
                
                // 缓存数据
                $redisCache->set($cacheKey, $inventoryAlerts, $cacheExpire);
                $redisCache->set($totalCountCacheKey, $totalCount, $cacheExpire);
            }
        }
        
        // 计算总页数
        $totalPages = ceil($totalCount / $limit);
        
        // 再次过滤掉全零的SKU，作为双重保障
        $filteredInventoryAlerts = [];
        foreach ($inventoryAlerts as $alert) {
            if ($alert['product_valid_num_excluding_wenzhou'] > 0 || $alert['product_onway_excluding_wenzhou'] > 0 || 
                $alert['product_valid_num_wenzhou'] > 0 || $alert['quantity_receive_wenzhou'] > 0 || $alert['outbound_30days'] > 0) {
                $filteredInventoryAlerts[] = $alert;
            }
        }
        $inventoryAlerts = $filteredInventoryAlerts;
        
        // 获取所有数据的统计信息，用于显示在卡片中
        // 无论是否分页，都获取所有数据的总和
        $totalStats = [];
        
        // 确保$hasBatchQuery和$skuList变量在前面的处理中已经正确设置
        // 如果是POST请求且有批量查询数据，$hasBatchQuery应该已经被设置为true
        // 由于在GET请求时已经清除了会话中的批量查询数据，所以这里不需要再检查会话
        if (!isset($hasBatchQuery)) {
            $hasBatchQuery = false;
        }
        
        // 只有在有批量查询标记的情况下，才需要确保$skuList变量被正确设置
        if ($hasBatchQuery && empty($skuList)) {
            $skuList = [];
        }
        
        // 根据是否有批量查询条件，获取对应的统计数据
        if ($hasBatchQuery) {
            // 如果有批量查询条件，获取所有符合条件的SKU的统计数据
            $allInventoryAlerts = $this->inventoryDetailsModel->getInventoryAlertBySkuList($skuList);
            
            // 计算所有数据的总和
            $totalStats['product_valid_num_excluding_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_valid_num_excluding_wenzhou'));
            $totalStats['product_onway_excluding_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_onway_excluding_wenzhou'));
            $totalStats['product_valid_num_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_valid_num_wenzhou'));
            $totalStats['quantity_receive_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'quantity_receive_wenzhou'));
            $totalStats['outbound_30days'] = array_sum(array_column($allInventoryAlerts, 'outbound_30days'));
            $totalStats['sku_count'] = count($allInventoryAlerts);
        } else {
            // 生成统计数据缓存键
            $statsCacheKey = $cachePrefix . ':stats';
            
            // 尝试从缓存获取统计数据
            $cachedStats = $redisCache->get($statsCacheKey);
            
            if ($cachedStats) {
                $totalStats = $cachedStats;
            } else {
                // 否则获取所有库存预警数据的统计信息
                $allInventoryAlerts = $this->inventoryDetailsModel->getInventoryAlert();
                
                // 计算所有数据的总和
                $totalStats['product_valid_num_excluding_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_valid_num_excluding_wenzhou'));
                $totalStats['product_onway_excluding_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_onway_excluding_wenzhou'));
                $totalStats['product_valid_num_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'product_valid_num_wenzhou'));
                $totalStats['quantity_receive_wenzhou'] = array_sum(array_column($allInventoryAlerts, 'quantity_receive_wenzhou'));
                $totalStats['outbound_30days'] = array_sum(array_column($allInventoryAlerts, 'outbound_30days'));
                $totalStats['sku_count'] = count($allInventoryAlerts);
                
                // 缓存统计数据
                $redisCache->set($statsCacheKey, $totalStats, $cacheExpire);
            }
        }
        
        $title = '库存预警（海外仓）';
        
        // 准备分页相关的变量
        $pagination = [
            'totalCount' => $totalCount,
            'totalPages' => $totalPages,
            'currentPage' => $page,
            'pageSize' => $limit,
            'nextPage' => $page < $totalPages ? $page + 1 : null,
            'prevPage' => $page > 1 ? $page - 1 : null
        ];
        
        include VIEWS_DIR . '/layouts/header.php';
        include VIEWS_DIR . '/inventory_details/inventory_alert.php';
        include VIEWS_DIR . '/layouts/footer.php';
    }
    
    // 导出库存预警数据
    public function exportInventoryAlert() {
        if (!hasPermission('inventory_details.view')) {
            showError('您没有权限访问此页面');
            redirect(ADMIN_PANEL_URL . '/dashboard.php');
        }
        
        // 检查会话中是否有批量查询的SKU列表
        if (isset($_SESSION['batch_sku_list']) && !empty($_SESSION['batch_sku_list'])) {
            // 使用批量查询方法获取数据
            $inventoryAlerts = $this->inventoryDetailsModel->getInventoryAlertBySkuList($_SESSION['batch_sku_list']);
        } else {
            // 如果没有批量查询，则获取所有库存预警数据
            $inventoryAlerts = $this->inventoryDetailsModel->getInventoryAlert();
        }
        
        // 再次过滤掉全零的SKU，作为双重保障
        $filteredInventoryAlerts = [];
        foreach ($inventoryAlerts as $alert) {
            if ($alert['product_valid_num_excluding_wenzhou'] > 0 || $alert['product_onway_excluding_wenzhou'] > 0 || 
                $alert['product_valid_num_wenzhou'] > 0 || $alert['quantity_receive_wenzhou'] > 0 || $alert['outbound_30days'] > 0) {
                $filteredInventoryAlerts[] = $alert;
            }
        }
        $inventoryAlerts = $filteredInventoryAlerts;
        
        // 设置CSV输出头
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="inventory_alert_export_' . date('Y-m-d_H-i-s') . '.csv"');
        
        // 打开输出流
        $output = fopen('php://output', 'w');
        
        // 设置UTF-8 BOM，解决中文乱码问题
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // 写入CSV头部
        $header = [
            'SKU',
            '商品名称',
            '可用量（不含温州仓）',
            '调拨在途（不含温州仓）',
            '可用量（温州仓）',
            '待到货量（温州仓）',
            '最近30天出库量'
        ];
        fputcsv($output, $header);
        
        // 写入数据
        foreach ($inventoryAlerts as $alert) {
            $row = [
                $alert['sku'],
                $alert['product_name'] ?? '',
                $alert['product_valid_num_excluding_wenzhou'],
                $alert['product_onway_excluding_wenzhou'],
                $alert['product_valid_num_wenzhou'],
                $alert['quantity_receive_wenzhou'],
                $alert['outbound_30days']
            ];
            fputcsv($output, $row);
        }
        
        fclose($output);
        exit;
    }
}
