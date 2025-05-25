<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    //header('Location:../index.php');
    //  exit();
}
$error = '';
include('../connect.php');
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
                User Management
            </div>

        </div>
    </div>
    <?php include ('../footer.php'); ?>

   
</body>

</html>