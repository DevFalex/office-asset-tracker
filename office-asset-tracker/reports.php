<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin'){ header("Location: login.php"); exit(); }
include "db.php";
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reports - Office Asset Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-bar-chart-line-fill text-danger"></i> Reports & Analytics</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-house"></i> Dashboard</a>
    </div>

    <!-- Assets by Status -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white"><i class="bi bi-check-circle"></i> Assets by Status</div>
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark"><tr><th>Status</th><th>Total</th></tr></thead>
                <tbody>
                    <?php
                    $statuses = ["Available","In Use","Under Repair","Disposed"];
                    foreach($statuses as $status){
                        $count = $conn->query("SELECT COUNT(*) as total FROM assets WHERE status='$status'")->fetch_assoc()['total'];
                        echo "<tr><td>$status</td><td><span class='badge bg-dark'>$count</span></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assets by Department -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white"><i class="bi bi-building"></i> Assets by Department</div>
        <div class="card-body">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark"><tr><th>Department</th><th>Total Assigned</th></tr></thead>
                <tbody>
                    <?php
                    $sql = "SELECT u.department, COUNT(*) as total
                            FROM asset_assignments aa
                            JOIN users u ON aa.staff_id = u.user_id
                            WHERE aa.return_date IS NULL
                            GROUP BY u.department";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()){
                        echo "<tr><td>{$row['department']}</td><td><span class='badge bg-primary'>{$row['total']}</span></td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Assignment History -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white"><i class="bi bi-clock-history"></i> Assignment History</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark"><tr><th>Asset</th><th>Staff</th><th>Assigned</th><th>Returned</th></tr></thead>
                <tbody>
                    <?php
                    $sql = "SELECT a.asset_name, a.serial_number, u.full_name, aa.assigned_date, aa.return_date
                            FROM asset_assignments aa
                            JOIN assets a ON aa.asset_id=a.asset_id
                            JOIN users u ON aa.staff_id=u.user_id
                            ORDER BY aa.assigned_date DESC";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()){
                        echo "<tr>
                                <td>{$row['asset_name']} ({$row['serial_number']})</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['assigned_date']}</td>
                                <td>".($row['return_date'] ? $row['return_date'] : "<span class='badge bg-danger'>Not Returned</span>")."</td>
                              </tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
