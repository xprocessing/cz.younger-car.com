<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>编辑角色</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo APP_URL; ?>/roles.php?action=edit&id=<?php echo $role['id']; ?>" method="POST">
                    <div class="mb-3">
                        <label for="name" class="form-label">角色名称</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?php echo $role['name']; ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">角色描述</label>
                        <textarea class="form-control" id="description" name="description" rows="3"><?php echo $role['description']; ?></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">权限分配</label>
                        <div class="border p-3 rounded">
                            <?php foreach ($permissions as $module => $modulePermissions): ?>
                                <div class="mb-3">
                                    <h6><?php echo ucfirst($module); ?></h6>
                                    <div class="row">
                                        <?php foreach ($modulePermissions as $permission): ?>
                                            <div class="col-md-4">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" id="permission_<?php echo $permission['id']; ?>" name="permissions[]" value="<?php echo $permission['id']; ?>" <?php echo in_array($permission['id'], $rolePermissionIds) ? 'checked' : ''; ?>>
                                                    <label class="form-check-label" for="permission_<?php echo $permission['id']; ?>">
                                                        <?php echo $permission['name']; ?> (<?php echo $permission['slug']; ?>)
                                                    </label>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">更新角色</button>
                    <a href="<?php echo APP_URL; ?>/roles.php" class="btn btn-secondary">取消</a>
                </form>
            </div>
        </div>
    </div>
</div>