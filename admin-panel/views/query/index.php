        <div class="query-card">
            <div class="query-header">
                <i class="fa fa-truck fa-3x mb-3"></i>
                <h2>运费查询</h2>
                <p class="mb-0">请输入订单号查询运费信息</p>
            </div>
            <div class="query-body">
                <form id="queryForm" method="GET" action="<?php echo ADMIN_PANEL_URL; ?>/query.php?action=search">
                    <div class="mb-4">
                        <label for="order_no" class="form-label">
                            <i class="fa fa-search"></i> 订单号
                        </label>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="order_no" 
                               name="order_no" 
                               placeholder="请输入订单号，如：ORDER123456"
                               required
                               autocomplete="off">
                        <div class="form-text">请输入完整的订单号进行查询</div>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary btn-lg">
                            <span class="spinner-border spinner-border-sm loading" role="status" aria-hidden="true"></span>
                            <i class="fa fa-search"></i> 
                            <span class="btn-text">查询运费</span>
                        </button>
                    </div>
                </form>
                
                <hr class="my-4">
                
                <div class="text-center">
                    <p class="text-muted mb-2">或者使用快速查询</p>
                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="quickQuery()">
                        <i class="fa fa-bolt"></i> API查询
                    </button>
                </div>
                
                <!-- 快速查询结果显示 -->
                <div id="quickResult" class="mt-3" style="display: none;">
                    <!-- 动态内容 -->
                </div>
            </div>
        </div>
    </div>

    <script>
        // 表单提交处理
        document.getElementById('queryForm').addEventListener('submit', function(e) {
            const btn = this.querySelector('button[type="submit"]');
            const loading = btn.querySelector('.loading');
            const btnText = btn.querySelector('.btn-text');
            
            loading.style.display = 'inline-block';
            btnText.textContent = '查询中...';
            btn.disabled = true;
        });
        
        // 快速查询功能
        function quickQuery() {
            const orderNo = document.getElementById('order_no').value.trim();
            const resultDiv = document.getElementById('quickResult');
            
            if (!orderNo) {
                alert('请先输入订单号');
                return;
            }
            
            // 显示加载状态
            resultDiv.innerHTML = `
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">查询中...</span>
                    </div>
                    <p class="mt-2 text-muted">正在查询运费信息...</p>
                </div>
            `;
            resultDiv.style.display = 'block';
            
            // 发送API请求
            fetch('<?php echo ADMIN_PANEL_URL; ?>/query.php?action=api&order_no=' + encodeURIComponent(orderNo))
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        displayQuickResult(data.data);
                    } else {
                        displayQuickError(data.message);
                    }
                })
                .catch(error => {
                    displayQuickError('查询失败，请稍后重试');
                });
        }
        
        // 显示快速查询结果
        function displayQuickResult(data) {
            const resultDiv = document.getElementById('quickResult');
            const yunfeiHtml = formatYunfeiData(data.yunfei);
            
            resultDiv.innerHTML = `
                <div class="card border-success">
                    <div class="card-header bg-success text-white">
                        <h6 class="mb-0"><i class="fa fa-check-circle"></i> 查询成功</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>订单号：</strong><br>
                                <span class="info-badge">${data.global_order_no}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>创建时间：</strong><br>
                                <span class="info-badge">${data.create_at}</span>
                            </div>
                        </div>
                        <div class="mb-3">
                            <strong>运费信息：</strong>
                            <div class="json-display mt-2">${yunfeiHtml}</div>
                        </div>
                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="clearQuickResult()">
                                <i class="fa fa-times"></i> 清除结果
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // 显示快速查询错误
        function displayQuickError(message) {
            const resultDiv = document.getElementById('quickResult');
            
            resultDiv.innerHTML = `
                <div class="card border-danger">
                    <div class="card-header bg-danger text-white">
                        <h6 class="mb-0"><i class="fa fa-exclamation-triangle"></i> 查询失败</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-2">${message}</p>
                        <div class="text-center">
                            <button type="button" class="btn btn-sm btn-secondary" onclick="clearQuickResult()">
                                <i class="fa fa-times"></i> 清除结果
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // 清除快速查询结果
        function clearQuickResult() {
            document.getElementById('quickResult').style.display = 'none';
        }
        
        // 格式化运费数据
        function formatYunfeiData(data) {
            if (typeof data !== 'object' || data === null) {
                return '无运费数据';
            }
            
            if (data.error) {
                return '<span class="text-danger">' + data.error + '</span>';
            }
            
            // 美化显示常用字段
            let html = '<div class="row">';
            
            Object.keys(data).forEach(key => {
                const value = data[key];
                html += `
                    <div class="col-md-6 mb-2">
                        <strong>${key}：</strong> ${value}
                    </div>
                `;
            });
            
            html += '</div>';
            return html;
        }
        
        // 自动聚焦订单号输入框
        document.getElementById('order_no').focus();
        
        // 清除成功/错误消息
        setTimeout(() => {
            const errorBoxes = document.querySelectorAll('.error-box');
            const successBoxes = document.querySelectorAll('.success-box');
            errorBoxes.forEach(box => box.remove());
            successBoxes.forEach(box => box.remove());
        }, 5000);
    </script>