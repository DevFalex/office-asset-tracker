<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin'){ header("Location: login.php"); exit(); }
include "db.php";

if(isset($_POST['assign_asset'])){
    $asset_id = $_POST['asset_id'];
    $staff_id = $_POST['staff_id'];
    $assigned_date = $_POST['assigned_date'];

    $sql = "INSERT INTO asset_assignments (asset_id, staff_id, assigned_date) 
            VALUES ('$asset_id','$staff_id','$assigned_date')";
    if($conn->query($sql)){
        // update asset status
        $conn->query("UPDATE assets SET status='In Use' WHERE asset_id=$asset_id");
        echo "<script>alert('Asset assigned successfully'); window.location='assign.php';</script>";
    } else {
        echo "<script>alert('Error: ".$conn->error."');</script>";
    }
}

// Return Asset
if(isset($_GET['return'])){
    $id = $_GET['return'];
    $today = date("Y-m-d");

    $sql = "UPDATE asset_assignments SET return_date='$today' WHERE assignment_id=$id";
    if($conn->query($sql)){
        // get asset ID and update status back to Available
        $asset_id = $conn->query("SELECT asset_id FROM asset_assignments WHERE assignment_id=$id")->fetch_assoc()['asset_id'];
        $conn->query("UPDATE assets SET status='Available' WHERE asset_id=$asset_id");

        echo "<script>alert('Asset marked as returned'); window.location='assign.php';</script>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Asset Assignment - Office Asset Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-arrow-left-right text-warning"></i> Asset Assignment</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-house"></i> Dashboard</a>
    </div>

    <!-- Assign Asset Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-warning text-dark"><i class="bi bi-plus-circle"></i> Assign Asset</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <select name="asset_id" class="form-select" required>
                        <option value="">Select Asset</option>
                        <?php
                        $assets = $conn->query("SELECT * FROM assets WHERE status='Available'");
                        while($a = $assets->fetch_assoc()){
                            echo "<option value='{$a['asset_id']}'>{$a['asset_name']} ({$a['serial_number']})</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <select name="staff_id" class="form-select" required>
                        <option value="">Select Staff</option>
                        <?php
                        $staff = $conn->query("SELECT * FROM users WHERE role='Staff'");
                        while($s = $staff->fetch_assoc()){
                            echo "<option value='{$s['user_id']}'>{$s['full_name']} - {$s['department']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-2"><input type="date" name="assigned_date" value="<?php echo date('Y-m-d'); ?>" class="form-control"></div>
                <div class="col-md-2"><button type="submit" name="assign_asset" class="btn btn-warning w-100"><i class="bi bi-save"></i> Assign</button></div>
            </form>
        </div>
    </div>

    <!-- Current Assignments -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white"><i class="bi bi-list-task"></i> Current Assignments</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Asset</th><th>Staff</th><th>Assigned Date</th><th>Return Date</th><th>Action</th></tr>
                </thead>
                <tbody>
                    <?php
                    $sql = "SELECT aa.assignment_id, a.asset_name, a.serial_number, u.full_name, u.department, aa.assigned_date, aa.return_date
                            FROM asset_assignments aa
                            JOIN assets a ON aa.asset_id=a.asset_id
                            JOIN users u ON aa.staff_id=u.user_id
                            ORDER BY aa.assignment_id DESC";
                    $result = $conn->query($sql);
                    while($row = $result->fetch_assoc()){
                        echo "<tr>
                                <td>{$row['assignment_id']}</td>
                                <td>{$row['asset_name']} ({$row['serial_number']})</td>
                                <td>{$row['full_name']} - {$row['department']}</td>
                                <td>{$row['assigned_date']}</td>
                                <td>".($row['return_date'] ? $row['return_date'] : "<span class='badge bg-danger'>Not Returned</span>")."</td>
                                <td>";
                        if(!$row['return_date']){
                            echo "<a href='?return={$row['assignment_id']}' class='btn btn-sm btn-success'><i class='bi bi-check2-circle'></i> Mark Returned</a>";
                        }
                        echo "</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
