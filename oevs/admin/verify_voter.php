<?php
include('dbcon.php'); // Connect to the database

if (isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    // Update the Verified status
    $query = "UPDATE voters SET Verified = 'Verified' WHERE VoterID = '$id'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "success";
    } else {
        echo "error";
    }
} else {
    echo "invalid";
}
?>
