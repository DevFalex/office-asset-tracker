<?php
session_start();
if(!isset($_SESSION['user_id']) || $_SESSION['role'] != 'Admin'){ header("Location: login.php"); exit(); }
include "db.php";

if(isset($_POST['add_staff'])){
    $full_name = $_POST['full_name'];
    $username = $_POST['username'];
    $password = md5($_POST['password']); // simple hash to match login
    $department = $_POST['department'];

    $sql = "INSERT INTO users (full_name, username, password, department, role) 
            VALUES ('$full_name', '$username', '$password', '$department', 'Staff')";
    if($conn->query($sql)){
        echo "<script>alert('Staff added successfully'); window.location='staff.php';</script>";
    } else {
        echo "<script>alert('Error: ".$conn->error."');</script>";
    }
}

if(isset($_GET['delete'])){
    $id = $_GET['delete'];
    $conn->query("DELETE FROM users WHERE user_id=$id");
    echo "<script>alert('Staff deleted successfully'); window.location='staff.php';</script>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Management - Office Asset Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<div class="container my-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people text-success"></i> Staff Management</h2>
        <a href="index.php" class="btn btn-secondary"><i class="bi bi-house"></i> Dashboard</a>
    </div>

    <!-- Add Staff -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success text-white"><i class="bi bi-plus-circle"></i> Add New Staff</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-3"><input type="text" name="full_name" class="form-control" placeholder="Full Name" required></div>
                <div class="col-md-3"><input type="text" name="username" class="form-control" placeholder="Username" required></div>
                <div class="col-md-3"><input type="password" name="password" class="form-control" placeholder="Password" required></div>
                <div class="col-md-3"><input type="text" name="department" class="form-control" placeholder="Department"></div>
                <div class="col-md-12"><button type="submit" name="add_staff" class="btn btn-success w-100"><i class="bi bi-save"></i> Save</button></div>
            </form>
        </div>
    </div>

    <!-- Staff List -->
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white"><i class="bi bi-list-ul"></i> Staff List</div>
        <div class="card-body table-responsive">
            <table class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr><th>ID</th><th>Full Name</th><th>Username</th><th>Department</th><th>Actions</th></tr>
                </thead>
                <tbody>
                    <?php
                    $result = $conn->query("SELECT * FROM users WHERE role='Staff'");
                    while($row = $result->fetch_assoc()){
                        echo "<tr>
                                <td>{$row['user_id']}</td>
                                <td>{$row['full_name']}</td>
                                <td>{$row['username']}</td>
                                <td><span class='badge bg-info text-dark'>{$row['department']}</span></td>
                                <td>
                                    <a href='edit_staff.php?id={$row['user_id']}' class='btn btn-sm btn-warning'><i class='bi bi-pencil'></i></a>
                                    <a href='?delete={$row['user_id']}' class='btn btn-sm btn-danger' onclick=\"return confirm('Delete this staff?')\"><i class='bi bi-trash'></i></a>
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
