<?php

include('../connect.php');


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); 
    $role = (int)$_POST['role']; 
    $status = (int)$_POST['status']; 

    $sql = "INSERT INTO users (username, password, role, status) 
            VALUES ('$username', '$password', $role, $status)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('User added successfully'); window.location.href='users.php';</script>";
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}

mysqli_close($conn);
?>
