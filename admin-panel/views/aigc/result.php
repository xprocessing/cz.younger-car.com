<?php include VIEWS_DIR . '/layouts/header.php'; ?>

<div class="container-fluid">
    <!-- 页面标题 -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">图片处理结果</h1>
        <a href="<?php echo APP_URL; ?>/aigc.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> 返回处理页面
        </a>
    </div>

    <div class="card shadow mb-4">
        <!-- 卡片标题 -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">处理结果列表</h6>
            <div>
                <a href="<?php echo APP_URL; ?>/aigc.php" class="btn btn-sm btn-primary">
                    <i class="fas fa-redo"></i> 继续处理
                </a>
            </div>
        </div>
        <!-- 卡片内容 -->
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
                                        <?php echo basename($result['original_image']); ?>
                                        <span class="badge badge-<?php echo $result['processed'] ? 'success' : 'danger'; ?>">
                                            <?php echo $result['processed'] ? '处理成功' : '处理失败'; ?>
                                        </span>
                                    </h5>
                                </div>
                                <div class="card-body">
                                    <?php if (!$result['processed']): ?>
                                        <div class="alert alert-danger" role="alert">
                                            错误信息：<?php echo $result['error']; ?>
                                        </div>
                                    <?php else: ?>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <h6>原图</h6>
                                                <div class="img-container">
                                                    <img src="<?php echo APP_URL; ?>/public/temp/<?php echo basename($result['original_image']); ?>" 
                                                         class="img-thumbnail" alt="原图" style="max-width: 100%; max-height: 300px;">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <h6>处理后</h6>
                                                <div class="img-container">
                                                    <!-- 这里需要根据API返回的实际格式进行调整 -->
                                                    <img src="data:image/jpeg;base64,<?php echo $result['result']; ?>" 
                                                         class="img-thumbnail" alt="处理后" style="max-width: 100%; max-height: 300px;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mt-3 text-center">
                                            <a href="data:image/jpeg;base64,<?php echo $result['result']; ?>" 
                                               class="btn btn-sm btn-primary" 
                                               download="processed_<?php echo basename($result['original_image']); ?>">
                                                <i class="fas fa-download"></i> 下载
                                            </a>
                                            <button class="btn btn-sm btn-secondary" 
                                                    onclick="viewImage('data:image/jpeg;base64,<?php echo $result['result']; ?>')">
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
