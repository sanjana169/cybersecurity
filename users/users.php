<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}
$role = $_SESSION['role'] ?? 'user';

$user = $_SESSION['user'];
include('../connect.php');
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cybersecurity Incident</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="default.css">

</head>

<body>
    <div class="logo">
        <span class="navbar-brand mb-0 h1">User Panel</span>
    </div>
    <nav class="navbar navbar-dark bg-dark px-3">
        <div class="container-fluid">
            <nav style=" padding:10px; color:#fff;">
                Welcome <strong><?php echo $user['role']; ?></strong> -
                <strong><?php echo $user['username']; ?></strong>
            </nav>
            <a class="btn btn-outline-light" href="../logout.php">Logout</a>
        </div>
    </nav>
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>
    <div class="container main-panel" id="manageUsers">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <div class="card-title d-flex m-0 justify-content-between">
                    <h4>Incidents</h4>
                    <div><button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#incidentModal"><i class="bi bi-plus-circle me-2"></i>Add Incident</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="filterCategory" class="form-label">Filter by Category</label>
                        <select id="filterCategory" class="form-select">
                            <option value="">All Categories</option>
                            <option value="Phishing">Phishing</option>
                            <option value="Malware">Malware</option>
                            <option value="Unauthorized Access">Unauthorized Access</option>
                            <option value="Data Breach">Data Breach</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterStatus" class="form-label">Filter by Status</label>
                        <select id="filterStatus" class="form-select">
                            <option value="">All Status</option>
                            <option value="Pending">Pending</option>
                            <option value="Open">Open</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Closed">Closed</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filterDate" class="form-label">Filter by Date</label>
                        <input type="date" id="filterDate" class="form-control" />
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button id="resetFilters" class="btn btn-secondary">Reset Filters</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table id="incidentsTable" class="display table table-striped table-bordered table-hover"
                        style="width:100%">
                        <thead>
                            <tr>
                                <th>Id</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Incident_date</th>
                                <th>Status</th>
                                <th>Evidence</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM incidents ORDER BY id DESC";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0){
                                while ($row = mysqli_fetch_assoc($result)){ ?>
                            <tr data-incident-id="<?php echo $row['id']; ?>">
                                <td onclick="window.location.href='view_incident.php?id=<?php echo $row['id']; ?>'">
                                    <?php echo $row['id']; ?></td>
                                <td onclick="window.location.href='view_incident.php?id=<?php echo $row['id']; ?>'">
                                    <?php echo$row['title']; ?></td>
                                <td onclick="window.location.href='view_incident.php?id=<?php echo $row['id']; ?>'">
                                    <?php echo $row['description']; ?></td>
                                <td onclick="window.location.href='view_incident.php?id=<?php echo $row['id']; ?>'">
                                    <?php echo $row['category']; ?></td>
                                <td onclick="window.location.href='view_incident.php?id=<?php echo $row['id']; ?>'">
                                    <span
                                        class="badge bg-<?php echo $row['priority'] === 'High' ? 'danger' : ($row['priority'] === 'Medium' ? 'warning text-dark' : 'success'); ?>">
                                        <?php echo $row['priority']; ?></span>
                                </td>
                                <td onclick="window.location.href='view_incident.php?id=<?php echo $row['id']; ?>'">
                                    <?php echo $row['incident_date']; ?></td>
                                <td onclick="window.location.href='view_incident.php?id=<?php echo $row['id']; ?>'"
                                    class="incident-status"><?php echo $row['status'];?></td>
                                <td>
                                    <?php
                                        $evidence_files = json_decode($row['evidence_files'], true);
                                        if ($evidence_files && is_array($evidence_files)) {
                                            foreach ($evidence_files as $file) {
                                                echo "<a href='$file' target='_blank'>View</a><br>";
                                            }
                                        } else {
                                            echo "No files";
                                        }
                                    ?>
                                </td>
                                <td onclick="window.location.href='view_incident.php?id=<?php echo $row['id']; ?>'">
                                    <?php echo date('Y-m-d', strtotime($row['created_at'])); ?></td>
                            </tr>
                            <?php
                                }
                             } else{ ?>
                            <tr>
                                <td colspan="8" class="text-center">No records found</td>
                            </tr>
                            <?php
                             }
                        ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- incident_modal -->
    <div class="modal fade" id="incidentModal" tabindex="-1" aria-labelledby="incidentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="incidentForm" method="POST" action="add_incident.php" enctype="multipart/form-data">
                    <div class="modal-header">
                        <h5 class="modal-title" id="incidentModalLabel">Report Cybersecurity Incident</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="title" class="form-label">Title *</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description *</label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                required></textarea>
                        </div>

                        <div class="mb-3">
                            <label for="category" class="form-label">Category *</label>
                            <select class="form-select" id="category" name="category" required>
                                <option value="">Select Category</option>
                                <option value="Phishing">Phishing</option>
                                <option value="Malware">Malware</option>
                                <option value="Unauthorized Access">Unauthorized Access</option>
                                <option value="Data Breach">Data Breach</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Priority *</label><br>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="priority" value="Low" required>
                                <label class="form-check-label">Low</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="priority" value="Medium" required>
                                <label class="form-check-label">Medium</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="priority" value="High" required>
                                <label class="form-check-label">High</label>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="date" class="form-label">Date *</label>
                            <input type="date" class="form-control" id="date" name="date" required>
                        </div>

                        <div class="mb-3">
                            <label for="evidence" class="form-label">Evidence Upload (Images/PDFs) *</label>
                            <input type="file" class="form-control" id="evidence" name="evidence[]"
                                accept=".jpg,.jpeg,.png,.pdf" multiple required>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Submit</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>


    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    (() => {
        const form = document.getElementById('incidentForm');

        form.addEventListener('submit', function(event) {
            const files = document.getElementById('evidence').files;
            let valid = true;

            for (let file of files) {
                if (file.size > 5 * 1024 * 1024) {
                    valid = false;
                    break;
                }
            }

            if (!form.checkValidity() || !valid) {
                event.preventDefault();
                event.stopPropagation();
                if (!valid) {
                    alert('Each file must be less than 5MB.');
                }
            }

            form.classList.add('was-validated');
        }, false);
    })();
    </script>
    <script>
    $(document).ready(function() {
        var table = $('#incidentsTable').DataTable({
            order: [
                [0, 'desc']
            ],
        });

        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
            var selectedCategory = $('#filterCategory').val() ? $('#filterCategory').val().toLowerCase()
                .trim() : '';
            var selectedStatus = $('#filterStatus').val() ? $('#filterStatus').val().toLowerCase()
                .trim() : '';
            var selectedDate = $('#filterDate').val() ? $('#filterDate').val().trim() : '';

            var category = data[3] ? data[3].toLowerCase().trim() : '';
            var status = data[6] ? data[6].toLowerCase().trim() : '';
            var incidentDate = data[5] ? data[5].trim() : '';

            if (selectedCategory && category !== selectedCategory) {
                return false;
            }

            if (selectedStatus && status !== selectedStatus) {
                return false;
            }

            if (selectedDate && incidentDate !== selectedDate) {
                return false;
            }

            return true;
        });

        $('#filterCategory, #filterStatus, #filterDate').on('change', function() {
            table.draw();
        });

        $('#resetFilters').on('click', function() {
            $('#filterCategory').val('');
            $('#filterStatus').val('');
            $('#filterDate').val('');
            table.draw();
        });
    });
    </script>
    <script>
    document.querySelectorAll('.assignBtn').forEach(button => {
        button.addEventListener('click', () => {
            const incidentId = button.dataset.incidentId;
            const statusSelect = document.querySelector(`#statusSelect-${incidentId}`);
            const newStatus = statusSelect.value;

            fetch('update_status.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: `incident_id=${incidentId}&status=${newStatus}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {

                        const row = document.querySelector(
                            `tr[data-incident-id="${data.incident_id}"]`);
                        if (row) {
                            const statusCell = row.querySelector(".incident-status");
                            if (statusCell) {
                                statusCell.textContent = data.new_status;
                            }
                        }
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: data.message,
                            showConfirmButton: false,
                            timer: 3000
                        });
                    } else {
                        alert("Update failed: " + (data.error || "Unknown error"));
                    }
                });
        });
    });
    </script>



    <script>
    const currentUserRole = "<?php echo $_SESSION['role'] ?? 'user'; ?>";
    let lastNotifId = 0;

    function showToast(msg) {
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
            .then(notifs => {
                if (notifs.length > 0) {
                    notifs.forEach(n => {

                        showToast(n.message);

                        const row = document.querySelector(`tr[data-incident-id="${n.incident_id}"]`);
                        if (row) {
                            const statusCell = row.querySelector(".incident-status");
                            if (statusCell) {
                                statusCell.textContent = n.status;
                            }
                        }

                    });

                    lastNotifId = notifs[notifs.length - 1].id;
                }
            })
            .catch(console.error);
    }

    pollNotifications();
    setInterval(pollNotifications, 5000);
    </script>



</body>

</html>