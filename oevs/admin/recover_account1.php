<?php
session_start();
require 'dbcon.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Validate passwords match
    if ($password !== $confirm_password) {
        $errors['password_mismatch'] = "Passwords do not match.";
    }

    // Validate inputs not empty (already required in HTML, but double-check)
    if (empty($firstname) || empty($lastname) || empty($username)) {
        $errors['missing_fields'] = "Please fill all required fields.";
    }

    if (empty($errors)) {
        // Check if user exists
        $stmt = $conn->prepare("SELECT User_id FROM users WHERE FirstName = ? AND LastName = ? AND UserName = ?");
        $stmt->bind_param("sss", $firstname, $lastname, $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // User found, update password (note: NOT hashed, but better to hash!)
            $user = $result->fetch_assoc();
            $user_id = $user['User_id'];

            $stmt_update = $conn->prepare("UPDATE users SET Password = ? WHERE User_id = ?");
            $stmt_update->bind_param("si", $password, $user_id);
            if ($stmt_update->execute()) {
                $_SESSION['success'] = "Password updated successfully!";
            } else {
                $errors['db_error'] = "Failed to update password. Please try again.";
            }
            $stmt_update->close();
        } else {
            $errors['user_exist'] = "User with provided details not found.";
        }
        $stmt->close();
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
    }

    header("Location: recoverpassword.php");
    exit();
}
