<?php
// Include the configuration to connect to the database
require_once 'config/config.php';

try {
    // Create a new PDO instance
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS
    );
    // Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if inventory_details table exists
    $checkTableQuery = "SHOW TABLES LIKE 'inventory_details'";
    $tableExists = $pdo->query($checkTableQuery)->rowCount() > 0;
    
    if ($tableExists) {
        echo "inventory_details table exists.\n";
        
        // Check the structure of the table
        echo "\nTable structure:\n";
        $descQuery = "DESCRIBE inventory_details";
        $descResult = $pdo->query($descQuery);
        while ($row = $descResult->fetch(PDO::FETCH_ASSOC)) {
            echo "{$row['Field']} - {$row['Type']} - {$row['Null']} - {$row['Default']}\n";
        }
        
        // Get data from inventory_details, focusing on product_onway field
        echo "\nData in inventory_details table:\n";
        $dataQuery = "SELECT sku, product_onway, product_valid_num, quantity_receive FROM inventory_details LIMIT 20";
        $dataResult = $pdo->query($dataQuery);
        $rowCount = $dataResult->rowCount();
        echo "Total rows: {$rowCount}\n";
        
        if ($rowCount > 0) {
            echo "\nSKU | product_onway | product_valid_num | quantity_receive\n";
            echo "----------------------------------------------------------\n";
            while ($row = $dataResult->fetch(PDO::FETCH_ASSOC)) {
                echo "{$row['sku']} | {$row['product_onway']} | {$row['product_valid_num']} | {$row['quantity_receive']}\n";
            }
        } else {
            echo "\nThe inventory_details table is empty.\n";
        }
        
        // Check if there are any non-zero product_onway values
        $onwayQuery = "SELECT COUNT(*) as non_zero_count FROM inventory_details WHERE product_onway > 0";
        $onwayResult = $pdo->query($onwayQuery)->fetch(PDO::FETCH_ASSOC);
        echo "\nNumber of records with product_onway > 0: {$onwayResult['non_zero_count']}\n";
        
    } else {
        echo "inventory_details table does not exist.\n";
    }
    
} catch (PDOException $e) {
    echo "Database error: " . $e->getMessage() . "\n";
}

echo "\nVerification complete.";
?>