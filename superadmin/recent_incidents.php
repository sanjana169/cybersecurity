<?php
include('../connect.php');

// FIX: Use the actual foreign key column name in your `incidents` table
$sql = "SELECT i.*, u.name AS assigned_to
        FROM incidents i
        LEFT JOIN users u ON i.admin_id = u.id
        ORDER BY i.created_at DESC
        LIMIT 10";

$result = mysqli_query($conn, $sql);
while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
        <td>{$row['title']}</td>
        <td>{$row['category']}</td>
        <td>{$row['priority']}</td>
        <td>{$row['created_at']}</td>
        <td>{$row['status']}</td>
        <td>" . ($row['assigned_to'] ?? 'Unassigned') . "</td>
        <td><a href='view_incident.php?id={$row['id']}' class='btn btn-sm btn-primary'>View</a></td>
    </tr>";
}
?>
