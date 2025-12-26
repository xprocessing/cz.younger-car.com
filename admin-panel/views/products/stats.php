<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>商品统计</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/products.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">商品总数</h6>
                <h2 class="text-primary"><?php echo number_format($totalProducts); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">分类数量</h6>
                <h2 class="text-success"><?php echo number_format($totalCategories); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">品牌数量</h6>
                <h2 class="text-info"><?php echo number_format($totalBrands); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <h6 class="card-title">在售商品</h6>
                <h2 class="text-warning"><?php echo number_format($activeProducts); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">按分类统计</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>分类名称</th>
                                <th>商品数量</th>
                                <th>占比</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($categoryStats)): ?>
                                <?php foreach ($categoryStats as $stat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stat['category_name'] ?? '未分类'); ?></td>
                                        <td><?php echo number_format($stat['count']); ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-primary" role="progressbar" 
                                                     style="width: <?php echo round($stat['percentage'], 2); ?>%;" 
                                                     aria-valuenow="<?php echo $stat['percentage']; ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <?php echo round($stat['percentage'], 2); ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">暂无数据</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title mb-0">按品牌统计</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>品牌名称</th>
                                <th>商品数量</th>
                                <th>占比</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($brandStats)): ?>
                                <?php foreach ($brandStats as $stat): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($stat['brand_name'] ?? '未分类'); ?></td>
                                        <td><?php echo number_format($stat['count']); ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-info" role="progressbar" 
                                                     style="width: <?php echo round($stat['percentage'], 2); ?>%;" 
                                                     aria-valuenow="<?php echo $stat['percentage']; ?>" 
                                                     aria-valuemin="0" aria-valuemax="100">
                                                    <?php echo round($stat['percentage'], 2); ?>%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="3" class="text-center">暂无数据</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h5 class="card-title mb-0">按状态统计</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>状态</th>
                        <th>商品数量</th>
                        <th>占比</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($statusStats)): ?>
                        <?php foreach ($statusStats as $stat): ?>
                            <tr>
                                <td>
                                    <?php 
                                    $statusText = '';
                                    $badgeClass = '';
                                    switch($stat['status']) {
                                        case '0':
                                            $statusText = '停售';
                                            $badgeClass = 'bg-danger';
                                            break;
                                        case '1':
                                            $statusText = '在售';
                                            $badgeClass = 'bg-success';
                                            break;
                                        case '2':
                                            $statusText = '开发中';
                                            $badgeClass = 'bg-warning';
                                            break;
                                        case '3':
                                            $statusText = '清仓';
                                            $badgeClass = 'bg-secondary';
                                            break;
                                        default:
                                            $statusText = '未知';
                                            $badgeClass = 'bg-dark';
                                    }
                                    ?>
                                    <span class="badge <?php echo $badgeClass; ?>"><?php echo $statusText; ?></span>
                                </td>
                                <td><?php echo number_format($stat['count']); ?></td>
                                <td>
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar <?php echo $badgeClass; ?>" role="progressbar" 
                                             style="width: <?php echo round($stat['percentage'], 2); ?>%;" 
                                             aria-valuenow="<?php echo $stat['percentage']; ?>" 
                                             aria-valuemin="0" aria-valuemax="100">
                                            <?php echo round($stat['percentage'], 2); ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3" class="text-center">暂无数据</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
