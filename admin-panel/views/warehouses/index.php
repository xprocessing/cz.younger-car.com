<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>仓库管理</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/warehouses.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增仓库
        </a>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/warehouses.php" class="row g-3">
            <div class="col-md-6">
                <label for="keyword" class="form-label">关键词搜索</label>
                <input type="text" name="keyword" class="form-control" placeholder="搜索仓库名称、T仓库名称、T仓库代码..." 
                       value="<?php echo $_GET['keyword'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="action_search" class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" name="action" value="search" class="btn btn-outline-primary">
                        <i class="fa fa-search"></i> 搜索
                    </button>
                    <a href="<?php echo APP_URL; ?>/warehouses.php" class="btn btn-outline-secondary">
                        <i class="fa fa-refresh"></i> 重置
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>仓库ID</th>
                        <th>仓库名称</th>
                        <th>类型</th>
                        <th>子类型</th>
                        <th>国家/地区编码</th>
                        <th>WP ID</th>
                        <th>WP名称</th>
                        <th>T仓库名称</th>
                        <th>T仓库代码</th>
                        <th>T国家/地区</th>
                        <th>T状态</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($warehouses)): ?>
                        <?php foreach ($warehouses as $warehouse): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($warehouse['wid']); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['name']); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['type']); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['sub_type']); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['country_code'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['wp_id'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['wp_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['t_warehouse_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['t_warehouse_code'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['t_country_area_name'] ?? '-'); ?></td>
                                <td><?php echo htmlspecialchars($warehouse['t_status'] ?? '-'); ?></td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo APP_URL; ?>/warehouses.php?action=edit&wid=<?php echo $warehouse['wid']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="编辑">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="confirmDelete('<?php echo $warehouse['wid']; ?>')" title="删除">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center">暂无数据</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <?php if (isset($totalPages) && $totalPages > 1): ?>
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <?php
                $currentPage = $page;
                $delta = 2;
                $range = [];
                
                if ($currentPage - $delta > 1) {
                    $range[] = 1;
                    if ($currentPage - $delta > 2) {
                        $range[] = '...';
                    }
                }
                
                for ($i = max(1, $currentPage - $delta); $i <= min($totalPages, $currentPage + $delta); $i++) {
                    $range[] = $i;
                }
                
                if ($currentPage + $delta < $totalPages) {
                    if ($currentPage + $delta < $totalPages - 1) {
                        $range[] = '...';
                    }
                    $range[] = $totalPages;
                }
                
                foreach ($range as $p):
                    if ($p === '...'):
                ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php else: ?>
                    <li class="page-item <?php echo $p == $currentPage ? 'active' : ''; ?>">
                        <a class="page-link" href="<?php echo APP_URL; ?>/warehouses.php?page=<?php echo $p; ?><?php echo !empty($keyword) ? '&keyword=' . urlencode($keyword) : ''; ?>">
                            <?php echo $p; ?>
                        </a>
                    </li>
                <?php
                    endif;
                endforeach;
                ?>
            </ul>
        </nav>
        <?php endif; ?>
    </div>
</div>

<script>
function confirmDelete(wid) {
    if (confirm('确定要删除这个仓库吗？')) {
        window.location.href = '<?php echo APP_URL; ?>/warehouses.php?action=delete&wid=' + wid;
    }
}
</script>
