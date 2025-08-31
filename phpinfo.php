<?php
echo "<h1>PHP Test Page</h1>";
echo "<p>PHP is working!</p>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

// Test database connection
echo "<h2>Database Test</h2>";
try {
    $pdo = new PDO("mysql:host=localhost;dbname=magusa_taxi;charset=utf8mb4", "root", "");
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
    // Test if tables exist
    $stmt = $pdo->query("SHOW TABLES");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "<p>Tables found: " . implode(', ', $tables) . "</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h2>File Path Info</h2>";
echo "<p>Current directory: " . __DIR__ . "</p>";
echo "<p>Document root: " . $_SERVER['DOCUMENT_ROOT'] . "</p>";
echo "<p>Script name: " . $_SERVER['SCRIPT_NAME'] . "</p>";
?>
