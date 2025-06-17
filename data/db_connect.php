<?php
try {
    $pdo = new PDO("sqlite:" . __DIR__ . "/orange_line_project.db");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->exec("PRAGMA foreign_keys = ON");

    return $pdo; // ✅ required for stations.php to receive $pdo
} catch (PDOException $e) {
    // If the DB connection fails, this stops everything and shows the message
    die("Database connection failed: " . $e->getMessage());
}
