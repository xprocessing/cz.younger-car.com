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
                            <?php 
                            // 处理运费数据，过滤出有运费的记录
                            $validShipmentOptions = [];
                            
                            // 检查数据结构
                            if (isset($yunfeiData['ems']) && isset($yunfeiData['ems']['results'])) {
                                foreach ($yunfeiData['ems']['results'] as $key => $shipmentOption) {
                                    // 过滤掉失败的和没有运费的记录
                                    $hasFee = false;
                                    
                                    // 检查是否有有效运费
                                    if (($shipmentOption['status'] === 'Success') && 
                                        (isset($shipmentOption['total_fee_cny']) && $shipmentOption['total_fee_cny'] !== null && $shipmentOption['total_fee_cny'] > 0) ||
                                        (isset($shipmentOption['ship_fee_cny']) && $shipmentOption['ship_fee_cny'] !== null && $shipmentOption['ship_fee_cny'] > 0)) {
                                        $hasFee = true;
                                    }
                                    
                                    if ($hasFee) {
                                        $validShipmentOptions[] = $shipmentOption;
                                    }
                                }
                            }
                            
                            if (empty($validShipmentOptions)): ?>
                                <div class="text-muted text-center py-3">
                                    <i class="fa fa-info-circle fa-2x"></i>
                                    <p class="mt-2">暂无有效运费数据</p>
                                </div>
                            <?php else: ?>
                                <!-- 运费表格 -->
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped">
                                        <thead class="table-primary">
                                            <tr>
                                                <th>物流渠道名称</th>
                                                <th>物流渠道代码</th>
                                                <th>仓库代码</th>
                                                <th>总费用(CNY)</th>
                                                <th>运费(CNY)</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($validShipmentOptions as $option): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($option['channel_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($option['channel_code']); ?></td>
                                                    <td><?php echo htmlspecialchars($option['warehouse_code']); ?></td>
                                                    <td><?php echo isset($option['total_fee_cny']) && $option['total_fee_cny'] !== null ? '¥' . number_format($option['total_fee_cny'], 2) : '-'; ?></td>
                                                    <td><?php echo isset($option['ship_fee_cny']) && $option['ship_fee_cny'] !== null ? '¥' . number_format($option['ship_fee_cny'], 2) : '-'; ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                            
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
                    <a href="<?php echo APP_URL; ?>/query.php" class="btn btn-primary">
                        <i class="fa fa-arrow-left"></i> 返回查询
                    </a>
                </div>
            </div>
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
        });
        
        window.addEventListener('afterprint', function() {
            document.querySelector('.query-header').style.display = 'block';
            document.querySelector('.text-center.mt-4').style.display = 'block';
        });
    </script>