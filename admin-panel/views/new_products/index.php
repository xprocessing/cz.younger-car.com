<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>新产品管理</h4>
    <div>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/new_products.php?action=create" class="btn btn-primary">
            <i class="fa fa-plus"></i> 新增新产品
        </a>
    </div>
</div>

<!-- 搜索框 -->
<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo ADMIN_PANEL_URL; ?>/new_products.php" class="row g-3">
            <div class="col-md-8">
                <input type="text" name="keyword" class="form-control" placeholder="搜索需求编号、需求名称、新产品ID、SKU..." 
                       value="<?php echo $_GET['keyword'] ?? ''; ?>">
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-outline-primary">
                    <i class="fa fa-search"></i> 搜索
                </button>
                <a href="<?php echo ADMIN_PANEL_URL; ?>/new_products.php" class="btn btn-outline-secondary">
                    <i class="fa fa-refresh"></i> 重置
                </a>
            </div>
        </form>
    </div>
</div>

<!-- 数据表格 -->
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>需求编号</th>
                        <th>图片</th>
                        <th>需求名称</th>
                        <th>新产品ID</th>
                        <th>SKU</th>
                        <th>创建时间</th>
                        <th>当前进度</th>
                        <th>操作</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="9" class="text-center">暂无数据</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($products as $product): ?>
                            <tr>
                                <td><?php echo $product['id']; ?></td>
                                <td>
                                    <strong><?php echo htmlspecialchars($product['require_no'] ?? ''); ?></strong>
                                </td>
                                <td>
                                    <?php if (!empty($product['img_url'])): ?>
                                        <img src="<?php echo htmlspecialchars($product['img_url'] ?? ''); ?>" 
                                             alt="图片" style="max-width: 50px; max-height: 50px; object-fit: cover;">
                                    <?php else: ?>
                                        <span class="text-muted">无图片</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($product['require_title'] ?? ''); ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($product['npdId'] ?? ''); ?>
                                </td>
                                <td>
                                    <code><?php echo htmlspecialchars($product['sku'] ?? ''); ?></code>
                                </td>
                                <td>
                                    <?php echo $product['create_time'] ?? ''; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        步骤 <?php echo $product['current_step'] ?? 0; ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?php echo ADMIN_PANEL_URL; ?>/new_products.php?action=edit&id=<?php echo $product['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary" title="编辑">
                                            <i class="fa fa-edit"></i>
                                        </a>
                                        <a href="javascript:void(0);" 
                                           onclick="if(confirm('确定要删除这个新产品吗？')) {
                                               window.location.href='<?php echo ADMIN_PANEL_URL; ?>/new_products.php?action=delete&id=<?php echo $product['id']; ?>';
                                           }" 
                                           class="btn btn-sm btn-outline-danger" title="删除">
                                            <i class="fa fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                            <?php if (!empty($product['remark'])): ?>
                            <tr>
                                <td colspan="9">
                                    <small class="text-muted">
                                        <strong>备注：</strong><?php echo htmlspecialchars($product['remark'] ?? ''); ?>
                                    </small>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- 进度明细模态框 -->
<div class="modal fade" id="processModal" tabindex="-1" aria-labelledby="processModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="processModalLabel">进度明细</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="processContent">
                    <!-- 动态内容 -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// 显示进度明细
function showProcess(id) {
    fetch('<?php echo ADMIN_PANEL_URL; ?>/new_products.php?action=get_process&id=' + id)
        .then(response => response.json())
        .then(data => {
            let html = '';
            if (data.error) {
                html = '<div class="alert alert-danger">' + data.error + '</div>';
            } else if (data.length === 0) {
                html = '<div class="alert alert-info">暂无进度明细</div>';
            } else {
                html = '<div class="timeline">';
                data.forEach((step, index) => {
                    const statusColor = step.status === 'completed' ? 'success' : 
                                       step.status === 'in_progress' ? 'warning' : 'secondary';
                    html += `
                        <div class="d-flex mb-3">
                            <div class="flex-shrink-0">
                                <div class="badge bg-${statusColor}">${step.step}</div>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6>${step.name}</h6>
                                <p class="mb-0">${step.description || '暂无描述'}</p>
                                <small class="text-muted">状态: ${step.status}</small>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
            }
            document.getElementById('processContent').innerHTML = html;
            new bootstrap.Modal(document.getElementById('processModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('processContent').innerHTML = 
                '<div class="alert alert-danger">加载失败</div>';
            new bootstrap.Modal(document.getElementById('processModal')).show();
        });
}
</script>

<style>
.timeline {
    position: relative;
    padding-left: 20px;
}
.timeline::before {
    content: '';
    position: absolute;
    left: 8px;
    top: 0;
    bottom: 0;
    width: 2px;
    background-color: #e9ecef;
}
</style>