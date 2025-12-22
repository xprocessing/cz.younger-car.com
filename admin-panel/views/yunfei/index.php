<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>运费管理</h5>
                <?php if (hasPermission('yunfei.create')): ?>
                    <a href="<?php echo APP_URL; ?>/yunfei.php?action=create" class="btn btn-primary">
                        <i class="fa fa-plus"></i> 创建运费记录
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- 搜索表单 -->
                <form method="GET" action="<?php echo APP_URL; ?>/yunfei.php" class="mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="search" placeholder="搜索订单号" value="<?php echo htmlspecialchars($search ?? ''); ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> 搜索
                                </button>
                                <a href="<?php echo APP_URL; ?>/yunfei.php" class="btn btn-secondary">
                                    <i class="fa fa-refresh"></i> 重置
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- 运费列表 -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>订单号</th>
                            <th>运费数据</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($yunfeiList)): ?>
                            <tr>
                                <td colspan="5" class="text-center">暂无数据</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($yunfeiList as $yunfei): ?>
                                <tr>
                                    <td><?php echo $yunfei['id']; ?></td>
                                    <td><?php echo htmlspecialchars($yunfei['global_order_no']); ?></td>
                                    <td>
                                        <?php 
                                        $yunfeiData = json_decode($yunfei['shisuanyunfei'], true);
                                        if (json_last_error() !== JSON_ERROR_NONE) {
                                            echo '<span class="text-danger">JSON解析失败</span>';
                                        } elseif (empty($yunfeiData)) {
                                            echo '<span class="text-muted">无数据</span>';
                                        } else {
                                            echo '<pre class="mb-0" style="max-height: 100px; overflow-y: auto;">' . htmlspecialchars(json_encode($yunfeiData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                                        }
                                        ?>
                                    </td>
                                    <td><?php echo $yunfei['create_at']; ?></td>
                                    <td>
                                        <?php if (hasPermission('yunfei.edit')): ?>
                                            <a href="<?php echo APP_URL; ?>/yunfei.php?action=edit&id=<?php echo $yunfei['id']; ?>" class="btn btn-sm btn-primary">
                                                <i class="fa fa-edit"></i> 编辑
                                            </a>
                                        <?php endif; ?>
                                        
                                        <?php if (hasPermission('yunfei.delete')): ?>
                                            <a href="<?php echo APP_URL; ?>/yunfei.php?action=delete&id=<?php echo $yunfei['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这条运费记录吗？');">
                                                <i class="fa fa-trash"></i> 删除
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <!-- 分页 -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Page navigation">
                        <ul class="pagination justify-content-center">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo APP_URL; ?>/yunfei.php?page=<?php echo $page - 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">上一页</a>
                                </li>
                            <?php endif; ?>
                            
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                    <a class="page-link" href="<?php echo APP_URL; ?>/yunfei.php?page=<?php echo $i; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                            
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?php echo APP_URL; ?>/yunfei.php?page=<?php echo $page + 1; ?><?php echo $search ? '&search=' . urlencode($search) : ''; ?>">下一页</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>