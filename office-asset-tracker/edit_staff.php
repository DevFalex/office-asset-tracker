<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin'){
    header("Location: /login.php");
    exit();
}
include "db.php";

$id = $_GET['id'] ?? null;

if(isset($_POST['update_staff'])){
    $conn->query(
        "UPDATE users SET full_name = ?, username = ?, department = ? WHERE user_id = ? AND role = 'Staff'",
        [$_POST['full_name'], $_POST['username'], $_POST['department'], $id]
    );
    header("Location: /staff.php");
    exit();
}

$result = $conn->query("SELECT * FROM users WHERE user_id = ? AND role = 'Staff'", [$id]);
$staff = $result ? $result->fetch_assoc() : null;
if(!$staff){ header("Location: /staff.php"); exit(); }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Staff</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <h2>Edit Staff</h2>
    <form method="POST" class="row g-3">
        <div class="col-md-4">
            <input type="text" name="full_name" value="<?php echo e($staff['full_name']); ?>" class="form-control" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="username" value="<?php echo e($staff['username']); ?>" class="form-control" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="department" value="<?php echo e($staff['department']); ?>" class="form-control">
        </div>
        <div class="col-md-12">
            <button type="submit" name="update_staff" class="btn btn-success w-100">Update</button>
        </div>
    </form>
</div>
<footer class="text-center text-muted py-3 mt-4"><small>Built by DevFalex.</small></footer>
</body>
</html>
