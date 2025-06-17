<?php
header('Content-Type: application/json');

try {
    $db = new PDO('sqlite:data/orange_line_project.db');
    $sql = "SELECT name FROM stations ORDER BY name ASC";
    $stmt = $db->query($sql);
    $stations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($stations);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
