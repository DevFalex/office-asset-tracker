<?php
session_start();
include "db.php";

if(isset($_POST['login'])){
    $username = $_POST['username'];
    $password = md5($_POST['password']); // simple hash for demo

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if($result->num_rows == 1){
        $user = $result->fetch_assoc();
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];

        if($user['role'] == 'Admin'){
            header("Location: index.php");
        }elseif($user['role'] == 'Staff'){
            header("Location: staff-dashboard.php");
        } else {
            header("Location: login.php");
        }
        
        exit();
    } else {
        $error = "Invalid username or password!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login - Office Asset Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body">
                    <h3 class="text-center">Login</h3>
                    <?php if(isset($error)) echo "<p class='text-danger'>$error</p>"; ?>
                    <form method="POST" action="">
                        <div class="mb-3">
                            <input type="text" name="username" class="form-control" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="password" name="password" class="form-control" placeholder="Password" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                    </form>
                </div>
            </div>
            <div class="text-center mt-3">
                <p>Developed By Ope Faleye (249074041)</p>
                <p>Admin Username:<strong> admin</strong></p>
                <p>Admin Password:<strong> mit_admin123</strong></p>
            </div>
        </div>
        <p class="text-center mt-3"><strong>Note: You can register as a staff and login using your preferred password</strong></p>
    </div>
</div>
</body>
</html>
