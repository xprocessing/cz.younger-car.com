<?php
// 定义APP_ROOT常量
define('APP_ROOT', __DIR__);

// 引入配置文件
require_once 'config/config.php';

// 直接使用PDO连接数据库，避免依赖Database类的其他配置
try {
    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
    $pdo = new PDO($dsn, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "成功连接到数据库！\n";
    
    // 执行创建car_data表的SQL语句
    $sql = "CREATE TABLE IF NOT EXISTS car_data (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '主键ID，自增',
        make VARCHAR(50) DEFAULT NULL COMMENT '汽车品牌（英文）',
        make_cn VARCHAR(50) DEFAULT NULL COMMENT '汽车品牌（中文）',
        model VARCHAR(50) DEFAULT NULL COMMENT '汽车型号',
        year INT DEFAULT NULL COMMENT '生产年份',
        trim VARCHAR(50) DEFAULT NULL COMMENT '配置版本',
        trim_description TEXT DEFAULT NULL COMMENT '配置版本详细描述',
        market VARCHAR(50) DEFAULT NULL COMMENT '销售市场',
        create_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '创建时间',
        update_at TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
        PRIMARY KEY (id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='车型数据';";
    
    $pdo->exec($sql);
    echo "成功创建car_data表！\n";
    
    // 插入一些示例数据，以便测试
    $insertSql = "INSERT IGNORE INTO car_data (make, make_cn, model, year, trim, trim_description, market) VALUES
        ('Toyota', '丰田', 'Camry', 2023, 'LE', '标准配置', '中国大陆'),
        ('Honda', '本田', 'Accord', 2023, 'EX', '豪华配置', '中国大陆'),
        ('Ford', '福特', 'Fusion', 2022, 'SE', '运动配置', '北美'),
        ('Volkswagen', '大众', 'Passat', 2023, 'SEL', '舒适配置', '欧洲')";
    
    $inserted = $pdo->exec($insertSql);
    echo "成功插入示例数据：$inserted 条\n";
    
} catch (PDOException $e) {
    echo "数据库操作失败：" . $e->getMessage() . "\n";
    exit(1);
}
