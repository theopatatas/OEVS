<?php
session_start();
require 'dbcon.php';

// Collect form inputs safely
$username = trim($_POST['username']);
$schoolid = trim($_POST['schoolid']);
$password = $_POST['password'];
$confirm_password = $_POST['confirm_password'];

// Initialize error array
$errors = [];

if ($password !== $confirm_password) {
    $errors['user_exist'] = "Passwords do not match.";
} else {
    // Check if user exists with matching Username and SchoolID
    $stmt = $conn->prepare("SELECT * FROM voters WHERE Username = ? AND SchoolID = ?");
    if ($stmt) {
        $stmt->bind_param("ss", $username, $schoolid);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result && $result->num_rows === 1) {
            // User found – update password without hashing
            $update = $conn->prepare("UPDATE voters SET Password = ? WHERE Username = ? AND SchoolID = ?");
            if ($update) {
                $update->bind_param("sss", $password, $username, $schoolid);
                if ($update->execute()) {
                    $_SESSION['success'] = "Password updated successfully. You can now log in.";
                } else {
                    $errors['user_exist'] = "Error updating password. Please try again.";
                }
                $update->close();
            } else {
                $errors['user_exist'] = "Failed to prepare update statement.";
            }
        } else {
            $errors['user_exist'] = "No user found with the provided AU Email and School ID.";
        }

        $stmt->close();
    } else {
        $errors['user_exist'] = "Failed to prepare statement.";
    }
}

$conn->close();

// Set error messages in session and redirect
$_SESSION['errors'] = $errors;
header("Location: recoverpassword.php");
exit;
