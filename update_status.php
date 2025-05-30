<?php
include 'connect.php'; 
session_start();

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $incident_id = intval($_POST['incident_id']);
    $status = mysqli_real_escape_string($conn, $_POST['status']);

    if ($incident_id && $status) {
        $update = "UPDATE incidents SET status = '$status' WHERE id = $incident_id";

        if (mysqli_query($conn, $update)) {
            $message = "Incident #$incident_id status updated to $status";

            $roles = ['user', 'admin', 'super_admin'];
            foreach ($roles as $role) {
                $safe_role = mysqli_real_escape_string($conn, $role);
                $safe_message = mysqli_real_escape_string($conn, $message);
                $query = "INSERT INTO notifications (user_role, message) VALUES ('$safe_role', '$safe_message')";
                mysqli_query($conn, $query);
            }
            $lastId = $conn->insert_id;
                        echo json_encode([
                "success"  => true,
                "lastId"   => $lastId,
                "new_status" => $status,
                "message" => $message
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "error" => mysqli_error($conn)
            ]);
        }
    } else {
        echo json_encode([
            "success" => false,
            "error" => "Invalid data"
        ]);
    }
}
?>