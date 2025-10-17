<?php
include('dbcon.php');

if (isset($_POST['save'])) {
    $rfirstname = trim($_POST['rfirstname']);
    $rlastname = trim($_POST['rlastname']);
    $rgender = trim($_POST['rgender']);
    $ryear = trim($_POST['ryear']);
    $rposition = trim($_POST['rposition']);
    $rmname = trim($_POST['rmname']);
    $party = trim($_POST['party']);
    $qualification = trim($_POST['qualification']);
    $user_name = trim($_POST['user_name']);

    // Map positions to codes
    $abc_map = [
        'President' => 'p',
        'Vice-President' => 'vp',
        'Governor' => 'a',
        'Vice-Governor' => 'b',
        'Secretary' => 's',
        'Treasurer' => 't',
        'Social-Media Officer' => 'smo',
        'Representative' => 'r'
    ];

    $abc = $abc_map[$rposition] ?? '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = basename($_FILES['image']['name']);
        $image_type = mime_content_type($image_tmp);
        $image_size = $_FILES['image']['size'];

        $allowed_types = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($image_type, $allowed_types)) {
            die("Error: Only JPG and PNG images are allowed.");
        }

        if ($image_size > 2 * 1024 * 1024) {
            die("Error: Image size exceeds 2MB.");
        }

        // Generate unique name and move to admin/upload
        $unique_name = time() . "_" . preg_replace("/[^a-zA-Z0-9.\-_]/", "", $image_name);
        $upload_dir = __DIR__ . "/upload/";
        $upload_path = $upload_dir . $unique_name;

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        if (!move_uploaded_file($image_tmp, $upload_path)) {
            die("Error: Failed to upload image to admin/upload.");
        }

        // ✅ Copy to election_officer/upload folder
        $officer_folder = __DIR__ . "/../election_officer/upload/";
        if (!is_dir($officer_folder)) {
            mkdir($officer_folder, 0777, true);
        }

        $officer_path = $officer_folder . $unique_name;

        if (!copy($upload_path, $officer_path)) {
            error_log("⚠️ Failed to copy image to election_officer/upload folder: $upload_path → $officer_path");
        } else {
            error_log("✅ Image copied successfully to election_officer/upload: $officer_path");
        }

        // Store relative path for database
        $db_photo_path = "upload/" . $unique_name;

    } else {
        die("Error: No image uploaded or upload failed.");
    }

    // Insert into candidate table
    $stmt = $conn->prepare("
        INSERT INTO candidate 
            (FirstName, LastName, Year, Position, Gender, MiddleName, Photo, Party, abc, Qualification)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param(
        "ssssssssss",
        $rfirstname, $rlastname, $ryear, $rposition,
        $rgender, $rmname, $db_photo_path, $party, $abc, $qualification
    );

    if ($stmt->execute()) {
        $fullname = "$rfirstname $rlastname";
        $action = "Added Candidate";

        $history_stmt = $conn->prepare("INSERT INTO history (data, action, date, user) VALUES (?, ?, NOW(), ?)");
        if ($history_stmt) {
            $history_stmt->bind_param("sss", $fullname, $action, $user_name);
            $history_stmt->execute();
            $history_stmt->close();
        }

        header("Location: candidate_list.php");
        exit();
    } else {
        die("Error: Could not save candidate. " . $stmt->error);
    }

    $stmt->close();
}
?>
