<!-- 调试代码 -->
<div style="background: #ffffcc; padding: 10px; border: 1px solid #ccc; margin-bottom: 20px;">
    <h3>调试信息</h3>
    <p><strong>inventoryDetails变量类型:</strong> <?php echo gettype($inventoryDetails); ?></p>
    <p><strong>inventoryDetails变量长度:</strong> <?php echo count($inventoryDetails); ?></p>
    <p><strong>是否为空:</strong> <?php echo empty($inventoryDetails) ? '是' : '否'; ?></p>
    <p><strong>PHP版本:</strong> <?php echo phpversion(); ?></p>
    <?php if (!empty($inventoryDetails)): ?>
        <p><strong>第一条记录:</strong> <?php echo htmlspecialchars(print_r($inventoryDetails[0], true)); ?></p>
    <?php endif; ?>
</div>

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
                            <th rowspan="2"><input type="checkbox" id="selectAll"></th>
                            <th colspan="12">基本信息</th>
                            <th colspan="18">库存数量</th>
                            <th colspan="18">库存成本</th>
                            <th colspan="10">库存分析</th>
                            <th rowspan="2">操作</th>
                        </tr>
                        <tr>
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
                            <th>可用总数</th>
                            <th>FBA可售</th>
                            <th>待调仓</th>
                            <th>调仓中</th>
                            <th>待发货</th>
                            <th>FBM可售</th>
                            <th>不可售</th>
                            <th>计划入库</th>
                            <th>在途</th>
                            <th>入库中</th>
                            <th>实际在途</th>
                            <th>调查中数量</th>
                            <th>总可用库存</th>
                            <th>0-1个月库龄</th>
                            <th>1-2个月库龄</th>
                            <th>2-3个月库龄</th>
                            <th>0-3个月库龄</th>
                            <th>3-6个月库龄</th>
                            <th>6-9个月库龄</th>
                            <th>9-11个月库龄</th>
                            <th>9-12个月库龄</th>
                            <th>11-12个月库龄</th>
                            <th>12个月以上库龄</th>
                            
                            <th>总价</th>
                            <th>可用总数成本价</th>
                            <th>FBA可售成本价</th>
                            <th>待调仓成本价</th>
                            <th>调仓中成本价</th>
                            <th>待发货成本价</th>
                            <th>FBM可售成本价</th>
                            <th>不可售成本价</th>
                            <th>计划入库成本价</th>
                            <th>在途成本价</th>
                            <th>入库中成本价</th>
                            <th>实际在途成本价</th>
                            <th>调查中数量成本价</th>
                            <th>0-1个月库龄成本价</th>
                            <th>1-2个月库龄成本价</th>
                            <th>2-3个月库龄成本价</th>
                            <th>0-3个月库龄成本价</th>
                            <th>3-6个月库龄成本价</th>
                            <th>6-9个月库龄成本价</th>
                            <th>9-11个月库龄成本价</th>
                            <th>9-12个月库龄成本价</th>
                            <th>11-12个月库龄成本价</th>
                            <th>12个月以上库龄成本价</th>
                            <th>历史供货天数成本价</th>
                            <th>单位采购成本</th>
                            <th>单位头程费用</th>
                            
                            <th>推荐操作</th>
                            <th>售出率</th>
                            <th>预计冗余数量</th>
                            <th>预计30天仓储费用</th>
                            <th>最低库存水平</th>
                            <th>库存水平健康度</th>
                            <th>历史供货天数</th>
                            <th>低库存水平费收取情况</th>
                            <th>配送方式</th>
                            <th>FBA可售信息列表</th>
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
                                    <td><?php echo $item['available_total']; ?></td>
                                    <td><?php echo $item['afn_fulfillable_quantity']; ?></td>
                                    <td><?php echo $item['reserved_fc_transfers']; ?></td>
                                    <td><?php echo $item['reserved_fc_processing']; ?></td>
                                    <td><?php echo $item['reserved_customerorders']; ?></td>
                                    <td><?php echo $item['quantity']; ?></td>
                                    <td><?php echo $item['afn_unsellable_quantity']; ?></td>
                                    <td><?php echo $item['afn_inbound_working_quantity']; ?></td>
                                    <td><?php echo $item['afn_inbound_shipped_quantity']; ?></td>
                                    <td><?php echo $item['afn_inbound_receiving_quantity']; ?></td>
                                    <td><?php echo $item['stock_up_num']; ?></td>
                                    <td><?php echo $item['afn_researching_quantity']; ?></td>
                                    <td><?php echo $item['total_fulfillable_quantity']; ?></td>
                                    <td><?php echo $item['inv_age_0_to_30_days']; ?></td>
                                    <td><?php echo $item['inv_age_31_to_60_days']; ?></td>
                                    <td><?php echo $item['inv_age_61_to_90_days']; ?></td>
                                    <td><?php echo $item['inv_age_0_to_90_days']; ?></td>
                                    <td><?php echo $item['inv_age_91_to_180_days']; ?></td>
                                    <td><?php echo $item['inv_age_181_to_270_days']; ?></td>
                                    <td><?php echo $item['inv_age_271_to_330_days']; ?></td>
                                    <td><?php echo $item['inv_age_271_to_365_days']; ?></td>
                                    <td><?php echo $item['inv_age_331_to_365_days']; ?></td>
                                    <td><?php echo $item['inv_age_365_plus_days']; ?></td>
                                    
                                    <td><?php echo number_format($item['total_price'], 2); ?></td>
                                    <td><?php echo $item['available_total_price']; ?></td>
                                    <td><?php echo $item['afn_fulfillable_quantity_price']; ?></td>
                                    <td><?php echo $item['reserved_fc_transfers_price']; ?></td>
                                    <td><?php echo $item['reserved_fc_processing_price']; ?></td>
                                    <td><?php echo $item['reserved_customerorders_price']; ?></td>
                                    <td><?php echo $item['quantity_price']; ?></td>
                                    <td><?php echo $item['afn_unsellable_quantity_price']; ?></td>
                                    <td><?php echo $item['afn_inbound_working_quantity_price']; ?></td>
                                    <td><?php echo $item['afn_inbound_shipped_quantity_price']; ?></td>
                                    <td><?php echo $item['afn_inbound_receiving_quantity_price']; ?></td>
                                    <td><?php echo $item['stock_up_num_price']; ?></td>
                                    <td><?php echo $item['afn_researching_quantity_price']; ?></td>
                                    <td><?php echo $item['inv_age_0_to_30_price']; ?></td>
                                    <td><?php echo $item['inv_age_31_to_60_price']; ?></td>
                                    <td><?php echo $item['inv_age_61_to_90_price']; ?></td>
                                    <td><?php echo $item['inv_age_0_to_90_price']; ?></td>
                                    <td><?php echo $item['inv_age_91_to_180_price']; ?></td>
                                    <td><?php echo $item['inv_age_181_to_270_price']; ?></td>
                                    <td><?php echo $item['inv_age_271_to_330_price']; ?></td>
                                    <td><?php echo $item['inv_age_271_to_365_price']; ?></td>
                                    <td><?php echo $item['inv_age_331_to_365_price']; ?></td>
                                    <td><?php echo $item['inv_age_365_plus_price']; ?></td>
                                    <td><?php echo $item['historical_days_of_supply_price']; ?></td>
                                    <td><?php echo $item['cg_price']; ?></td>
                                    <td><?php echo $item['cg_transport_costs']; ?></td>
                                    
                                    <td><?php echo htmlspecialchars($item['recommended_action']); ?></td>
                                    <td><?php echo number_format($item['sell_through'], 2); ?></td>
                                    <td><?php echo number_format($item['estimated_excess_quantity'], 2); ?></td>
                                    <td><?php echo number_format($item['estimated_storage_cost_next_month'], 2); ?></td>
                                    <td><?php echo number_format($item['fba_minimum_inventory_level'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['fba_inventory_level_health_status']); ?></td>
                                    <td><?php echo number_format($item['historical_days_of_supply'], 2); ?></td>
                                    <td><?php echo htmlspecialchars($item['low_inventory_level_fee_applied']); ?></td>
                                    <td><?php echo htmlspecialchars($item['fulfillment_channel']); ?></td>
                                    <td title="<?php echo htmlspecialchars($item['fba_storage_quantity_list']); ?>">
                                        <?php echo mb_strlen($item['fba_storage_quantity_list']) > 50 ? mb_substr($item['fba_storage_quantity_list'], 0, 50) . '...' : htmlspecialchars($item['fba_storage_quantity_list']); ?>
                                    </td>
                                    
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
                                <td colspan="60" class="text-center">暂无数据</td>
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