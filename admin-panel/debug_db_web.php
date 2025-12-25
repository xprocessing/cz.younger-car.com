<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>数据库查询调试</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h1>数据库查询调试</h1>
        
        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        
        require_once __DIR__ . '/config/config.php';
        require_once __DIR__ . '/includes/database.php';
        
        $last30DaysStart = date('Y-m-d', strtotime('-30 days'));
        $today = date('Y-m-d');
        
        echo "<div class='alert alert-info'>";
        echo "<strong>查询日期范围:</strong> {$last30DaysStart} 到 {$today}";
        echo "</div>";
        
        try {
            $db = Database::getInstance();
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>1. order_profit 表总记录数</div>";
            echo "<div class='card-body'>";
            $sql = "SELECT COUNT(*) as count FROM order_profit";
            $stmt = $db->query($sql);
            $result = $stmt->fetch();
            echo "<h3>{$result['count']} 条记录</h3>";
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>2. 最近30天的数据</div>";
            echo "<div class='card-body'>";
            $sql = "SELECT COUNT(*) as count FROM order_profit 
                    WHERE DATE(global_purchase_time) >= ? AND DATE(global_purchase_time) <= ?";
            $stmt = $db->query($sql, [$last30DaysStart, $today]);
            $result = $stmt->fetch();
            echo "<h3>{$result['count']} 条记录</h3>";
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>3. global_purchase_time 字段的值(最新5条)</div>";
            echo "<div class='card-body'>";
            $sql = "SELECT id, global_order_no, global_purchase_time, store_id 
                    FROM order_profit 
                    ORDER BY global_purchase_time DESC 
                    LIMIT 5";
            $stmt = $db->query($sql);
            $data = $stmt->fetchAll();
            echo "<table class='table table-sm'>";
            echo "<thead><tr><th>ID</th><th>订单号</th><th>下单时间</th><th>店铺ID</th></tr></thead>";
            echo "<tbody>";
            foreach ($data as $row) {
                echo "<tr><td>{$row['id']}</td><td>{$row['global_order_no']}</td><td>{$row['global_purchase_time']}</td><td>{$row['store_id']}</td></tr>";
            }
            echo "</tbody></table>";
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>4. store 表总记录数</div>";
            echo "<div class='card-body'>";
            $sql = "SELECT COUNT(*) as count FROM store";
            $stmt = $db->query($sql);
            $result = $stmt->fetch();
            echo "<h3>{$result['count']} 条记录</h3>";
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>5. 最近30天 order_profit 和 store 表的关联</div>";
            echo "<div class='card-body'>";
            $sql = "SELECT op.*, s.platform_name, s.store_name 
                    FROM order_profit op
                    LEFT JOIN store s ON op.store_id = s.store_id
                    WHERE DATE(op.global_purchase_time) >= ? AND DATE(op.global_purchase_time) <= ?
                    LIMIT 5";
            $stmt = $db->query($sql, [$last30DaysStart, $today]);
            $data = $stmt->fetchAll();
            if (empty($data)) {
                echo "<div class='alert alert-warning'>最近30天没有关联数据</div>";
            } else {
                echo "<table class='table table-sm'>";
                echo "<thead><tr><th>订单号</th><th>平台</th><th>店铺</th><th>下单时间</th></tr></thead>";
                echo "<tbody>";
                foreach ($data as $row) {
                    $platform = $row['platform_name'] ?? '<span class="text-danger">NULL</span>';
                    $store = $row['store_name'] ?? '<span class="text-danger">NULL</span>';
                    echo "<tr><td>{$row['global_order_no']}</td><td>{$platform}</td><td>{$store}</td><td>{$row['global_purchase_time']}</td></tr>";
                }
                echo "</tbody></table>";
            }
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>6. 有店铺关联的记录数</div>";
            echo "<div class='card-body'>";
            $sql = "SELECT COUNT(*) as count 
                    FROM order_profit op
                    LEFT JOIN store s ON op.store_id = s.store_id
                    WHERE s.store_id IS NOT NULL";
            $stmt = $db->query($sql);
            $result = $stmt->fetch();
            echo "<h3>{$result['count']} 条记录</h3>";
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>7. 没有店铺关联的记录数</div>";
            echo "<div class='card-body'>";
            $sql = "SELECT COUNT(*) as count 
                    FROM order_profit op
                    LEFT JOIN store s ON op.store_id = s.store_id
                    WHERE s.store_id IS NULL";
            $stmt = $db->query($sql);
            $result = $stmt->fetch();
            echo "<h3>{$result['count']} 条记录</h3>";
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>8. store 表的数据样本</div>";
            echo "<div class='card-body'>";
            $sql = "SELECT store_id, platform_name, store_name FROM store LIMIT 10";
            $stmt = $db->query($sql);
            $data = $stmt->fetchAll();
            echo "<table class='table table-sm'>";
            echo "<thead><tr><th>店铺ID</th><th>平台</th><th>店铺名</th></tr></thead>";
            echo "<tbody>";
            foreach ($data as $row) {
                echo "<tr><td>{$row['store_id']}</td><td>{$row['platform_name']}</td><td>{$row['store_name']}</td></tr>";
            }
            echo "</tbody></table>";
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>9. 直接测试 getPlatformStats 方法</div>";
            echo "<div class='card-body'>";
            require_once __DIR__ . '/models/OrderProfit.php';
            require_once __DIR__ . '/helpers/functions.php';
            $orderProfitModel = new OrderProfit();
            $platformStats = $orderProfitModel->getPlatformStats($last30DaysStart, $today);
            if (empty($platformStats)) {
                echo "<div class='alert alert-warning'>getPlatformStats 返回空数组</div>";
            } else {
                echo "<table class='table'>";
                echo "<thead><tr><th>平台名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr></thead>";
                echo "<tbody>";
                foreach ($platformStats as $platform) {
                    echo "<tr>";
                    echo "<td>{$platform['platform_name']}</td>";
                    echo "<td>{$platform['order_count']}</td>";
                    echo "<td>¥" . number_format($platform['total_profit'], 2) . "</td>";
                    echo "<td>" . number_format($platform['avg_profit_rate'], 2) . "%</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            echo "</div></div>";
            
            echo "<div class='card mb-3'>";
            echo "<div class='card-header'>10. 直接测试 getStoreStats 方法</div>";
            echo "<div class='card-body'>";
            $storeStats = $orderProfitModel->getStoreStats($last30DaysStart, $today);
            if (empty($storeStats)) {
                echo "<div class='alert alert-warning'>getStoreStats 返回空数组</div>";
            } else {
                echo "<table class='table'>";
                echo "<thead><tr><th>平台名称</th><th>店铺名称</th><th>订单数量</th><th>总利润</th><th>平均利润率</th></tr></thead>";
                echo "<tbody>";
                foreach ($storeStats as $store) {
                    echo "<tr>";
                    echo "<td>{$store['platform_name']}</td>";
                    echo "<td>{$store['store_name']}</td>";
                    echo "<td>{$store['order_count']}</td>";
                    echo "<td>¥" . number_format($store['total_profit'], 2) . "</td>";
                    echo "<td>" . number_format($store['avg_profit_rate'], 2) . "%</td>";
                    echo "</tr>";
                }
                echo "</tbody></table>";
            }
            echo "</div></div>";
            
        } catch (Exception $e) {
            echo "<div class='alert alert-danger'>";
            echo "<strong>错误:</strong> " . $e->getMessage();
            echo "</div>";
        }
        ?>
    </div>
</body>
</html>
