<?php

session_start();
include_once '../database.php';
header('Content-Type: application/json');

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

// Ensure request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit();
}

// Retrieve and sanitize input
$userId = $_SESSION['user_id'];
$species = sanitizeInput($_POST['species'] ?? '');
$breed = sanitizeInput($_POST['breed'] ?? '');
$gender = sanitizeInput($_POST['gender'] ?? '');
$birthDate = sanitizeInput($_POST['birthdate'] ?? '');
$healthStatus = sanitizeInput($_POST['health'] ?? '');
$notes = sanitizeInput($_POST['notes'] ?? '');

// Validate required fields
if (empty($species) || empty($breed) || empty($gender) || empty($birthDate) || empty($healthStatus)) {
    echo json_encode(['success' => false, 'message' => 'All required fields must be filled out']);
    exit();
}

// Validate birthDate format
if (!DateTime::createFromFormat('Y-m-d', $birthDate)) {
    echo json_encode(['success' => false, 'message' => 'Invalid birthdate format. Please use YYYY-MM-DD']);
    exit();
}

// Attempt to add livestock
if (addLivestock($conn, $userId, $species, $breed, $gender, $birthDate, $healthStatus, $notes)) {
    echo json_encode(['success' => true, 'message' => 'Livestock added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add livestock']);
}

?>
