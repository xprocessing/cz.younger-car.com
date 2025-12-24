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
            <div class="col-md-3">
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