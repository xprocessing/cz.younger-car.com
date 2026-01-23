<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php" class="btn btn-outline-secondary">
        <i class="fa fa-arrow-left"></i> 返回列表
    </a>
</div>

<?php if ($error): ?>
    <div class="alert alert-danger" role="alert">
        <?php echo $error; ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['import_errors']) && !empty($_SESSION['import_errors'])): ?>
    <div class="alert alert-warning" role="alert">
        <h6>导入错误信息：</h6>
        <ul>
            <?php foreach ($_SESSION['import_errors'] as $error): ?>
                <li><?php echo $error; ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['import_errors']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <div class="alert alert-info" role="alert">
            <h6>导入说明：</h6>
            <ul>
                <li>支持CSV格式文件导入</li>
                <li>支持直接粘贴CSV内容导入</li>
                <li>必填字段：订单号、本地SKU、国家</li>
                <li>CSV格式：订单号,本地SKU,国家,城市,邮编,仓库ID,物流方式ID,预估邮费,审单状态,审单时间,审单备注</li>
                <li>单次最多导入1000条记录</li>
            </ul>
        </div>
        
        <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?action=importPost" enctype="multipart/form-data">
            <!-- 文件上传 -->
            <div class="mb-4">
                <label for="excel_file" class="form-label">选择CSV文件</label>
                <input type="file" name="excel_file" class="form-control" accept=".csv">
            </div>
            
            <!-- 粘贴导入 -->
            <div class="mb-4">
                <label for="csv_content" class="form-label">或粘贴CSV内容</label>
                <textarea name="csv_content" class="form-control" rows="10" placeholder="订单号,本地SKU,国家,城市,邮编,仓库ID,物流方式ID,预估邮费,审单状态,审单时间,审单备注"></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary">开始导入</button>
        </form>
    </div>
</div>