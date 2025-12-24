<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>订单利润管理</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/order_profit.php?action=import" class="btn btn-outline-success me-2">
            <i class="fa fa-upload"></i> 批量导入
        </a>
        <a href="<?php echo APP_URL; ?>/order_profit.php?action=stats" class="btn btn-outline-info me-2">
            <i class="fa fa-bar-chart"></i> 利润统计
        </a>
        <a href="<?php echo APP_URL; ?>/order_profit.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增订单利润
        </a>
    </div>
</div>

<!-- 搜索和筛选框 -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/order_profit.php" class="row g-3">
            <div class="col-md-3">
                <label for="keyword" class="form-label">关键词搜索</label>
                <input type="text" name="keyword" class="form-control" placeholder="搜索订单号、SKU..." 
                       value="<?php echo $_GET['keyword'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="store_id" class="form-label">店铺筛选</label>
                <select name="store_id" class="form-select">
                    <option value="">全部店铺</option>
                    <?php if (!empty($storeList)): ?>
                        <?php foreach ($storeList as $store): ?>
                            <option value="<?php echo htmlspecialchars($store); ?>" 
                                    <?php echo (($_GET['store_id'] ?? '') == $store ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($store); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="rate_min" class="form-label">
                    <i class="fa fa-percent text-success"></i> 最小利润率(%)
                </label>
                <input type="number" name="rate_min" class="form-control" placeholder="0.00" step="0.01"
                       value="<?php echo $_GET['rate_min'] ?? ''; ?>"
                       title="输入最小利润率，用于筛选利润率大于等于此值的订单">
                <div class="form-text">≥ 此值%</div>
            </div>
            <div class="col-md-2">
                <label for="rate_max" class="form-label">
                    <i class="fa fa-percent text-danger"></i> 最大利润率(%)
                </label>
                <input type="number" name="rate_max" class="form-control" placeholder="100.00" step="0.01"
                       value="<?php echo $_GET['rate_max'] ?? ''; ?>"
                       title="输入最大利润率，用于筛选利润率小于等于此值的订单">
                <div class="form-text">≤ 此值%</div>
            </div>
            <div class="col-md-1">
                <label for="action_search" class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" name="action" value="search" class="btn btn-outline-primary">
                        <i class="fa fa-search"></i> 搜索
                    </button>
                    <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-outline-secondary">
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
            // 构建基础URL，保持现有的筛选条件
            $baseUrl = APP_URL . '/order_profit.php?';
            $params = [];
            if (!empty($_GET['keyword'])) $params[] = 'keyword=' . urlencode($_GET['keyword']);
            if (!empty($_GET['store_id'])) $params[] = 'store_id=' . urlencode($_GET['store_id']);
            $baseQuery = implode('&', $params);
            if ($baseQuery) $baseQuery .= '&';
            ?>
            
            <div class="col-auto">
                <a href="<?php echo $baseUrl . $baseQuery; ?>rate_min=0&rate_max=10" class="btn btn-sm btn-outline-primary">
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
                <a href="<?php echo $baseUrl . $baseQuery; ?>rate_min=50" class="btn btn-sm btn-outline-danger">
                    > 50%
                </a>
            </div>
            <div class="col-auto">
                <a href="<?php echo $baseUrl . $baseQuery; ?>rate_min=-100&rate_max=0" class="btn btn-sm btn-outline-secondary">
                    亏损订单 (< 0%)
                </a>
            </div>
        </div>
    </div>
</div>

<!-- 调试信息 -->
<?php if (!empty($_GET['rate_min']) || !empty($_GET['rate_max'])): ?>
<div class="alert alert-warning" role="alert">
    <strong>调试信息：</strong>
    筛选参数: rate_min="<?php echo htmlspecialchars($_GET['rate_min'] ?? ''); ?>", 
    rate_max="<?php echo htmlspecialchars($_GET['rate_max'] ?? ''); ?>"
</div>
<?php endif; ?>

<!-- 当前筛选条件 -->
<?php 
$hasFilters = !empty($_GET['keyword']) || !empty($_GET['store_id']) || !empty($_GET['rate_min']) || !empty($_GET['rate_max']);
if ($hasFilters): ?>
<div class="alert alert-info d-flex align-items-center" role="alert">
    <i class="fa fa-filter me-2"></i>
    <div class="me-auto">
        <strong>当前筛选条件：</strong>
        <?php 
        $filters = [];
        if (!empty($_GET['keyword'])) $filters[] = "关键词: " . htmlspecialchars($_GET['keyword']);
        if (!empty($_GET['store_id'])) $filters[] = "店铺: " . htmlspecialchars($_GET['store_id']);
        if (!empty($_GET['rate_min'])) $filters[] = "最小利润率: " . htmlspecialchars($_GET['rate_min']) . "%";
        if (!empty($_GET['rate_max'])) $filters[] = "最大利润率: " . htmlspecialchars($_GET['rate_max']) . "%";
        echo implode(' | ', $filters);
        ?>
    </div>
    <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-sm btn-outline-secondary">
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
                        <th>收货国家</th>
                        <th>下单时间</th>
                        <th>SKU</th>
                        <th>订单总额</th>
                        <th>毛利润</th>
                        <th>利润率</th>
                        <th>实际成本</th>
                        <th>实际运费</th>
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
                                        <?php echo htmlspecialchars($profit['store_id'] ?? ''); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="https://erp.lingxing.com/erp/mmulti/mpOrderDetail?orderSn=<?php echo htmlspecialchars($profit['global_order_no'] ?? ''); ?>" target="_blank">
                                        <strong><?php echo htmlspecialchars($profit['global_order_no'] ?? ''); ?></strong>
                                    </a>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($profit['receiver_country'] ?? ''); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($profit['global_purchase_time'] ?? ''); ?>
                                </td>
                                <td>
                                    <code><?php echo htmlspecialchars($profit['local_sku'] ?? ''); ?></code>
                                </td>
                                <td>
                                    <span class="text-success fw-bold">
                                        ¥<?php echo number_format($profit['order_total_amount'] ?? 0, 2); ?>
                                    </span>
                                </td>
                                <td>
                                    <?php $profitAmount = $profit['profit_amount'] ?? 0; ?>
                                    <span class="<?php echo $profitAmount >= 0 ? 'text-success' : 'text-danger'; ?> fw-bold">
                                        ¥<?php echo number_format($profitAmount, 2); ?>
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
                                        ¥<?php echo number_format($profit['wms_outbound_cost_amount'] ?? 0, 2); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-danger">
                                        ¥<?php echo number_format($profit['wms_shipping_price_amount'] ?? 0, 2); ?>
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted">
                                        <?php echo htmlspecialchars($profit['update_time'] ?? ''); ?>
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo APP_URL; ?>/order_profit.php?action=edit&id=<?php echo $profit['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="编辑">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" 
                                           onclick="if(confirm('确定要删除这条订单利润记录吗？')) {
                                               window.location.href='<?php echo APP_URL; ?>/order_profit.php?action=delete&id=<?php echo $profit['id']; ?>';
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
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo !empty($_GET['store_id']) ? '&store_id=' . urlencode($_GET['store_id']) : ''; ?>">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo !empty($_GET['store_id']) ? '&store_id=' . urlencode($_GET['store_id']) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?><?php echo !empty($_GET['store_id']) ? '&store_id=' . urlencode($_GET['store_id']) : ''; ?>">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<!-- 统计摘要 -->
<?php if (!empty($profits)): ?>
<div class="card mt-3">
    <div class="card-header">
        <h5 class="mb-0">当前页面统计</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-primary"><?php echo count($profits); ?></h5>
                        <p class="card-text">订单数量</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-success">
                            ¥<?php echo number_format(array_sum(array_column($profits, 'order_total_amount')), 2); ?>
                        </h5>
                        <p class="card-text">总订单金额</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-info">
                            ¥<?php echo number_format(array_sum(array_column($profits, 'profit_amount')), 2); ?>
                        </h5>
                        <p class="card-text">总毛利润</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-center">
                    <div class="card-body">
                        <h5 class="card-title text-warning">
                            <?php 
                            $totalAmount = array_sum(array_column($profits, 'order_total_amount'));
                            $totalProfit = array_sum(array_column($profits, 'profit_amount'));
                            $avgRate = $totalAmount > 0 ? ($totalProfit / $totalAmount * 100) : 0;
                            echo number_format($avgRate, 2); ?>%
                        </h5>
                        <p class="card-text">平均利润率</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// 表格行点击查看详情
document.querySelectorAll('tbody tr').forEach(function(row) {
    row.style.cursor = 'pointer';
    row.addEventListener('click', function(e) {
        if (e.target.tagName === 'A' || e.target.closest('a')) {
            return;
        }
        const orderId = this.querySelector('td:nth-child(3)').textContent.trim();
        if (orderId) {
            // 可以在这里添加查看详情的逻辑
            console.log('查看订单详情:', orderId);
        }
    });
});
</script>