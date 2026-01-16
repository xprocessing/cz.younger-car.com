<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>店铺管理</h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/store.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增店铺
        </a>
    </div>
</div>

<!-- 搜索框 -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo ADMIN_PANEL_URL; ?>/store.php" class="row g-3">
            <div class="col-md-6">
                <label for="keyword" class="form-label">关键词搜索</label>
                <input type="text" name="keyword" class="form-control" placeholder="搜索店铺ID、店铺名称、平台名称..." 
                       value="<?php echo $_GET['keyword'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="action_search" class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" name="action" value="search" class="btn btn-outline-primary">
                        <i class="fa fa-search"></i> 搜索
                    </button>
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/store.php" class="btn btn-outline-secondary">
                        <i class="fa fa-refresh"></i> 重置
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- 店铺列表表格 -->
<div class="card mb-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>店铺ID</th>
                        <th>店铺编号</th>
                        <th>店铺名称</th>
                        <th>平台编码</th>
                        <th>平台名称</th>
                        <th>货币类型</th>
                        <th>是否同步</th>
                        <th>状态</th>
                        <th>国家/地区编码</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($stores)): ?>
                        <?php foreach ($stores as $store): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($store['store_id']); ?></td>
                                <td><?php echo htmlspecialchars($store['sid'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($store['store_name']); ?></td>
                                <td><?php echo htmlspecialchars($store['platform_code']); ?></td>
                                <td><?php echo htmlspecialchars($store['platform_name']); ?></td>
                                <td><?php echo htmlspecialchars($store['currency']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $store['is_sync'] == 1 ? 'success' : 'secondary'; ?>">
                                        <?php echo $store['is_sync'] == 1 ? '是' : '否'; ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-<?php echo $store['status'] == 1 ? 'success' : 'danger'; ?>">
                                        <?php echo $store['status'] == 1 ? '正常' : '异常'; ?>
                                    </span>
                                </td>
                                <td><?php echo htmlspecialchars($store['country_code'] ?? '-'); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo ADMIN_PANEL_URL; ?>/store.php?action=edit&id=<?php echo $store['store_id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="编辑">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete('<?php echo $store['store_id']; ?>')" title="删除">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="10" class="text-center">暂无数据</td>
                        </tr>
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
                        <a class="page-link" href="?page=<?php echo $page - 1; ?><?php echo !empty($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?>">
                            <i class="fa fa-chevron-left"></i>
                        </a>
                    </li>
                <?php endif; ?>
                
                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                        <a class="page-link" href="?page=<?php echo $i; ?><?php echo !empty($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    </li>
                <?php endfor; ?>
                
                <?php if ($page < $totalPages): ?>
                    <li class="page-item">
                        <a class="page-link" href="?page=<?php echo $page + 1; ?><?php echo !empty($_GET['keyword']) ? '&keyword=' . urlencode($_GET['keyword']) : ''; ?>">
                            <i class="fa fa-chevron-right"></i>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(storeId) {
    if (confirm('确定要删除此店铺吗？此操作不可恢复。')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '<?php echo ADMIN_PANEL_URL; ?>/store.php?action=delete';
        form.innerHTML = '<input type="hidden" name="id" value="' + storeId + '">';
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
