<style>
.truncate-text {
    max-width: 80px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    cursor: pointer;
    position: relative;
}

.truncate-text:hover {
    background-color: #f8f9fa;
}

.tooltip-custom {
    position: absolute;
    background: #333;
    color: #fff;
    padding: 10px;
    border-radius: 4px;
    font-size: 12px;
    max-width: 400px;
    max-height: 300px;
    overflow-y: auto;
    z-index: 1000;
    white-space: pre-wrap;
    word-wrap: break-word;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.tooltip-custom::before {
    content: '';
    position: absolute;
    top: -6px;
    left: 10px;
    width: 0;
    height: 0;
    border-left: 6px solid transparent;
    border-right: 6px solid transparent;
    border-bottom: 6px solid #333;
}
</style>
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
                        <th>店铺ID</th>
                        <th>店铺</th>
                        <th>订单号</th>
                        <th>本地SKU</th>
                        <th>国家</th>
                        <th>城市</th>
                        <th>邮编</th>
                        <th>运德运费-试算</th>
                        <th>中邮运费-试算</th>
                        <th>仓库ID（已设置）</th>
                        <th>仓库名称</th>
                        <th>物流方式ID（已设置）</th>
                        <th>物流渠道编码</th>
                        <th>预估邮费</th>
                        <th>审单状态</th>
                        <th>审单时间</th>
                        <th>审单备注</th>
                        <th>创建时间</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($orderReviews)): ?>
                        <?php foreach ($orderReviews as $review): ?>
                            <tr>
                                <td><?php echo $review['id']; ?></td>
                                <td> <?php echo mb_substr($review['store_id'] ?? '', 0, 3); ?>
                            </td>
                                <td>
                                    <?php 
                                    $platform_name = $review['platform_name'] ?? '';
                                    $store_name = $review['store_name'] ?? '';
                                    if ($platform_name && $store_name) {
                                        echo htmlspecialchars($platform_name . ' - ' . $store_name);
                                    } elseif ($store_name) {
                                        echo htmlspecialchars($store_name);
                                    } else {
                                        echo htmlspecialchars($review['store_id'] ?? '');
                                    }
                                    ?>
                                </td>
                                <td> <a href="https://erp.lingxing.com/erp/mmulti/mpOrderDetail?orderSn=<?php echo $review['global_order_no']; ?>" target="_blank"> <?php echo $review['global_order_no']; ?> </a> </td>
                                <td><?php echo $review['local_sku']; ?></td>
                                <td><?php echo $review['receiver_country_code']; ?></td>
                                <td><?php echo $review['city'] ?? ''; ?></td>
                                <td><?php echo $review['postal_code'] ?? ''; ?></td>
                                <td>
                                    <div class="truncate-text" 
                                         data-full="<?php echo htmlspecialchars($review['wd_yunfei'] ?? '', ENT_QUOTES); ?>"
                                         >
                                        运德运费<?php echo mb_substr($review['wd_yunfei'] ?? '', 0, 10); ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="truncate-text" 
                                         data-full="<?php echo htmlspecialchars($review['ems_yunfei'] ?? '', ENT_QUOTES); ?>"
                                         title="<?php echo htmlspecialchars($review['ems_yunfei'] ?? '', ENT_QUOTES); ?>">
                                        中邮运费<?php echo mb_substr($review['ems_yunfei'] ?? '', 0, 10); ?>
                                    </div>
                                </td>
                                <td><?php echo $review['wid'] ?? ''; ?></td>
                                <td><?php echo htmlspecialchars($review['name'] ?? ''); ?></td>
                                <td><?php echo $review['logistics_type_id'] ?? ''; ?></td>



                                <td><?php echo htmlspecialchars($review['code'] ?? ''); ?></td>


                                <td><?php echo number_format($review['estimated_yunfei'], 2) ?? ''; ?></td>
                                <td><?php echo $review['review_status'] ?? ''; ?></td>
                                <td><?php echo $review['review_time'] ?? ''; ?></td>
                                <td><?php echo $review['review_remark'] ?? ''; ?></td>
                                <td><?php echo $review['create_at'] ?? ''; ?></td>
                                <td>
                                    <div class="d-flex gap-1">
                                         <a href="https://cz.younger-car.com/xlingxing/php/batch_review_order.php?global_order_no=<?php echo $review['global_order_no']; ?>" 
                                           class="btn btn-sm btn-outline-primary" target="_blank">
                                            <i class="fa fa-shipping-fast"></i>发货
                                        </a>
                                        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?action=edit&id=<?php echo $review['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fa fa-pencil"></i>改
                                        </a>
                                        <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?action=delete&id=<?php echo $review['id']; ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('确定要删除这条记录吗？');">
                                            <i class="fa fa-trash"></i>删
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const truncateTexts = document.querySelectorAll('.truncate-text');
    
    truncateTexts.forEach(function(element) {
        element.addEventListener('mouseenter', function(e) {
            const fullText = this.getAttribute('data-full');
            if (!fullText || fullText === '') return;
            
            let tooltip = document.createElement('div');
            tooltip.className = 'tooltip-custom';
            
            try {
                const jsonData = JSON.parse(fullText);
                if (Array.isArray(jsonData)) {
                    let html = '<strong>运费列表：</strong><br>';
                    jsonData.forEach(function(item, index) {
                        html += `<br><strong>${index + 1}.</strong> `;
                        if (item.channel_code) {
                            html += `渠道: ${item.channel_code} | `;
                        }
                        if (item.currency && item.totalFee) {
                            html += `费用: ${item.currency} ${item.totalFee}`;
                        } else if (item.totalFee) {
                            html += `费用: ${item.totalFee}`;
                        }
                        if (item.currency === null || item.totalFee === null) {
                            html += ' (无报价)';
                        }
                    });
                    tooltip.innerHTML = html;
                } else {
                    tooltip.textContent = fullText;
                }
            } catch (e) {
                tooltip.textContent = fullText;
            }
            
            document.body.appendChild(tooltip);
            
            const rect = this.getBoundingClientRect();
            const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft;
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            const viewportWidth = window.innerWidth || document.documentElement.clientWidth;
            const viewportHeight = window.innerHeight || document.documentElement.clientHeight;
            
            let tooltipLeft = rect.left + scrollLeft;
            let tooltipTop = rect.bottom + scrollTop + 5;
            
            const tooltipRect = tooltip.getBoundingClientRect();
            
            if (tooltipLeft + tooltipRect.width > viewportWidth) {
                tooltipLeft = rect.right + scrollLeft - tooltipRect.width;
            }
            
            if (tooltipTop + tooltipRect.height > viewportHeight + scrollTop) {
                tooltipTop = rect.top + scrollTop - tooltipRect.height - 5;
            }
            
            tooltip.style.left = tooltipLeft + 'px';
            tooltip.style.top = tooltipTop + 'px';
            
            this._tooltip = tooltip;
        });
        
        element.addEventListener('mouseleave', function() {
            if (this._tooltip) {
                document.body.removeChild(this._tooltip);
                this._tooltip = null;
            }
        });
    });
});
</script>