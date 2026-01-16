<div class="container-fluid">
    <!-- 页面标题 -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">任务历史</h1>
        <a href="<?php echo ADMIN_PANEL_URL; ?>/aigc.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> 返回处理页面
        </a>
    </div>

    <!-- 任务列表 -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">任务列表</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="tasksTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>任务ID</th>
                            <th>任务名称</th>
                            <th>任务类型</th>
                            <th>状态</th>
                            <th>用户ID</th>
                            <th>原始路径</th>
                            <th>处理状态</th>
                            <th>结果URL</th>
                            <th>开始时间</th>
                            <th>完成时间</th>
                            <th>操作</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>任务ID</th>
                            <th>任务名称</th>
                            <th>任务类型</th>
                            <th>状态</th>
                            <th>用户ID</th>
                            <th>原始路径</th>
                            <th>处理状态</th>
                            <th>结果URL</th>
                            <th>开始时间</th>
                            <th>完成时间</th>
                            <th>操作</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        <?php if (empty($tasks)): ?>
                            <tr>
                                <td colspan="11" class="text-center">暂无任务记录</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
                                    <td><?php echo $task['task_id']; ?></td>
                                    <td><?php echo htmlspecialchars($task['task_name']); ?></td>
                                    <td>
                                        <?php 
                                            $typeMap = [
                                                'remove_defect' => '批量去瑕疵',
                                                'crop_png' => '批量抠图(PNG)',
                                                'crop_white_bg' => '批量抠图(白底)',
                                                'resize' => '批量改尺寸',
                                                'watermark' => '批量打水印',
                                                'face_swap' => '智能换脸',
                                                'multi_angle' => '多角度图片',
                                                'image_to_image' => '图生图',
                                                'text_to_image' => '文生图',
                                                'other' => '其他'
                                            ];
                                            echo $typeMap[$task['task_type']] ?? $task['task_type'];
                                        ?>
                                    </td>
                                    <td>
                                        <?php 
                                            $statusClass = '';
                                            $statusText = '';
                                            switch ($task['task_status']) {
                                                case 'pending':
                                                    $statusClass = 'badge-warning';
                                                    $statusText = '等待处理';
                                                    break;
                                                case 'processing':
                                                    $statusClass = 'badge-info';
                                                    $statusText = '处理中';
                                                    break;
                                                case 'completed':
                                                    $statusClass = 'badge-success';
                                                    $statusText = '已完成';
                                                    break;
                                                case 'failed':
                                                    $statusClass = 'badge-danger';
                                                    $statusText = '失败';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-secondary';
                                                    $statusText = '未知状态';
                                            }
                                        ?>
                                        <span class="badge <?php echo $statusClass; ?>"><?php echo $statusText; ?></span>
                                    </td>
                                    <td><?php echo $task['user_id']; ?></td>
                                    <td><?php echo htmlspecialchars($task['original_path'] ?? '-'); ?></td>
                                    <td>
                                        <?php if ($task['process_status']): ?>
                                            <span class="badge badge-<?php echo $task['process_status'] == 'success' ? 'success' : 'danger'; ?>">
                                                <?php echo $task['process_status'] == 'success' ? '成功' : '失败'; ?>
                                            </span>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($task['result_url']): ?>
                                            <a href="<?php echo htmlspecialchars($task['result_url']); ?>" target="_blank">查看</a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $task['started_at'] ? date('Y-m-d H:i:s', strtotime($task['started_at'])) : '-'; ?></td>
                                    <td><?php echo $task['completed_at'] ? date('Y-m-d H:i:s', strtotime($task['completed_at'])) : '-'; ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-info view-task-btn" data-task-id="<?php echo $task['task_id']; ?>">
                                            <i class="fas fa-eye"></i> 查看
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- 任务详情卡片 -->
    <div id="taskDetailCard" class="card shadow mb-4" style="display: none;">
        <div class="card-header py-3">
            <div class="row">
                <div class="col-md-9">
                    <h6 class="m-0 font-weight-bold text-primary" id="taskDetailTitle">任务详情</h6>
                </div>
                <div class="col-md-3 text-right">
                    <button class="btn btn-sm btn-secondary" id="closeDetailBtn">
                        <i class="fas fa-times"></i> 关闭
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body">
            <div id="taskDetailContent">
                <!-- 任务详情将通过AJAX加载 -->
            </div>
        </div>
    </div>
</div>

