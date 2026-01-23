<?php $title = '编辑物流渠道'; ?>

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="container-fluid">
            <div class="row mb-2">
                <div class="col-sm-6">
                    <h1><?php echo $title; ?></h1>
                </div>
                <div class="col-sm-6">
                    <ol class="breadcrumb float-sm-right">
                        <li class="breadcrumb-item"><a href="<?php echo ADMIN_PANEL_URL; ?>/dashboard.php">首页</a></li>
                        <li class="breadcrumb-item"><a href="<?php echo ADMIN_PANEL_URL; ?>/logistics.php">物流渠道管理</a></li>
                        <li class="breadcrumb-item active">编辑物流渠道</li>
                    </ol>
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">物流渠道信息</h3>
                        </div>
                        <!-- /.card-header -->
                        <div class="card-body">
                            <form action="<?php echo ADMIN_PANEL_URL; ?>/logistics.php?action=editPost" method="POST">
                                <input type="hidden" name="type_id" value="<?php echo htmlspecialchars($logistics['type_id']); ?>">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="type_id_view">物流类型ID</label>
                                            <input type="text" class="form-control" id="type_id_view" value="<?php echo htmlspecialchars($logistics['type_id']); ?>" disabled>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="name">物流渠道名称 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="name" name="name" placeholder="请输入物流渠道名称" value="<?php echo htmlspecialchars($logistics['name']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="code">物流渠道编码 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="code" name="code" placeholder="请输入物流渠道编码" value="<?php echo htmlspecialchars($logistics['code']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="logistics_provider_id">物流服务商ID <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="logistics_provider_id" name="logistics_provider_id" placeholder="请输入物流服务商ID" value="<?php echo htmlspecialchars($logistics['logistics_provider_id']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="logistics_provider_name">物流服务商名称 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="logistics_provider_name" name="logistics_provider_name" placeholder="请输入物流服务商名称" value="<?php echo htmlspecialchars($logistics['logistics_provider_name']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="supplier_code">供应商编码 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="supplier_code" name="supplier_code" placeholder="请输入供应商编码" value="<?php echo htmlspecialchars($logistics['supplier_code']); ?>" required>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="wp_code">仓库编码 <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control" id="wp_code" name="wp_code" placeholder="请输入仓库编码" value="<?php echo htmlspecialchars($logistics['wp_code']); ?>" required>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="is_used">是否启用</label>
                                            <select class="form-control" id="is_used" name="is_used">
                                                <option value="1" <?php echo $logistics['is_used'] == 1 ? 'selected' : ''; ?>>启用</option>
                                                <option value="0" <?php echo $logistics['is_used'] == 0 ? 'selected' : ''; ?>>禁用</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="order_type">订单类型</label>
                                            <input type="number" class="form-control" id="order_type" name="order_type" placeholder="请输入订单类型" value="<?php echo $logistics['order_type']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="channel_type">渠道类型</label>
                                            <input type="number" class="form-control" id="channel_type" name="channel_type" placeholder="请输入渠道类型" value="<?php echo $logistics['channel_type']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="relate_olt_id">关联OLT ID</label>
                                            <input type="text" class="form-control" id="relate_olt_id" name="relate_olt_id" placeholder="请输入关联OLT ID" value="<?php echo htmlspecialchars($logistics['relate_olt_id']); ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="fee_template_id">费用模板ID</label>
                                            <input type="number" class="form-control" id="fee_template_id" name="fee_template_id" placeholder="请输入费用模板ID" value="<?php echo $logistics['fee_template_id']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="billing_type">计费类型</label>
                                            <input type="number" class="form-control" id="billing_type" name="billing_type" placeholder="请输入计费类型" value="<?php echo $logistics['billing_type']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="volume_param">体积参数</label>
                                            <input type="number" class="form-control" id="volume_param" name="volume_param" placeholder="请输入体积参数" value="<?php echo $logistics['volume_param']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="warehouse_type">仓库类型</label>
                                            <input type="number" class="form-control" id="warehouse_type" name="warehouse_type" placeholder="请输入仓库类型" value="<?php echo $logistics['warehouse_type']; ?>">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="provider_is_used">服务商是否启用</label>
                                            <select class="form-control" id="provider_is_used" name="provider_is_used">
                                                <option value="1" <?php echo $logistics['provider_is_used'] == 1 ? 'selected' : ''; ?>>启用</option>
                                                <option value="0" <?php echo $logistics['provider_is_used'] == 0 ? 'selected' : ''; ?>>禁用</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="is_platform_provider">是否平台服务商</label>
                                            <select class="form-control" id="is_platform_provider" name="is_platform_provider">
                                                <option value="1" <?php echo $logistics['is_platform_provider'] == 1 ? 'selected' : ''; ?>>是</option>
                                                <option value="0" <?php echo $logistics['is_platform_provider'] == 0 ? 'selected' : ''; ?>>否</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="type">物流类型</label>
                                            <input type="number" class="form-control" id="type" name="type" placeholder="请输入物流类型" value="<?php echo $logistics['type']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="wid">仓库ID</label>
                                            <input type="number" class="form-control" id="wid" name="wid" placeholder="请输入仓库ID" value="<?php echo $logistics['wid']; ?>">
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="is_combine_channel">是否组合渠道</label>
                                            <select class="form-control" id="is_combine_channel" name="is_combine_channel">
                                                <option value="0" <?php echo $logistics['is_combine_channel'] == 0 ? 'selected' : ''; ?>>否</option>
                                                <option value="1" <?php echo $logistics['is_combine_channel'] == 1 ? 'selected' : ''; ?>>是</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="tms_provider_id">TMS服务商ID</label>
                                            <input type="number" class="form-control" id="tms_provider_id" name="tms_provider_id" placeholder="请输入TMS服务商ID" value="<?php echo $logistics['tms_provider_id']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="tms_provider_type">TMS服务商类型</label>
                                            <input type="number" class="form-control" id="tms_provider_type" name="tms_provider_type" placeholder="请输入TMS服务商类型" value="<?php echo $logistics['tms_provider_type']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="supplier_id">供应商ID</label>
                                            <input type="number" class="form-control" id="supplier_id" name="supplier_id" placeholder="请输入供应商ID" value="<?php echo $logistics['supplier_id']; ?>">
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="is_support_domestic_provider">是否支持国内服务商</label>
                                            <select class="form-control" id="is_support_domestic_provider" name="is_support_domestic_provider">
                                                <option value="0" <?php echo $logistics['is_support_domestic_provider'] == 0 ? 'selected' : ''; ?>>否</option>
                                                <option value="1" <?php echo $logistics['is_support_domestic_provider'] == 1 ? 'selected' : ''; ?>>是</option>
                                            </select>
                                        </div>
                                        
                                        <div class="form-group">
                                            <label for="is_need_marking">是否需要标记</label>
                                            <select class="form-control" id="is_need_marking" name="is_need_marking">
                                                <option value="0" <?php echo $logistics['is_need_marking'] == 0 ? 'selected' : ''; ?>>否</option>
                                                <option value="1" <?php echo $logistics['is_need_marking'] == 1 ? 'selected' : ''; ?>>是</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group mt-4">
                                    <button type="submit" class="btn btn-primary">保存</button>
                                    <button type="button" class="btn btn-default" onclick="window.location.href='<?php echo ADMIN_PANEL_URL; ?>/logistics.php'">取消</button>
                                </div>
                            </form>
                        </div>
                        <!-- /.card-body -->
                    </div>
                    <!-- /.card -->
                </div>
            </div>
        </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
</div>
<!-- /.content-wrapper -->