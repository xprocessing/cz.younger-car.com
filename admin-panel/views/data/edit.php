<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>编辑产品</h5>
            </div>
            <div class="card-body">
                <form action="<?php echo APP_URL; ?>/data.php?action=edit&id=<?php echo $product['id']; ?>" method="POST">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="name" class="form-label">产品名称</label>
                                <input type="text" class="form-control" id="name" name="name" value="<?php echo $product['name']; ?>" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">产品价格</label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label">产品分类</label>
                                <select class="form-select" id="category" name="category" required>
                                    <?php foreach ($categories as $category): ?>
                                        <option value="<?php echo $category; ?>" <?php echo ($product['category'] == $category) ? 'selected' : ''; ?>><?php echo $category; ?></option>
                                    <?php endforeach; ?>
                                    <option value="其他" <?php echo ($product['category'] == '其他') ? 'selected' : ''; ?>>其他</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="status" class="form-label">产品状态</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="active" <?php echo ($product['status'] == 'active') ? 'selected' : ''; ?>>激活</option>
                                    <option value="inactive" <?php echo ($product['status'] == 'inactive') ? 'selected' : ''; ?>>停用</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">产品描述</label>
                        <textarea class="form-control" id="description" name="description" rows="5"><?php echo $product['description']; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">更新产品</button>
                    <a href="<?php echo APP_URL; ?>/data.php" class="btn btn-secondary">取消</a>
                </form>
            </div>
        </div>
    </div>
</div>