<?php
session_start();
if (!isset($_SESSION['id']) || !isset($_SESSION['otp_verified']) || $_SESSION['otp_verified'] !== true) {
    header('location:index.php');
    exit();
}
?>
