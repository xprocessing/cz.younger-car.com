        <div class="query-card">
            <div class="query-header">
                <i class="fa fa-exclamation-triangle fa-3x mb-3"></i>
                <h2>查询错误</h2>
                <p class="mb-0"><?php echo htmlspecialchars($error); ?></p>
            </div>
            <div class="query-body">
                <div class="text-center">
                    <p class="text-muted mb-4">请检查订单号是否正确，然后重新查询</p>
                    <a href="./index.php" class="btn btn-primary btn-lg">
                        <i class="fa fa-arrow-left"></i> 返回查询
                    </a>
                </div>
            </div>
        </div>

        <div class="powered-by">
            <p>Powered by CZ Admin Panel</p>
        </div>
    </div>