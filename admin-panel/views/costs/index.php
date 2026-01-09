<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo APP_URL; ?>/costs.php?action=import" class="btn btn-outline-success me-2">
            <i class="fa fa-upload"></i> 批量导入
        </a>
        <a href="<?php echo APP_URL; ?>/costs.php?action=export<?php 
            $exportParams = [];
            if (!empty($_GET['platform_name'])) $exportParams[] = 'platform_name=' . urlencode($_GET['platform_name']);
            if (!empty($_GET['store_name'])) $exportParams[] = 'store_name=' . urlencode($_GET['store_name']);
            if (!empty($_GET['start_date'])) $exportParams[] = 'start_date=' . urlencode($_GET['start_date']);
            if (!empty($_GET['end_date'])) $exportParams[] = 'end_date=' . urlencode($_GET['end_date']);
            if (!empty($exportParams)) echo '&' . implode('&', $exportParams);
        ?>" class="btn btn-outline-primary me-2">
            <i class="fa fa-download"></i> 批量导出
        </a>
        <a href="<?php echo APP_URL; ?>/costs.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增广告费记录
        </a>
    </div>
</div>

<!-- 搜索和筛选框 -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/costs.php" class="row g-3">
            <div class="col-md-2">
                <label for="platform_name" class="form-label">平台名称</label>
                <select name="platform_name" class="form-select">
                    <option value="">全部平台</option>
                    <?php if (!empty($platformList)): ?>
                        <?php foreach ($platformList as $platform): ?>
                            <option value="<?php echo htmlspecialchars($platform); ?>" 
                                    <?php echo (($_GET['platform_name'] ?? '') == $platform ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($platform); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="store_name" class="form-label">店铺名称</label>
                <select name="store_name" class="form-select">
                    <option value="">全部店铺</option>
                    <?php if (!empty($storeList)): ?>
                        <?php foreach ($storeList as $store): ?>
                            <option value="<?php echo htmlspecialchars($store); ?>" 
                                    <?php echo (($_GET['store_name'] ?? '') == $store ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($store); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="start_date" class="form-label">开始日期</label>
                <input type="date" name="start_date" class="form-control" 
                       value="<?php echo $_GET['start_date'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="end_date" class="form-label">结束日期</label>
                <input type="date" name="end_date" class="form-control" 
                       value="<?php echo $_GET['end_date'] ?? ''; ?>">
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fa fa-search"></i> 筛选
                </button>
                <a href="<?php echo APP_URL; ?>/costs.php" class="btn btn-outline-secondary">
                    <i class="fa fa-refresh"></i> 重置
                </a>
            </div>
        </form>
    </div>
</div>

<!-- 数据表格 -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>平台名称</th>
                        <th>店铺名称</th>
                        <th>日均广告花费（美元）</th>
                        <th>日期</th>
                        <th>创建时间</th>
                        <th>更新时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($costs)): ?>
                        <?php foreach ($costs as $cost): ?>
                            <tr>
                                <td><?php echo $cost['id']; ?></td>
                                <td><?php echo htmlspecialchars($cost['platform_name']); ?></td>
                                <td><?php echo htmlspecialchars($cost['store_name']); ?></td>
                                <td><?php echo $cost['cost']; ?></td>
                                <td><?php echo $cost['date']; ?></td>
                                <td><?php echo $cost['create_at']; ?></td>
                                <td><?php echo $cost['update_at']; ?></td>
                                <td>
                                    <a href="<?php echo APP_URL; ?>/costs.php?action=edit&id=<?php echo $cost['id']; ?>" 
                                       class="btn btn-sm btn-outline-primary me-1">
                                        <i class="fa fa-edit"></i> 编辑
                                    </a>
                                    <a href="<?php echo APP_URL; ?>/costs.php?action=delete&id=<?php echo $cost['id']; ?>" 
                                       class="btn btn-sm btn-outline-danger" 
                                       onclick="return confirm('确定要删除这条记录吗？');">
                                        <i class="fa fa-trash"></i> 删除
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center">暂无数据</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 分页控件 -->
<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            <li class="page-item <?php echo ($page == 1) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo APP_URL; ?>/costs.php?page=<?php echo $page - 1; ?><?php 
                    $queryParams = [];
                    if (!empty($_GET['platform_name'])) $queryParams[] = 'platform_name=' . urlencode($_GET['platform_name']);
                    if (!empty($_GET['store_name'])) $queryParams[] = 'store_name=' . urlencode($_GET['store_name']);
                    if (!empty($_GET['start_date'])) $queryParams[] = 'start_date=' . urlencode($_GET['start_date']);
                    if (!empty($_GET['end_date'])) $queryParams[] = 'end_date=' . urlencode($_GET['end_date']);
                    if (!empty($queryParams)) echo '&' . implode('&', $queryParams);
                ?>" tabindex="-1">上一页</a>
            </li>
            <?php 
            $startPage = max(1, $page - 2);
            $endPage = min($totalPages, $page + 2);
            
            if ($startPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo APP_URL; ?>/costs.php?page=1<?php 
                        if (!empty($queryParams)) echo '&' . implode('&', $queryParams);
                    ?>">1</a>
                </li>
                <?php if ($startPage > 2): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
            <?php endif; ?>
            
            <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                    <a class="page-link" href="<?php echo APP_URL; ?>/costs.php?page=<?php echo $i; ?><?php 
                        if (!empty($queryParams)) echo '&' . implode('&', $queryParams);
                    ?>"><?php echo $i; ?></a>
                </li>
            <?php endfor; ?>
            
            <?php if ($endPage < $totalPages): ?>
                <?php if ($endPage < $totalPages - 1): ?>
                    <li class="page-item disabled"><span class="page-link">...</span></li>
                <?php endif; ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo APP_URL; ?>/costs.php?page=<?php echo $totalPages; ?><?php 
                        if (!empty($queryParams)) echo '&' . implode('&', $queryParams);
                    ?>"><?php echo $totalPages; ?></a>
                </li>
            <?php endif; ?>
            <li class="page-item <?php echo ($page == $totalPages) ? 'disabled' : ''; ?>">
                <a class="page-link" href="<?php echo APP_URL; ?>/costs.php?page=<?php echo $page + 1; ?><?php 
                    if (!empty($queryParams)) echo '&' . implode('&', $queryParams);
                ?>">下一页</a>
            </li>
        </ul>
    </nav>
<?php endif; ?>