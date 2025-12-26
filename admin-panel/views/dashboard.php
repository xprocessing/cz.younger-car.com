<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>欢迎回来，<?php echo $_SESSION['full_name']; ?></h5>
            </div>
            <div class="card-body">
                <p>这是您的管理后台仪表盘。</p>
                <div class="row mt-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary mb-3">
                            <div class="card-body">
                                <h5 class="card-title">用户管理</h5>
                                <p class="card-text">管理系统用户</p>
                                <a href="<?php echo APP_URL; ?>/users.php" class="btn btn-light">进入</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success mb-3">
                            <div class="card-body">
                                <h5 class="card-title">角色管理</h5>
                                <p class="card-text">管理用户角色</p>
                                <a href="<?php echo APP_URL; ?>/roles.php" class="btn btn-light">进入</a>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning mb-3">
                            <div class="card-body">
                                <h5 class="card-title">权限管理</h5>
                                <p class="card-text">管理系统权限</p>
                                <a href="<?php echo APP_URL; ?>/permissions.php" class="btn btn-light">进入</a>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="card text-white bg-danger mb-3">
                            <div class="card-body">
                                <h5 class="card-title">订单利润管理</h5>
                                <p class="card-text">订单利润数据</p>
                                <a href="<?php echo APP_URL; ?>/order_profit.php" class="btn btn-light">进入</a>
                            </div>
                        </div>
                    </div>
                     <div class="col-md-3">
                        <div class="card text-white bg-danger mb-3">
                            <div class="card-body">
                                <h5 class="card-title">运费管理</h5>
                                <p class="card-text">运费数据</p>
                                <a href="<?php echo APP_URL; ?>/yunfei.php" class="btn btn-light">进入</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>