<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>欢迎回来，<?php echo $_SESSION['full_name']; ?></h5>
            </div>
            <div class="card-body">
                <p>这是您的管理面板。您可以从这里管理所有内容。</p>
            </div>
        </div>
    </div>
</div>

<?php include VIEWS_DIR . '/layouts/footer.php'; ?>