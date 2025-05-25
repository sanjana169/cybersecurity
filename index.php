<?php
session_start();
$error = '';
include('connect.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, trim($_POST['username']));
    $password = $_POST['password'];
    $role = $_POST['role'];

    switch ($role) {
        case 'Super Admin':
            $table = 'super_admins';
            $redirect = 'superadmin/dashboard.php';
            break;
        case 'Admin':
            $table = 'admin';
            $redirect = 'admin/dashboard.php';
            break;
        case 'User':
            $table = 'users';
            $redirect = 'users/users.php';
            break;
        default:
            $error = "Invalid role selected.";
            $table = null;
    }

    if ($table) {
        $sql = "SELECT * FROM `$table` WHERE username = '$username' LIMIT 1";
        $result = mysqli_query($conn, $sql);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                if ($user['status'] === '1') {
                    $_SESSION['user'] = $user;
                    $_SESSION['role'] = $role;
                    header("Location: $redirect");
                    exit();
                } else {
                    $error = "Account is blocked.";
                }
            } else {
                $error = "Invalid password.";
            }
        } else {
            $error = "User not found.";
        }
    }

    mysqli_close($conn);
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>CyberSecurity | Login</title>
    <style>
        body { font-family: Arial; background: #f0f0f0; }
        body {
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .login-box {
            background: white;
            padding: 20px;
            margin: 100px auto;
            max-width: 300px;
            box-shadow: 0 0 10px #aaa;
        }
         .login-box {
            background: #ffffff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
            width: 350px;
            text-align: center;
        }

        .login-box h2 {
            margin-bottom: 25px;
            color: #333;
        }

        label {
            float: left;
            margin: 10px 0 5px 2px;
            font-weight: 600;
            color: #555;
        }

        input[type="text"], input[type="password"], select {
            width: 100%;
            padding: 10px 12px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
        }

        input:focus, select:focus {
            border-color: #2980b9;
            outline: none;
        }

        button {
            background: #2980b9;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        button:hover {
            background: #1c5980;
        }

        p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
   <div class="login-box">
    <h2>Login</h2>
    <?php if (!empty($error)): ?>
        <p style="color:red;"><?= $error ?></p>
    <?php endif; ?>
    <form method="POST">
        <label>Username</label>
        <input type="text" name="username" required>

        <label>Password</label>
        <input type="password" name="password" required>

        <label>Role</label>
        <select name="role" required>
            <option value="">Select Role</option>
            <option value="Super Admin">Super Admin</option>
            <option value="Admin">Admin</option>
            <option value="User">User</option>
        </select>

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
