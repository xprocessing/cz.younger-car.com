<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5>产品列表</h5>
                <?php if (hasPermission('data.create')): ?>
                    <a href="<?php echo APP_URL; ?>/data.php?action=create" class="btn btn-primary">
                        <i class="fa fa-plus"></i> 创建产品
                    </a>
                <?php endif; ?>
            </div>
            <div class="card-body">
                <!-- 过滤表单 -->
                <form method="GET" action="<?php echo APP_URL; ?>/data.php" class="mb-4">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="mb-3">
                                <input type="text" class="form-control" name="search" placeholder="搜索产品名称或描述" value="<?php echo $_GET['search'] ?? ''; ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <select class="form-select" name="category">
                                    <option value="">所有分类</option>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category; ?>" <?php echo (isset($_GET['category']) && $_GET['category'] == $category) ? 'selected' : ''; ?>>
                                            <?php echo $category; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <select class="form-select" name="status">
                                    <option value="">所有状态</option>
                                    <option value="active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'active') ? 'selected' : ''; ?>>激活</option>
                                    <option value="inactive" <?php echo (isset($_GET['status']) && $_GET['status'] == 'inactive') ? 'selected' : ''; ?>>停用</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="mb-3">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fa fa-search"></i> 搜索
                                </button>
                                <a href="<?php echo APP_URL; ?>/data.php" class="btn btn-secondary">
                                    <i class="fa fa-refresh"></i> 重置
                                </a>
                            </div>
                        </div>
                    </div>
                </form>
                
                <!-- 产品列表 -->
                <table class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>名称</th>
                            <th>描述</th>
                            <th>价格</th>
                            <th>分类</th>
                            <th>状态</th>
                            <th>创建时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td><?php echo $product['name']; ?></td>
                                <td><?php echo substr($product['description'], 0, 50); ?><?php echo strlen($product['description']) > 50 ? '...' : ''; ?></td>
                                <td>¥<?php echo number_format($product['price'], 2); ?></td>
                                <td><?php echo $product['category']; ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $product['status'] == 'active' ? 'success' : 'danger'; ?>">
                                        <?php echo $product['status'] == 'active' ? '激活' : '停用'; ?>
                                    </span>
                                </td>
                                <td><?php echo $product['created_at']; ?></td>
                                <td>
                                    <?php if (hasPermission('data.edit')): ?>
                                        <a href="<?php echo APP_URL; ?>/data.php?action=edit&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fa fa-edit"></i> 编辑
                                        </a>
                                    <?php endif; ?>
                                    
                                    <?php if (hasPermission('data.delete')): ?>
                                        <a href="<?php echo APP_URL; ?>/data.php?action=delete&id=<?php echo $product['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('确定要删除这个产品吗？');">
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