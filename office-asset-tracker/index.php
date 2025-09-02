<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}
include "db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Office Asset Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="index.php">Office Asset Tracker - 249074041 </a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item"><span class="nav-link text-white">Welcome, <?php echo $_SESSION['full_name']; ?> (<?php echo $_SESSION['role']; ?>)</span></li>
        <li class="nav-item"><a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4 text-center">Dashboard</h2>
    <div class="row g-4">
        <?php
        $statuses = ["Available","In Use","Under Repair","Disposed"];
        $icons = ["bi-check-circle-fill text-success", "bi-people-fill text-primary", "bi-tools text-warning", "bi-trash-fill text-danger"];
        $i=0;
        foreach($statuses as $status){
            $count = $conn->query("SELECT COUNT(*) as total FROM assets WHERE status='$status'")->fetch_assoc()['total'];
            echo "
            <div class='col-md-3'>
                <div class='card shadow-sm h-100 text-center'>
                    <div class='card-body'>
                        <i class='bi {$icons[$i]} display-5'></i>
                        <h5 class='mt-2'>$status</h5>
                        <p class='display-6 fw-bold'>$count</p>
                    </div>
                </div>
            </div>";
            $i++;
        }
        ?>
    </div>

    <hr class="my-5">

    <!-- Quick Links -->
    <div class="row g-4">
        <div class="col-md-3">
            <a href="assets.php" class="text-decoration-none">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-box-seam display-5 text-primary"></i>
                        <h5 class="mt-2">Manage Assets</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="staff.php" class="text-decoration-none">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-people display-5 text-success"></i>
                        <h5 class="mt-2">Manage Staff</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="assign.php" class="text-decoration-none">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-arrow-left-right display-5 text-warning"></i>
                        <h5 class="mt-2">Assign Assets</h5>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-3">
            <a href="reports.php" class="text-decoration-none">
                <div class="card text-center shadow-sm h-100">
                    <div class="card-body">
                        <i class="bi bi-bar-chart-line-fill display-5 text-danger"></i>
                        <h5 class="mt-2">Reports</h5>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>

</body>
</html>
