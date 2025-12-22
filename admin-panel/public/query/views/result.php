        <div class="query-card">
            <div class="query-header">
                <i class="fa fa-check-circle fa-3x mb-3"></i>
                <h2>查询成功</h2>
                <p class="mb-0">已找到订单运费信息</p>
            </div>
            <div class="query-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="text-center">
                            <h5 class="text-muted">订单号</h5>
                            <h4 class="text-primary"><?php echo htmlspecialchars($result['global_order_no']); ?></h4>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center">
                            <h5 class="text-muted">创建时间</h5>
                            <h4><?php echo htmlspecialchars($result['create_at']); ?></h4>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0"><i class="fa fa-list"></i> 运费详细信息</h6>
                    </div>
                    <div class="card-body">
                        <?php if (isset($yunfeiData['error'])): ?>
                            <div class="alert alert-warning">
                                <i class="fa fa-exclamation-triangle"></i> <?php echo $yunfeiData['error']; ?>
                            </div>
                        <?php elseif (empty($yunfeiData)): ?>
                            <div class="text-muted text-center py-3">
                                <i class="fa fa-info-circle fa-2x"></i>
                                <p class="mt-2">暂无运费数据</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($yunfeiData as $key => $value): ?>
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <i class="fa fa-tag text-muted"></i>
                                            </div>
                                            <div>
                                                <strong><?php echo htmlspecialchars(ucfirst(str_replace('_', ' ', $key))); ?>:</strong>
                                                <div class="text-primary">
                                                    <?php 
                                                    if (is_numeric($value) && $value > 0 && strpos(strtolower($key), 'fee') !== false) {
                                                        echo '¥' . number_format($value, 2);
                                                    } elseif (is_array($value)) {
                                                        echo '<pre class="mb-0">' . htmlspecialchars(json_encode($value, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
                                                    } else {
                                                        echo htmlspecialchars($value);
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            
                            <hr>
                            
                            <div class="text-center">
                                <button class="btn btn-outline-secondary" onclick="window.print()">
                                    <i class="fa fa-print"></i> 打印信息
                                </button>
                                <button class="btn btn-outline-primary ms-2" onclick="copyToClipboard()">
                                    <i class="fa fa-copy"></i> 复制订单号
                                </button>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="text-center mt-4">
                    <a href="./index.php" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> 返回查询
                    </a>
                </div>
            </div>
        </div>

        <div class="powered-by">
            <p>Powered by CZ Admin Panel</p>
        </div>
    </div>

    <script>
        // 复制订单号到剪贴板
        function copyToClipboard() {
            const orderNo = '<?php echo htmlspecialchars($result['global_order_no']); ?>';
            
            if (navigator.clipboard) {
                navigator.clipboard.writeText(orderNo).then(() => {
                    showToast('订单号已复制到剪贴板');
                });
            } else {
                // 降级方案
                const textArea = document.createElement('textarea');
                textArea.value = orderNo;
                document.body.appendChild(textArea);
                textArea.select();
                document.execCommand('copy');
                document.body.removeChild(textArea);
                showToast('订单号已复制到剪贴板');
            }
        }
        
        // 显示提示消息
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 end-0 p-3';
            toast.style.zIndex = '1050';
            toast.innerHTML = `
                <div class="toast show" role="alert">
                    <div class="toast-header">
                        <i class="fa fa-check-circle text-success me-2"></i>
                        <strong class="me-auto">成功</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">
                        ${message}
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 3000);
        }
        
        // 打印样式
        window.addEventListener('beforeprint', function() {
            document.querySelector('.query-header').style.display = 'none';
            document.querySelector('.text-center.mt-4').style.display = 'none';
            document.querySelector('.powered-by').style.display = 'none';
        });
        
        window.addEventListener('afterprint', function() {
            document.querySelector('.query-header').style.display = 'block';
            document.querySelector('.text-center.mt-4').style.display = 'block';
            document.querySelector('.powered-by').style.display = 'block';
        });
    </script>