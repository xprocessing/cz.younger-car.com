<?php include VIEWS_DIR . '/layouts/header.php'; ?>

<div class="container-fluid">
    <!-- 页面标题 -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">任务历史</h1>
        <a href="<?php echo APP_URL; ?>/aigc.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-arrow-left fa-sm text-white-50"></i> 返回处理页面
        </a>
    </div>

    <div class="card shadow mb-4">
        <!-- 卡片标题 -->
        <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
            <h6 class="m-0 font-weight-bold text-primary">任务列表</h6>
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
                                <th>状态</th>
                                <th>总数量</th>
                                <th>成功</th>
                                <th>失败</th>
                                <th>创建时间</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tasks as $task): ?>
                                <tr>
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
                                    </td>
                                    <td><?php echo $task['total_count']; ?></td>
                                    <td><?php echo $task['success_count']; ?></td>
                                    <td><?php echo $task['failed_count']; ?></td>
                                    <td><?php echo date('Y-m-d H:i:s', strtotime($task['created_at'])); ?></td>
                                    <td>
                                        <a href="<?php echo APP_URL; ?>/aigc.php?action=taskDetail&id=<?php echo $task['id']; ?>" class="btn btn-sm btn-primary">
                                            <i class="fas fa-eye"></i> 查看详情
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>