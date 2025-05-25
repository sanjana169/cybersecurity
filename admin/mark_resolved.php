<?php
require '../connect.php'; 

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['ids']) || !is_array($_POST['ids'])) {
        echo json_encode(['message' => 'Invalid input.']);
        exit;
    }

    $ids = array_map('intval', $_POST['ids']);
    $idList = implode(',', $ids);

    $sql = "UPDATE incidents SET status = 'Resolved' WHERE id IN ($idList)";
    $result = mysqli_query($conn, $sql);

    if ($result) {
        echo json_encode(['message' => 'Selected incidents marked as Resolved.']);
    } else {
        http_response_code(500);
        echo json_encode(['message' => 'Database update failed.']);
    }
} else {
    http_response_code(405);
    echo json_encode(['message' => 'Method not allowed']);
}
?>
