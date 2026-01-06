<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/database.php';

try {
    $db = Database::getInstance();
    
    // 创建warehouses表
    $warehousesSql = "CREATE TABLE IF NOT EXISTS `warehouses` (
        `wid` INT PRIMARY KEY,
        `type` VARCHAR(20),
        `sub_type` VARCHAR(20),
        `name` VARCHAR(50),
        `is_delete` TINYINT DEFAULT 0,
        `country_code` VARCHAR(10),
        `wp_id` VARCHAR(20),
        `wp_name` VARCHAR(50),
        `t_warehouse_name` VARCHAR(50),
        `t_warehouse_code` VARCHAR(20),
        `t_country_area_name` VARCHAR(50),
        `t_status` VARCHAR(10)
    );";
    $db->query($warehousesSql);
    echo "Created warehouses table successfully.\n";
    
    // 创建inventory_details表
    $inventoryDetailsSql = "CREATE TABLE IF NOT EXISTS `inventory_details` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `wid` INT,
        `sku` VARCHAR(50),
        `product_valid_num` INT DEFAULT 0,
        `quantity_receive` VARCHAR(20) DEFAULT '0',
        `average_age` INT DEFAULT 0,
        `purchase_price` DECIMAL(10, 2) DEFAULT 0,
        `head_stock_price` DECIMAL(10, 2) DEFAULT 0,
        `stock_price` DECIMAL(10, 2) DEFAULT 0,
        `product_onway` INT DEFAULT 0,
        FOREIGN KEY (`wid`) REFERENCES `warehouses`(`wid`) ON DELETE SET NULL
    );";
    $db->query($inventoryDetailsSql);
    echo "Created inventory_details table successfully.\n";
    
} catch (Exception $e) {
    echo "Error creating tables: " . $e->getMessage() . "\n";
}
?>