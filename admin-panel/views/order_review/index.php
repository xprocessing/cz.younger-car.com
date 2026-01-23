<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?action=import" class="btn btn-outline-success me-2">
            <i class="fa fa-upload"></i> 批量导入
        </a>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?action=export<?php 
            $exportParams = [];
            if (!empty($_GET['keyword'])) $exportParams[] = 'keyword=' . urlencode($_GET['keyword']);
            if (!empty($_GET['review_status'])) $exportParams[] = 'review_status=' . urlencode($_GET['review_status']);
            if (!empty($_GET['start_date'])) $exportParams[] = 'start_date=' . urlencode($_GET['start_date']);
            if (!empty($_GET['end_date'])) $exportParams[] = 'end_date=' . urlencode($_GET['end_date']);
            if (!empty($exportParams)) echo '&' . implode('&', $exportParams);
        ?>" class="btn btn-outline-primary me-2">
            <i class="fa fa-download"></i> 批量导出
        </a>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增审核记录
        </a>
    </div>
</div>

<!-- 搜索和筛选框 -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo ADMIN_PANEL_URL; ?>/order_review.php" class="row g-3">
            <div class="col-md-3">
                <label for="keyword" class="form-label">关键词搜索</label>
                <input type="text" name="keyword" class="form-control" placeholder="订单号、SKU、国家" 
                       value="<?php echo $_GET['keyword'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <label for="review_status" class="form-label">审单状态</label>
                <select name="review_status" class="form-select">
                    <option value="">全部状态</option>
                    <?php if (!empty($reviewStatusList)): ?>
                        <?php foreach ($reviewStatusList as $status): ?>
                            <option value="<?php echo htmlspecialchars($status); ?>" 
                                    <?php echo (($_GET['review_status'] ?? '') == $status ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($status); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="start_date" class="form-label">开始日期</label>
                <input type="date" name="start_date" class="form-control" 
                       value="<?php echo $_GET['start_date'] ?? ''; ?>">
            </div>
            <div class="col-md-3">
                <label for="end_date" class="form-label">结束日期</label>
                <input type="date" name="end_date" class="form-control" 
                       value="<?php echo $_GET['end_date'] ?? ''; ?>">
            </div>
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fa fa-search"></i> 筛选
                </button>
                <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php" class="btn btn-outline-secondary">
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
                        <th>订单号</th>
                        <th>本地SKU</th>
                        <th>国家</th>
                        <th>城市</th>
                        <th>邮编</th>
                        <th>仓库ID</th>
                        <th>物流方式ID</th>
                        <th>预估邮费</th>
                        <th>审单状态</th>
                        <th>审单时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orderReviews)): ?>
                        <?php foreach ($orderReviews as $review): ?>
                            <tr>
                                <td><?php echo $review['id']; ?></td>
                                <td><?php echo $review['global_order_no']; ?></td>
                                <td><?php echo $review['local_sku']; ?></td>
                                <td><?php echo $review['receiver_country_code']; ?></td>
                                <td><?php echo $review['city'] ?? ''; ?></td>
                                <td><?php echo $review['postal_code'] ?? ''; ?></td>
                                <td><?php echo $review['wid'] ?? ''; ?></td>
                                <td><?php echo $review['logistics_type_id'] ?? ''; ?></td>
                                <td><?php echo $review['estimated_yunfei'] ?? ''; ?></td>
                                <td><?php echo $review['review_status'] ?? ''; ?></td>
                                <td><?php echo $review['review_time'] ?? ''; ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?action=edit&id=<?php echo $review['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?action=delete&id=<?php echo $review['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('确定要删除这条记录吗？');">
                                            <i class="fa fa-trash"></i>
                                        </a>
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
        
        <!-- 分页 -->
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?page=<?php echo $page - 1; ?><?php 
                                $params = [];
                                if (!empty($_GET['keyword'])) $params[] = 'keyword=' . urlencode($_GET['keyword']);
                                if (!empty($_GET['review_status'])) $params[] = 'review_status=' . urlencode($_GET['review_status']);
                                if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
                                if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                上一页
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                        <li class="page-item <?php echo ($i == $page ? 'active' : ''); ?>">
                            <a class="page-link" href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?page=<?php echo $i; ?><?php 
                                $params = [];
                                if (!empty($_GET['keyword'])) $params[] = 'keyword=' . urlencode($_GET['keyword']);
                                if (!empty($_GET['review_status'])) $params[] = 'review_status=' . urlencode($_GET['review_status']);
                                if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
                                if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?page=<?php echo $page + 1; ?><?php 
                                $params = [];
                                if (!empty($_GET['keyword'])) $params[] = 'keyword=' . urlencode($_GET['keyword']);
                                if (!empty($_GET['review_status'])) $params[] = 'review_status=' . urlencode($_GET['review_status']);
                                if (!empty($_GET['start_date'])) $params[] = 'start_date=' . urlencode($_GET['start_date']);
                                if (!empty($_GET['end_date'])) $params[] = 'end_date=' . urlencode($_GET['end_date']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                下一页
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>