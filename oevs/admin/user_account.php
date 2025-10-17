<?php
session_start();
include('dbcon.php');
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function redirect($url) {
    header("Location: $url");
    exit;
}

// ✅ OTP Verification Handler
if (isset($_POST['verify_otp'])) {
    $entered_otp = trim($_POST['otp']);

    if (!isset($_SESSION['login_otp'])) {
        $_SESSION['otp_error'] = "Session expired. Please log in again.";
        redirect('index.php');
    }

    if ($entered_otp == $_SESSION['login_otp']) {
        // Set final session variables for logged in and OTP verified user
        $_SESSION['id']         = $_SESSION['login_user_id'];     // Used by session.php
        $_SESSION['username']   = $_SESSION['login_username'];
        $_SESSION['schoolid']   = $_SESSION['login_schoolid'];
        $_SESSION['year']       = $_SESSION['login_year'];
        $_SESSION['status']     = $_SESSION['login_status'];
        $_SESSION['otp_verified'] = true;

        // Clean up temp login session data
        unset($_SESSION['login_otp'], $_SESSION['login_user_id'], $_SESSION['login_username'], $_SESSION['login_schoolid'], $_SESSION['login_year'], $_SESSION['login_status']);

        // Redirect to appropriate voting page
        switch ($_SESSION['year']) {
            case '1st year': redirect('voting.php');
            case '2nd year': redirect('voting2.php');
            case '3rd year': redirect('voting3.php');
            case '4th year': redirect('voting4.php');
            default:         redirect('voting.php');
        }
    } else {
        $_SESSION['otp_error'] = "Invalid OTP. Please try again.";
        redirect('verify_otp_login.php');
    }
}

// ✅ Login and Send OTP
if (isset($_POST['Login'])) {
    $UserName = mysqli_real_escape_string($conn, $_POST['UserName']);
    $SchoolID = mysqli_real_escape_string($conn, $_POST['SchoolID']);
    $Password = mysqli_real_escape_string($conn, $_POST['Password']);

    $query_user = "SELECT * FROM voters WHERE Username='$UserName'";
    $result_user = mysqli_query($conn, $query_user);

    if (mysqli_num_rows($result_user) == 0) {
        $_SESSION['login_error'] = "Invalid AU Email.";
        redirect('index.php');
    }

    $user = mysqli_fetch_assoc($result_user);

    // Verify account and credentials
    if ($user['Verified'] !== 'Verified') {
        $_SESSION['login_error'] = "Your account is not verified yet.";
        redirect('index.php');
    }

    if ($user['SchoolID'] !== $SchoolID) {
        $_SESSION['login_error'] = "Invalid School ID.";
        redirect('index.php');
    }

    if ($user['Password'] !== $Password) {
        $_SESSION['login_error'] = "Invalid Password.";
        redirect('index.php');
    }

    if ($user['Status'] === 'Voted') {
        $_SESSION['login_error'] = "You can only vote once.";
        redirect('index.php');
    }

    // ✅ Passed all checks — generate and send OTP
    $otp = rand(100000, 999999);

    // Save info to session temporarily
    $_SESSION['login_otp']       = $otp;
    $_SESSION['login_user_id']   = $user['VoterID'];
    $_SESSION['login_username']  = $UserName;
    $_SESSION['login_schoolid']  = $SchoolID;
    $_SESSION['login_year']      = $user['Year'];
    $_SESSION['login_status']    = $user['Status'];

    // Send OTP via email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'southphinmaau@gmail.com';     // Your Gmail
        $mail->Password   = 'rdac fski ttfd wqso';          // App password
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('southphinmaau@gmail.com', 'OEVS OTP');
        $mail->addAddress($UserName); // To AU student email

        $mail->isHTML(false);
        $mail->Subject = 'Your Login OTP Code';
        $mail->Body    = "Your OTP code is: $otp";

        $mail->send();
        redirect('verify_otp_login.php');
    } catch (Exception $e) {
        $_SESSION['login_error'] = "OTP could not be sent. Mailer Error: " . $mail->ErrorInfo;
        redirect('index.php');
    }
}

// ❌ Direct access fallback
redirect('index.php');
