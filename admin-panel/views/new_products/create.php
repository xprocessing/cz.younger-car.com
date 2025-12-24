<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>创建新产品</h4>
    <div>
        <a href="<?php echo APP_URL; ?>/new_products.php" class="btn btn-secondary">
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
                <form method="POST" action="<?php echo APP_URL; ?>/new_products.php?action=create_post">
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
                                  placeholder='[{"step": 1, "name": "需求分析", "status": "completed", "description": "完成需求分析"}, {"step": 2, "name": "设计", "status": "in_progress", "description": "进行中"}]'></textarea>
                        <div class="form-text">
                            <small>
                                JSON格式示例：<br>
                                [{"step": 1, "name": "需求分析", "status": "completed", "description": "完成需求分析"},<br>
                                &nbsp;&nbsp;{"step": 2, "name": "设计", "status": "in_progress", "description": "进行中"}]<br>
                                status值: pending(待处理), in_progress(进行中), completed(已完成)
                            </small>
                        </div>
                    </div>

                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="<?php echo APP_URL; ?>/new_products.php" class="btn btn-secondary me-md-2">
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
    "step": 1,
    "name": "需求分析",
    "status": "completed",
    "description": "完成需求分析"
  },
  {
    "step": 2,
    "name": "设计",
    "status": "in_progress",
    "description": "进行中"
  }
]</code></pre>

                <h6 class="mt-3">字段说明：</h6>
                <ul class="list-unstyled">
                    <li><strong>step</strong>: 步骤编号（数字）</li>
                    <li><strong>name</strong>: 步骤名称</li>
                    <li><strong>status</strong>: 状态</li>
                    <li><strong>description</strong>: 描述</li>
                </ul>

                <h6 class="mt-3">状态值：</h6>
                <ul class="list-unstyled">
                    <li><span class="badge bg-secondary">pending</span> 待处理</li>
                    <li><span class="badge bg-warning">in_progress</span> 进行中</li>
                    <li><span class="badge bg-success">completed</span> 已完成</li>
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
                {"step": 1, "name": "需求分析", "status": "pending", "description": ""},
                {"step": 2, "name": "设计", "status": "pending", "description": ""},
                {"step": 3, "name": "开发", "status": "pending", "description": ""},
                {"step": 4, "name": "测试", "status": "pending", "description": ""},
                {"step": 5, "name": "发布", "status": "pending", "description": ""}
            ], null, 2);
            break;
        case 'detailed':
            processList.value = JSON.stringify([
                {"step": 1, "name": "需求收集", "status": "pending", "description": "收集和整理用户需求"},
                {"step": 2, "name": "需求分析", "status": "pending", "description": "分析需求可行性和技术方案"},
                {"step": 3, "name": "产品设计", "status": "pending", "description": "产品设计和技术设计"},
                {"step": 4, "name": "开发实现", "status": "pending", "description": "编码实现功能"},
                {"step": 5, "name": "单元测试", "status": "pending", "description": "开发和测试团队进行测试"},
                {"step": 6, "name": "集成测试", "status": "pending", "description": "系统集成测试"},
                {"step": 7, "name": "用户验收", "status": "pending", "description": "用户验收测试"},
                {"step": 8, "name": "部署准备", "status": "pending", "description": "准备生产环境部署"},
                {"step": 9, "name": "正式发布", "status": "pending", "description": "正式上线发布"},
                {"step": 10, "name": "运维支持", "status": "pending", "description": "后续运维和支持"}
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