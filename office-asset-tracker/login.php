<?php
session_start();
include "db.php";

if (isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    // Parameterised lookup — no user input is ever concatenated into SQL.
    $res  = $conn->query("SELECT * FROM users WHERE username = ?", [$username]);
    $user = $res ? $res->fetch_assoc() : null;

    $ok = false;
    if ($user) {
        $stored = $user['password'];
        if (password_verify($password, $stored)) {
            $ok = true;
        } elseif (preg_match('/^[a-f0-9]{32}$/i', $stored) && md5($password) === strtolower($stored)) {
            // Legacy MD5 account: accept once, then transparently upgrade to bcrypt.
            $ok = true;
            $conn->query(
                "UPDATE users SET password = ? WHERE user_id = ?",
                [password_hash($password, PASSWORD_DEFAULT), $user['user_id']]
            );
        }
    }

    if ($ok) {
        session_regenerate_id(true);
        $_SESSION['user_id']   = $user['user_id'];
        $_SESSION['username']  = $user['username'];
        $_SESSION['role']      = $user['role'];
        $_SESSION['full_name'] = $user['full_name'];
        header("Location: " . ($user['role'] === 'Admin' ? "/index.php" : "/staff-dashboard.php"));
        exit();
    }
    $error = "Invalid username or password!";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sign in · Office Asset Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, #1e3a8a 0%, #0f172a 100%);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, sans-serif;
        }
        .auth-card { border: none; border-radius: 1rem; overflow: hidden; box-shadow: 0 20px 60px rgba(0,0,0,.35); }
        .brand-icon {
            width: 64px; height: 64px; border-radius: 16px;
            display: inline-flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #2563eb, #1e3a8a); color: #fff; font-size: 1.9rem;
        }
        .demo-box { background: #f8fafc; border: 1px dashed #cbd5e1; border-radius: .75rem; }
        .demo-row { cursor: pointer; transition: background .15s; border-radius: .5rem; }
        .demo-row:hover { background: #eef2ff; }
        .role-badge { font-size: .7rem; letter-spacing: .04em; }
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5 col-xl-4">
            <div class="card auth-card">
                <div class="card-body p-4 p-sm-5">

                    <div class="text-center mb-4">
                        <span class="brand-icon mb-3"><i class="bi bi-hdd-stack-fill"></i></span>
                        <h4 class="fw-bold mb-1">Office Asset Tracker</h4>
                        <p class="text-muted small mb-0">Asset Management System</p>
                    </div>

                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger py-2 small d-flex align-items-center" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i><?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/login.php">
                        <div class="mb-3">
                            <label class="form-label small fw-semibold">Username</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-person"></i></span>
                                <input type="text" name="username" id="username" class="form-control" placeholder="Enter username" required autofocus>
                            </div>
                        </div>
                        <div class="mb-4">
                            <label class="form-label small fw-semibold">Password</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light"><i class="bi bi-lock"></i></span>
                                <input type="password" name="password" id="password" class="form-control" placeholder="Enter password" required>
                            </div>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100 py-2 fw-semibold">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Sign in
                        </button>
                    </form>

                    <div class="demo-box p-3 mt-4">
                        <p class="small text-muted mb-2 fw-semibold text-uppercase" style="letter-spacing:.05em;">
                            <i class="bi bi-stars me-1"></i> Demo accounts — click to fill
                        </p>
                        <div class="demo-row d-flex align-items-center justify-content-between p-2"
                             onclick="fillLogin('admin','admin123')">
                            <div>
                                <span class="badge bg-primary role-badge me-2">ADMIN</span>
                                <code>admin</code> / <code>admin123</code>
                            </div>
                            <small class="text-primary">Full access <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                        <div class="demo-row d-flex align-items-center justify-content-between p-2 mt-1"
                             onclick="fillLogin('Falex','staff123')">
                            <div>
                                <span class="badge bg-success role-badge me-2">STAFF</span>
                                <code>Falex</code> / <code>staff123</code>
                            </div>
                            <small class="text-success">My assets <i class="bi bi-arrow-right-short"></i></small>
                        </div>
                    </div>

                </div>
            </div>
            <p class="text-center text-white-50 small mt-3 mb-0">Built by DevFalex.</p>
        </div>
    </div>
</div>

<script>
    function fillLogin(user, pass) {
        document.getElementById('username').value = user;
        document.getElementById('password').value = pass;
        document.getElementById('password').focus();
    }
</script>
</body>
</html>
