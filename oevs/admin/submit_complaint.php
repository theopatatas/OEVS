<?php
include('session.php');
include('dbcon.php');

// Check if user is logged in
if (!isset($_SESSION['id'])) {
    die("You must be logged in to submit a complaint.");
}

$voterID = $_SESSION['id'];

// Fetch voter data
$query = "SELECT Username, SchoolID, Year FROM voters WHERE VoterID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $voterID);
$stmt->execute();
$result = $stmt->get_result();
$voter = $result->fetch_assoc();

// If voter not found
if (!$voter) {
    die("Voter data not found.");
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $subject = trim($_POST['subject'] ?? '');
    $description = trim($_POST['description'] ?? '');

    // Check required fields
    if (empty($subject) || empty($description)) {
        header("Location: complaint_form.php?error=1");
        exit();
    }

    // Prepare insert query
    $insertQuery = "INSERT INTO complaint (voterID, subject, description, status, Username, SchoolID, Year)
                    VALUES (?, ?, ?, 'pending', ?, ?, ?)";
    $stmtInsert = $conn->prepare($insertQuery);

    if (!$stmtInsert) {
        die("Prepare failed: " . $conn->error);
    }

    // Bind parameters and execute
    $stmtInsert->bind_param(
        "isssss",
        $voterID,
        $subject,
        $description,
        $voter['Username'],
        $voter['SchoolID'],
        $voter['Year']
    );

    if ($stmtInsert->execute()) {
        // Redirect to form with success message
        header("Location: complaint_form.php?success=1");
        exit();
    } else {
        echo "Error submitting complaint: " . $stmtInsert->error;
    }

    $stmtInsert->close();
} else {
    die("Invalid request method.");
}
?>
