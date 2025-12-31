<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title ?? '运费查询'; ?></title>
    <link href="<?php echo APP_URL; ?>/public/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo APP_URL; ?>/public/css/font-awesome.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }
        .query-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .query-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 600px;
            width: 100%;
        }
        .query-header {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 30px;
            text-align: center;
        }
        .query-body {
            padding: 40px 30px;
        }
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #667eea 0%, #764ba2 100%);
            border: none;
            padding: 12px 30px;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #764ba2 0%, #667eea 100%);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }
        .loading {
            display: none;
        }
        .result-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            max-width: 800px;
            width: 100%;
            margin-top: 20px;
        }
        .result-header {
            background: linear-gradient(45deg, #28a745 0%, #20c997 100%);
            color: white;
            border-radius: 15px 15px 0 0;
            padding: 20px 30px;
        }
        .json-display {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 20px;
            font-family: 'Courier New', monospace;
            white-space: pre-wrap;
            word-break: break-all;
            max-height: 400px;
            overflow-y: auto;
        }
        .error-box {
            background: #f8d7da;
            border: 1px solid #f5c6cb;
            color: #721c24;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .success-box {
            background: #d4edda;
            border: 1px solid #c3e6cb;
            color: #155724;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .info-badge {
            background: #e9ecef;
            color: #495057;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.85rem;
        }
    </style>
</head>
<body>
    <div class="query-container">
        <?php if ($error = getError()): ?>
            <div class="error-box" style="position: fixed; top: 20px; right: 20px; z-index: 1000; max-width: 400px;">
                <?php echo $error; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($success = getSuccess()): ?>
            <div class="success-box" style="position: fixed; top: 20px; right: 20px; z-index: 1000; max-width: 400px;">
                <?php echo $success; ?>
            </div>
        <?php endif; ?>