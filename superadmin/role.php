<?php
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_role'])) {
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $check = mysqli_query($conn, "SELECT * FROM roles WHERE role_name = '$role'");
    
    if (mysqli_num_rows($check) > 0) {
        echo "<script>alert('Role already exists'); window.location.href='role.php';</script>";
        exit;
    }

    $sql = "INSERT INTO roles (role_name) VALUES ('$role')";
    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Role added successfully'); window.location.href='role.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

$roles = ['Super Admin', 'Admin', 'User'];
$editUser = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = "SELECT * FROM roles WHERE id = $edit_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $editUser = $result->fetch_assoc();
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $id       = intval($_POST['id']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);  

    $sql = "UPDATE roles SET role_name = '$role' WHERE id = $id";

    if ($conn->query($sql)) {
        header("Location: role.php"); 
        exit;
    } else {
        echo "Error updating role: " . $conn->error;
    }
}



//Delete user code
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    $sql = "DELETE FROM roles WHERE id = $delete_id";
    if ($conn->query($sql)) {
        
        header("Location: role.php");
        exit;
    } else {
        echo "Error deleting user: " . $conn->error;
    }
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

    <div class="mainpanel" id="manageUsers">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <div class="card-title d-flex m-0 justify-content-between">
                    <h4>Users</h4>
                    <div><button type="button" class="btn btn-success w-100" data-bs-toggle="modal"data-bs-target="#addUserModal">Add Role</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                <table id="users" class="display table table-striped table-bordered table-hover" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            
                            <th>Role</th>
                            
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $sql = "SELECT * FROM roles";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0): 
                            while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo($row['id']) ?></td>
                            
                            <td><?php  echo $row['role_name']; ?></td>
                           
                            <td>
                                <a href="role.php?edit_id=<?= $row['id'] ?>" class="btn btn-sm btn-primary">Edit</a>
                                <a href="role.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
                            </td>
                        </tr>
                        <?php 
                        endwhile; 
                        else: ?>
                        <tr>
                            <td colspan="5" class="text-center">No users found.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                        </div>
            </div>
        </div>
    </div>



    <div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Form Inside Modal -->
                    <form class="mb-4" method="POST" action="#">
                        <input type="hidden" name="add_role" value="1">
                        <div class="row g-3">
                            

                            <div class="col-md-12">
                                <label for="role" class="form-label">Role</label>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select role</option>
                                    <option value="Super Admin">Super Admin</option>
                                    <option value="Admin">Admin</option>
                                    <option value="User">User</option>
                                </select>
                            </div>

                            

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">Add Role</button>
                            </div>
                        </div>
                    </form>



                </div>
            </div>
        </div>
    </div>

    <?php if ($editUser): ?>
    <div class="modal show d-block" tabindex="-1" role="dialog" style="background:rgba(0,0,0,0.5);">
        <div class="modal-dialog" role="document">
            <form method="post" action="#">
                <input type="hidden" name="update_user" value="1">
                <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                    </div>
                    <div class="modal-body">
                        
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
                                <?php foreach ($roles as $roleName): ?>
                                <option value="<?= htmlspecialchars($roleName) ?>"
                                    <?= $editUser['role_name'] === $roleName ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($roleName) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                       
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="role.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <?php include ('../footer.php'); ?>

<script>
    $(document).ready(function() {
        var table = $('#users').DataTable({
            order: [
                [0, 'desc']
            ],
        });
    });
    </script>
</body>

</html>