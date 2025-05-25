<?php
include('../connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_admin'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $status = (int)$_POST['status'];

    $sql = "INSERT INTO admin (username, email, password, role, status) 
            VALUES ('$username', '$email', '$password', '$role', '$status')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('Admin added successfully'); window.location.href='admin.php';</script>";
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
$roles = ['Super Admin', 'Admin', 'User'];
$editUser = null;
if (isset($_GET['edit_id'])) {
    $edit_id = intval($_GET['edit_id']);
    $sql = "SELECT * FROM admin WHERE id = $edit_id";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        $editUser = $result->fetch_assoc();
    }
}
$roles = [];
$result = mysqli_query($conn, "SELECT * FROM roles");

if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $roles[] = $row;
    }
}
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_user'])) {
    $id       = intval($_POST['id']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $role     = mysqli_real_escape_string($conn, $_POST['role']);  
    $status   = intval($_POST['status']);

    
    $sql = "UPDATE 'admin' SET username = '$username', role = '$role', status = $status WHERE id = $id";

    if ($conn->query($sql)) {
        $success = "admin updated successfully.";
        header("Location: admin.php"); 
        exit;
    } else {
        $error = "Error updating user: " . $conn->error;
    }
}


//Delete user code
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    
    $sql = "DELETE FROM admin WHERE id = $delete_id";
    if ($conn->query($sql)) {
        
        header("Location: admin.php");
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
                    <div><button type="button" class="btn btn-success w-100" data-bs-toggle="modal"
                            data-bs-target="#addUserModal">Add Admin</button>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="users" class="display table table-striped table-bordered table-hover" style="width:100%">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Username</th>
                                <th>Role</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT * FROM admin WHERE role ='Admin'";
                            $result = $conn->query($sql);
                            if ($result->num_rows > 0): 
                            while($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo($row['id']) ?></td>
                                <td><?php echo ($row['username']) ?></td>
                                <td><?php  echo $row['role']; ?></td>
                                <td><?php echo $row['status'];?></td>
                                <td>
                                    <a href="admin.php?edit_id=<?= $row['id'] ?>"
                                        class="btn btn-sm btn-primary">Edit</a>
                                    <a href="admin.php?delete_id=<?= $row['id'] ?>" class="btn btn-sm btn-danger"
                                        onclick="return confirm('Are you sure you want to delete this user?')">Delete</a>
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
                    <form class="mb-4" method="POST" action="admin.php">
                        <input type="hidden" name="add_admin" value="1">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Enter username" required>
                            </div>

                            <div class="col-md-12">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                    placeholder="Enter email" required>
                            </div>

                            <div class="col-md-12">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password"
                                    placeholder="Enter password" required>
                            </div>

                            <div class="col-md-12">
                                <label for="role" class="form-label">Role</label>
                                <?php  $roles = mysqli_query($conn, "SELECT * FROM roles"); ?>
                                <select class="form-select" id="role" name="role" required>
                                    <option value="">Select role</option>
                                    <?php while($role = mysqli_fetch_assoc($roles)) { ?>
                                    <option value="<?php echo $role['role_name']; ?>"><?php echo $role['role_name']; ?>
                                    </option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="1">Active</option>
                                    <option value="0">Blocked</option>
                                </select>
                            </div>

                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-success w-100">Add User</button>
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
            <form method="post" action="admin.php">
                <input type="hidden" name="update_user" value="1">
                <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit User</h5>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" class="form-control"
                                value="<?php echo ($editUser['username']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Role</label>
                            <select name="role" class="form-control" required>
                                <option value="">Select a role</option>
                                <?php foreach ($roles as $role): ?>
                                <option value="<?php echo $role['role_name']; ?>"
                                    <?php echo $editUser['id'] == $role['id'] ? 'selected' : '' ?>>
                                    <?php echo $role['role_name']; ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Status</label>
                            <select name="status" class="form-control" required>
                                <option value="1" <?php echo $editUser['status'] == 1 ? 'selected' : '' ?>>Active
                                </option>
                                <option value="0" <?php echo $editUser['status'] == 0 ? 'selected' : '' ?>>Blocked
                                </option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                        <a href="admin.php" class="btn btn-secondary">Cancel</a>
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