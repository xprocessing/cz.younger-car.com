<?php
// 定义必要的常量和配置
// 从public目录向上一级到达admin-panel根目录
define('APP_ROOT', dirname(__DIR__));
define('APP_URL', 'https://cz.younger-car.com/admin-panel');

// 加载数据库配置
require_once __DIR__ . '/../config/config.php';

// 引入核心文件
require_once __DIR__ . '/../models/Yunfei.php';

// 如果是AJAX请求，执行查询并返回JSON
if (isset($_GET['ajax'])) {
    header('Content-Type: application/json');
    
    $orderNo = trim($_GET['order_no'] ?? '');
    $response = ['success' => false, 'data' => null, 'message' => ''];
    
    if (empty($orderNo)) {
        $response['message'] = '请输入订单号';
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        exit;
    }
    
    $yunfeiModel = new Yunfei();
    $result = $yunfeiModel->getByOrderNo($orderNo);
    
    if (!$result) {
        $response['message'] = '未找到该订单号的运费信息';
    } else {
        $yunfeiData = json_decode($result['shisuanyunfei'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            $yunfeiData = ['error' => '运费数据解析失败'];
        }
        
        $response['success'] = true;
        $response['data'] = [
            'id' => $result['id'],
            'global_order_no' => $result['global_order_no'],
            'yunfei' => $yunfeiData,
            'create_at' => $result['create_at']
        ];
    }
    
    echo json_encode($response, JSON_UNESCAPED_UNICODE);
    exit;
}

// GET请求显示查询页面
?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>运费查询 - CZ物流</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/font-awesome@4.7.0/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Microsoft YaHei', sans-serif;
        }
        .track-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .track-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 50px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 500px;
            overflow: hidden;
        }
        .track-header {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }
        .track-body {
            padding: 30px;
        }
        .search-box {
            position: relative;
        }
        .search-box input {
            border-radius: 25px;
            padding-left: 50px;
            border: 2px solid #e9ecef;
            transition: all 0.3s;
        }
        .search-box input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .search-box .search-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }
        .btn-search {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 40px;
            font-weight: bold;
            transition: all 0.3s;
        }
        .btn-search:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .result-card {
            margin-top: 20px;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        .result-header {
            background: linear-gradient(45deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        .info-item {
            border-bottom: 1px solid #f8f9fa;
            padding: 15px 0;
        }
        .info-item:last-child {
            border-bottom: none;
        }
        .info-label {
            color: #6c757d;
            font-size: 0.9rem;
            margin-bottom: 5px;
        }
        .info-value {
            font-weight: bold;
            color: #212529;
        }
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }
        .spinner {
            width: 50px;
            height: 50px;
            border: 5px solid #f3f3f3;
            border-top: 5px solid #667eea;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .powered-by {
            text-align: center;
            margin-top: 20px;
            color: white;
            font-size: 0.85rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="track-container">
        <div class="track-card">
            <div class="track-header">
                <i class="fa fa-truck fa-4x mb-3"></i>
                <h2 class="mb-3">运费查询</h2>
                <p class="mb-0">输入订单号即可查询运费信息</p>
            </div>
            <div class="track-body">
                <form id="trackForm">
                    <div class="search-box mb-4">
                        <i class="fa fa-search search-icon"></i>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="orderNo" 
                               name="order_no" 
                               placeholder="请输入订单号"
                               required
                               autocomplete="off">
                    </div>
                    
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary btn-lg btn-search">
                            <i class="fa fa-search me-2"></i> 查询运费
                        </button>
                    </div>
                </form>
                
                <!-- 查询结果 -->
                <div id="resultContainer"></div>
            </div>
        </div>
        
        <div class="powered-by">
            <p>Powered by CZ Admin Panel</p>
        </div>
    </div>
    
    <!-- 加载动画 -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <script>
        const trackForm = document.getElementById('trackForm');
        const resultContainer = document.getElementById('resultContainer');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        // 表单提交
        trackForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const orderNo = document.getElementById('orderNo').value.trim();
            
            if (!orderNo) {
                showMessage('请输入订单号', 'warning');
                return;
            }
            
            queryYunfei(orderNo);
        });
        
        // 查询运费
        function queryYunfei(orderNo) {
            loadingOverlay.style.display = 'flex';
            
            fetch('?ajax=1&order_no=' + encodeURIComponent(orderNo))
                .then(response => response.json())
                .then(data => {
                    loadingOverlay.style.display = 'none';
                    
                    if (data.success) {
                        displayResult(data.data);
                    } else {
                        showError(data.message);
                    }
                })
                .catch(error => {
                    loadingOverlay.style.display = 'none';
                    showError('查询失败，请稍后重试');
                });
        }
        
        // 显示查询结果
        function displayResult(data) {
            const yunfeiData = data.yunfei;
            let yunfeiHtml = '';
            
            if (yunfeiData.error) {
                yunfeiHtml = `<div class="text-warning">${yunfeiData.error}</div>`;
            } else if (typeof yunfeiData === 'object' && yunfeiData !== null) {
                yunfeiHtml = Object.keys(yunfeiData).map(key => {
                    let value = yunfeiData[key];
                    
                    // 格式化数值
                    if (typeof value === 'number' && value > 0 && key.toLowerCase().includes('fee')) {
                        value = '¥' + value.toFixed(2);
                    } else if (Array.isArray(value)) {
                        value = value.join(', ');
                    } else if (typeof value === 'object') {
                        value = JSON.stringify(value, null, 2);
                    }
                    
                    return `
                        <div class="info-item">
                            <div class="info-label">${key}</div>
                            <div class="info-value">${value}</div>
                        </div>
                    `;
                }).join('');
            } else {
                yunfeiHtml = '<div class="text-muted">暂无运费数据</div>';
            }
            
            resultContainer.innerHTML = `
                <div class="card result-card fade-in">
                    <div class="card-header result-header">
                        <h6 class="mb-0">
                            <i class="fa fa-check-circle me-2"></i> 
                            查询成功
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">订单号</div>
                            <div class="info-value">
                                <span class="badge bg-primary">${data.global_order_no}</span>
                            </div>
                        </div>
                        <div class="info-item">
                            <div class="info-label">创建时间</div>
                            <div class="info-value">${data.create_at}</div>
                        </div>
                        ${yunfeiHtml ? `
                            <div class="info-item">
                                <div class="info-label">运费信息</div>
                                <div>${yunfeiHtml}</div>
                            </div>
                        ` : ''}
                        
                        <div class="text-center mt-3">
                            <button class="btn btn-sm btn-outline-primary" onclick="copyOrderNo('${data.global_order_no}')">
                                <i class="fa fa-copy me-1"></i> 复制订单号
                            </button>
                            <button class="btn btn-sm btn-outline-secondary" onclick="newQuery()">
                                <i class="fa fa-refresh me-1"></i> 重新查询
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // 显示错误
        function showError(message) {
            resultContainer.innerHTML = `
                <div class="alert alert-danger fade-in">
                    <i class="fa fa-exclamation-triangle me-2"></i>
                    ${message}
                </div>
            `;
        }
        
        // 显示消息
        function showMessage(message, type) {
            const alertClass = type === 'warning' ? 'alert-warning' : 'alert-info';
            resultContainer.innerHTML = `
                <div class="alert ${alertClass} fade-in">
                    ${message}
                </div>
            `;
        }
        
        // 复制订单号
        function copyOrderNo(orderNo) {
            if (navigator.clipboard) {
                navigator.clipboard.writeText(orderNo).then(() => {
                    showToast('订单号已复制');
                });
            } else {
                const input = document.createElement('input');
                input.value = orderNo;
                document.body.appendChild(input);
                input.select();
                document.execCommand('copy');
                document.body.removeChild(input);
                showToast('订单号已复制');
            }
        }
        
        // 新查询
        function newQuery() {
            document.getElementById('orderNo').value = '';
            document.getElementById('orderNo').focus();
            resultContainer.innerHTML = '';
        }
        
        // Toast提示
        function showToast(message) {
            const toast = document.createElement('div');
            toast.className = 'position-fixed top-0 start-50 translate-middle-x mt-3';
            toast.style.zIndex = '9999';
            toast.innerHTML = `
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            document.body.appendChild(toast);
            
            setTimeout(() => toast.remove(), 3000);
        }
        
        // 自动聚焦
        document.getElementById('orderNo').focus();
        
        // 支持回车键查询
        document.getElementById('orderNo').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                trackForm.dispatchEvent(new Event('submit'));
            }
        });
    </script>
</body>
</html>