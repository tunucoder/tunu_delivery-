<?php
// Test Database Connection
include 'includes/config.php';

echo "<h2>TUNU DELIVERY - Database Test</h2>";

// Test connection
if ($conn) {
    echo "✅ Database connection successful!<br><br>";
    
    // Test tables
    $tables = ['users', 'categories', 'menu_items', 'orders', 'order_items', 'reviews'];
    
    echo "<h3>Checking Tables:</h3>";
    foreach($tables as $table) {
        $result = $conn->query("SELECT COUNT(*) as count FROM $table");
        if($result) {
            $row = $result->fetch_assoc();
            echo "✅ Table '$table': {$row['count']} records<br>";
        } else {
            echo "❌ Table '$table': ERROR - " . $conn->error . "<br>";
        }
    }
    
    echo "<br><h3>Sample Menu Items:</h3>";
    $menu = $conn->query("SELECT * FROM menu_items LIMIT 3");
    if($menu && $menu->num_rows > 0) {
        while($item = $menu->fetch_assoc()) {
            echo "- {$item['item_name']} (TSH {$item['price']})<br>";
        }
    } else {
        echo "❌ No menu items found. Please import database.sql!<br>";
    }
    
} else {
    echo "❌ Database connection failed!<br>";
    echo "Error: " . $conn->connect_error;
}

echo "<br><br><a href='index.php'>← Back to Homepage</a>";
?>
