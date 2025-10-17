<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
include 'dbcon.php';  // your DB connection

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_otp'])) {
    $email = trim($_POST['username']);
    $errors = [];

    // Basic email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = "Please enter a valid email address.";
    } else {
        // Generate OTP
        $otp = rand(100000, 999999);

        // Save OTP and email to session for verification later
        $_SESSION['otp'] = $otp;
        $_SESSION['otp_email'] = $email;

        $mail = new PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'southphinmaau@gmail.com';    // Your Gmail email
            $mail->Password   = 'rdac fski ttfd wqso';        // Your Gmail App Password (16 chars, no spaces)
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('southphinmaau@gmail.com', 'OEVS OTP');
            $mail->addAddress($email);  // Use $email, not $username

            // Content
            $mail->isHTML(true);
            $mail->Subject = 'Your OTP Code';
            $mail->Body    = "Your OTP code is <b>$otp</b>";

            $mail->send();

            $_SESSION['success'] = "OTP sent to your email: $email";
            header('Location: verify_otp.php'); // redirect to OTP input page
            exit;
        } catch (Exception $e) {
            $errors['email'] = "OTP could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }
    }

    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        header('Location: register1.php');  // back to your OTP send form
        exit;
    }
} else {
    // If direct access or no form submit
    header('Location: register1.php');
    exit;
}
