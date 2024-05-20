<?php
session_start();
require 'database_connection.php'; // Assuming this file contains the database connection

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userId = $_SESSION['user_id'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];

    // Update the user's personal details in the database
    $sql = "UPDATE users SET first_name = ?, last_name = ?, email = ?, phone = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $firstName, $lastName, $email, $phone, $userId);
    if ($stmt->execute()) {
        echo "Personal details updated successfully.";
    } else {
        echo "Error updating personal details.";
    }
    $stmt->close();
}
?>
