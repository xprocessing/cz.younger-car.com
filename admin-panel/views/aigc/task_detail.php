<?php include VIEWS_DIR . '/layouts/header.php'; ?>

<div class="container-fluid">
    <!-- 页面标题 -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">任务详情</h1>
        <a href="<?php echo APP_URL; ?>/aigc.php?action=taskHistory" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> 返回生成结果
        </a>
    </div>

    <?php if (empty($task)): ?>
        <div class="alert alert-danger" role="alert">
            任务不存在或已被删除
        </div>
    <?php else: ?>
        <!-- 任务基本信息 -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title"><?php echo htmlspecialchars($task['task_name']); ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <p><strong>任务类型:</strong></p>
                        <p><?php 
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
                        ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>任务状态:</strong></p>
                        <p>
                            <span class="badge badge-<?php echo $task['status'] == 'completed' ? 'success' : ($task['status'] == 'failed' ? 'danger' : 'warning'); ?>">
                                <?php 
                                    $statusMap = [
                                        'pending' => '等待处理',
                                        'processing' => '处理中',
                                        'completed' => '已完成',
                                        'failed' => '失败'
                                    ];
                                    echo $statusMap[$task['status']] ?? $task['status'];
                                ?>
                            </span>
                        </p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>处理数量:</strong></p>
                        <p><?php echo $task['total_count']; ?></p>
                    </div>
                    <div class="col-md-3">
                        <p><strong>成功/失败:</strong></p>
                        <p><?php echo $task['success_count'] . '/' . $task['failed_count']; ?></p>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <p><strong>开始时间:</strong></p>
                        <p><?php echo date('Y-m-d H:i:s', strtotime($task['started_at'])); ?></p>
                    </div>
                    <div class="col-md-6">
                        <p><strong>完成时间:</strong></p>
                        <p><?php echo $task['completed_at'] ? date('Y-m-d H:i:s', strtotime($task['completed_at'])) : '未完成'; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- 处理结果 -->
        <div class="card shadow mb-4">
            <div class="card-header">
                <h5 class="card-title">处理结果</h5>
            </div>
            <div class="card-body">
                <?php if (empty($results)): ?>
                    <p class="text-center text-muted">没有处理结果</p>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($results as $index => $result): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card">
                                    <div class="card-header">
                                        <h5 class="card-title">
                                            <?php echo htmlspecialchars($result['original_filename']); ?>
                                            <span class="badge badge-<?php echo $result['status'] == 'success' ? 'success' : 'danger'; ?>">
                                                <?php echo $result['status'] == 'success' ? '处理成功' : '处理失败'; ?>
                                            </span>
                                        </h5>
                                    </div>
                                    <div class="card-body">
                                        <?php if ($result['status'] == 'failed'): ?>
                                            <div class="alert alert-danger" role="alert">
                                                错误信息：<?php echo htmlspecialchars($result['error_message']); ?>
                                            </div>
                                        <?php else: ?>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <h6>处理结果</h6>
                                                    <div class="img-container">
                                                        <img src="data:image/jpeg;base64,<?php echo $result['result_data']; ?>" 
                                                             class="img-thumbnail" alt="处理后" style="max-width: 100%; max-height: 300px;">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="mt-3 text-center">
                                                <a href="data:image/jpeg;base64,<?php echo $result['result_data']; ?>" 
                                                   class="btn btn-sm btn-primary" 
                                                   download="processed_<?php echo htmlspecialchars($result['original_filename']); ?>">
                                                    <i class="fas fa-download"></i> 下载
                                                </a>
                                                <button class="btn btn-sm btn-secondary" 
                                                        onclick="viewImage('data:image/jpeg;base64,<?php echo $result['result_data']; ?>')">
                                                    <i class="fas fa-eye"></i> 预览
                                                </button>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<!-- 图片预览模态框 -->
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

<!-- JavaScript 用于图片预览 -->
<script>
    function viewImage(imageData) {
        const previewImage = document.getElementById('previewImage');
        const downloadPreviewLink = document.getElementById('downloadPreviewLink');
        
        previewImage.src = imageData;
        downloadPreviewLink.href = imageData;
        
        $('#imagePreviewModal').modal('show');
    }
</script>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>