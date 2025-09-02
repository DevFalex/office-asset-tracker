<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Staff'){
    header("Location: login.php");
    exit();
}
include "db.php";
$staff_id = $_SESSION['user_id'];
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Dashboard - Office Asset Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand fw-bold" href="#">Office Asset Tracker</a>
    <div class="collapse navbar-collapse">
      <ul class="navbar-nav ms-auto">
        <li class="nav-item">
            <span class="nav-link text-white">
                Welcome, <?php echo $_SESSION['full_name']; ?> (Staff)
            </span>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container my-5">
    <h2 class="mb-4 text-center"><i class="bi bi-laptop"></i> My Assigned Assets</h2>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-list-check"></i> Current Assignments
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Asset</th>
                        <th>Serial Number</th>
                        <th>Assigned Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT a.asset_name, a.serial_number, aa.assigned_date, a.status 
                            FROM asset_assignments aa
                            JOIN assets a ON aa.asset_id=a.asset_id
                            WHERE aa.staff_id=$staff_id AND aa.return_date IS NULL";
                    $result = $conn->query($sql);
                    if($result->num_rows > 0){
                        while($row = $result->fetch_assoc()){
                            echo "<tr>
                                    <td>{$row['asset_name']}</td>
                                    <td>{$row['serial_number']}</td>
                                    <td>{$row['assigned_date']}</td>
                                    <td><span class='badge bg-".
                                        ($row['status']=="Available" ? "success" : 
                                        ($row['status']=="In Use" ? "primary" : 
                                        ($row['status']=="Under Repair" ? "warning text-dark" : "danger")))."'>
                                        {$row['status']}
                                    </span></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='4' class='text-center text-muted'>No assets assigned yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
