<?php
// Test script to check order_profit data for the last 30 days

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include database configuration
require_once 'config/config.php';
require_once 'models/OrderProfit.php';
require_once 'helpers/functions.php';

// Get the last 30 days date range
$last30DaysStart = date('Y-m-d', strtotime('-30 days'));
$endDate = date('Y-m-d');

echo "Testing order_profit stats for the last 30 days...\n";
echo "Date range: $last30DaysStart to $endDate\n\n";

// Create OrderProfit model instance
$orderProfitModel = new OrderProfit();

// Test 1: Check total records in the table
try {
    $sql = "SELECT COUNT(*) as total FROM order_profit";
    $stmt = $orderProfitModel->db->query($sql);
    $total = $stmt->fetch()['total'];
    echo "1. Total records in order_profit table: $total\n";
} catch (PDOException $e) {
    echo "Error checking total records: " . $e->getMessage() . "\n";
}

// Test 2: Check records with global_purchase_time data
try {
    $sql = "SELECT COUNT(*) as count FROM order_profit WHERE global_purchase_time IS NOT NULL AND global_purchase_time != ''";
    $stmt = $orderProfitModel->db->query($sql);
    $withPurchaseTime = $stmt->fetch()['count'];
    echo "2. Records with global_purchase_time: $withPurchaseTime\n";
} catch (PDOException $e) {
    echo "Error checking records with global_purchase_time: " . $e->getMessage() . "\n";
}

// Test 3: Check records in the last 30 days
try {
    $sql = "SELECT COUNT(*) as count FROM order_profit WHERE DATE(global_purchase_time) >= ? AND DATE(global_purchase_time) <= ?";
    $stmt = $orderProfitModel->db->query($sql, [$last30DaysStart, $endDate]);
    $recentCount = $stmt->fetch()['count'];
    echo "3. Records in the last 30 days: $recentCount\n";
} catch (PDOException $e) {
    echo "Error checking recent records: " . $e->getMessage() . "\n";
}

// Test 4: Check a sample of recent records
try {
    $sql = "SELECT id, store_id, global_purchase_time, profit_amount, profit_rate FROM order_profit WHERE DATE(global_purchase_time) >= ? AND DATE(global_purchase_time) <= ? LIMIT 5";
    $stmt = $orderProfitModel->db->query($sql, [$last30DaysStart, $endDate]);
    $sample = $stmt->fetchAll();
    echo "\n4. Sample recent records:\n";
    if (!empty($sample)) {
        foreach ($sample as $row) {
            echo "   ID: {$row['id']}, Store: {$row['store_id']}, Date: {$row['global_purchase_time']}, Profit: {$row['profit_amount']}, Rate: {$row['profit_rate']}%\n";
        }
    } else {
        echo "   No recent records found\n";
    }
} catch (PDOException $e) {
    echo "Error getting sample records: " . $e->getMessage() . "\n";
}

// Test 5: Run the actual platform stats query
try {
    $platformStats = $orderProfitModel->getPlatformStats($last30DaysStart, $endDate);
    echo "\n5. Platform stats result: " . count($platformStats) . " platforms found\n";
    if (!empty($platformStats)) {
        foreach ($platformStats as $platform) {
            echo "   Platform: {$platform['platform_name']}, Orders: {$platform['order_count']}, Total Profit: {$platform['total_profit']}, Avg Rate: {$platform['avg_profit_rate']}%\n";
        }
    }
} catch (Exception $e) {
    echo "Error getting platform stats: " . $e->getMessage() . "\n";
}

// Test 6: Run the actual store stats query
try {
    $storeStats = $orderProfitModel->getStoreStats($last30DaysStart, $endDate);
    echo "\n6. Store stats result: " . count($storeStats) . " stores found\n";
    if (!empty($storeStats)) {
        foreach ($storeStats as $store) {
            echo "   Store: {$store['store_name']}, Orders: {$store['order_count']}, Total Profit: {$store['total_profit']}, Avg Rate: {$store['avg_profit_rate']}%\n";
        }
    }
} catch (Exception $e) {
    echo "Error getting store stats: " . $e->getMessage() . "\n";
}

echo "\nTest completed.";
?>