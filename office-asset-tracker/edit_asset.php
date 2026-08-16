<?php
session_start();
if(!isset($_SESSION['user_id'])){
    header("Location: /login.php");
    exit();
}
include "db.php";

$id = $_GET['id'] ?? null;

if(isset($_POST['update_asset'])){
    $conn->query(
        "UPDATE assets SET asset_name = ?, asset_type = ?, serial_number = ?, purchase_date = ?, status = ?
         WHERE asset_id = ?",
        [$_POST['asset_name'], $_POST['asset_type'], $_POST['serial_number'],
         $_POST['purchase_date'], $_POST['status'], $id]
    );
    header("Location: /assets.php");
    exit();
}

$result = $conn->query("SELECT * FROM assets WHERE asset_id = ?", [$id]);
$asset = $result ? $result->fetch_assoc() : null;
if(!$asset){ header("Location: /assets.php"); exit(); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Asset</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Asset</h2>
    <form method="POST" class="row g-3">
        <div class="col-md-6">
            <input type="text" name="asset_name" value="<?php echo e($asset['asset_name']); ?>" class="form-control" required>
        </div>
        <div class="col-md-6">
            <input type="text" name="asset_type" value="<?php echo e($asset['asset_type']); ?>" class="form-control">
        </div>
        <div class="col-md-6">
            <input type="text" name="serial_number" value="<?php echo e($asset['serial_number']); ?>" class="form-control">
        </div>
        <div class="col-md-6">
            <input type="date" name="purchase_date" value="<?php echo e($asset['purchase_date']); ?>" class="form-control">
        </div>
        <div class="col-md-6">
            <select name="status" class="form-control">
                <option <?php if($asset['status']=="Available") echo "selected"; ?>>Available</option>
                <option <?php if($asset['status']=="In Use") echo "selected"; ?>>In Use</option>
                <option <?php if($asset['status']=="Under Repair") echo "selected"; ?>>Under Repair</option>
                <option <?php if($asset['status']=="Disposed") echo "selected"; ?>>Disposed</option>
            </select>
        </div>
        <div class="col-md-6">
            <button type="submit" name="update_asset" class="btn btn-success w-100">Update</button>
        </div>
    </form>
</div>
<footer class="text-center text-muted py-3 mt-4"><small>Built by DevFalex.</small></footer>
</body>
</html>
