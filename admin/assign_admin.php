<?php
require '../connect.php'; // Adjust path if needed

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $incidentId = intval($_POST['incident_id']);
    $adminId = intval($_POST['admin_id']);

    $sql = "UPDATE incidents SET assigned_admin_id = $adminId WHERE id = $incidentId";

    if (mysqli_query($conn, $sql)) {
        echo "Incident assigned successfully.";
    } else {
        echo "Failed to assign incident: " . mysqli_error($conn);
    }

    mysqli_close($conn);
}
?>
