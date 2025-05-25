<?php
include 'connect.php';
session_start();
header('Content-Type: application/json');

$role  = $_GET['role']  ?? '';
$after = intval($_GET['after'] ?? 0);          

$out = [];

if ($role) {
    $sql  = "SELECT id, message FROM notifications  WHERE user_role = ? AND id > ? ORDER BY id ASC";                
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $role, $after);
    $stmt->execute();
    $res  = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $out[] = $row;
    }

    
    if (!empty($out)) {
        $maxId = end($out)['id'];
        $mark  = $conn->prepare(
            "UPDATE notifications SET is_read = 1
             WHERE user_role = ? AND id <= ?"
        );
        $mark->bind_param("si", $role, $maxId);
        $mark->execute();
    }
}

echo json_encode($out);