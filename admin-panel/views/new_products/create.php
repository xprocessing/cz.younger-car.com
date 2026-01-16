<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>创建新产品</h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/new_products.php" class="btn btn-secondary">
            <i class="fa fa-arrow-left"></i> 返回列表
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">基本信息</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="<?php echo ADMIN_PANEL_URL; ?>/new_products.php?action=create_post">
                    <div class="mb-3">
                        <label for="require_no" class="form-label">需求编号 <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="require_no" name="require_no" required
                               placeholder="请输入需求编号" maxlength="50">
                        <div class="form-text">唯一标识一个需求</div>
                    </div>

                    <div class="mb-3">
                        <label for="require_title" class="form-label">需求名称</label>
                        <input type="text" class="form-control" id="require_title" name="require_title"
                               placeholder="请输入需求名称" maxlength="255">
                    </div>

                    <div class="mb-3">
                        <label for="img_url" class="form-label">图片URL</label>
                        <input type="url" class="form-control" id="img_url" name="img_url"
                               placeholder="请输入图片URL" maxlength="255">
                        <div class="form-text">支持HTTP/HTTPS链接</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="npdId" class="form-label">新产品ID</label>
                                <input type="text" class="form-control" id="npdId" name="npdId"
                                       placeholder="请输入新产品ID" maxlength="50">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sku" class="form-label">SKU</label>
                                <input type="text" class="form-control" id="sku" name="sku"
                                       placeholder="请输入SKU" maxlength="50">
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_time" class="form-label">创建时间</label>
                        <input type="date" class="form-control" id="create_time" name="create_time"
                               value="<?php echo date('Y-m-d'); ?>">
                    </div>

                    <div class="mb-3">
                        <label for="current_step" class="form-label">当前进度</label>
                        <select class="form-control" id="current_step" name="current_step">
                            <option value="0">未开始</option>
                            <option value="1">步骤 1</option>
                            <option value="2">步骤 2</option>
                            <option value="3">步骤 3</option>
                            <option value="4">步骤 4</option>
                            <option value="5">步骤 5</option>
                            <option value="6">步骤 6</option>
                            <option value="7">步骤 7</option>
                            <option value="8">步骤 8</option>
                            <option value="9">步骤 9</option>
                            <option value="10">步骤 10</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="remark" class="form-label">备注</label>
                        <textarea class="form-control" id="remark" name="remark" rows="3"
                                  placeholder="请输入备注信息" maxlength="255"></textarea>
                    </div>

                    <div class="mb-3">
                        <label for="process_list" class="form-label">进度明细（JSON格式）</label>
                        <textarea class="form-control" id="process_list" name="process_list" rows="8"
                                  placeholder='[{"flowNodeName": "创建", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []}, {"flowNodeName": "提交", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []}]'></textarea>
                        <div class="form-text">
                            <small>
                                JSON格式示例：<br>
                                [{"flowNodeName": "创建", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},<br>
                                &nbsp;&nbsp;{"flowNodeName": "提交", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []}]<br>
                                请按照提供的完整格式填写所有流程节点
                            </small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo ADMIN_PANEL_URL; ?>/new_products.php" class="btn btn-secondary me-md-2">
                            <i class="fa fa-times"></i> 取消
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> 创建
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">JSON格式说明</h5>
            </div>
            <div class="card-body">
                <h6>进度明细格式：</h6>
                <pre class="bg-light p-3 rounded"><code>[
  {
    "flowNodeName": "创建",
    "auditStatus": null,
    "defaultAuditUserId": 10846285,
    "defaultAuditUserName": "王荣",
    "defaultTime": "",
    "auditUserList": []
  },
  {
    "flowNodeName": "提交",
    "auditStatus": null,
    "defaultAuditUserId": 10846285,
    "defaultAuditUserName": "王荣",
    "defaultTime": "",
    "auditUserList": []
  }
]</code></pre>

                <h6 class="mt-3">字段说明：</h6>
                <ul class="list-unstyled">
                    <li><strong>flowNodeName</strong>: 流程节点名称</li>
                    <li><strong>auditStatus</strong>: 审核状态（null: 未开始, 121: 进行中, 123: 已完成）</li>
                    <li><strong>defaultAuditUserId</strong>: 默认审核人ID</li>
                    <li><strong>defaultAuditUserName</strong>: 默认审核人名称</li>
                    <li><strong>defaultTime</strong>: 默认时间</li>
                    <li><strong>auditUserList</strong>: 审核用户列表（数组）</li>
                </ul>

                <h6 class="mt-3">auditUserList字段说明：</h6>
                <ul class="list-unstyled">
                    <li><strong>auditUserId</strong>: 审核人ID</li>
                    <li><strong>auditUserName</strong>: 审核人名称</li>
                    <li><strong>auditComments</strong>: 审核备注</li>
                    <li><strong>auditStatus</strong>: 审核状态</li>
                    <li><strong>auditTime</strong>: 审核时间</li>
                </ul>

                <h6 class="mt-3">状态值：</h6>
                <ul class="list-unstyled">
                    <li><span class="badge bg-secondary">null</span> 未开始</li>
                    <li><span class="badge bg-warning">121</span> 进行中</li>
                    <li><span class="badge bg-success">123</span> 已完成</li>
                </ul>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <h5 class="mb-0">快速模板</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <button class="btn btn-sm btn-outline-primary w-100" onclick="fillTemplate('simple')">
                        简单模板
                    </button>
                </div>
                <div class="mb-2">
                    <button class="btn btn-sm btn-outline-info w-100" onclick="fillTemplate('detailed')">
                        详细模板
                    </button>
                </div>
                <div class="mb-2">
                    <button class="btn btn-sm btn-outline-secondary w-100" onclick="fillTemplate('empty')">
                        清空内容
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function fillTemplate(type) {
    const processList = document.getElementById('process_list');
    
    switch(type) {
        case 'simple':
            processList.value = JSON.stringify([
                {"flowNodeName": "需求分析", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "设计", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "开发", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "测试", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "发布", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []}
            ], null, 2);
            break;
        case 'detailed':
            processList.value = JSON.stringify([
                {"flowNodeName": "需求收集", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "需求分析", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "产品设计", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "开发实现", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "单元测试", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "集成测试", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "用户验收", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "部署准备", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "正式发布", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []},
                {"flowNodeName": "运维支持", "auditStatus": null, "defaultAuditUserId": 10846285, "defaultAuditUserName": "王荣", "defaultTime": "", "auditUserList": []}
            ], null, 2);
            break;
        case 'empty':
            processList.value = '';
            break;
    }
}

// 表单验证
document.querySelector('form').addEventListener('submit', function(e) {
    const requireNo = document.getElementById('require_no').value.trim();
    if (!requireNo) {
        e.preventDefault();
        alert('请输入需求编号');
        document.getElementById('require_no').focus();
        return;
    }

    const processList = document.getElementById('process_list').value.trim();
    if (processList) {
        try {
            JSON.parse(processList);
        } catch (e) {
            e.preventDefault();
            alert('进度明细JSON格式错误，请检查格式');
            document.getElementById('process_list').focus();
            return;
        }
    }
});
</script>