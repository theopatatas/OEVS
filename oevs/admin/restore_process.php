<?php
include('session.php');

// Allow only admin
if (!isset($_SESSION['User_Type']) || strtolower($_SESSION['User_Type']) != 'admin') {
    die("Unauthorized: Only admin can restore data.");
}

// Check if restore request is valid
if (isset($_POST['restore']) && !empty($_POST['backup_file'])) {
    // Get and sanitize filename
    $backupFile = 'backup/' . basename($_POST['backup_file']);

    if (!file_exists($backupFile)) {
        exit("❌ Backup file does not exist.");
    }

    // DB credentials
    $dbUser = 'root';
    $dbPass = ''; // Set your MySQL password if applicable
    $dbName = 'oevs';

    // Set mysql path (adjust for XAMPP or OS)
    $mysqlPath = '"C:\\xampp\\mysql\\bin\\mysql.exe"'; // Windows
    // $mysqlPath = 'mysql'; // Use this for Linux if mysql is in PATH

    // Build command
    $command = "$mysqlPath -u $dbUser";
    if ($dbPass !== '') {
        $command .= " -p\"$dbPass\"";
    }
    $command .= " $dbName < \"$backupFile\"";

    // Execute using shell with redirection
    $fullCommand = "cmd /c \"$command\""; // for Windows
    // $fullCommand = "$command"; // for Linux/macOS

    exec($fullCommand, $output, $result);

    if ($result === 0) {
        // ✅ Redirect on success
        header("Location: home.php?restore=success");
        exit;
    } else {
        echo "<pre>❌ Restore failed.\nCommand:\n$fullCommand\nOutput:\n" . implode("\n", $output) . "</pre>";
    }
} else {
    echo "⚠️ No backup file selected.";
}
?>
