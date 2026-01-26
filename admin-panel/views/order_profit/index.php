<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>订单利润管理</h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_profit.php?action=import" class="btn btn-outline-success me-2">
            <i class="fa fa-upload"></i> 批量导入
        </a>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_profit.php?action=export<?php 
            $exportParams = [];
            if (!empty($_GET['keyword'])) $exportParams[] = 'keyword=' . urlencode($_GET['keyword']);
            if (!empty($_GET['platform_name'])) $exportParams[] = 'platform_name=' . urlencode($_GET['platform_name']);
            if (!empty($_GET['store_id'])) $exportParams[] = 'store_id=' . urlencode($_GET['store_id']);
            if (!empty($_GET['warehouse_name'])) $exportParams[] = 'warehouse_name=' . urlencode($_GET['warehouse_name']);
            if (!empty($_GET['start_date'])) $exportParams[] = 'start_date=' . urlencode($_GET['start_date']);
            if (!empty($_GET['end_date'])) $exportParams[] = 'end_date=' . urlencode($_GET['end_date']);
            if (isset($_GET['rate_min'])) $exportParams[] = 'rate_min=' . urlencode($_GET['rate_min']);
            if (isset($_GET['rate_max'])) $exportParams[] = 'rate_max=' . urlencode($_GET['rate_max']);
            if (!empty($exportParams)) echo '&' . implode('&', $exportParams);
        ?>" class="btn btn-outline-primary me-2">
            <i class="fa fa-download"></i> 批量导出
        </a>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_profit.php?action=stats" class="btn btn-outline-info me-2">
            <i class="fa fa-bar-chart"></i> 利润统计
        </a>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_profit.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增订单利润
        </a>
    </div>
</div>

