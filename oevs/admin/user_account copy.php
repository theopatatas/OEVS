<?php
session_start();
include('dbcon.php');

// Redirect helper
function redirect($url) {
    header("Location: $url");
    exit;
}

if (isset($_POST['Login'])) {
    $UserName = mysqli_real_escape_string($conn, $_POST['UserName']);
    $SchoolID = mysqli_real_escape_string($conn, $_POST['SchoolID']);
    $Password = mysqli_real_escape_string($conn, $_POST['Password']);

    // Check if the username exists
    $query_user = "SELECT * FROM voters WHERE Username='$UserName'";
    $result_user = mysqli_query($conn, $query_user);

    if (mysqli_num_rows($result_user) == 0) {
        $_SESSION['login_error'] = "Invalid AU Email.";
        redirect('Index.php');
    }

    $user = mysqli_fetch_assoc($result_user);

    // Check if user is verified
    if ($user['Verified'] !== 'Verified') {
        $_SESSION['login_error'] = "Your account is not verified, please wait.";
        redirect('Index.php');
    }

    // Check School ID
    if ($user['SchoolID'] !== $SchoolID) {
        $_SESSION['login_error'] = "Invalid School ID.";
        redirect('Index.php');
    }

    // Check Password (plain text, but you should hash passwords)
    if ($user['Password'] !== $Password) {
        $_SESSION['login_error'] = "Invalid Password.";
        redirect('Index.php');
    }

    // Check voting status
    if ($user['Status'] === 'Voted') {
        $_SESSION['login_error'] = "You can only vote once.";
        redirect('Index.php');
    }

    // Set session
    $_SESSION['id'] = $user['VoterID'];

    // Redirect by year, default to voting.php if unknown year
    switch ($user['Year']) {
        case '1st year':
            redirect('voting.php');
            break;
        case '2nd year':
            redirect('voting2.php');
            break;
        case '3rd year':
            redirect('voting3.php');
            break;
        case '4th year':
            redirect('voting4.php');
            break;
        default:
            // Unknown year — just redirect to voting.php or homepage without error
            redirect('voting.php');
    }
} else {
    // Redirect to login if accessed directly
    redirect('Index.php');
}
?>
