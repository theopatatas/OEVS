<?php
session_start();
require 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname'] ?? '');
    $lastname = trim($_POST['lastname'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $position = trim($_POST['position'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $errors = [];

    // Validate all required fields
    if (empty($firstname) || empty($lastname) || empty($username) || empty($position)) {
        $errors['missing_fields'] = "Please fill all required fields.";
    }

    // Validate passwords match
    if ($password !== $confirm_password) {
        $errors['password_mismatch'] = "Passwords do not match.";
    }

    if (empty($errors)) {
        // Check if user exists with all details
        $stmt = $conn->prepare("SELECT User_id FROM users WHERE FirstName = ? AND LastName = ? AND UserName = ? AND Position = ?");
        if (!$stmt) {
            $errors['db_error'] = "Database error: " . $conn->error;
        } else {
            $stmt->bind_param("ssss", $firstname, $lastname, $username, $position);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                // User found, update password without hashing
                $user = $result->fetch_assoc();
                $user_id = $user['User_id'];

                $stmt_update = $conn->prepare("UPDATE users SET Password = ? WHERE User_id = ?");
                if (!$stmt_update) {
                    $errors['db_error'] = "Database error: " . $conn->error;
                } else {
                    $stmt_update->bind_param("si", $password, $user_id);
                    if ($stmt_update->execute()) {
                        $_SESSION['success'] = "Password updated successfully!";
                    } else {
                        $errors['db_error'] = "Failed to update password. Please try again.";
                    }
                    $stmt_update->close();
                }
            } else {
                $errors['user_exist'] = "User with provided details not found.";
            }
            $stmt->close();
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }

    header("Location: recoverpassword.php");
    exit();
}
