<?php
include('session.php');
include('dbcon.php');

if (isset($_POST['reset'])) {

    // Authorize only admin
    if (!isset($_SESSION['User_Type']) || strtolower($_SESSION['User_Type']) != 'admin') {
        die("Unauthorized: Only admin can reset data.");
    }

    // Database credentials
    $dbUser = 'root';
    $dbPass = ''; // Set your password here if needed
    $dbName = 'oevs';

    // Sanitize custom filename
    $customName = isset($_POST['custom_name']) ? preg_replace('/[^a-zA-Z0-9_\-]/', '_', $_POST['custom_name']) : 'oevs_backup';
    $timestamp = date('Y-m-d_H-i-s');

    // Backup folder inside admin folder (adjust if your script location differs)
    $backupDir = __DIR__ . DIRECTORY_SEPARATOR . 'backup';
    if (!is_dir($backupDir)) {
        mkdir($backupDir, 0777, true);
    }

    $backupFile = $backupDir . DIRECTORY_SEPARATOR . "{$customName}_{$timestamp}.sql";

    // Set mysqldump path for XAMPP on Windows — use full path with quotes
    $mysqldumpPath = '"C:\\xampp\\mysql\\bin\\mysqldump.exe"';

    // Tables to back up
    $tables = ['candidate', 'complaint', 'history', 'voters', 'votes', 'users'];

    // Build the command
    $command = $mysqldumpPath . " -u $dbUser";
    if ($dbPass !== '') {
        $command .= " -p\"$dbPass\"";
    }
    $command .= " $dbName " . implode(' ', $tables);
    $command .= " > \"$backupFile\" 2>&1"; // redirect stderr for debugging

    // Execute and capture output and return code
    exec($command, $output, $return_var);

    if ($return_var !== 0) {
        echo "<pre>Backup command failed with status $return_var\nCommand:\n$command\nOutput:\n" . implode("\n", $output) . "</pre>";
        exit("❌ Backup failed. Reset aborted.");
    }

    // Backup successful, proceed with reset
    $conn->query("SET FOREIGN_KEY_CHECKS=0");
    foreach (['candidate', 'complaint', 'history', 'voters', 'votes'] as $table) {
        $conn->query("TRUNCATE TABLE `$table`");
    }
    $conn->query("DELETE FROM users WHERE Position != 'Admin'");
    $conn->query("SET FOREIGN_KEY_CHECKS=1");

    header("Location: home.php?reset=success");
    exit;
}
