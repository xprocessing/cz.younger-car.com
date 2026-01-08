<div class="container-fluid">
    <div class="row">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4><?php echo htmlspecialchars($title); ?></h4>
                <div>
                    <a href="<?php echo APP_URL; ?>/car_data.php?action=import" class="btn btn-outline-success me-2">
                        <i class="fa fa-upload"></i> 导入数据
                    </a>
                    <a href="<?php echo APP_URL; ?>/car_data.php?action=export<?php 
                        $exportParams = [];
                        if (!empty($_GET['keyword'])) $exportParams[] = 'keyword=' . urlencode($_GET['keyword']);
                        if (!empty($_GET['make'])) $exportParams[] = 'make=' . urlencode($_GET['make']);
                        if (!empty($_GET['make_cn'])) $exportParams[] = 'make_cn=' . urlencode($_GET['make_cn']);
                        if (!empty($_GET['year'])) $exportParams[] = 'year=' . urlencode($_GET['year']);
                        if (!empty($_GET['market'])) $exportParams[] = 'market=' . urlencode($_GET['market']);
                        if (!empty($exportParams)) echo '&' . implode('&', $exportParams);
                    ?>" class="btn btn-outline-primary me-2">
                        <i class="fa fa-download"></i> 导出数据
                    </a>
                    <a href="<?php echo APP_URL; ?>/car_data.php?action=create" class="btn btn-primary">
                        <i class="fa fa-plus"></i> 创建车型数据
                    </a>
                </div>
            </div>

            <script>
                // 动态筛选逻辑
                document.addEventListener('DOMContentLoaded', function() {
                    const makeSelect = document.getElementById('make');
                    const makeCnSelect = document.getElementById('make_cn');
                    const modelSelect = document.getElementById('model');
                    const yearSelect = document.getElementById('year');
                    const marketSelect = document.getElementById('market');
                    
                    // 根据品牌获取车型列表
                    function loadModels(makeType, makeValue) {
                        let url = '';
                        if (makeType === 'make') {
                            url = '<?php echo APP_URL; ?>/car_data.php?action=getModelsByMake&make=' + encodeURIComponent(makeValue);
                        } else {
                            url = '<?php echo APP_URL; ?>/car_data.php?action=getModelsByMakeCn&make_cn=' + encodeURIComponent(makeValue);
                        }
                        
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // 清空车型、年份、市场列表
                                    modelSelect.innerHTML = '<option value="">全部</option>';
                                    yearSelect.innerHTML = '<option value="">全部</option>';
                                    marketSelect.innerHTML = '<option value="">全部</option>';
                                    
                                    // 添加车型选项
                                    data.models.forEach(model => {
                                        const option = document.createElement('option');
                                        option.value = model;
                                        option.textContent = model;
                                        modelSelect.appendChild(option);
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('加载车型列表失败:', error);
                            });
                    }
                    
                    // 根据品牌和车型获取年份列表
                    function loadYears() {
                        const make = makeSelect.value;
                        const makeCn = makeCnSelect.value;
                        const model = modelSelect.value;
                        
                        if (!model) return;
                        
                        let url = '';
                        if (make) {
                            url = '<?php echo APP_URL; ?>/car_data.php?action=getYearsByMakeAndModel&make=' + encodeURIComponent(make) + '&model=' + encodeURIComponent(model);
                        } else if (makeCn) {
                            url = '<?php echo APP_URL; ?>/car_data.php?action=getYearsByMakeCnAndModel&make_cn=' + encodeURIComponent(makeCn) + '&model=' + encodeURIComponent(model);
                        } else {
                            return;
                        }
                        
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // 清空年份和市场列表
                                    yearSelect.innerHTML = '<option value="">全部</option>';
                                    marketSelect.innerHTML = '<option value="">全部</option>';
                                    
                                    // 添加年份选项
                                    data.years.forEach(year => {
                                        const option = document.createElement('option');
                                        option.value = year;
                                        option.textContent = year;
                                        yearSelect.appendChild(option);
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('加载年份列表失败:', error);
                            });
                    }
                    
                    // 根据品牌、车型和年份获取市场列表
                    function loadMarkets() {
                        const make = makeSelect.value;
                        const makeCn = makeCnSelect.value;
                        const model = modelSelect.value;
                        const year = yearSelect.value;
                        
                        if (!model || !year) return;
                        
                        let url = '';
                        if (make) {
                            url = '<?php echo APP_URL; ?>/car_data.php?action=getMarketsByMakeModelAndYear&make=' + encodeURIComponent(make) + '&model=' + encodeURIComponent(model) + '&year=' + encodeURIComponent(year);
                        } else if (makeCn) {
                            url = '<?php echo APP_URL; ?>/car_data.php?action=getMarketsByMakeCnModelAndYear&make_cn=' + encodeURIComponent(makeCn) + '&model=' + encodeURIComponent(model) + '&year=' + encodeURIComponent(year);
                        } else {
                            return;
                        }
                        
                        fetch(url)
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    // 清空市场列表
                                    marketSelect.innerHTML = '<option value="">全部</option>';
                                    
                                    // 添加市场选项
                                    data.markets.forEach(market => {
                                        const option = document.createElement('option');
                                        option.value = market;
                                        option.textContent = market;
                                        marketSelect.appendChild(option);
                                    });
                                }
                            })
                            .catch(error => {
                                console.error('加载市场列表失败:', error);
                            });
                    }
                    
                    // 品牌(英文)选择事件
                    makeSelect.addEventListener('change', function() {
                        const makeValue = this.value;
                        // 如果选择了品牌(英文)，则清空品牌(中文)选择
                        if (makeValue) {
                            makeCnSelect.value = '';
                        }
                        // 加载车型列表
                        if (makeValue) {
                            loadModels('make', makeValue);
                        } else {
                            // 清空所有关联下拉框
                            modelSelect.innerHTML = '<option value="">全部</option>';
                            yearSelect.innerHTML = '<option value="">全部</option>';
                            marketSelect.innerHTML = '<option value="">全部</option>';
                        }
                    });
                    
                    // 品牌(中文)选择事件
                    makeCnSelect.addEventListener('change', function() {
                        const makeCnValue = this.value;
                        // 如果选择了品牌(中文)，则清空品牌(英文)选择
                        if (makeCnValue) {
                            makeSelect.value = '';
                        }
                        // 加载车型列表
                        if (makeCnValue) {
                            loadModels('make_cn', makeCnValue);
                        } else {
                            // 清空所有关联下拉框
                            modelSelect.innerHTML = '<option value="">全部</option>';
                            yearSelect.innerHTML = '<option value="">全部</option>';
                            marketSelect.innerHTML = '<option value="">全部</option>';
                        }
                    });
                    
                    // 车型选择事件
                    modelSelect.addEventListener('change', function() {
                        const modelValue = this.value;
                        // 加载年份列表
                        if (modelValue) {
                            loadYears();
                        } else {
                            // 清空所有关联下拉框
                            yearSelect.innerHTML = '<option value="">全部</option>';
                            marketSelect.innerHTML = '<option value="">全部</option>';
                        }
                    });
                    
                    // 年份选择事件
                    yearSelect.addEventListener('change', function() {
                        const yearValue = this.value;
                        // 加载市场列表
                        if (yearValue) {
                            loadMarkets();
                        } else {
                            // 清空市场下拉框
                            marketSelect.innerHTML = '<option value="">全部</option>';
                        }
                    });
                });
            </script>
            
            <!-- 筛选表单 -->
            <div class="card mb-4">
                <div class="card-header">
                    <i class="fa fa-filter"></i> 筛选条件
                </div>
                <div class="card-body">
                    <form method="get" action="<?php echo APP_URL; ?>/car_data.php" class="row g-3">
                        <div class="col-md-2">
                            <label for="keyword" class="form-label">关键词：</label>
                            <input type="text" class="form-control" id="keyword" name="keyword" placeholder="品牌/车型/配置/市场" value="<?php echo htmlspecialchars($keyword ?? ''); ?>">
                        </div>
                        <div class="col-md-1">
                            <label for="make" class="form-label">品牌(英文)：</label>
                            <select class="form-select" id="make" name="make">
                                <option value="">全部</option>
                                <?php foreach ($makeList as $makeItem): ?>
                                    <option value="<?php echo htmlspecialchars($makeItem); ?>" <?php echo isset($make) && $make == $makeItem ? 'selected' : ''; ?>><?php echo htmlspecialchars($makeItem); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="make_cn" class="form-label">品牌(中文)：</label>
                            <select class="form-select" id="make_cn" name="make_cn">
                                <option value="">全部</option>
                                <?php foreach ($makeCnList as $makeCnItem): ?>
                                    <option value="<?php echo htmlspecialchars($makeCnItem); ?>" <?php echo isset($make_cn) && $make_cn == $makeCnItem ? 'selected' : ''; ?>><?php echo htmlspecialchars($makeCnItem); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="model" class="form-label">车型：</label>
                            <select class="form-select" id="model" name="model">
                                <option value="">全部</option>
                                <?php foreach ($modelList as $modelItem): ?>
                                    <option value="<?php echo htmlspecialchars($modelItem); ?>" <?php echo isset($model) && $model == $modelItem ? 'selected' : ''; ?>><?php echo htmlspecialchars($modelItem); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="year" class="form-label">年份：</label>
                            <select class="form-select" id="year" name="year">
                                <option value="">全部</option>
                                <?php foreach ($yearList as $yearItem): ?>
                                    <option value="<?php echo htmlspecialchars($yearItem); ?>" <?php echo isset($year) && $year == $yearItem ? 'selected' : ''; ?>><?php echo htmlspecialchars($yearItem); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1">
                            <label for="market" class="form-label">市场：</label>
                            <select class="form-select" id="market" name="market">
                                <option value="">全部</option>
                                <?php foreach ($marketList as $marketItem): ?>
                                    <option value="<?php echo htmlspecialchars($marketItem); ?>" <?php echo isset($market) && $market == $marketItem ? 'selected' : ''; ?>><?php echo htmlspecialchars($marketItem); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-1 align-self-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa fa-search"></i> 筛选
                            </button>
                        </div>
                        <div class="col-md-1 align-self-end">
                            <a href="<?php echo APP_URL; ?>/car_data.php" class="btn btn-secondary">
                                <i class="fa fa-refresh"></i> 重置
                            </a>
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
                        <table class="table table-striped table-hover">
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