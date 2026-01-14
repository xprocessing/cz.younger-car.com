

<div class="d-flex justify-content-between align-items-center mb-3">
    <h4>FBA库存详情管理</h4>
    <div>
        <!-- 默认字段按钮 -->
        <button type="button" class="btn btn-secondary me-2" id="resetToDefaultFields">
            <i class="fa fa-refresh"></i> 默认字段
        </button>
        <!-- 字段选择按钮 -->
        <button type="button" class="btn btn-primary me-2" data-bs-toggle="modal" data-bs-target="#fieldSelectionModal">
            <i class="fa fa-cog"></i> 字段选择
        </button>
        <!-- 批量删除按钮 -->
        <button type="submit" form="batchDeleteForm" class="btn btn-danger" 
                onclick="return confirm('确定要删除选中的FBA库存详情记录吗？');">
            <i class="fa fa-trash"></i> 批量删除
        </button>
    </div>
</div>

<!-- 字段选择模态框 -->
<div class="modal fade" id="fieldSelectionModal" tabindex="-1" aria-labelledby="fieldSelectionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="fieldSelectionModalLabel">选择显示字段</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- 基本信息字段 -->
                    <div class="col-md-4">
                        <h6 class="mb-2">基本信息</h6>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="name" id="field-name" checked>
                            <label class="form-check-label" for="field-name">仓库名</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="seller_group_name" id="field-seller_group_name" checked>
                            <label class="form-check-label" for="field-seller_group_name">共享仓店铺名</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="sid" id="field-sid" checked>
                            <label class="form-check-label" for="field-sid">店铺ID</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="asin" id="field-asin" checked>
                            <label class="form-check-label" for="field-asin">ASIN</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="product_name" id="field-product_name" checked>
                            <label class="form-check-label" for="field-product_name">商品名称</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="small_image_url" id="field-small_image_url" checked>
                            <label class="form-check-label" for="field-small_image_url">预览图</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="seller_sku" id="field-seller_sku">
                            <label class="form-check-label" for="field-seller_sku">MSKU</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="fnsku" id="field-fnsku">
                            <label class="form-check-label" for="field-fnsku">FNSKU</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="sku" id="field-sku" checked>
                            <label class="form-check-label" for="field-sku">SKU</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="category_text" id="field-category_text">
                            <label class="form-check-label" for="field-category_text">分类</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="product_brand_text" id="field-product_brand_text">
                            <label class="form-check-label" for="field-product_brand_text">品牌</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="share_type" id="field-share_type">
                            <label class="form-check-label" for="field-share_type">共享类型</label>
                        </div>
                    </div>
                    
                    <!-- 库存数量字段 -->
                    <div class="col-md-4">
                        <h6 class="mb-2">库存数量</h6>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="total" id="field-total">
                            <label class="form-check-label" for="field-total">总数</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="available_total" id="field-available_total">
                            <label class="form-check-label" for="field-available_total">可用总数</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_fulfillable_quantity" id="field-afn_fulfillable_quantity" checked>
                            <label class="form-check-label" for="field-afn_fulfillable_quantity">FBA可售</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="reserved_fc_transfers" id="field-reserved_fc_transfers">
                            <label class="form-check-label" for="field-reserved_fc_transfers">待调仓</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="reserved_fc_processing" id="field-reserved_fc_processing">
                            <label class="form-check-label" for="field-reserved_fc_processing">调仓中</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="reserved_customerorders" id="field-reserved_customerorders">
                            <label class="form-check-label" for="field-reserved_customerorders">待发货</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="quantity" id="field-quantity">
                            <label class="form-check-label" for="field-quantity">FBM可售</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_unsellable_quantity" id="field-afn_unsellable_quantity">
                            <label class="form-check-label" for="field-afn_unsellable_quantity">不可售</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_inbound_working_quantity" id="field-afn_inbound_working_quantity">
                            <label class="form-check-label" for="field-afn_inbound_working_quantity">计划入库</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_inbound_shipped_quantity" id="field-afn_inbound_shipped_quantity">
                            <label class="form-check-label" for="field-afn_inbound_shipped_quantity">在途</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_inbound_receiving_quantity" id="field-afn_inbound_receiving_quantity">
                            <label class="form-check-label" for="field-afn_inbound_receiving_quantity">入库中</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="stock_up_num" id="field-stock_up_num">
                            <label class="form-check-label" for="field-stock_up_num">实际在途</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_researching_quantity" id="field-afn_researching_quantity">
                            <label class="form-check-label" for="field-afn_researching_quantity">调查中数量</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="total_fulfillable_quantity" id="field-total_fulfillable_quantity">
                            <label class="form-check-label" for="field-total_fulfillable_quantity">总可用库存</label>
                        </div>
                        
                        <!-- 库龄相关字段 -->
                        <h6 class="mt-3 mb-2">库龄信息</h6>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_0_to_30_days" id="field-inv_age_0_to_30_days">
                            <label class="form-check-label" for="field-inv_age_0_to_30_days">0-1个月库龄</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_31_to_60_days" id="field-inv_age_31_to_60_days">
                            <label class="form-check-label" for="field-inv_age_31_to_60_days">1-2个月库龄</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_61_to_90_days" id="field-inv_age_61_to_90_days">
                            <label class="form-check-label" for="field-inv_age_61_to_90_days">2-3个月库龄</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_0_to_90_days" id="field-inv_age_0_to_90_days">
                            <label class="form-check-label" for="field-inv_age_0_to_90_days">0-3个月库龄</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_91_to_180_days" id="field-inv_age_91_to_180_days">
                            <label class="form-check-label" for="field-inv_age_91_to_180_days">3-6个月库龄</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_181_to_270_days" id="field-inv_age_181_to_270_days">
                            <label class="form-check-label" for="field-inv_age_181_to_270_days">6-9个月库龄</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_271_to_330_days" id="field-inv_age_271_to_330_days">
                            <label class="form-check-label" for="field-inv_age_271_to_330_days">9-11个月库龄</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_271_to_365_days" id="field-inv_age_271_to_365_days">
                            <label class="form-check-label" for="field-inv_age_271_to_365_days">9-12个月库龄</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_331_to_365_days" id="field-inv_age_331_to_365_days">
                            <label class="form-check-label" for="field-inv_age_331_to_365_days">11-12个月库龄</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_365_plus_days" id="field-inv_age_365_plus_days">
                            <label class="form-check-label" for="field-inv_age_365_plus_days">12个月以上库龄</label>
                        </div>
                    </div>
                    
                    <!-- 库存成本字段 -->
                    <div class="col-md-4">
                        <h6 class="mb-2">库存成本</h6>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="total_price" id="field-total_price">
                            <label class="form-check-label" for="field-total_price">总价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="available_total_price" id="field-available_total_price">
                            <label class="form-check-label" for="field-available_total_price">可用总数成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_fulfillable_quantity_price" id="field-afn_fulfillable_quantity_price">
                            <label class="form-check-label" for="field-afn_fulfillable_quantity_price">FBA可售成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="reserved_fc_transfers_price" id="field-reserved_fc_transfers_price">
                            <label class="form-check-label" for="field-reserved_fc_transfers_price">待调仓成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="reserved_fc_processing_price" id="field-reserved_fc_processing_price">
                            <label class="form-check-label" for="field-reserved_fc_processing_price">调仓中成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="reserved_customerorders_price" id="field-reserved_customerorders_price">
                            <label class="form-check-label" for="field-reserved_customerorders_price">待发货成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="quantity_price" id="field-quantity_price">
                            <label class="form-check-label" for="field-quantity_price">FBM可售成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_unsellable_quantity_price" id="field-afn_unsellable_quantity_price">
                            <label class="form-check-label" for="field-afn_unsellable_quantity_price">不可售成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_inbound_working_quantity_price" id="field-afn_inbound_working_quantity_price">
                            <label class="form-check-label" for="field-afn_inbound_working_quantity_price">计划入库成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_inbound_shipped_quantity_price" id="field-afn_inbound_shipped_quantity_price">
                            <label class="form-check-label" for="field-afn_inbound_shipped_quantity_price">在途成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_inbound_receiving_quantity_price" id="field-afn_inbound_receiving_quantity_price">
                            <label class="form-check-label" for="field-afn_inbound_receiving_quantity_price">入库中成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="stock_up_num_price" id="field-stock_up_num_price">
                            <label class="form-check-label" for="field-stock_up_num_price">实际在途成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="afn_researching_quantity_price" id="field-afn_researching_quantity_price">
                            <label class="form-check-label" for="field-afn_researching_quantity_price">调查中数量成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="cg_price" id="field-cg_price">
                            <label class="form-check-label" for="field-cg_price">单位采购成本</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="cg_transport_costs" id="field-cg_transport_costs">
                            <label class="form-check-label" for="field-cg_transport_costs">单位头程费用</label>
                        </div>
                    </div>
                    
                    <!-- 库龄成本字段 -->
                    <div class="col-md-4">
                        <h6 class="mb-2">库龄成本</h6>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_0_to_30_price" id="field-inv_age_0_to_30_price">
                            <label class="form-check-label" for="field-inv_age_0_to_30_price">0-1个月库龄成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_31_to_60_price" id="field-inv_age_31_to_60_price">
                            <label class="form-check-label" for="field-inv_age_31_to_60_price">1-2个月库龄成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_61_to_90_price" id="field-inv_age_61_to_90_price">
                            <label class="form-check-label" for="field-inv_age_61_to_90_price">2-3个月库龄成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_0_to_90_price" id="field-inv_age_0_to_90_price">
                            <label class="form-check-label" for="field-inv_age_0_to_90_price">0-3个月库龄成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_91_to_180_price" id="field-inv_age_91_to_180_price">
                            <label class="form-check-label" for="field-inv_age_91_to_180_price">3-6个月库龄成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_181_to_270_price" id="field-inv_age_181_to_270_price">
                            <label class="form-check-label" for="field-inv_age_181_to_270_price">6-9个月库龄成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_271_to_330_price" id="field-inv_age_271_to_330_price">
                            <label class="form-check-label" for="field-inv_age_271_to_330_price">9-11个月库龄成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_271_to_365_price" id="field-inv_age_271_to_365_price">
                            <label class="form-check-label" for="field-inv_age_271_to_365_price">9-12个月库龄成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_331_to_365_price" id="field-inv_age_331_to_365_price">
                            <label class="form-check-label" for="field-inv_age_331_to_365_price">11-12个月库龄成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="inv_age_365_plus_price" id="field-inv_age_365_plus_price">
                            <label class="form-check-label" for="field-inv_age_365_plus_price">12个月以上库龄成本价</label>
                        </div>
                    </div>
                    
                    <!-- 其他字段 -->
                    <div class="col-md-4">
                        <h6 class="mb-2">其他字段</h6>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="recommended_action" id="field-recommended_action">
                            <label class="form-check-label" for="field-recommended_action">推荐操作</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="sell_through" id="field-sell_through">
                            <label class="form-check-label" for="field-sell_through">售出率</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="estimated_excess_quantity" id="field-estimated_excess_quantity">
                            <label class="form-check-label" for="field-estimated_excess_quantity">预计冗余数量</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="estimated_storage_cost_next_month" id="field-estimated_storage_cost_next_month">
                            <label class="form-check-label" for="field-estimated_storage_cost_next_month">预计30天仓储费用</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="fba_minimum_inventory_level" id="field-fba_minimum_inventory_level">
                            <label class="form-check-label" for="field-fba_minimum_inventory_level">最低库存水平</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="fba_inventory_level_health_status" id="field-fba_inventory_level_health_status">
                            <label class="form-check-label" for="field-fba_inventory_level_health_status">库存水平健康度</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="historical_days_of_supply" id="field-historical_days_of_supply">
                            <label class="form-check-label" for="field-historical_days_of_supply">历史供货天数</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="historical_days_of_supply_price" id="field-historical_days_of_supply_price">
                            <label class="form-check-label" for="field-historical_days_of_supply_price">历史供货天数成本价</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="low_inventory_level_fee_applied" id="field-low_inventory_level_fee_applied">
                            <label class="form-check-label" for="field-low_inventory_level_fee_applied">低库存水平费收取情况</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="fulfillment_channel" id="field-fulfillment_channel">
                            <label class="form-check-label" for="field-fulfillment_channel">配送方式</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input field-checkbox" type="checkbox" value="fba_storage_quantity_list" id="field-fba_storage_quantity_list">
                            <label class="form-check-label" for="field-fba_storage_quantity_list">FBA可售信息列表</label>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">关闭</button>
                <button type="button" class="btn btn-primary" id="saveFieldSelection">保存设置</button>
            </div>
        </div>
    </div>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET" action="<?php echo APP_URL; ?>/inventory_details_fba.php" class="row g-3">
            <div class="col-md-3">
                <label for="search" class="form-label">关键词搜索</label>
                <input type="text" name="search" class="form-control" 
                       placeholder="搜索仓库名、SKU、ASIN、商品名称..." 
                       value="<?php echo $_GET['search'] ?? ''; ?>">
            </div>
            <div class="col-md-2">
                <label for="name" class="form-label">仓库名</label>
                <select name="name" class="form-select">
                    <option value="">全部仓库</option>
                    <?php if (!empty($warehouseNames)): ?>
                        <?php foreach ($warehouseNames as $warehouseName): ?>
                            <option value="<?php echo htmlspecialchars($warehouseName); ?>" 
                                    <?php echo (($_GET['name'] ?? '') == $warehouseName ? 'selected' : ''); ?>>
                                <?php echo htmlspecialchars($warehouseName); ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="sku" class="form-label">SKU</label>
                <input type="text" name="sku" class="form-control" 
                       placeholder="搜索SKU" 
                       value="<?php echo $_GET['sku'] ?? ''; ?>">
            </div>
            <div class="col-12 text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fa fa-search"></i> 搜索
                </button>
                <a href="<?php echo APP_URL; ?>/inventory_details_fba.php" class="btn btn-secondary">
                    <i class="fa fa-refresh"></i> 重置
                </a>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="POST" action="<?php echo APP_URL; ?>/inventory_details_fba.php?action=batchDelete" id="batchDeleteForm">
            <div class="table-responsive">
                <table class="table table-hover table-striped">
                    <thead>
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th data-field="name" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    仓库名
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="seller_group_name" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    共享仓店铺名
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="sid" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    店铺ID
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="asin" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    ASIN
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="product_name" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    商品名称
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="small_image_url" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    预览图
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="seller_sku" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    MSKU
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="fnsku" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    FNSKU
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="sku" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    SKU
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="category_text" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    分类
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="product_brand_text" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    品牌
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="share_type" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    共享类型
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>                            
                            <th data-field="total" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    总数
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="available_total" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    可用总数
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_fulfillable_quantity" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    FBA可售
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="reserved_fc_transfers" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    待调仓
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="reserved_fc_processing" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    调仓中
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="reserved_customerorders" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    待发货
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="quantity" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    FBM可售
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_unsellable_quantity" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    不可售
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_inbound_working_quantity" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    计划入库
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_inbound_shipped_quantity" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    在途
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_inbound_receiving_quantity" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    入库中
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="stock_up_num" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    实际在途
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_researching_quantity" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    调查中数量
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="total_fulfillable_quantity" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    总可用库存
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_0_to_30_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    0-1个月库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_31_to_60_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    1-2个月库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_61_to_90_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    2-3个月库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_0_to_90_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    0-3个月库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_91_to_180_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    3-6个月库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_181_to_270_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    6-9个月库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_271_to_330_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    9-11个月库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_271_to_365_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    9-12个月库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_331_to_365_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    11-12个月库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_365_plus_days" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    12个月以上库龄
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            
                            <th data-field="total_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    总价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="available_total_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    可用总数成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_fulfillable_quantity_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    FBA可售成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="reserved_fc_transfers_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    待调仓成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="reserved_fc_processing_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    调仓中成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="reserved_customerorders_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    待发货成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="quantity_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    FBM可售成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_unsellable_quantity_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    不可售成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_inbound_working_quantity_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    计划入库成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_inbound_shipped_quantity_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    在途成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_inbound_receiving_quantity_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    入库中成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="stock_up_num_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    实际在途成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="afn_researching_quantity_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    调查中数量成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_0_to_30_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    0-1个月库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_31_to_60_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    1-2个月库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_61_to_90_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    2-3个月库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_0_to_90_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    0-3个月库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_91_to_180_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    3-6个月库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_181_to_270_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    6-9个月库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_271_to_330_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    9-11个月库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_271_to_365_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    9-12个月库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_331_to_365_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    11-12个月库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="inv_age_365_plus_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    12个月以上库龄成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="historical_days_of_supply_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    历史供货天数成本价
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="cg_price" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    单位采购成本
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="cg_transport_costs" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    单位头程费用
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            
                            <th data-field="recommended_action" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    推荐操作
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="sell_through" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    售出率
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="estimated_excess_quantity" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    预计冗余数量
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="estimated_storage_cost_next_month" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    预计30天仓储费用
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="fba_minimum_inventory_level" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    最低库存水平
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="fba_inventory_level_health_status" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    库存水平健康度
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="historical_days_of_supply" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    历史供货天数
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="low_inventory_level_fee_applied" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    低库存水平费收取情况
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="fulfillment_channel" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    配送方式
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                            <th data-field="fba_storage_quantity_list" data-sort="none" class="sortable">
                                <div class="d-flex justify-content-between align-items-center">
                                    FBA可售信息列表
                                    <span class="sort-icons"><i class="fa fa-sort" aria-hidden="true"></i></span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($inventoryDetails)): ?>
                            <?php foreach ($inventoryDetails as $item): ?>
                                <tr>
                                    <td><input type="checkbox" name="records[]" value="<?php echo urlencode($item['name']) . '|' . urlencode($item['sku']); ?>"></td>
                                    <td data-field="name"><?php echo htmlspecialchars($item['name']); ?></td>
                                    <td data-field="seller_group_name"><?php echo htmlspecialchars($item['seller_group_name']); ?></td>
                                    <td data-field="sid"><?php echo $item['sid']; ?></td>
                                    <td data-field="asin"><?php echo htmlspecialchars($item['asin']); ?></td>
                                    <td data-field="product_name" title="<?php echo htmlspecialchars($item['product_name']); ?>">
                                        <?php echo mb_strlen($item['product_name']) > 20 ? mb_substr($item['product_name'], 0, 20) . '...' : htmlspecialchars($item['product_name']); ?>
                                    </td>
                                    <td data-field="small_image_url">
                                        <?php if (!empty($item['small_image_url'])): ?>
                                            <div style="display: inline-block; position: relative; width: 50px; height: 50px; ">
                                                <img src="<?php echo htmlspecialchars($item['small_image_url']); ?>" 
                                                     alt="预览图" 
                                                     class="img-thumbnail" 
                                                     style="width: 50px; height: 50px; object-fit: cover; transition: transform 0.3s ease;"
                                                     onmouseover="this.style.transform='scale(3)'"
                                                     onmouseout="this.style.transform='scale(1)'">
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted">无图片</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-field="seller_sku"><?php echo htmlspecialchars($item['seller_sku']); ?></td>
                                    <td data-field="fnsku"><?php echo htmlspecialchars($item['fnsku']); ?></td>
                                    <td data-field="sku"><?php echo htmlspecialchars($item['sku']); ?></td>
                                    <td data-field="category_text" title="<?php echo htmlspecialchars($item['category_text']); ?>">
                                        <?php echo mb_strlen($item['category_text']) > 20 ? mb_substr($item['category_text'], 0, 20) . '...' : htmlspecialchars($item['category_text']); ?>
                                    </td>
                                    <td data-field="product_brand_text"><?php echo htmlspecialchars($item['product_brand_text']); ?></td>
                                    <td data-field="share_type">
                                        <?php 
                                        $shareTypeText = '';
                                        switch($item['share_type']) {
                                            case 0:
                                                $shareTypeText = '非共享';
                                                break;
                                            case 1:
                                                $shareTypeText = '北美共享';
                                                break;
                                            case 2:
                                                $shareTypeText = '欧洲共享';
                                                break;
                                            default:
                                                $shareTypeText = '未知';
                                        }
                                        ?>
                                        <span class="badge <?php echo $item['share_type'] == 0 ? 'bg-secondary' : 'bg-primary'; ?>">
                                            <?php echo $shareTypeText; ?>
                                        </span>
                                    </td>
                                    
                                    <td data-field="total"><?php echo $item['total']; ?></td>
                                    <td data-field="available_total"><?php echo $item['available_total']; ?></td>
                                    <td data-field="afn_fulfillable_quantity"><?php echo $item['afn_fulfillable_quantity']; ?></td>
                                    <td data-field="reserved_fc_transfers"><?php echo $item['reserved_fc_transfers']; ?></td>
                                    <td data-field="reserved_fc_processing"><?php echo $item['reserved_fc_processing']; ?></td>
                                    <td data-field="reserved_customerorders"><?php echo $item['reserved_customerorders']; ?></td>
                                    <td data-field="quantity"><?php echo $item['quantity']; ?></td>
                                    <td data-field="afn_unsellable_quantity"><?php echo $item['afn_unsellable_quantity']; ?></td>
                                    <td data-field="afn_inbound_working_quantity"><?php echo $item['afn_inbound_working_quantity']; ?></td>
                                    <td data-field="afn_inbound_shipped_quantity"><?php echo $item['afn_inbound_shipped_quantity']; ?></td>
                                    <td data-field="afn_inbound_receiving_quantity"><?php echo $item['afn_inbound_receiving_quantity']; ?></td>
                                    <td data-field="stock_up_num"><?php echo $item['stock_up_num']; ?></td>
                                    <td data-field="afn_researching_quantity"><?php echo $item['afn_researching_quantity']; ?></td>
                                    <td data-field="total_fulfillable_quantity"><?php echo $item['total_fulfillable_quantity']; ?></td>
                                    <td data-field="inv_age_0_to_30_days"><?php echo $item['inv_age_0_to_30_days']; ?></td>
                                    <td data-field="inv_age_31_to_60_days"><?php echo $item['inv_age_31_to_60_days']; ?></td>
                                    <td data-field="inv_age_61_to_90_days"><?php echo $item['inv_age_61_to_90_days']; ?></td>
                                    <td data-field="inv_age_0_to_90_days"><?php echo $item['inv_age_0_to_90_days']; ?></td>
                                    <td data-field="inv_age_91_to_180_days"><?php echo $item['inv_age_91_to_180_days']; ?></td>
                                    <td data-field="inv_age_181_to_270_days"><?php echo $item['inv_age_181_to_270_days']; ?></td>
                                    <td data-field="inv_age_271_to_330_days"><?php echo $item['inv_age_271_to_330_days']; ?></td>
                                    <td data-field="inv_age_271_to_365_days"><?php echo $item['inv_age_271_to_365_days']; ?></td>
                                    <td data-field="inv_age_331_to_365_days"><?php echo $item['inv_age_331_to_365_days']; ?></td>
                                    <td data-field="inv_age_365_plus_days"><?php echo $item['inv_age_365_plus_days']; ?></td>
                                    
                                    <td data-field="total_price"><?php echo number_format($item['total_price'], 2); ?></td>
                                    <td data-field="available_total_price"><?php echo $item['available_total_price']; ?></td>
                                    <td data-field="afn_fulfillable_quantity_price"><?php echo $item['afn_fulfillable_quantity_price']; ?></td>
                                    <td data-field="reserved_fc_transfers_price"><?php echo $item['reserved_fc_transfers_price']; ?></td>
                                    <td data-field="reserved_fc_processing_price"><?php echo $item['reserved_fc_processing_price']; ?></td>
                                    <td data-field="reserved_customerorders_price"><?php echo $item['reserved_customerorders_price']; ?></td>
                                    <td data-field="quantity_price"><?php echo $item['quantity_price']; ?></td>
                                    <td data-field="afn_unsellable_quantity_price"><?php echo $item['afn_unsellable_quantity_price']; ?></td>
                                    <td data-field="afn_inbound_working_quantity_price"><?php echo $item['afn_inbound_working_quantity_price']; ?></td>
                                    <td data-field="afn_inbound_shipped_quantity_price"><?php echo $item['afn_inbound_shipped_quantity_price']; ?></td>
                                    <td data-field="afn_inbound_receiving_quantity_price"><?php echo $item['afn_inbound_receiving_quantity_price']; ?></td>
                                    <td data-field="stock_up_num_price"><?php echo $item['stock_up_num_price']; ?></td>
                                    <td data-field="afn_researching_quantity_price"><?php echo $item['afn_researching_quantity_price']; ?></td>
                                    <td data-field="inv_age_0_to_30_price"><?php echo $item['inv_age_0_to_30_price']; ?></td>
                                    <td data-field="inv_age_31_to_60_price"><?php echo $item['inv_age_31_to_60_price']; ?></td>
                                    <td data-field="inv_age_61_to_90_price"><?php echo $item['inv_age_61_to_90_price']; ?></td>
                                    <td data-field="inv_age_0_to_90_price"><?php echo $item['inv_age_0_to_90_price']; ?></td>
                                    <td data-field="inv_age_91_to_180_price"><?php echo $item['inv_age_91_to_180_price']; ?></td>
                                    <td data-field="inv_age_181_to_270_price"><?php echo $item['inv_age_181_to_270_price']; ?></td>
                                    <td data-field="inv_age_271_to_330_price"><?php echo $item['inv_age_271_to_330_price']; ?></td>
                                    <td data-field="inv_age_271_to_365_price"><?php echo $item['inv_age_271_to_365_price']; ?></td>
                                    <td data-field="inv_age_331_to_365_price"><?php echo $item['inv_age_331_to_365_price']; ?></td>
                                    <td data-field="inv_age_365_plus_price"><?php echo $item['inv_age_365_plus_price']; ?></td>
                                    <td data-field="historical_days_of_supply_price"><?php echo $item['historical_days_of_supply_price']; ?></td>
                                    <td data-field="cg_price"><?php echo $item['cg_price']; ?></td>
                                    <td data-field="cg_transport_costs"><?php echo $item['cg_transport_costs']; ?></td>
                                    
                                    <td data-field="recommended_action"><?php echo htmlspecialchars($item['recommended_action']); ?></td>
                                    <td data-field="sell_through"><?php echo number_format($item['sell_through'], 2); ?></td>
                                    <td data-field="estimated_excess_quantity"><?php echo number_format($item['estimated_excess_quantity'], 2); ?></td>
                                    <td data-field="estimated_storage_cost_next_month"><?php echo number_format($item['estimated_storage_cost_next_month'], 2); ?></td>
                                    <td data-field="fba_minimum_inventory_level"><?php echo number_format($item['fba_minimum_inventory_level'], 2); ?></td>
                                    <td data-field="fba_inventory_level_health_status"><?php echo htmlspecialchars($item['fba_inventory_level_health_status'] ?? ''); ?></td>
                                    <td data-field="historical_days_of_supply"><?php echo number_format($item['historical_days_of_supply'], 2); ?></td>
                                    <td data-field="low_inventory_level_fee_applied"><?php echo htmlspecialchars($item['low_inventory_level_fee_applied'] ?? ''); ?></td>
                                    <td data-field="fulfillment_channel"><?php echo htmlspecialchars($item['fulfillment_channel'] ?? ''); ?></td>
                                    <td data-field="fba_storage_quantity_list" title="<?php echo htmlspecialchars($item['fba_storage_quantity_list'] ?? ''); ?>">
                                        <?php 
                                        $storageList = $item['fba_storage_quantity_list'] ?? '';
                                        echo mb_strlen($storageList) > 50 ? mb_substr($storageList, 0, 50) . '...' : htmlspecialchars($storageList); 
                                        ?>
                                    </td>
                                    
                                    <td>
                                        <a href="<?php echo APP_URL; ?>/inventory_details_fba.php?action=delete&name=<?php echo urlencode($item['name']); ?>&sku=<?php echo urlencode($item['sku']); ?>" 
                                           class="btn btn-sm btn-danger" 
                                           onclick="return confirm('确定要删除该FBA库存详情记录吗？');">
                                            <i class="fa fa-trash"></i> 删除
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="60" class="text-center">暂无数据</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </form>
        
        <?php if ($totalPages > 1): ?>
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page - 1; ?><?php 
                                $params = [];
                                if (!empty($_GET['search'])) $params[] = 'search=' . urlencode($_GET['search']);
                                if (!empty($_GET['name'])) $params[] = 'name=' . urlencode($_GET['name']);
                                if (!empty($_GET['sku'])) $params[] = 'sku=' . urlencode($_GET['sku']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                上一页
                            </a>
                        </li>
                    <?php endif; ?>
                    
                    <?php
                    $showPages = [];
                    $showPages[] = 1;
                    
                    if ($totalPages > 1) {
                        $startPage = max(2, $page - 2);
                        $endPage = min($totalPages - 1, $page + 2);
                        
                        if ($startPage > 2) {
                            $showPages[] = '...';
                        }
                        
                        for ($i = $startPage; $i <= $endPage; $i++) {
                            $showPages[] = $i;
                        }
                        
                        if ($endPage < $totalPages - 1) {
                            $showPages[] = '...';
                        }
                        
                        if ($totalPages > 1) {
                            $showPages[] = $totalPages;
                        }
                    }
                    
                    foreach ($showPages as $showPage):
                        if ($showPage == '...'):
                    ?>
                        <li class="page-item disabled">
                            <span class="page-link">...</span>
                        </li>
                    <?php else:
                        $activeClass = $page == $showPage ? 'active' : '';
                    ?>
                        <li class="page-item <?php echo $activeClass; ?>">
                            <a class="page-link" href="?page=<?php echo $showPage; ?><?php 
                                $params = [];
                                if (!empty($_GET['search'])) $params[] = 'search=' . urlencode($_GET['search']);
                                if (!empty($_GET['name'])) $params[] = 'name=' . urlencode($_GET['name']);
                                if (!empty($_GET['sku'])) $params[] = 'sku=' . urlencode($_GET['sku']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                <?php echo $showPage; ?>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php endforeach; ?>
                    
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item">
                            <a class="page-link" href="?page=<?php echo $page + 1; ?><?php 
                                $params = [];
                                if (!empty($_GET['search'])) $params[] = 'search=' . urlencode($_GET['search']);
                                if (!empty($_GET['name'])) $params[] = 'name=' . urlencode($_GET['name']);
                                if (!empty($_GET['sku'])) $params[] = 'sku=' . urlencode($_GET['sku']);
                                if (!empty($params)) echo '&' . implode('&', $params);
                            ?>">
                                下一页
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<!-- 全选/取消全选脚本 -->
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('input[name="records[]"]');
        checkboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
</script>

<!-- 字段选择脚本 -->
<script>
    // 初始化字段选择
    document.addEventListener('DOMContentLoaded', function() {
        // 从localStorage加载用户的字段选择
        const savedFields = JSON.parse(localStorage.getItem('fbaInventoryFields')) || {
            // 默认显示的字段
            'name': true,
            'seller_group_name': true,
            'sid': true,
            'asin': true,
            'product_name': true,
            'small_image_url': true,
            'sku': true,
            'afn_fulfillable_quantity': true
        };
        
        // 设置复选框状态和表格列显示
        updateFieldSelection(savedFields);
        
        // 默认字段按钮事件
        document.getElementById('resetToDefaultFields').addEventListener('click', function() {
            // 定义默认字段
            const defaultFields = {
                'name': true,
                'seller_group_name': true,
                'sid': true,
                'asin': true,
                'product_name': true,
                'small_image_url': true,
                'sku': true,
                'afn_fulfillable_quantity': true
            };
            
            // 将默认字段设置保存到localStorage
            localStorage.setItem('fbaInventoryFields', JSON.stringify(defaultFields));
            
            // 更新字段选择和表格显示
            updateFieldSelection(defaultFields);
            
            // 提示用户
            alert('已恢复为默认字段设置');
        });
        
        // 保存设置按钮事件
        document.getElementById('saveFieldSelection').addEventListener('click', function() {
            const selectedFields = {};
            
            // 获取所有字段复选框的状态
            document.querySelectorAll('.field-checkbox').forEach(checkbox => {
                selectedFields[checkbox.value] = checkbox.checked;
            });
            
            // 保存到localStorage
            localStorage.setItem('fbaInventoryFields', JSON.stringify(selectedFields));
            
            // 更新表格列显示
            updateTableColumns(selectedFields);
            
            // 关闭模态框
            const modal = bootstrap.Modal.getInstance(document.getElementById('fieldSelectionModal'));
            modal.hide();
        });
    });
    
    // 更新字段选择状态
    function updateFieldSelection(savedFields) {
        document.querySelectorAll('.field-checkbox').forEach(checkbox => {
            checkbox.checked = savedFields[checkbox.value] || false;
        });
        
        updateTableColumns(savedFields);
    }
    
    // 更新表格列显示
    function updateTableColumns(selectedFields) {
        // 获取所有唯一的字段名
        const allFields = new Set();
        document.querySelectorAll('th[data-field]').forEach(th => {
            allFields.add(th.dataset.field);
        });
        
        // 遍历所有字段，设置它们的显示状态
        allFields.forEach(field => {
            const isVisible = selectedFields[field] || false;
            
            // 隐藏/显示表头列
            document.querySelectorAll(`th[data-field="${field}"]`).forEach(th => {
                th.style.display = isVisible ? '' : 'none';
            });
            
            // 隐藏/显示数据列
            document.querySelectorAll(`td[data-field="${field}"]`).forEach(td => {
                td.style.display = isVisible ? '' : 'none';
            });
        });
    }

    // 表格排序功能
    document.addEventListener('DOMContentLoaded', function() {
        // 为所有可排序的表头添加点击事件
        document.querySelectorAll('.sortable').forEach(th => {
            th.addEventListener('click', function() {
                const field = this.dataset.field;
                const currentSort = this.dataset.sort;
                
                // 切换排序状态
                let newSort = 'asc';
                if (currentSort === 'asc') {
                    newSort = 'desc';
                } else if (currentSort === 'desc') {
                    newSort = 'none';
                }
                
                // 更新当前表头的排序状态
                this.dataset.sort = newSort;
                
                // 更新所有表头的排序图标
                updateSortIcons();
                
                // 如果是"none"状态，则不排序
                if (newSort === 'none') {
                    // 恢复原始顺序
                    const table = this.closest('table');
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    rows.forEach(row => tbody.appendChild(row));
                    return;
                }
                
                // 执行排序
                sortTable(field, newSort);
            });
        });
        
        // 更新排序图标
        function updateSortIcons() {
            document.querySelectorAll('.sortable').forEach(th => {
                const sort = th.dataset.sort;
                const icon = th.querySelector('.sort-icons i');
                
                // 移除所有排序图标类
                icon.className = 'fa';
                
                // 根据排序状态添加相应的图标类
                if (sort === 'asc') {
                    icon.classList.add('fa-sort-asc');
                } else if (sort === 'desc') {
                    icon.classList.add('fa-sort-desc');
                } else {
                    icon.classList.add('fa-sort');
                }
            });
        }
        
        // 排序表格
        function sortTable(field, direction) {
            const table = document.querySelector('table');
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // 根据字段和方向排序行
            rows.sort((a, b) => {
                const aValue = getCellValue(a, field);
                const bValue = getCellValue(b, field);
                
                // 确定比较类型（数字或字符串）
                if (!isNaN(parseFloat(aValue)) && isFinite(aValue) && 
                    !isNaN(parseFloat(bValue)) && isFinite(bValue)) {
                    // 数字比较
                    return direction === 'asc' ? parseFloat(aValue) - parseFloat(bValue) : parseFloat(bValue) - parseFloat(aValue);
                } else {
                    // 字符串比较
                    if (direction === 'asc') {
                        return aValue.localeCompare(bValue, 'zh-CN');
                    } else {
                        return bValue.localeCompare(aValue, 'zh-CN');
                    }
                }
            });
            
            // 重新排列行
            rows.forEach(row => tbody.appendChild(row));
        }
        
        // 获取单元格值
        function getCellValue(row, field) {
            const cell = row.querySelector(`td[data-field="${field}"]`);
            return cell ? cell.textContent.trim() : '';
        }
    });
</script>