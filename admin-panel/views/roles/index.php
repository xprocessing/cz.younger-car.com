<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>角色列表</h5>
                <?php if (hasPermission('roles.create')): ?>
                    <a href="<?php echo APP_URL; ?>/roles.php?action=create" class="btn btn-primary">
                        <i class="fa fa-plus"></i> 创建角色
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>名称</th>
                            <th>描述</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td><?php echo $role['id']; ?></td>
                                <td><?php echo $role['name']; ?></td>
                                <td><?php echo $role['description']; ?></td>
                                <td><?php echo $role['created_at']; ?></td>
                                <td>
                                    <?php if (hasPermission('roles.edit')): ?>
                                        <a href="<?php echo APP_URL; ?>/roles.php?action=edit&id=<?php echo $role['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fa fa-edit"></i> 编辑
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (hasPermission('roles.delete') && $role['name'] !== 'admin'): ?>
                                        <a href="<?php echo APP_URL; ?>/roles.php?action=delete&id=<?php echo $role['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这个角色吗？');">
                                            <i class="fa fa-trash"></i> 删除
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>