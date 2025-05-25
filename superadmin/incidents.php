<?php

session_start();
$role = $_SESSION['role'] ?? 'super_admin';
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <link rel="stylesheet" href="../style.css">


</head>

<body>
    <?php include('sidebar.php'); ?>
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1100"></div>

    <div class="mainpanel " id="manageUsers">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <div class="card-title d-flex m-0 justify-content-between">
                    <h4>Incidents</h4>
                    <div id="actionContainer" style="display: none;">
    <button id="deleteSelectedBtn" class="btn btn-danger">
        Delete <span id="selectedCount">0</span> Selected
    </button>
</div>

                    <div>
                        <button onclick="exportTableToCSV('incidents.csv')" class="btn btn-success ">Export as
                            CSV</button>
                        <button onclick="exportTableToPDF()" class="btn btn-danger">Export as PDF</button>
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
                                <th><input type="checkbox" id="selectAll"></th>
                                <th>Id</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>Category</th>
                                <th>Priority</th>
                                <th>Incident date</th>
                                <th>Status</th>
                                <!--<th>Evidence_files</th>-->
                                <th>Date</th>
                                <!--<th>Assigned Admin</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM incidents ORDER BY id DESC";
                            $result = mysqli_query($conn, $sql);
                            if (mysqli_num_rows($result) > 0){
                                while ($row = mysqli_fetch_assoc($result)){ ?>
                            <tr>
                                <td><input type="checkbox" class="incidentCheckbox" value="<?php echo $row['id']; ?>">
                                </td>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo $row['title']; ?></td>
                                <td><?php echo $row['description']; ?></td>
                                <td><?php echo $row['category']; ?></td>
                                <td><span
                                        class="badge bg-<?php echo $row['priority'] === 'High' ? 'danger' : ($row['priority'] === 'Medium' ? 'warning text-dark' : 'success'); ?>">
                                        <?php echo $row['priority']; ?>
                                    </span>
                                </td>
                                <td><?php echo $row['incident_date']; ?></td>
                                <td>
                                    <select class="form-select statusDropdown" data-incident-id="<?= $row['id'] ?>">
                                        <option value="Pending" <?= $row['status'] == 'Pending' ? 'selected' : '' ?>>
                                            Pending</option>
                                        <option value="Open" <?= $row['status'] == 'Open' ? 'selected' : '' ?>>Open
                                        </option>
                                        <option value="In Progress"
                                            <?= $row['status'] == 'In Progress' ? 'selected' : '' ?>>In Progress
                                        </option>
                                        <option value="Resolved" <?= $row['status'] == 'Resolved' ? 'selected' : '' ?>>
                                            Resolved</option>

                                    </select>
                                </td>
                                <!--<td>
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
                                </td>-->
                                <td><?php echo date('d-m-Y', strtotime($row['created_at'])); ?></td>
                                <!-- <td>
                                    <?php
                                   
                                    $admins = mysqli_query($conn, "SELECT id, username FROM users WHERE role = 'admin'");
                                    ?>
                                    <select class="form-select assignAdmin" data-incident-id="<?= $row['id'] ?>"
                                        style="display:inline-block; width:auto; margin-right:10px;">
                                        <option value=""> Select Admin</option>
                                        <?php while ($admin = mysqli_fetch_assoc($admins)) { ?>
                                        <option value="<?= $admin['id'] ?>"
                                            <?= ($row['assigned_admin_id'] == $admin['id']) ? 'selected' : '' ?>>
                                            <?= htmlspecialchars($admin['username']) ?>
                                        </option>
                                        <?php } ?>
                                    </select>
                                    <button class="btn btn-sm btn-primary assignBtn"
                                        data-incident-id="<?php echo $row['id']; ?>"
                                        style="display:inline-block;padding: 2px 10px;margin-top: 5px;">
                                        Assign
                                    </button>
                                </td> -->
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

    <?php include ('../footer.php'); ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
    $(document).ready(function() {
        var table = $('#incidentsTable').DataTable({
            order: [
                [6, 'desc']
            ],
        });


        $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {

            var selectedCategory = $('#filterCategory').val() ? $('#filterCategory').val().toLowerCase()
                .trim() : '';
            var selectedStatus = $('#filterStatus').val() ? $('#filterStatus').val().toLowerCase()
                .trim() : '';
            var selectedDate = $('#filterDate').val() ? $('#filterDate').val().trim() : '';


            var category = data[4] ? data[4].toLowerCase().trim() : '';
            var status = data[7] ? data[7].toLowerCase().trim() : '';
            var incidentDate = data[6] ? data[6].trim() : '';


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
    $(document).ready(function() {

        $('#selectAll').on('change', function() {
            $('.incidentCheckbox').prop('checked', this.checked).trigger('change');
        });


        $(document).on('change', '.incidentCheckbox', function() {
            const selectedCount = $('.incidentCheckbox:checked').length;
            $('#selectedCount').text(selectedCount);

            if (selectedCount > 0) {
                $('#actionContainer').show();
            } else {
                $('#actionContainer').hide();
                $('#selectAll').prop('checked', false);
            }
        });

        $('#deleteSelectedBtn').click(function() {
    const selected = $('.incidentCheckbox:checked').map(function() {
        return this.value;
    }).get();

    if (selected.length === 0) {
        alert('Please select at least one incident.');
        return;
    }

    if (!confirm("Are you sure you want to delete " + selected.length + " incident(s)?")) return;

    $.ajax({
        url: 'delete_incidents.php',
        type: 'POST',
        data: {
            ids: selected
        },
        success: function(response) {
            alert(response.message);
            location.reload();
        },
        error: function(xhr) {
            alert("Something went wrong.");
            console.error(xhr.responseText);
        }
    });
});

    });
    </script>
    <script>
    function downloadCSV(csv, filename) {
        let csvFile = new Blob([csv], {
            type: "text/csv"
        });
        let downloadLink = document.createElement("a");
        downloadLink.download = filename;
        downloadLink.href = window.URL.createObjectURL(csvFile);
        downloadLink.style.display = "none";
        document.body.appendChild(downloadLink);
        downloadLink.click();
    }

    function exportTableToCSV(filename) {
        let csv = [];
        let rows = document.querySelectorAll("#incidentsTable tr");

        for (let i = 0; i < rows.length; i++) {
            let row = [],
                cols = rows[i].querySelectorAll("td, th");
            for (let j = 0; j < cols.length; j++) {
                if (j !== 0) {
                    row.push('"' + cols[j].innerText.replace(/"/g, '""') + '"');
                }
            }
            csv.push(row.join(","));
        }

        downloadCSV(csv.join("\n"), filename);
    }
    </script>
    <script>
    function exportTableToPDF() {
        const {
            jsPDF
        } = window.jspdf;
        const doc = new jsPDF('l', 'pt', 'a4');

        // Get headers
        const headers = [];
        document.querySelectorAll("#incidentsTable thead th").forEach((th, index) => {
            if (index !== 0) {
                headers.push(th.innerText);
            }
        });

        // Get rows
        const data = [];
        document.querySelectorAll("#incidentsTable tbody tr").forEach(row => {
            const rowData = [];
            row.querySelectorAll("td").forEach((td, index) => {
                if (index !== 0) {
                    rowData.push(td.innerText);
                }
            });
            data.push(rowData);
        });

        doc.autoTable({
            head: [headers],
            body: data,
            startY: 20,
            styles: {
                fontSize: 8,
            },
            headStyles: {
                fillColor: [22, 160, 133],
            },
        });

        doc.save('incidents.pdf');
    }
    </script>
    <script>
    $(document).ready(function() {
        $('.assignBtn').click(function() {
            const incidentId = $(this).data('incident-id');
            const adminId = $(`select[data-incident-id='${incidentId}']`).val();

            if (!adminId) {
                alert('Please select an admin.');
                return;
            }

            $.ajax({
                url: 'assign_admin.php',
                method: 'POST',
                data: {
                    incident_id: incidentId,
                    admin_id: adminId
                },
                success: function(response) {
                    alert(response);
                    location.reload();
                },
                error: function(xhr, status, error) {
                    alert('Error assigning incident.');
                }
            });
        });
    });
    </script>

    <script>
    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll(".statusDropdown").forEach(function(dropdown) {
            dropdown.addEventListener("change", function() {
                let incidentId = this.getAttribute("data-incident-id");
                let newStatus = this.value;

                fetch("../update_status.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "incident_id=" + incidentId + "&status=" + encodeURIComponent(
                            newStatus)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            showPopupNotification(data
                            .message);
                        } else {
                            alert("Error: " + data.error);
                        }
                    })
                    .catch(error => {
                        console.error("Error:", error);
                    });
            });
        });
    });
    </script>


    <script>
    function showPopupNotification(message) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'info',
            title: message,
            showConfirmButton: false,
            timer: 4000
        });
    }
    const currentUserRole = "<?php echo $role; ?>"; 

    function fetchNotifications() {
        fetch("../get_notification.php?role=" + currentUserRole)
            .then(response => response.json())
            .then(data => {
                if (data.length > 0) {
                    data.forEach(n => {
                        showPopupNotification(n.message);
                    });
                }
            })
            .catch(err => console.error("Fetch error:", err));
    }
    //setInterval(fetchNotifications, 10000); // every 10 seconds
    fetchNotifications(); // on page load
    </script>




</body>

</html>