<?php $title = '物流渠道管理'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo ADMIN_PANEL_URL; ?>/dashboard.php">首页</a></li>
                        <li class="breadcrumb-item active">物流渠道管理</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">物流渠道列表</h3>
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 300px;">
                                    <input type="text" name="keyword" class="form-control float-right" placeholder="搜索物流渠道名称、编码或服务商" value="<?php echo htmlspecialchars($keyword ?? ''); ?>">
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-default" onclick="window.location.href='<?php echo ADMIN_PANEL_URL; ?>/logistics.php?keyword='+encodeURIComponent(document.querySelector('input[name=keyword]').value)">
                                            <i class="fas fa-search"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-info btn-sm ml-2" onclick="window.location.href='<?php echo ADMIN_PANEL_URL; ?>/logistics.php?action=create'">
                                    <i class="fas fa-plus"></i> 创建物流渠道
                                </button>
                            </div>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body table-responsive p-0">
                            <table class="table table-hover text-nowrap">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>物流类型ID</th>
                                        <th>物流渠道名称</th>
                                        <th>编码</th>
                                        <th>服务商</th>
                                        <th>供应商编码</th>
                                        <th>仓库编码</th>
                                        <th>是否启用</th>
                                        <th>操作</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($logisticsList)): ?>
                                        <?php foreach ($logisticsList as $logistics): ?>
                                            <tr>
                                                <td><?php echo $logistics['id']; ?></td>
                                                <td><?php echo $logistics['type_id']; ?></td>
                                                <td><?php echo htmlspecialchars($logistics['name']); ?></td>
                                                <td><?php echo htmlspecialchars($logistics['code']); ?></td>
                                                <td><?php echo htmlspecialchars($logistics['logistics_provider_name']); ?></td>
                                                <td><?php echo htmlspecialchars($logistics['supplier_code']); ?></td>
                                                <td><?php echo htmlspecialchars($logistics['wp_code']); ?></td>
                                                <td>
                                                    <span class="badge <?php echo $logistics['is_used'] ? 'bg-success' : 'bg-danger'; ?>">
                                                        <?php echo $logistics['is_used'] ? '启用' : '禁用'; ?>
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="<?php echo ADMIN_PANEL_URL; ?>/logistics.php?action=edit&type_id=<?php echo $logistics['type_id']; ?>" class="btn btn-sm btn-primary">
                                                        <i class="fas fa-edit"></i> 编辑
                                                    </a>
                                                    <a href="<?php echo ADMIN_PANEL_URL; ?>/logistics.php?action=delete&type_id=<?php echo $logistics['type_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除此物流渠道吗？');">
                                                        <i class="fas fa-trash"></i> 删除
                                                    </a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="9" class="text-center">暂无数据</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                        <!-- /.card-body -->
                        <div class="card-footer clearfix">
                            <ul class="pagination pagination-sm m-0 float-right">
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo ADMIN_PANEL_URL; ?>/logistics.php?page=<?php echo $page - 1; ?>&keyword=<?php echo urlencode($keyword ?? ''); ?>">&laquo;</a>
                                    </li>
                                <?php endif; ?>
                                
                                <?php for ($i = max(1, $page - 2); $i <= min($totalPages, $page + 2); $i++): ?>
                                    <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="<?php echo ADMIN_PANEL_URL; ?>/logistics.php?page=<?php echo $i; ?>&keyword=<?php echo urlencode($keyword ?? ''); ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>
                                
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?php echo ADMIN_PANEL_URL; ?>/logistics.php?page=<?php echo $page + 1; ?>&keyword=<?php echo urlencode($keyword ?? ''); ?>">&raquo;</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->