<!-- 搜索和筛选框 -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo ADMIN_PANEL_URL; ?>/order_profit.php" class="row g-3">
            <div class="col-md-2">
                <label for="keyword" class="form-label">关键词搜索</label>
                <input type="text" name="keyword" class="form-control" placeholder="搜索订单号、SKU..." 
                       value="<?php echo $_GET['keyword'] ?? ''; ?>">
            </div>
            <div class="col-md-1">
                <label for="platform_name" class="form-label">平台名称</label>
                <select name="platform_name" class="form-select">
                    <option value="">全部平台</option>
                    <?php 
                    // 提取唯一的平台名称
                    $platformNames = [];
                    if (!empty($storeList)) {
                        foreach ($storeList as $store) {
                            if (!in_array($store['platform_name'], $platformNames)) {
                                $platformNames[] = $store['platform_name'];
                            }
                        }
                        sort($platformNames);
                    }
                    ?>
                    <?php foreach ($platformNames as $platform): ?>
                        <option value="<?php echo htmlspecialchars($platform); ?>" 
                                <?php echo (($_GET['platform_name'] ?? '') == $platform ? 'selected' : ''); ?>>
                            <?php echo htmlspecialchars($platform); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label for="store_id" class="form-label">店铺筛选</label>
                <select name="store_id" class="form-select">
                    <option value="">全部店铺</option>
                    <?php if (!empty($storeList)): ?>
                        <?php foreach ($storeList as $store): ?>
                            <?php 
                            // 根据平台筛选店铺
                            $showStore = true;
                            if (isset($_GET['platform_name']) && !empty($_GET['platform_name'])) {
                                if ($store['platform_name'] != $_GET['platform_name']) {
                                    $showStore = false;
                                }
                            }
                            if ($showStore): ?>
                                <option value="<?php echo htmlspecialchars($store['store_id']); ?>" 
                                        <?php echo (($_GET['store_id'] ?? '') == $store['store_id'] ? 'selected' : ''); ?>>
                                    <?php echo htmlspecialchars($store['platform_name'] . ' - ' . $store['store_name']); ?>
                                </option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-1">
                <label for="warehouse_name" class="form-label">发货仓库</label>
                <select name="warehouse_name" class="form-select">
                    <option value="">全部仓库</option>
                    <?php if (!empty($warehouseList)): ?>
                        <?php foreach ($warehouseList as $warehouse): ?>
                            <option value="<?php echo htmlspecialchars($warehouse['warehouse_name']); ?>" 
                                    <?php echo (($_GET['warehouse_name'] ?? '') == $warehouse['warehouse_name'] ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($warehouse['warehouse_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="start_date" class="form-label">下单时间起始</label>
                <input type="date" name="start_date" class="form-control"
                       value="<?php echo $_GET['start_date'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="end_date" class="form-label">下单时间结束</label>
                <input type="date" name="end_date" class="form-control"
                       value="<?php echo $_GET['end_date'] ?? ''; ?>">
            </div>
            <div class="col-md-1">
                <label for="rate_min" class="form-label">
                    最小利润率(%)
                </label>
                <input type="number" name="rate_min" class="form-control" placeholder="-100" step="0.01"
                       value="<?php echo $_GET['rate_min'] ?? ''; ?>"
                       title="输入最小利润率，用于筛选利润率大于等于此值的订单">
               
            </div>
            <div class="col-md-1">
                <label for="rate_max" class="form-label">
                     最大利润率(%)
                </label>
                <input type="number" name="rate_max" class="form-control" placeholder="100.00" step="0.01"
                       value="<?php echo $_GET['rate_max'] ?? ''; ?>"
                       title="输入最大利润率，用于筛选利润率小于等于此值的订单">
                
            </div>
            <div class="col-md-1">
                <label for="action_search" class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" name="action" value="search" class="btn btn-outline-primary">
                        <i class="fa fa-search"></i> 搜索
                    </button>
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/order_profit.php" class="btn btn-outline-secondary">
                        <i class="fa fa-refresh"></i> 重置
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 利润率快捷筛选 -->
<div class="card mb-3">
    <div class="card-body">
        <h6 class="card-title mb-3">
            <i class="fa fa-filter me-2"></i>利润率快捷筛选
        </h6>
        <div class="row g-2">
            <?php 
            // 构建基础URL，保持现有的筛选条件，但排除rate_min和rate_max，因为这些会被快捷筛选覆盖
            $baseUrl = ADMIN_PANEL_URL . '/order_profit.php?';
            $params = [];
            if (!empty($_GET['keyword'])) $params[] = 'keyword=' . urlencode($_GET['keyword']);
            if (!empty($_GET['platform_name'])) $params[] = 'platform_name=' . urlencode($_GET['platform_name']);
            if (!empty($_GET['store_id'])) $params[] = 'store_id=' . urlencode($_GET['store_id']);
            if (!empty($_GET['warehouse_name'])) $params[] = 'warehouse_name=' . urlencode($_GET['warehouse_name']);
            if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
            if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
            $baseQuery = implode('&', $params);
            if ($baseQuery) $baseQuery .= '&';
            ?>
            
            <div class="col-auto">
                <a href="<?php echo $baseUrl . $baseQuery; ?>rate_min=0&rate_max=10" class="btn btn-sm btn-outline-danger">
                    0% - 10%
                </a>
            </div>
            <div class="col-auto">
                <a href="<?php echo $baseUrl . $baseQuery; ?>rate_min=10&rate_max=20" class="btn btn-sm btn-outline-success">
                    10% - 20%
                </a>
            </div>
            <div class="col-auto">
                <a href="<?php echo $baseUrl . $baseQuery; ?>rate_min=20&rate_max=30" class="btn btn-sm btn-outline-info">
                    20% - 30%
                </a>
            </div>
            <div class="col-auto">
                <a href="<?php echo $baseUrl . $baseQuery; ?>rate_min=30&rate_max=50" class="btn btn-sm btn-outline-warning">
                    30% - 50%
                </a>
            </div>
            <div class="col-auto">
                <a href="<?php echo $baseUrl . $baseQuery; ?>rate_min=50" class="btn btn-sm btn-outline-warning">
                    > 50%
                </a>
            </div>
            <div class="col-auto">
                <a href="<?php echo $baseUrl . $baseQuery; ?>rate_min=-100&rate_max=0" class="btn btn-sm btn-outline-danger">
                    亏损订单 (< 0%)
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 当前筛选条件 -->
<?php 
$hasFilters = !empty($_GET['keyword']) || !empty($_GET['platform_name']) || !empty($_GET['store_id']) || !empty($_GET['warehouse_name']) || !empty($_GET['start_date']) || !empty($_GET['end_date']) || isset($_GET['rate_min']) || isset($_GET['rate_max']);
if ($hasFilters): ?>
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="fa fa-filter me-2"></i>
    <div class="me-auto">
        <strong>当前筛选条件：</strong>
        <?php 
        $filters = [];
        if (!empty($_GET['keyword'])) $filters[] = "关键词: " . htmlspecialchars($_GET['keyword']);
        if (!empty($_GET['platform_name'])) $filters[] = "平台: " . htmlspecialchars($_GET['platform_name']);
        if (!empty($_GET['store_id'])) {
            // 查找对应的店铺名称
            $storeName = $_GET['store_id'];
            foreach ($storeList as $store) {
                if ($store['store_id'] == $_GET['store_id']) {
                    $storeName = $store['platform_name'] . '-' . $store['store_name'];
                    break;
                }
            }
            $filters[] = "店铺: " . htmlspecialchars($storeName);
        }
        if (!empty($_GET['warehouse_name'])) $filters[] = "发货仓库: " . htmlspecialchars($_GET['warehouse_name']);
        // 添加下单时间筛选条件显示
        if (!empty($_GET['start_date']) || !empty($_GET['end_date'])) {
            $dateFilter = "下单时间: ";
            if (!empty($_GET['start_date'])) {
                $dateFilter .= htmlspecialchars($_GET['start_date']);
            }
            if (!empty($_GET['start_date']) && !empty($_GET['end_date'])) {
                $dateFilter .= " - ";
            }
            if (!empty($_GET['end_date'])) {
                $dateFilter .= htmlspecialchars($_GET['end_date']);
            }
            $filters[] = $dateFilter;
        }
        if (isset($_GET['rate_min'])) $filters[] = "最小利润率: " . htmlspecialchars($_GET['rate_min']) . "%";
        if (isset($_GET['rate_max'])) $filters[] = "最大利润率: " . htmlspecialchars($_GET['rate_max']) . "%";
        echo implode(' | ', $filters);
        ?>
    </div>
    <a href="<?php echo ADMIN_PANEL_URL; ?>/order_profit.php" class="btn btn-sm btn-outline-secondary">
        <i class="fa fa-times"></i> 清除筛选
    </a>
</div>
<?php endif; ?>

<!-- 数据表格 -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>店铺ID</th>
                        <th>订单号</th>
                        <th>仓库ID</th>
                        <th>发货仓库</th>
                        <th>收货国家</th>
                        <th>下单时间</th>
                        <th>SKU</th>
                        <th>商品图片</th>
                        <th>订单总额</th>
                        <th>毛利润</th>
                        <th>利润率</th>
                        <th>出库成本</th>
                        <th>采购成本</th>
                        <th>实际运费</th>
                        <th>交易费</th>                        
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($profits)): ?>
                        <tr>
                            <td colspan="14" class="text-center">暂无数据</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($profits as $profit): ?>
                            <tr>
                                <td><?php echo $profit['id']; ?></td>
                                <td>
                                    <span class="badge bg-info">
                                        <?php echo htmlspecialchars(($profit['platform_name'] ?? '') . '-' . ($profit['store_name'] ?? '')); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="https://erp.lingxing.com/erp/mmulti/mpOrderDetail?orderSn=<?php echo htmlspecialchars($profit['global_order_no'] ?? ''); ?>" target="_blank" title="在ERP中查看订单详情">
                                        <strong><?php echo htmlspecialchars($profit['global_order_no'] ?? ''); ?></strong>
                                    </a>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        <?php echo htmlspecialchars($profit['wid'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-primary">
                                        <?php echo htmlspecialchars($profit['warehouse_name'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($profit['receiver_country'] ?? ''); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($profit['global_purchase_time'] ?? ''); ?>
                                </td>
                                <td>
                                    <code><a href="products.php?keyword=<?php echo urlencode($profit['local_sku'] ?? ''); ?>" target="_blank" title="查看商品"><?php echo htmlspecialchars($profit['local_sku'] ?? ''); ?></a></code>
                                </td>
                                <td>
                                    <?php if (!empty($profit['product_image'])): ?>
                                        <div class="image-zoom-container" style="display: inline-block; position: relative; width: 60px; height: 60px; ">
                                            <img src="<?php echo htmlspecialchars($profit['product_image']); ?>" alt="商品图片" width="60" height="60" class="img-thumbnail" title="查看商品图片" style="transition: transform 0.3s ease; cursor: pointer;" onmouseover="this.style.transform='scale(3)'; this.style.zIndex='1000'; this.style.position='relative';" onmouseout="this.style.transform='scale(1)'; this.style.zIndex='1'; this.style.position='relative';">
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">无图片</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="text-success fw-bold">
                                        <?php echo htmlspecialchars($profit['order_total_amount'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $profitAmount = $profit['profit_amount'] ?? ''; ?>
                                    <span class="<?php echo ($profit['profit_rate'] ?? 0) >= 0 ? 'text-success' : 'text-danger'; ?> fw-bold">
                                        <?php echo htmlspecialchars($profitAmount); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $profitRate = $profit['profit_rate'] ?? 0; ?>
                                    <span class="badge bg-<?php echo $profitRate >= 0 ? 'success' : 'danger'; ?>">
                                        <?php echo number_format($profitRate, 2); ?>%
                                    </span>
                                </td>
                                <td>
                                    <span class="text-danger">
                                        <?php echo htmlspecialchars($profit['wms_outbound_cost_amount'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-danger">
                                        <?php echo htmlspecialchars($profit['cg_price_amount'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-danger">
                                        <?php echo htmlspecialchars($profit['wms_shipping_price_amount'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-danger">
                                        <?php echo htmlspecialchars($profit['transaction_fee_amount'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($profit['update_time'] ?? ''); ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_profit.php?action=edit&id=<?php echo $profit['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="编辑">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" 
                                           onclick="if(confirm('确定要删除这条订单利润记录吗？')) {
                                               window.location.href='<?php echo ADMIN_PANEL_URL; ?>/order_profit.php?action=delete&id=<?php echo $profit['id']; ?>';
                                           }" 
                                           class="btn btn-sm btn-outline-danger" title="删除">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- 分页 -->
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php if ($page > 1): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo !empty($_GET['platform_name']) ? '&platform_name=' . urlencode($_GET['platform_name']) : ''; ?><?php echo !empty($_GET['store_id']) ? '&store_id=' . urlencode($_GET['store_id']) : ''; ?><?php echo !empty($_GET['start_date']) ? '&start_date=' . urlencode($_GET['start_date']) : ''; ?><?php echo !empty($_GET['end_date']) ? '&end_date=' . urlencode($_GET['end_date']) : ''; ?><?php echo isset($_GET['rate_min']) ? '&rate_min=' . urlencode($_GET['rate_min']) : ''; ?><?php echo isset($_GET['rate_max']) ? '&rate_max=' . urlencode($_GET['rate_max']) : ''; ?>">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo !empty($_GET['platform_name']) ? '&platform_name=' . urlencode($_GET['platform_name']) : ''; ?><?php echo !empty($_GET['store_id']) ? '&store_id=' . urlencode($_GET['store_id']) : ''; ?><?php echo !empty($_GET['start_date']) ? '&start_date=' . urlencode($_GET['start_date']) : ''; ?><?php echo !empty($_GET['end_date']) ? '&end_date=' . urlencode($_GET['end_date']) : ''; ?><?php echo isset($_GET['rate_min']) ? '&rate_min=' . urlencode($_GET['rate_min']) : ''; ?><?php echo isset($_GET['rate_max']) ? '&rate_max=' . urlencode($_GET['rate_max']) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo !empty($_GET['platform_name']) ? '&platform_name=' . urlencode($_GET['platform_name']) : ''; ?><?php echo !empty($_GET['store_id']) ? '&store_id=' . urlencode($_GET['store_id']) : ''; ?><?php echo !empty($_GET['start_date']) ? '&start_date=' . urlencode($_GET['start_date']) : ''; ?><?php echo !empty($_GET['end_date']) ? '&end_date=' . urlencode($_GET['end_date']) : ''; ?><?php echo isset($_GET['rate_min']) ? '&rate_min=' . urlencode($_GET['rate_min']) : ''; ?><?php echo isset($_GET['rate_max']) ? '&rate_max=' . urlencode($_GET['rate_max']) : ''; ?>">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

