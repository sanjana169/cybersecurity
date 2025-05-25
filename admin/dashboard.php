<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}
$role = $_SESSION['role'] ?? 'admin';

$user = $_SESSION['user'];
include('../connect.php');

$totalIncidentsQuery = $conn->query("SELECT COUNT(*) AS total FROM incidents");
$totalIncidents = $totalIncidentsQuery->fetch_assoc()['total'];


$openQuery = $conn->query("SELECT COUNT(*) AS total FROM incidents WHERE status = 'Open'");
$resolvedQuery = $conn->query("SELECT COUNT(*) AS total FROM incidents WHERE status = 'Resolved'");
$openIncidents = $openQuery->fetch_assoc()['total'];
$resolvedIncidents = $resolvedQuery->fetch_assoc()['total'];

//category data
$categoryData = [];
$result = $conn->query("SELECT category, COUNT(*) as total FROM incidents GROUP BY category");

while ($row = $result->fetch_assoc()) {
    $categoryData[$row['category']] = $row['total'];
}

$labels = json_encode(array_keys($categoryData));
$data = json_encode(array_values($categoryData));

//resolved problem
$query = "
    SELECT AVG(TIMESTAMPDIFF(SECOND, created_at, resolved_at)) AS avg_resolution_seconds
    FROM incidents
    WHERE status = 'Resolved' AND resolved_at IS NOT NULL
";

$result = $conn->query($query);
$row = $result->fetch_assoc();

$avgSeconds = $row['avg_resolution_seconds'];

if ($avgSeconds) {
    $avgHours = floor($avgSeconds / 3600);
    $avgMinutes = floor(($avgSeconds % 3600) / 60);
    $avgTimeFormatted = "$avgHours hr $avgMinutes min";
} else {
    $avgTimeFormatted = "N/A";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Dashboard</title>
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

    body {
        font-family: Arial, sans-serif;
        background: #f5f5f5;
    }

    .dashboard {}

    .card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    h2 {
        margin-top: 0;
    }


    #statusChart {
        width: 100%;
        max-width: 300px;
        height: 250px;
        max-height: 300px;
        margin: auto;
    }

    #categoryChart {
        max-width: 700px;
        margin: auto;
    }

    .incident-report span {
        font-size: 22px;
        margin-bottom: 20px;
        display: block;
    }

    .incident-report h4 {
        font-size: 34px;
        font-weight: 600;
    }
    </style>
</head>

<body>

    <?php include('sidebar.php'); ?>

    <!-- User Management -->
    <div class="mainpanel " id="dashboard">

        <div class="card-header mb-3">
            <div class="card-title">
                <h4>Admin Incident Reporting Dashboard</h4>
            </div>
        </div>

        <div class="dashboard ">
            <div class="col-12 row">
                <div class="col-6">
                    <div class="card incident-report">
                        <div class="count text-center">
                            <span class="title">Total Incidents</span>
                            <h4 id="totalIncidents"><?php echo $totalIncidents; ?></h4>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card incident-report">
                        <div class="text-center">
                            <span>Average Resolution Time</span>
                            <h4><?php echo $avgTimeFormatted; ?></h4>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 row mt-2">
                <div class="col-6">
                    <div class="card incident-report">
                        <span>Open vs Resolved Incidents</span>
                        <canvas id="statusChart" width="300" height="300"></canvas>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card incident-report">
                        <span>Most Common Categories</span>
                        <canvas id="categoryChart" width="500" height="300"></canvas>
                    </div>
                </div>
            </div>


        </div>
    </div>

    <?php include ('../footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    const ctx = document.getElementById('statusChart').getContext('2d');

    const statusChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: ['Open', 'Resolved'],
            datasets: [{
                label: 'Incident Status',
                data: [<?php echo $openIncidents; ?>, <?php echo $resolvedIncidents; ?>],
                backgroundColor: ['#f39c12', '#27ae60'],
                borderColor: ['#ffffff', '#ffffff'],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                title: {
                    display: true,
                    text: 'Open vs Resolved Incidents'
                }
            }
        }
    });
    </script>
    <script>
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');

    const categoryChart = new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: <?= $labels ?>,
            datasets: [{
                label: 'Number of Incidents',
                data: <?= $data ?>,
                backgroundColor: '#3498db',
                borderColor: '#2980b9',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                title: {
                    display: true,
                    text: 'Most Common Categories'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
    </script>
    <script>
    const currentUserRole = "<?php echo $_SESSION['role'] ?? 'admin'; ?>";
    let lastNotifId = 0;

    function toast(msg) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: msg,
            showConfirmButton: false,
            timer: 4000
        });
    }

    function pollNotifications() {
        fetch(`../get_notification.php?role=${currentUserRole}&after=${lastNotifId}`)
            .then(r => r.json())
            .then(list => {
                if (list.length) {
                    list.forEach(n => toast(n.message));
                    lastNotifId = list[list.length - 1].id;
                }
            })
            .catch(console.error);
    }


    pollNotifications();
    setInterval(pollNotifications, 5000);
    </script>

</body>

</html>