<?php
// Database configuration
$dbHost = "localhost";
$dbUsername = "root";
$dbPassword = "";
$dbName = "livestock_db";

// Step 1: Connect to MySQL server without specifying the database
$conn = new mysqli($dbHost, $dbUsername, $dbPassword);

// Check server connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Step 2: Create database if it doesn't exist
$sql = "CREATE DATABASE IF NOT EXISTS $dbName";
if ($conn->query($sql) === FALSE) {
    error_log("Error creating database: " . $conn->error); // Log error for debugging
    die("Error creating database: " . $conn->error);
}

// Step 3: Select the newly created or existing database
$conn->select_db($dbName);

// Step 4: Create tables if they don't exist
function setupDatabase($conn) {
    // Users table
    $usersTable = "CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(100) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        full_name VARCHAR(100),
        phone VARCHAR(20),
        address TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Livestock table
    $livestockTable = "CREATE TABLE IF NOT EXISTS livestock (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        species VARCHAR(50) NOT NULL,
        breed VARCHAR(100) NOT NULL,
        gender VARCHAR(10) NOT NULL,
        birth_date DATE NOT NULL,
        health_status VARCHAR(20) NOT NULL,
        notes TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
    )";
    
    // Contact messages table
    $contactTable = "CREATE TABLE IF NOT EXISTS contact_messages (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        subject VARCHAR(200) NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    
    // Execute queries
    if ($conn->query($usersTable) === FALSE) {
        error_log("Error creating users table: " . $conn->error); // Log error for debugging
    }
    if ($conn->query($livestockTable) === FALSE) {
        error_log("Error creating livestock table: " . $conn->error); // Log error for debugging
    }
    if ($conn->query($contactTable) === FALSE) {
        error_log("Error creating contact table: " . $conn->error); // Log error for debugging
    }
}

// Setup database tables
setupDatabase($conn);

// Function to sanitize input data
if (!function_exists('sanitizeInput')) {
    function sanitizeInput($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }
}

// Function to add livestock
function addLivestock($conn, $userId, $species, $breed, $gender, $birthDate, $healthStatus, $notes) {
    $sql = "INSERT INTO livestock (user_id, species, breed, gender, birth_date, health_status, notes) 
            VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $userId, $species, $breed, $gender, $birthDate, $healthStatus, $notes);
    
    if (!$stmt->execute()) {
        error_log("Error inserting livestock: " . $stmt->error); // Log error for debugging
        return false;
    }
    return true;
}

?>
