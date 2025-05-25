<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    header("Location: ../index.php");
    exit;
}

$user = $_SESSION['user'];
$currentPage = basename($_SERVER['PHP_SELF']);
?>

<nav>
    <div class="container-fluid">

    </div>
</nav>
<nav class="navbar navbar-dark bg-dark px-3">

    <div class="container-fluid">
        <nav style=" padding:8px; color:#fff;">
            <i id="menu-toggle" class="fas fa-bars" style="font-size:18px; cursor:pointer;margin-right:10px;"></i>
            Welcome <strong><?= htmlspecialchars($user['role']) ?></strong> -
            <strong><?= htmlspecialchars($user['username']) ?></strong>

        </nav>
        <a class="btn btn-outline-light" href="../logout.php">Logout</a>
    </div>

</nav>
<div class="logo" id="logo">
    <span class="navbar-brand mb-0 h1">Admin Panel</span>
</div>
<nav class="bg-dark text-white vh-100 p-3 main-menu" id="sidebar" style="width: 250px;">
    <ul class="nav flex-column ">
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>"
                href="dashboard.php">Dashboard</a>
        </li>
        <li class="nav-item">
            <a class="nav-link text-white <?php echo $currentPage == 'incidents.php' ? 'active' : ''; ?>"
                href="incidents.php">Incidents</a>
        </li>
    </ul>
</nav>



<script>
document.getElementById('menu-toggle').addEventListener('click', function() {
    document.body.classList.toggle('sidebar-open'); 
});
</script>