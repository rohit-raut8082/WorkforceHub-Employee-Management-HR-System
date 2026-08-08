<?php

// Database server details
$db_host = "localhost";      // Usually "localhost"
$db_user = "root";          // Usually "root" for local XAMPP
$db_pass = "";              // Leave empty for local XAMPP, add password if needed
$db_name = "ems_db";        // Name of our database

// Create a connection to MySQL
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check if connection was successful
if (!$conn) {
    // If connection fails, show error and stop
    die("Database Connection Error: " . mysqli_connect_error());
}

// Set character encoding to UTF-8 (supports all languages)
mysqli_set_charset($conn, "utf8mb4");

?>
