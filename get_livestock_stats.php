<?php
// Database connection details
$servername = "localhost";
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "livestock_db"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to get livestock statistics
$sql = "SELECT COUNT(*) as total, 
               SUM(CASE WHEN gender = 'male' THEN 1 ELSE 0 END) as males, 
               SUM(CASE WHEN gender = 'female' THEN 1 ELSE 0 END) as females 
        FROM livestock"; 

$result = $conn->query($sql);

// Check if query returns any results
if ($result->num_rows > 0) {
    // Fetch the result as an associative array
    $row = $result->fetch_assoc();

    // Return the results as JSON
    echo json_encode(array(
        'status' => 'success',
        'total' => $row['total'],
        'males' => $row['males'],
        'females' => $row['females']
    ));
} else {
    // If no results, return a failure response
    echo json_encode(array(
        'status' => 'failure',
        'message' => 'No livestock found'
    ));
}

// Close the connection
$conn->close();
?>