<!-- JavaScript 用于AJAX加载任务详情 -->
<script>
    $(document).ready(function() {
        // 查看任务详情按钮点击事件
        $('.view-task-btn').click(function() {
            var taskId = $(this).data('task-id');
            loadTaskDetail(taskId);
        });
        
        // 关闭详情按钮点击事件
        $('#closeDetailBtn').click(function() {
            $('#taskDetailCard').hide();
        });
    });
    
    // 加载任务详情
    function loadTaskDetail(taskId) {
        $.ajax({
            url: '<?php echo ADMIN_PANEL_URL; ?>/aigc.php?action=getTaskDetail',
            type: 'GET',
            data: { id: taskId },
            dataType: 'json',
            beforeSend: function() {
                $('#taskDetailContent').html('<div class="text-center"><i class="fas fa-spinner fa-spin" style="font-size: 24px;"></i> 加载中...</div>');
                $('#taskDetailCard').show();
            },
            success: function(response) {
                if (response.success) {
                    var task = response.task;
                    var results = response.results;
                    var html = buildTaskDetailHTML(task, results);
                    $('#taskDetailContent').html(html);
                    $('#taskDetailTitle').text('任务详情 - ' + task.task_name);
                } else {
                    $('#taskDetailContent').html('<div class="alert alert-danger" role="alert">' + response.message + '</div>');
                }
            },
            error: function() {
                $('#taskDetailContent').html('<div class="alert alert-danger" role="alert">加载任务详情失败</div>');
            }
        });
    }
    
    // 构建任务详情HTML
    function buildTaskDetailHTML(task, results) {
        var typeMap = {
            'remove_defect': '批量去瑕疵',
            'crop_png': '批量抠图(PNG)',
            'crop_white_bg': '批量抠图(白底)',
            'resize': '批量改尺寸',
            'watermark': '批量打水印',
            'face_swap': '智能换脸',
            'multi_angle': '多角度图片',
            'text_to_image': '文生图',
            'image_to_image': '图生图'
        };
        
        var statusMap = {
            'pending': '等待处理',
            'processing': '处理中',
            'completed': '已完成',
            'failed': '失败'
        };
        
        var html = '';
        
        // 任务基本信息
        html += '<div class="row">';
        html += '<div class="col-md-3">';
        html += '<p><strong>任务类型:</strong></p>';
        html += '<p>' + (typeMap[task.task_type] || task.task_type) + '</p>';
        html += '</div>';
        html += '<div class="col-md-3">';
        html += '<p><strong>任务状态:</strong></p>';
        html += '<p><span class="badge badge-' + (task.task_status == 'completed' ? 'success' : (task.task_status == 'failed' ? 'danger' : 'warning')) + '">' + (statusMap[task.task_status] || task.task_status) + '</span></p>';
        html += '</div>';
        
        html += '<div class="col-md-3">';
        html += '<p><strong>处理数量:</strong></p>';
        html += '<p>' + task.total_count + '</p>';
        html += '</div>';
        html += '<div class="col-md-3">';
        html += '<p><strong>成功/失败:</strong></p>';
        html += '<p>' + task.success_count + '/' + task.failed_count + '</p>';
        html += '</div>';
        html += '</div>';
        
        html += '<div class="row mt-3">';
        html += '<div class="col-md-6">';
        html += '<p><strong>开始时间:</strong></p>';
        html += '<p>' + new Date(task.started_at).toLocaleString() + '</p>';
        html += '</div>';
        html += '<div class="col-md-6">';
        html += '<p><strong>完成时间:</strong></p>';
        html += '<p>' + (task.completed_at ? new Date(task.completed_at).toLocaleString() : '未完成') + '</p>';
        html += '</div>';
        html += '</div>';
        
        // 处理结果
        html += '<div class="mt-5">';
        html += '<h5>处理结果</h5>';
        
        if (results.length === 0) {
            html += '<p class="text-center text-muted">没有处理结果</p>';
        } else {
            html += '<div class="row">';
            
            $.each(results, function(index, result) {
                html += '<div class="col-lg-6 mb-4">';
                html += '<div class="card">';
                html += '<div class="card-header">';
                html += '<h6 class="card-title">';
                html += result.original_filename || '未知文件名';
                html += '<span class="badge badge-' + (result.process_status == 'success' ? 'success' : 'danger') + '">';
                html += result.process_status == 'success' ? '处理成功' : '处理失败';
                html += '</span>';
                html += '</h6>';
                html += '</div>';
                html += '<div class="card-body">';
                
                if (result.process_status == 'failed') {
                    html += '<div class="alert alert-danger" role="alert">';
                    html += '错误信息：' + (result.error_message || '未知错误');
                    html += '</div>';
                } else {
                    html += '<div class="row">';
                    html += '<div class="col-md-12">';
                    html += '<h6>处理结果</h6>';
                    html += '<div class="img-container text-center">';
                    html += '<img src="' + result.result_url + '" ';
                    html += 'class="img-thumbnail" alt="处理后" style="max-width: 100%; max-height: 300px;">';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '<div class="mt-3 text-center">';
                    html += '<button class="btn btn-sm btn-secondary mr-2" ';
                    html += 'onclick="viewImage(\'' + result.result_url + '\')">';
                    html += '<i class="fas fa-eye"></i> 预览';
                    html += '</button>';
                    html += '<a href="' + result.result_url + '" ';
                    html += 'class="btn btn-sm btn-primary" ';
                    html += 'download="processed_' + (result.original_filename || 'image_' + index + '.jpg') + '">';
                    html += '<i class="fas fa-download"></i> 下载';
                    html += '</a>';
                    html += '</div>';
                }
                html += '</div>';
                html += '</div>';
                html += '</div>';
            });
            html += '</div>';
        }
        html += '</div>';
        return html;
    }
    
    // 图片预览功能
    function viewImage(imageData) {
        // 如果预览模态框不存在，则创建它
        if (!$('#imagePreviewModal').length) {
            var modalHTML = `
                <div class="modal fade" id="imagePreviewModal" tabindex="-1" role="dialog" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-lg" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imagePreviewModalLabel">图片预览</h5>
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                            <div class="modal-body text-center">
                                <img id="previewImage" src="" class="img-fluid" alt="图片预览">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                <a id="downloadPreviewLink" href="" class="btn btn-primary" download>下载图片</a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHTML);
        }
        
        // 获取元素引用（确保模态框已创建）
        const previewImage = document.getElementById('previewImage');
        const downloadPreviewLink = document.getElementById('downloadPreviewLink');
        
        // 设置预览图片
        previewImage.src = imageData;
        downloadPreviewLink.href = imageData;
        
        // 显示模态框
        $('#imagePreviewModal').modal('show');
    }
</script>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>