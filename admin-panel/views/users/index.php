<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>用户列表</h5>
                <?php if (hasPermission('users.create')): ?>
                    <a href="<?php echo APP_URL; ?>/users.php?action=create" class="btn btn-primary">
                        <i class="fa fa-plus"></i> 创建用户
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>用户名</th>
                            <th>邮箱</th>
                            <th>姓名</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo $user['id']; ?></td>
                                <td><?php echo $user['username']; ?></td>
                                <td><?php echo $user['email']; ?></td>
                                <td><?php echo $user['full_name']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $user['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo $user['status'] == 'active' ? '激活' : '停用'; ?>
                                    </span>
                                </td>
                                <td><?php echo $user['created_at']; ?></td>
                                <td>
                                    <?php if (hasPermission('users.edit')): ?>
                                        <a href="<?php echo APP_URL; ?>/users.php?action=edit&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fa fa-edit"></i> 编辑
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (hasPermission('users.delete') && $user['id'] != getCurrentUserId()): ?>
                                        <a href="<?php echo APP_URL; ?>/users.php?action=delete&id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这个用户吗？');">
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