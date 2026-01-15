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
