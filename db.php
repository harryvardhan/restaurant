<?php
$host = "srv1947.hstgr.io";  // Hostinger MySQL server (or use 82.25.121.3)
$user = "u971599292_user";   // Your MySQL user
$pass = "xJ1qKnZ]0";         // Your MySQL password
$dbname = "u971599292_restaurant_aut"; // Your database name

// Create connection
$conn = new mysqli($host, $user, $pass, $dbname);

// Check connection
if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}

// Optional: set charset to avoid encoding issues
$conn->set_charset("utf8mb4");
?>
