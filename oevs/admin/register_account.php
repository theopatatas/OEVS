<?php
session_start();
include('dbcon.php');
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup'])) {
    $firstname = trim($_POST['firstname']);
    $middlename = trim($_POST['middlename']);
    $lastname = trim($_POST['lastname']);
    $username = trim($_POST['username']);  // Assuming username = email
    $schoolid = trim($_POST['schoolid']);
    $year = trim($_POST['year']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    $errors = [];

    // Validate passwords match
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = "Passwords do not match.";
    }

    // Validate email format
    if (!filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $errors['invalid_email'] = "Invalid email address.";
    }

    // Check if username/email exists
    $checkStmt = $conn->prepare("SELECT * FROM voters WHERE Username = ?");
    if (!$checkStmt) {
        die("Prepare failed: " . $conn->error);
    }
    $checkStmt->bind_param("s", $username);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    if ($checkResult->num_rows > 0) {
        $errors['user_exist'] = "Username already exists.";
    }
    $checkStmt->close();

    // If errors found, save to session and redirect back
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        redirect("register1.php");
    }

    // <-- No password hashing here, store plain text password
    $plain_password = $password;

    $status = "Unvote";
    $verified = "Not Verified";

    // Prepare insert statement
    $insertStmt = $conn->prepare("INSERT INTO voters (FirstName, MiddleName, LastName, Username, Password, Year, Status, SchoolID, Verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    if (!$insertStmt) {
        die("Prepare failed: " . $conn->error);
    }

    $insertStmt->bind_param("sssssssss", $firstname, $middlename, $lastname, $username, $plain_password, $year, $status, $schoolid, $verified);

    if (!$insertStmt->execute()) {
        $_SESSION['errors']['db'] = "Error while saving user. Please try again.";
        redirect("register1.php");
    }

    $insertStmt->close();

    // Generate OTP and save it in session
    $otp = rand(100000, 999999);
    $_SESSION['otp'] = $otp;
    $_SESSION['username'] = $username;

    // Send OTP via email using PHPMailer
    $mail = new PHPMailer(true);
    try {
       $mail->isSMTP();
       $mail->Host       = 'smtp.gmail.com';
       $mail->SMTPAuth   = true;
       $mail->Username   = 'jayzmariano25@gmail.com';    // Your Gmail email
       $mail->Password   = 'daud hnke hjwi wjdd';        // Your Gmail App Password (16 chars, no spaces)
       $mail->SMTPSecure = 'tls';
       $mail->Port       = 587;

       $mail->setFrom('jayzmariano25@gmail.com', 'OEVS OTP');
       $mail->addAddress($username);

        $mail->isHTML(false);
        $mail->Subject = 'Your OTP Code for Registration';
        $mail->Body    = "Hello $firstname,\n\nYour OTP code for registration is: $otp\n\nPlease enter this code to verify your account.";

        $mail->send();

        // Redirect user to OTP verification page
        redirect("otp_verification.php");
    } catch (Exception $e) {
        $_SESSION['errors']['mail_error'] = "OTP could not be sent. Mailer Error: " . $mail->ErrorInfo;
        redirect("register1.php");
    }
}
?>
