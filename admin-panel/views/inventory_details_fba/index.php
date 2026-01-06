<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>FBA库存详情管理</h4>
    <div>
        <!-- 批量删除按钮 -->
        <button type="submit" form="batchDeleteForm" class="btn btn-danger" 
                onclick="return confirm('确定要删除选中的FBA库存详情记录吗？');">
            <i class="fa fa-trash"></i> 批量删除
        </button>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/inventory_details_fba.php" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">关键词搜索</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="搜索仓库名、SKU、ASIN、商品名称..." 
                       value="<?php echo $_GET['search'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="name" class="form-label">仓库名</label>
                <select name="name" class="form-select">
                    <option value="">全部仓库</option>
                    <?php if (!empty($warehouseNames)): ?>
                        <?php foreach ($warehouseNames as $warehouseName): ?>
                            <option value="<?php echo htmlspecialchars($warehouseName); ?>" 
                                    <?php echo (($_GET['name'] ?? '') == $warehouseName ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($warehouseName); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control" 
                       placeholder="搜索SKU" 
                       value="<?php echo $_GET['sku'] ?? ''; ?>">
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> 搜索
                </button>
                <a href="<?php echo APP_URL; ?>/inventory_details_fba.php" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> 重置
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/inventory_details_fba.php?action=batchDelete" id="batchDeleteForm">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>仓库名</th>
                            <th>共享仓店铺名</th>
                            <th>店铺ID</th>
                            <th>ASIN</th>
                            <th>商品名称</th>
                            <th>预览图</th>
                            <th>MSKU</th>
                            <th>FNSKU</th>
                            <th>SKU</th>
                            <th>分类</th>
                            <th>品牌</th>
                            <th>共享类型</th>
                            <th>总数</th>
                            <th>总价</th>
                            <th>FBA可售</th>
                            <th>待调仓</th>
                            <th>调仓中</th>
                            <th>待发货</th>
                            <th>FBM可售</th>
                            <th>不可售</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inventoryDetails)): ?>
                            <?php foreach ($inventoryDetails as $item): ?>
                                <tr>
                                    <td><input type="checkbox" name="records[]" value="<?php echo urlencode($item['name']) . '|' . urlencode($item['sku']); ?>"></td>
                                    <td><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['seller_group_name']); ?></td>
                                    <td><?php echo $item['sid']; ?></td>
                                    <td><?php echo htmlspecialchars($item['asin']); ?></td>
                                    <td title="<?php echo htmlspecialchars($item['product_name']); ?>">
                                        <?php echo mb_strlen($item['product_name']) > 20 ? mb_substr($item['product_name'], 0, 20) . '...' : htmlspecialchars($item['product_name']); ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($item['small_image_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($item['small_image_url']); ?>" 
                                                 alt="预览图" 
                                                 class="img-thumbnail" 
                                                 style="width: 50px; height: 50px; object-fit: cover;">
                                        <?php else: ?>
                                            <span class="text-muted">无图片</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['seller_sku']); ?></td>
                                    <td><?php echo htmlspecialchars($item['fnsku']); ?></td>
                                    <td><?php echo htmlspecialchars($item['sku']); ?></td>
                                    <td title="<?php echo htmlspecialchars($item['category_text']); ?>">
                                        <?php echo mb_strlen($item['category_text']) > 20 ? mb_substr($item['category_text'], 0, 20) . '...' : htmlspecialchars($item['category_text']); ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($item['product_brand_text']); ?></td>
                                    <td>
                                        <?php 
                                        $shareTypeText = '';
                                        switch($item['share_type']) {
                                            case 0:
                                                $shareTypeText = '非共享';
                                                break;
                                            case 1:
                                                $shareTypeText = '北美共享';
                                                break;
                                            case 2:
                                                $shareTypeText = '欧洲共享';
                                                break;
                                            default:
                                                $shareTypeText = '未知';
                                        }
                                        ?>
                                        <span class="badge <?php echo $item['share_type'] == 0 ? 'bg-secondary' : 'bg-primary'; ?>">
                                            <?php echo $shareTypeText; ?>
                                        </span>
                                    </td>
                                    <td><?php echo $item['total']; ?></td>
                                    <td><?php echo number_format($item['total_price'], 2); ?></td>
                                    <td><?php echo $item['afn_fulfillable_quantity']; ?></td>
                                    <td><?php echo $item['reserved_fc_transfers']; ?></td>
                                    <td><?php echo $item['reserved_fc_processing']; ?></td>
                                    <td><?php echo $item['reserved_customerorders']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo $item['afn_unsellable_quantity']; ?></td>
                                    <td>
                                        <a href="<?php echo APP_URL; ?>/inventory_details_fba.php?action=delete&name=<?php echo urlencode($item['name']); ?>&sku=<?php echo urlencode($item['sku']); ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('确定要删除该FBA库存详情记录吗？');">
                                            <i class="fa fa-trash"></i> 删除
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="21" class="text-center">暂无数据</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
        
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php 
                                $params = [];
                                if (!empty($_GET['search'])) $params[] = 'search=' . urlencode($_GET['search']);
                                if (!empty($_GET['name'])) $params[] = 'name=' . urlencode($_GET['name']);
                                if (!empty($_GET['sku'])) $params[] = 'sku=' . urlencode($_GET['sku']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                上一页
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php
                    $showPages = [];
                    $showPages[] = 1;
                    
                    if ($totalPages > 1) {
                        $startPage = max(2, $page - 2);
                        $endPage = min($totalPages - 1, $page + 2);
                        
                        if ($startPage > 2) {
                            $showPages[] = '...';
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            $showPages[] = $i;
                        }
                        
                        if ($endPage < $totalPages - 1) {
                            $showPages[] = '...';
                        }
                        
                        if ($totalPages > 1) {
                            $showPages[] = $totalPages;
                        }
                    }
                    
                    foreach ($showPages as $showPage):
                        if ($showPage == '...'):
                    ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php else:
                        $activeClass = $page == $showPage ? 'active' : '';
                    ?>
                        <li class="page-item <?php echo $activeClass; ?>">
                            <a class="page-link" href="?page=<?php echo $showPage; ?><?php 
                                $params = [];
                                if (!empty($_GET['search'])) $params[] = 'search=' . urlencode($_GET['search']);
                                if (!empty($_GET['name'])) $params[] = 'name=' . urlencode($_GET['name']);
                                if (!empty($_GET['sku'])) $params[] = 'sku=' . urlencode($_GET['sku']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                <?php echo $showPage; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php 
                                $params = [];
                                if (!empty($_GET['search'])) $params[] = 'search=' . urlencode($_GET['search']);
                                if (!empty($_GET['name'])) $params[] = 'name=' . urlencode($_GET['name']);
                                if (!empty($_GET['sku'])) $params[] = 'sku=' . urlencode($_GET['sku']);
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

<!-- 全选/取消全选脚本 -->
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="records[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>