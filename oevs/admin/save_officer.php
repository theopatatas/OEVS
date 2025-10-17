<?php
include('dbcon.php');  // your DB connection file

if (isset($_POST['save'])) {
    $FirstName = mysqli_real_escape_string($conn, $_POST['FirstName']);
    $LastName = mysqli_real_escape_string($conn, $_POST['LastName']);
    $UserName = mysqli_real_escape_string($conn, $_POST['UserName']);
    $Password = mysqli_real_escape_string($conn, $_POST['Password']);
    $Position = mysqli_real_escape_string($conn, $_POST['Position']);

    // Set User_Type automatically as 'admin'
    $User_Type = 'admin';

    // Insert query
    $sql = "INSERT INTO users (FirstName, LastName, UserName, Password, User_Type, Position) 
            VALUES ('$FirstName', '$LastName', '$UserName', '$Password', '$User_Type', '$Position')";

    if (mysqli_query($conn, $sql)) {
        // Redirect or success message
        header("Location: election_officer_list.php?success=1");
        exit();
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>
