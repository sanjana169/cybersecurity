<?php

ob_clean();
header('Content-Type: application/json');
include '../connect.php'; 

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ids = isset($_POST['ids']) ? $_POST['ids'] : [];

    if (!is_array($ids) || empty($ids)) {
        $response['message'] = 'No incidents selected.';
        echo json_encode($response);
        exit;
    }

    $ids = array_map('intval', $ids);
    $idList = implode(',', $ids);

    $sql = "DELETE FROM incidents WHERE id IN ($idList)";

    if (mysqli_query($conn, $sql)) {
        $response['message'] = 'Selected incidents deleted successfully.';
    } else {
        $response['message'] = 'Failed to delete incidents.';
    }

    echo json_encode($response);
    exit;
}
?>
