<?php
// CORRECT path: remove duplicate "data/"
$DB_PATH = __DIR__ . DIRECTORY_SEPARATOR . "data" . DIRECTORY_SEPARATOR . "orange_line_project.db";

try {
    if (!file_exists($DB_PATH)) {
        throw new Exception("Database file not found at: $DB_PATH");
    }

    $conn = new PDO("sqlite:" . $DB_PATH);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    die("DB Connection failed: " . $e->getMessage());
}
?>
