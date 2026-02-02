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

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/order_review.php?action=createPost" class="row g-3">
            <!-- 基本信息 -->
            <div class="col-md-6">
                <label for="store_id" class="form-label">店铺ID</label>
                <input type="text" name="store_id" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="global_order_no" class="form-label">订单号 <span class="text-danger">*</span></label>
                <input type="text" name="global_order_no" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="local_sku" class="form-label">本地SKU <span class="text-danger">*</span></label>
                <input type="text" name="local_sku" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="receiver_country_code" class="form-label">国家 <span class="text-danger">*</span></label>
                <input type="text" name="receiver_country_code" class="form-control" required>
            </div>
            <div class="col-md-6">
                <label for="city" class="form-label">城市</label>
                <input type="text" name="city" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="postal_code" class="form-label">邮编</label>
                <input type="text" name="postal_code" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="wid" class="form-label">仓库ID</label>
                <input type="number" name="wid" class="form-control">
            </div>
            
            <!-- 物流信息 -->
            <div class="col-md-6">
                <label for="logistics_type_id" class="form-label">物流方式ID</label>
                <input type="number" name="logistics_type_id" class="form-control">
            </div>
            <div class="col-md-6">
                <label for="estimated_yunfei" class="form-label">预估邮费</label>
                <input type="text" name="estimated_yunfei" class="form-control">
            </div>
            
            <!-- 审单信息 -->
            <div class="col-md-6">
                <label for="review_status" class="form-label">审单状态</label>
                <select name="review_status" class="form-select">
                    <option value="">请选择</option>
                    <option value="自动审核">自动审核</option>
                    <option value="人工审核">人工审核</option>
                </select>
            </div>
            <div class="col-md-6">
                <label for="review_time" class="form-label">审单时间</label>
                <input type="datetime-local" name="review_time" class="form-control">
            </div>
            <div class="col-md-12">
                <label for="review_remark" class="form-label">审单备注</label>
                <textarea name="review_remark" class="form-control" rows="3"></textarea>
            </div>
            
            <!-- 运费信息（JSON格式） -->
            <div class="col-md-12">
                <label class="form-label">运费信息（JSON格式，选填）</label>
                <div class="alert alert-info" role="alert">
                    请输入JSON格式的运费信息，例如：{"price": 100, "currency": "CNY"}
                </div>
            </div>
            <div class="col-md-6">
                <label for="wd_yunfei" class="form-label">运德运费</label>
                <textarea name="wd_yunfei" class="form-control" rows="2" placeholder='{"price": 0, "currency": "CNY"}'></textarea>
            </div>
            <div class="col-md-6">
                <label for="ems_yunfei" class="form-label">中邮运费</label>
                <textarea name="ems_yunfei" class="form-control" rows="2" placeholder='{"price": 0, "currency": "CNY"}'></textarea>
            </div>
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">保存记录</button>
                <a href="<?php echo ADMIN_PANEL_URL; ?>/order_review.php" class="btn btn-outline-secondary">取消</a>
            </div>
        </form>
    </div>
</div>