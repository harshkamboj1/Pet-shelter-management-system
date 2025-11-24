<?php
session_start();
include_once '../database.php';
header('Content-Type: application/json');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit();
}

$userId = $_SESSION['user_id'];

// Function to get livestock for a user
function getLivestockByUser($conn, $userId) {
    $sql = "SELECT * FROM livestock WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    
    if ($stmt === false) {
        die("Error preparing query: " . $conn->error);
    }
    
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $livestock = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $livestock;
}

// Handle the case where 'id' is provided in the query string
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $livestock = getLivestockById($conn, $id, $userId);
    
    if ($livestock) {
        echo json_encode(['success' => true, 'livestock' => $livestock]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Livestock not found']);
    }
} else {
    // Fetch all livestock for the current user
    $livestock = getLivestockByUser($conn, $userId);
    echo json_encode(['success' => true, 'livestock' => $livestock]);
}

// For adding livestock
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $species = sanitizeInput($_POST['species']);
    $breed = sanitizeInput($_POST['breed']);
    $gender = sanitizeInput($_POST['gender']);
    $birthDate = sanitizeInput($_POST['birthdate']);
    $healthStatus = sanitizeInput($_POST['health']);
    $notes = sanitizeInput($_POST['notes']);

    if (empty($species) || empty($breed) || empty($gender) || empty($birthDate) || empty($healthStatus)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled out']);
        exit();
    }

    if (addLivestock($conn, $userId, $species, $breed, $gender, $birthDate, $healthStatus, $notes)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to add livestock']);
    }
}

// For updating livestock
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $species = sanitizeInput($_POST['species']);
    $breed = sanitizeInput($_POST['breed']);
    $gender = sanitizeInput($_POST['gender']);
    $birthDate = sanitizeInput($_POST['birthdate']);
    $healthStatus = sanitizeInput($_POST['health']);
    $notes = sanitizeInput($_POST['notes']);

    if (empty($id) || empty($species) || empty($breed) || empty($gender) || empty($birthDate) || empty($healthStatus)) {
        echo json_encode(['success' => false, 'message' => 'All required fields must be filled out']);
        exit();
    }

    if (updateLivestock($conn, $id, $userId, $species, $breed, $gender, $birthDate, $healthStatus, $notes)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update livestock']);
    }
}

// For deleting livestock
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['id'])) {
    $id = intval($_GET['id']);

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid livestock ID']);
        exit();
    }

    if (deleteLivestock($conn, $id, $userId)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to delete livestock']);
    }
}

// For getting livestock stats
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['stats'])) {
    $stats = getLivestockStats($conn, $userId);
    echo json_encode(['success' => true, 'stats' => $stats]);
}
?>
