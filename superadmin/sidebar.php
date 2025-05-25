
  <?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user'])) {
    // Redirect to login or show guest navbar
    header("Location: ../index.php");
    exit;
}

$user = $_SESSION['user'];
$currentPage = basename($_SERVER['PHP_SELF']);
?>


<nav class="navbar navbar-dark bg-dark px-3">

    <div class="container-fluid">
        <nav style=" padding:10px; color:#fff;">
          <i id="menu-toggle" class="fas fa-bars" style="font-size:18px; cursor:pointer;margin-right:10px;"></i>
            Welcome -
            <strong><?= htmlspecialchars($user['username']) ?></strong>

        </nav>
        <a class="btn btn-outline-light" href="../logout.php">Logout</a>
    </div>

</nav>
<div class="logo">
    <span class="navbar-brand mb-0 h1">Super Admin Panel</span>
</div>
<nav class="bg-dark text-white vh-100 p-3 main-menu" style="width: 250px;">
    <ul class="nav flex-column ">
        <li class="nav-item">
        <a class="nav-link text-white <?php echo $currentPage == 'dashboard.php' ? 'active' : ''; ?>" href="dashboard.php">Dashboard</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?php echo $currentPage == 'users.php' ? 'active' : ''; ?>" href="users.php">Manage Users</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?php echo $currentPage == 'admin.php' ? 'active' : ''; ?>" href="admin.php">Manage Admin</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?php echo $currentPage == 'role.php' ? 'active' : ''; ?>" href="role.php">Manage Role</a>
      </li>
      <li class="nav-item">
        <a class="nav-link text-white <?php echo $currentPage == 'incidents.php' ? 'active' : ''; ?>" href="incidents.php">Manage Incidents</a>
      </li>
    </ul>
  </nav>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>
  <script>
document.getElementById('menu-toggle').addEventListener('click', function() {
    document.body.classList.toggle('sidebar-open'); // add/remove class on body
});
</script> 