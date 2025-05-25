<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    //header('Location:../index.php');
    //  exit();
}
$error = '';
include('../connect.php');

function getUserCount() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) FROM users");
    return mysqli_fetch_row($result)[0];
}

function getBlockedUsers() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE status = 'blocked'");
    return mysqli_fetch_row($result)[0];
}

function getActiveIncidents() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) FROM incidents WHERE status != 'closed'");
    return mysqli_fetch_row($result)[0];
}

function getAdminCount() {
    global $conn;
    $result = mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role = 'admin'");
    return mysqli_fetch_row($result)[0];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Super Admin - User Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../style.css">
    <style>
    body {
        background-color: #f8f9fa;
    }

    .navbar {
        margin-bottom: 20px;
    }

    .card {
        border-radius: 16px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }
    </style>
</head>

<body>

    <?php include('sidebar.php'); ?>

    <div class="mainpanel " id="dashboard">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <h4>Dashboard</h4>
            </div>
            <div class="container-fluid px-4 mt-4">
                <h2 class="mb-4">Super Admin Dashboard</h2>

                <!-- Stats Overview -->
                <div class="row g-4 mb-4">
                    <div class="col-md-3">
                        <div class="card text-white bg-primary">
                            <div class="card-body">
                                <h5>Total Users</h5>
                                <h3><?php echo getUserCount(); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-success">
                            <div class="card-body">
                                <h5>Active Incidents</h5>
                                <h3><?php echo getActiveIncidents(); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-warning">
                            <div class="card-body">
                                <h5>Blocked Users</h5>
                                <h3><?php echo getBlockedUsers(); ?></h3>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card text-white bg-danger">
                            <div class="card-body">
                                <h5>Admins</h5>
                                <h3><?php echo getAdminCount(); ?></h3>
                            </div>
                        </div>
                    </div>
                </div>

                

                <!-- Latest Incidents Table -->
                <div class="card mb-4">
                    <div class="card-header bg-dark text-white">
                        <h5>Recent Incidents</h5>
                    </div>
                    <div class="card-body table-responsive">
                        <table id="recentIncidents" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Title</th>
                                    <th>Category</th>
                                    <th>Priority</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Assigned To</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                               $sql = "SELECT incidents.id,
               incidents.title,
               incidents.category,
               incidents.priority,
               incidents.incident_date,
               incidents.status,
               admin.username AS assigned_to
        FROM incidents
        LEFT JOIN `admin` ON incidents.assigned_admin_id = admin.id
        ORDER BY incidents.incident_date DESC";
$result = $conn->query($sql);
                                ?>
                                <?php if ($result->num_rows > 0): ?>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['category']) ?></td>
                    <td><?= htmlspecialchars($row['priority']) ?></td>
                    <td><?= htmlspecialchars($row['incident_date']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['assigned_to']) ?: 'Unassigned' ?></td>
                   
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="7" class="text-center">No incidents found.</td></tr>
        <?php endif; ?>
                               
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


        </div>
    </div>
    <?php include ('../footer.php'); ?>


</body>

</html>