<?php
echo "<h1>PHP is working!</h1>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

// Test database connection
try {
    $pdo = new PDO("mysql:host=localhost;dbname=magusa_taxi;charset=utf8mb4", "root", "");
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
    echo "<p><strong>This is likely the problem!</strong></p>";
}
?>
