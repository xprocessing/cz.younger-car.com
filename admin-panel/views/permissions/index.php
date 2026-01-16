<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>权限列表</h5>
                <?php if (hasPermission('permissions.create')): ?>
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/permissions.php?action=create" class="btn btn-primary">
                        <i class="fa fa-plus"></i> 创建权限
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <?php foreach ($permissions as $module => $modulePermissions): ?>
                    <div class="mb-4">
                        <h6 class="text-primary"><?php echo ucfirst($module); ?></h6>
                        <table class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>名称</th>
                                    <th>标识</th>
                                    <th>描述</th>
                                    <th>创建时间</th>
                                    <th>操作</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($modulePermissions as $permission): ?>
                                    <tr>
                                        <td><?php echo $permission['id']; ?></td>
                                        <td><?php echo $permission['name']; ?></td>
                                        <td><?php echo $permission['slug']; ?></td>
                                        <td><?php echo $permission['description']; ?></td>
                                        <td><?php echo $permission['created_at']; ?></td>
                                        <td>
                                            <?php if (hasPermission('permissions.edit')): ?>
                                                <a href="<?php echo ADMIN_PANEL_URL; ?>/permissions.php?action=edit&id=<?php echo $permission['id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="fa fa-edit"></i> 编辑
                                                </a>
                                            <?php endif; ?>
                                            
                                            <?php if (hasPermission('permissions.delete')): ?>
                                                <a href="<?php echo ADMIN_PANEL_URL; ?>/permissions.php?action=delete&id=<?php echo $permission['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这个权限吗？');">
                                                    <i class="fa fa-trash"></i> 删除
                                                </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>