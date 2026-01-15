<?php include VIEWS_DIR . '/layouts/header.php'; ?>

<div class="container-fluid">
    <!-- 页面标题 -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">生成结果</h1>
        <a href="<?php echo APP_URL; ?>/aigc.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> 返回处理页面
        </a>
    </div>

    <div class="card shadow mb-4">
        <!-- 卡片标题 -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">生成结果列表</h6>
        </div>
        <!-- 卡片内容 -->
        <div class="card-body">
            <?php if (empty($tasks)): ?>
                <p class="text-center text-muted">没有任务历史记录</p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>任务名称</th>
                                <th>任务类型</th>
                                <th>任务状态</th>
                                <th>处理状态</th>
                                <th>总数量</th>
                                <th>成功</th>
                                <th>失败</th>
                                <th>开始时间</th>
                                <th>完成时间</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <tr class="task-row" data-task-id="<?php echo $task['id']; ?>">
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
                                                'text_to_image' => '文生图',
                                                'image_to_image' => '图生图'
                                            ];
                                            echo $typeMap[$task['task_type']] ?? $task['task_type'];
                                        ?>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo $task['task_status'] == 'completed' ? 'success' : ($task['task_status'] == 'failed' ? 'danger' : 'warning'); ?>">
                                            <?php 
                                                $taskStatusMap = [
                                                    'pending' => '等待处理',
                                                    'processing' => '处理中',
                                                    'completed' => '已完成',
                                                    'failed' => '失败'
                                                ];
                                                echo $taskStatusMap[$task['task_status']] ?? $task['task_status'];
                                            ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (isset($task['process_status'])): ?>
                                            <span class="badge badge-<?php echo $task['process_status'] == 'success' ? 'success' : 'danger'; ?>">
                                                <?php echo $task['process_status'] == 'success' ? '处理成功' : '处理失败'; ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-secondary">未设置</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo $task['total_count']; ?></td>
                                    <td><?php echo $task['success_count']; ?></td>
                                    <td><?php echo $task['failed_count']; ?></td>
                                    <td><?php echo isset($task['started_at']) ? date('Y-m-d H:i:s', strtotime($task['started_at'])) : '-'; ?></td>
                                    <td><?php echo isset($task['completed_at']) ? date('Y-m-d H:i:s', strtotime($task['completed_at'])) : '-'; ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($task['created_at'])); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-primary view-task-btn" data-task-id="<?php echo $task['id']; ?>">
                                            <i class="fas fa-eye"></i> 查看详情
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
    
    <!-- 任务详情区域 -->
    <div class="card shadow mb-4" id="taskDetailCard" style="display: none;">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title" id="taskDetailTitle">任务详情</h5>
                <button class="btn btn-sm btn-secondary" id="closeDetailBtn">
                    <i class="fas fa-times"></i> 关闭详情
                </button>
            </div>
        </div>
        <div class="card-body" id="taskDetailBody">
            <!-- 任务详情内容将通过AJAX动态加载 -->
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
        
        // 加载任务详情
        function loadTaskDetail(taskId) {
            $.ajax({
                url: '<?php echo APP_URL; ?>/aigc.php?action=getTaskDetail',
                method: 'GET',
                data: { id: taskId },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        // 显示任务详情卡片
                        $('#taskDetailCard').show();
                        
                        // 构建任务详情HTML
                        var html = buildTaskDetailHTML(response.task, response.results);
                        
                        // 设置任务标题
                        $('#taskDetailTitle').text('任务详情 - ' + response.task.task_name);
                        
                        // 填充任务详情内容
                        $('#taskDetailBody').html(html);
                    } else {
                        alert('加载任务详情失败: ' + response.message);
                    }
                },
                error: function(xhr, status, error) {
                    alert('加载任务详情失败: ' + error);
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
            html += '<p><strong>处理状态:</strong></p>';
            if (task.process_status) {
                html += '<p><span class="badge badge-' + (task.process_status == 'success' ? 'success' : 'danger') + '">' + (task.process_status == 'success' ? '处理成功' : '处理失败') + '</span></p>';
            } else {
                html += '<p><span class="badge badge-secondary">未设置</span></p>';
            }
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
                    html += result.original_filename;
                    html += '<span class="badge badge-' + (result.status == 'success' ? 'success' : 'danger') + '">';
                    html += result.status == 'success' ? '处理成功' : '处理失败';
                    html += '</span>';
                    html += '</h6>';
                    html += '</div>';
                    html += '<div class="card-body">';
                    
                    if (result.status == 'failed') {
                        html += '<div class="alert alert-danger" role="alert">';
                        html += '错误信息：' + result.error_message;
                        html += '</div>';
                    } else {
                        html += '<div class="row">';
                        html += '<div class="col-md-12">';
                        html += '<h6>处理结果</h6>';
                        html += '<div class="img-container">';
                        html += '<img src="data:image/jpeg;base64,' + result.result_data + '" ';
                        html += 'class="img-thumbnail" alt="处理后" style="max-width: 100%; max-height: 300px;">';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '<div class="mt-3 text-center">';
                        html += '<a href="data:image/jpeg;base64,' + result.result_data + '" ';
                        html += 'class="btn btn-sm btn-primary" ';
                        html += 'download="processed_' + result.original_filename + '">';
                        html += '<i class="fas fa-download"></i> 下载';
                        html += '</a>';
                        html += '<button class="btn btn-sm btn-secondary ml-2" ';
                        html += 'onclick="viewImage(\'data:image/jpeg;base64,' + result.result_data + '\')">';
                        html += '<i class="fas fa-eye"></i> 预览';
                        html += '</button>';
                        html += '<a href="data:image/jpeg;base64,' + result.result_data + '" class="btn btn-sm btn-primary ml-2" download="processed_' + result.original_filename + '">';
                        html += '<i class="fas fa-download"></i> 下载';
                        html += '</a>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    } else {
                        html += '<div class="alert alert-danger" role="alert">';
                        html += '错误信息：' + result.error_message;
                        html += '</div>';
                    }
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                    html += '</div>';
                });
                html += '</div>';
            }
            html += '</div>';
            return html;
        }
    });
    
    // 图片预览功能
    function viewImage(imageData) {
        const previewImage = document.getElementById('previewImage');
        const downloadPreviewLink = document.getElementById('downloadPreviewLink');
        
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
                                <img id="previewImage" src="" alt="预览图片" class="img-fluid">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">关闭</button>
                                <a id="downloadPreviewLink" href="" class="btn btn-primary" download>
                                    <i class="fas fa-download"></i> 下载
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHTML);
        }
        
        previewImage.src = imageData;
        downloadPreviewLink.href = imageData;
        
        $('#imagePreviewModal').modal('show');
    }
</script>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>