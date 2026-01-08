<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">
                <?php echo htmlspecialchars($title); ?>
                <div class="float-right">
                    <a href="<?php echo APP_URL; ?>/car_data.php?action=create" class="btn btn-success mr-2">
                        <i class="fa fa-plus"></i> 创建车型数据
                    </a>
                    <a href="<?php echo APP_URL; ?>/car_data.php?action=import" class="btn btn-info mr-2">
                        <i class="fa fa-upload"></i> 导入数据
                    </a>
                    <a href="<?php echo APP_URL; ?>/car_data.php?action=export<?php echo !empty($_SERVER['QUERY_STRING']) ? '&' . $_SERVER['QUERY_STRING'] : ''; ?>" class="btn btn-primary">
                        <i class="fa fa-download"></i> 导出数据
                    </a>
                </div>
            </h1>
            
            <!-- 筛选表单 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa fa-filter"></i> 筛选条件
                </div>
                <div class="card-body">
                    <form method="get" action="<?php echo APP_URL; ?>/car_data.php" class="form-inline">
                        <div class="form-row align-items-center flex-nowrap">
                            <div class="col-auto">
                                <label for="keyword" class="sr-only">关键词：</label>
                                <input type="text" class="form-control mr-sm-2" id="keyword" name="keyword" placeholder="品牌/车型/配置/市场" value="<?php echo htmlspecialchars($keyword ?? ''); ?>">
                            </div>
                            <div class="col-auto">
                                <label for="make" class="sr-only">品牌(英文)：</label>
                                <select class="form-control mr-sm-2" id="make" name="make">
                                    <option value="">全部品牌(英文)</option>
                                    <?php foreach ($makeList as $makeItem): ?>
                                        <option value="<?php echo htmlspecialchars($makeItem); ?>" <?php echo isset($make) && $make == $makeItem ? 'selected' : ''; ?>><?php echo htmlspecialchars($makeItem); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label for="make_cn" class="sr-only">品牌(中文)：</label>
                                <select class="form-control mr-sm-2" id="make_cn" name="make_cn">
                                    <option value="">全部品牌(中文)</option>
                                    <?php foreach ($makeCnList as $makeCnItem): ?>
                                        <option value="<?php echo htmlspecialchars($makeCnItem); ?>" <?php echo isset($make_cn) && $make_cn == $makeCnItem ? 'selected' : ''; ?>><?php echo htmlspecialchars($makeCnItem); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label for="year" class="sr-only">年份：</label>
                                <select class="form-control mr-sm-2" id="year" name="year">
                                    <option value="">全部年份</option>
                                    <?php foreach ($yearList as $yearItem): ?>
                                        <option value="<?php echo htmlspecialchars($yearItem); ?>" <?php echo isset($year) && $year == $yearItem ? 'selected' : ''; ?>><?php echo htmlspecialchars($yearItem); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <label for="market" class="sr-only">市场：</label>
                                <select class="form-control mr-sm-2" id="market" name="market">
                                    <option value="">全部市场</option>
                                    <?php foreach ($marketList as $marketItem): ?>
                                        <option value="<?php echo htmlspecialchars($marketItem); ?>" <?php echo isset($market) && $market == $marketItem ? 'selected' : ''; ?>><?php echo htmlspecialchars($marketItem); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary mr-sm-2">
                                    <i class="fa fa-search"></i> 筛选
                                </button>
                            </div>
                            <div class="col-auto">
                                <a href="<?php echo APP_URL; ?>/car_data.php" class="btn btn-default">
                                    <i class="fa fa-refresh"></i> 重置
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- 数据列表 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa fa-list"></i> 车型数据列表
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>ID</th>
                                    <th>品牌(英文)</th>
                                    <th>品牌(中文)</th>
                                    <th>车型</th>
                                    <th>年份</th>
                                    <th>配置版本</th>
                                    <th>配置描述</th>
                                    <th>销售市场</th>
                                    <th>创建时间</th>
                                    <th>更新时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($carDataList)): ?>
                                    <tr>
                                        <td colspan="11" class="text-center">暂无数据</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($carDataList as $carData): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($carData['id']); ?></td>
                                            <td><?php echo htmlspecialchars($carData['make'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($carData['make_cn'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($carData['model'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($carData['year'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($carData['trim'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars(substr($carData['trim_description'] ?? '', 0, 100) . (strlen($carData['trim_description'] ?? '') > 100 ? '...' : '')); ?></td>
                                            <td><?php echo htmlspecialchars($carData['market'] ?? ''); ?></td>
                                            <td><?php echo htmlspecialchars($carData['create_at']); ?></td>
                                            <td><?php echo htmlspecialchars($carData['update_at'] ?? ''); ?></td>
                                            <td>
                                                <a href="<?php echo APP_URL; ?>/car_data.php?action=edit&id=<?php echo htmlspecialchars($carData['id']); ?>" class="btn btn-sm btn-info" title="编辑">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                                <a href="<?php echo APP_URL; ?>/car_data.php?action=delete&id=<?php echo htmlspecialchars($carData['id']); ?>" class="btn btn-sm btn-danger" title="删除" onclick="return confirm('确定要删除这条车型数据吗？');">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- 分页导航 -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Page navigation">
                            <ul class="pagination justify-content-center">
                                <li class="page-item <?php echo $page == 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo APP_URL; ?>/car_data.php?page=1<?php echo !empty($keyword) ? '&keyword=' . urlencode($keyword) : ''; ?><?php echo !empty($make) ? '&make=' . urlencode($make) : ''; ?><?php echo !empty($make_cn) ? '&make_cn=' . urlencode($make_cn) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?><?php echo !empty($market) ? '&market=' . urlencode($market) : ''; ?>">
                                        首页
                                    </a>
                                </li>
                                <li class="page-item <?php echo $page == 1 ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo APP_URL; ?>/car_data.php?page=<?php echo ($page - 1); ?><?php echo !empty($keyword) ? '&keyword=' . urlencode($keyword) : ''; ?><?php echo !empty($make) ? '&make=' . urlencode($make) : ''; ?><?php echo !empty($make_cn) ? '&make_cn=' . urlencode($make_cn) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?><?php echo !empty($market) ? '&market=' . urlencode($market) : ''; ?>">
                                        上一页
                                    </a>
                                </li>
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $page == $i ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo APP_URL; ?>/car_data.php?page=<?php echo $i; ?><?php echo !empty($keyword) ? '&keyword=' . urlencode($keyword) : ''; ?><?php echo !empty($make) ? '&make=' . urlencode($make) : ''; ?><?php echo !empty($make_cn) ? '&make_cn=' . urlencode($make_cn) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?><?php echo !empty($market) ? '&market=' . urlencode($market) : ''; ?>">
                                            <?php echo $i; ?>
                                        </a>
                                    </li>
                                <?php endfor; ?>
                                <li class="page-item <?php echo $page == $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo APP_URL; ?>/car_data.php?page=<?php echo ($page + 1); ?><?php echo !empty($keyword) ? '&keyword=' . urlencode($keyword) : ''; ?><?php echo !empty($make) ? '&make=' . urlencode($make) : ''; ?><?php echo !empty($make_cn) ? '&make_cn=' . urlencode($make_cn) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?><?php echo !empty($market) ? '&market=' . urlencode($market) : ''; ?>">
                                        下一页
                                    </a>
                                </li>
                                <li class="page-item <?php echo $page == $totalPages ? 'disabled' : ''; ?>">
                                    <a class="page-link" href="<?php echo APP_URL; ?>/car_data.php?page=<?php echo $totalPages; ?><?php echo !empty($keyword) ? '&keyword=' . urlencode($keyword) : ''; ?><?php echo !empty($make) ? '&make=' . urlencode($make) : ''; ?><?php echo !empty($make_cn) ? '&make_cn=' . urlencode($make_cn) : ''; ?><?php echo !empty($year) ? '&year=' . urlencode($year) : ''; ?><?php echo !empty($market) ? '&market=' . urlencode($market) : ''; ?>">
                                        末页
                                    </a>
                                </li>
                            </ul>
                        </nav>
                        <div class="text-center">
                            <p class="text-muted">共 <?php echo $totalCount; ?> 条记录，第 <?php echo $page; ?> 页/共 <?php echo $totalPages; ?> 页</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>