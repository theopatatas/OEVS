<?php
include('dbcon.php'); // Your DB connection file

if (isset($_POST['id'])) {
    $id = intval($_POST['id']);  // sanitize input

    // Delete query
    $query = "DELETE FROM users WHERE User_id = $id";
    $result = mysqli_query($conn, $query);

    if ($result) {
        echo "success";
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    echo "No ID received.";
}
?>
