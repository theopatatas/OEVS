<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['verify_otp'])) {
    $entered_otp = trim($_POST['otp']);
    $stored_otp = $_SESSION['otp'] ?? null;
    $errors = [];

    if (!$stored_otp) {
        $errors['otp'] = "Session expired or OTP not set. Please resend OTP.";
    } elseif ($entered_otp !== (string)$stored_otp) {
        $errors['otp'] = "Invalid OTP. Please try again.";
    } else {
        // OTP verified successfully

        // Optional: Clear OTP from session
        unset($_SESSION['otp'], $_SESSION['otp_email']);

        // Proceed to the next step: registration, or login, etc.
        $_SESSION['success'] = "OTP verified successfully!";
        header('Location: register1.php');  // or your next page
        exit;
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: verify_otp.php');
        exit;
    }
} else {
    // Direct access without POST data, redirect
    header('Location: verify_otp.php');
    exit;
}
