<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>创建权限</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo ADMIN_PANEL_URL; ?>/permissions.php?action=create" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">权限名称</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="slug" class="form-label">权限标识</label>
                                <input type="text" class="form-control" id="slug" name="slug" required placeholder="例如：users.view">
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="module" class="form-label">所属模块</label>
                                <select class="form-select" id="module" name="module" required onchange="updateSlug()">
                                    <option value="">请选择模块</option>
                                    <option value="users">用户管理</option>
                                    <option value="roles">角色管理</option>
                                    <option value="permissions">权限管理</option>
                                    <option value="data">数据管理</option>
                                    <option value="yunfei">运费管理</option>
                                </select>
                                <div class="form-text">或者输入自定义模块名称</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="custom_module" class="form-label">自定义模块</label>
                                <input type="text" class="form-control" id="custom_module" name="custom_module" placeholder="如果选择上面没有的模块，请在此输入">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">权限描述</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">创建权限</button>
                    <a href="<?php echo ADMIN_PANEL_URL; ?>/permissions.php" class="btn btn-secondary">取消</a>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function updateSlug() {
    const module = document.getElementById('module').value;
    const name = document.getElementById('name').value;
    const slug = document.getElementById('slug');
    
    if (module && name) {
        // 生成建议的slug，格式：module.action
        const action = name.replace(/[查看|创建|编辑|删除]/g, '').trim();
        const suggestedSlug = module + '.' + action.toLowerCase();
        slug.value = suggestedSlug;
    }
}

// 当权限名称改变时也更新slug
document.getElementById('name').addEventListener('input', function() {
    updateSlug();
});

// 处理自定义模块
document.getElementById('custom_module').addEventListener('input', function() {
    const customModule = this.value;
    const moduleSelect = document.getElementById('module');
    
    if (customModule) {
        // 如果输入了自定义模块，清空选择
        moduleSelect.value = '';
        updateSlug();
    }
});

// 表单提交前处理模块选择
document.querySelector('form').addEventListener('submit', function(e) {
    const customModule = document.getElementById('custom_module').value;
    const moduleSelect = document.getElementById('module');
    
    if (customModule) {
        // 如果有自定义模块，使用自定义模块值
        moduleSelect.value = customModule;
    } else if (!moduleSelect.value) {
        // 如果两个都没填，阻止提交
        e.preventDefault();
        alert('请选择或输入模块名称');
    }
});
</script>