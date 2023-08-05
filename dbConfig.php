<?php
// Database configuration
$dbHost     = "localhost";
$dbUsername = "root";
$dbPassword = "root";
$dbName     = "link_shorter";

// Create database connection
try{
    $db = new PDO("mysql:host=$dbHost;dbname=$dbName", $dbUsername, $dbPassword);
}catch(PDOException $e){
    echo "Connection failed: " . $e->getMessage();
}