<?php
// Test script to check the parseCurrencyAmount function

// Set error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include the functions file
require_once 'helpers/functions.php';

echo "Testing parseCurrencyAmount function...\n\n";

// Test cases
$testCases = [
    '$100.00' => 100.00,
    '$1,234.56' => 1234.56,
    '¥500.00' => 500.00,
    '£250.50' => 250.50,
    '€300.75' => 300.75,
    '-$50.00' => -50.00,
    '$0.00' => 0.00,
    '150.25%' => 150.25,
    '-25.50%' => -25.50,
    '无' => 0.00,
    '' => 0.00,
    'invalid' => 0.00
];

// Run tests
$passed = 0;
$failed = 0;

foreach ($testCases as $input => $expected) {
    $result = parseCurrencyAmount($input);
    if ($result === $expected) {
        echo "✓ PASS: '$input' -> $result\n";
        $passed++;
    } else {
        echo "✗ FAIL: '$input' -> $result (expected: $expected)\n";
        $failed++;
    }
}

echo "\nResults: $passed passed, $failed failed\n";

echo "\nTesting with sample data from order_profit table...\n";

// Sample data that might be in the database
$sampleData = [
    ['profit_amount' => '$10.50', 'profit_rate' => '5.25%'],
    ['profit_amount' => '-$5.75', 'profit_rate' => '-2.10%'],
    ['profit_amount' => '¥100.00', 'profit_rate' => '15.50%'],
    ['profit_amount' => '$0.00', 'profit_rate' => '0.00%'],
    ['profit_amount' => '', 'profit_rate' => ''],
    ['profit_amount' => 'invalid', 'profit_rate' => 'invalid']
];

// Test parsing sample data
echo "Parsing sample order_profit data...\n";
foreach ($sampleData as $i => $data) {
    $parsedProfit = parseCurrencyAmount($data['profit_amount']);
    $parsedRate = parseCurrencyAmount($data['profit_rate']);
    echo "Sample $i: profit='{$data['profit_amount']}'->$parsedProfit, rate='{$data['profit_rate']}'->$parsedRate\n";
}
?>