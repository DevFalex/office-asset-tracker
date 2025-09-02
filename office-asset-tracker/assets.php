<?php
session_start();
if(!isset($_SESSION['user_id'])){ header("Location: login.php"); exit(); }
include "db.php";

if(isset($_POST['add_asset'])){
    $name = $_POST['asset_name'];
    $type = $_POST['asset_type'];
    $serial = $_POST['serial_number'];
    $purchase_date = $_POST['purchase_date'];
    $status = "Available";

    $sql = "INSERT INTO assets (asset_name, asset_type, serial_number, purchase_date, status) 
            VALUES ('$name','$type','$serial','$purchase_date','$status')";
    if($conn->query($sql)){
        echo "<script>alert('Asset added successfully'); window.location='assets.php';</script>";
    } else {
        echo "<script>alert('Error: ".$conn->error."');</script>";
    }
}

// Delete Asset
if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM assets WHERE asset_id=$id");
    echo "<script>alert('Asset deleted successfully'); window.location='assets.php';</script>";
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Assets - Office Asset Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-box-seam text-primary"></i> Manage Assets</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-house"></i> Dashboard</a>
    </div>

    <!-- Add Asset Form -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white">
            <i class="bi bi-plus-circle"></i> Add New Asset
        </div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-3"><input type="text" name="asset_name" class="form-control" placeholder="Asset Name" required></div>
                <div class="col-md-2"><input type="text" name="asset_type" class="form-control" placeholder="Type"></div>
                <div class="col-md-3"><input type="text" name="serial_number" class="form-control" placeholder="Serial Number"></div>
                <div class="col-md-2"><input type="date" name="purchase_date" class="form-control"></div>
                <div class="col-md-2"><button type="submit" name="add_asset" class="btn btn-primary w-100"><i class="bi bi-save"></i> Save</button></div>
            </form>
        </div>
    </div>

    <!-- Asset List -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="bi bi-list-ul"></i> Asset Inventory
        </div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th><th>Name</th><th>Type</th><th>Serial</th><th>Purchase Date</th><th>Status</th><th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM assets ORDER BY asset_id DESC");
                    while($row = $result->fetch_assoc()){
                        echo "<tr>
                                <td>{$row['asset_id']}</td>
                                <td>{$row['asset_name']}</td>
                                <td>{$row['asset_type']}</td>
                                <td>{$row['serial_number']}</td>
                                <td>{$row['purchase_date']}</td>
                                <td><span class='badge bg-".
                                    ($row['status']=="Available" ? "success" : 
                                    ($row['status']=="In Use" ? "primary" : 
                                    ($row['status']=="Under Repair" ? "warning text-dark" : "danger")))."'>
                                    {$row['status']}
                                    </span></td>
                                <td>
                                    <a href='edit_asset.php?id={$row['asset_id']}' class='btn btn-sm btn-warning'><i class='bi bi-pencil'></i></a>
                                    <a href='?delete={$row['asset_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Delete this asset?')\"><i class='bi bi-trash'></i></a>
                                </td>
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
