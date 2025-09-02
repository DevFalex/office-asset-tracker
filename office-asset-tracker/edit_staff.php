<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin'){
    header("Location: login.php");
    exit();
}
include "db.php";

$id = $_GET['id'];
$result = $conn->query("SELECT * FROM users WHERE user_id=$id AND role='Staff'");
$staff = $result->fetch_assoc();

if(isset($_POST['update_staff'])){
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $department = $_POST['department'];

    $sql = "UPDATE users SET 
                full_name='$full_name',
                username='$username',
                department='$department'
            WHERE user_id=$id AND role='Staff'";
    $conn->query($sql);
    header("Location: staff.php");
    exit();
}
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
            <input type="text" name="full_name" value="<?php echo $staff['full_name']; ?>" class="form-control" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="username" value="<?php echo $staff['username']; ?>" class="form-control" required>
        </div>
        <div class="col-md-4">
            <input type="text" name="department" value="<?php echo $staff['department']; ?>" class="form-control">
        </div>
        <div class="col-md-12">
            <button type="submit" name="update_staff" class="btn btn-success w-100">Update</button>
        </div>
    </form>
</div>
</body>
</html>
