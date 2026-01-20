<div class="d-flex justify-content-between align-items-center mb-3">
    <h4><?php echo $title; ?></h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/track_costs.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">编辑赛道费用记录</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/track_costs.php?action=edit_post">
                    <input type="hidden" name="id" value="<?php echo $cost['id']; ?>">
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="track_name" class="form-label">赛道名称 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="track_name" name="track_name" required
                                       value="<?php echo htmlspecialchars($cost['track_name']); ?>"
                                       placeholder="请输入赛道名称" maxlength="50">
                                <div class="form-text">赛道名称</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost" class="form-label">费用金额（人民币）<span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">¥</span>
                                    <input type="number" step="0.01" class="form-control" id="cost" 
                                           name="cost" required placeholder="0.00"
                                           value="<?php echo htmlspecialchars($cost['cost']); ?>">
                                </div>
                                <div class="form-text">费用金额（人民币）</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost_type" class="form-label">费用类型 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="cost_type" name="cost_type" required
                                       value="<?php echo htmlspecialchars($cost['cost_type']); ?>"
                                       placeholder="请输入费用类型（如仓库费用、人员工资、其他费用等等）" maxlength="50">
                                <div class="form-text">费用类型，如仓库费用、人员工资、其他费用等等</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="cost_date" class="form-label">日期 <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="cost_date" name="cost_date" required
                                       value="<?php echo $cost['cost_date']; ?>">
                                <div class="form-text">费用日期，按天存储</div>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-12">
                            <div class="mb-3">
                                <label for="remark" class="form-label">备注</label>
                                <textarea class="form-control" id="remark" name="remark" rows="3"
                                          placeholder="请输入备注信息" maxlength="255"><?php echo htmlspecialchars($cost['remark'] ?? ''); ?></textarea>
                                <div class="form-text">可选，最多255个字符</div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">更新时间</label>
                        <div class="form-control-plaintext">
                            <strong><?php echo $cost['update_at']; ?></strong>
                            <small class="text-muted">（保存后自动更新为当前时间）</small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo ADMIN_PANEL_URL; ?>/track_costs.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> 保存更改
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">字段说明</h5>
            </div>
            <div class="card-body">
                <ul class="list-unstyled">
                    <li><strong>赛道名称</strong>: 赛道名称</li>
                    <li><strong>费用金额</strong>: 赛道费用金额（人民币）</li>
                    <li><strong>费用类型</strong>: 费用类型，如仓库费用、人员工资、其他费用等等</li>
                    <li><strong>日期</strong>: 费用发生的日期</li>
                    <li><strong>备注</strong>: 可选，用于记录额外信息</li>
                </ul>
            </div>
        </div>
    </div>
</div>
