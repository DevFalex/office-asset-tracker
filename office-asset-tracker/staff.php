<?php
session_start();
include "db.php";
require_admin();

if (isset($_POST['add_staff'])) {
    $ok = $conn->query(
        "INSERT INTO users (full_name, username, password, department, role) VALUES (?, ?, ?, ?, 'Staff')",
        [$_POST['full_name'], $_POST['username'],
         password_hash($_POST['password'], PASSWORD_DEFAULT), $_POST['department']]
    );
    set_flash($ok ? 'success' : 'danger',
        $ok ? 'Staff member added.' : 'Could not add staff — the username may already exist.');
    header("Location: /staff.php");
    exit();
}

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM users WHERE user_id = ? AND role = 'Staff'", [$_GET['delete']]);
    set_flash('success', 'Staff member removed.');
    header("Location: /staff.php");
    exit();
}

$staff = $conn->query("SELECT * FROM users WHERE role = 'Staff' ORDER BY user_id DESC");

$page_title = "Staff";
$active = "staff";
include "partials/header.php";
?>

<div class="page-head">
    <div>
        <h2>Staff Management</h2>
        <p class="text-muted mb-0">Manage staff accounts who can be assigned assets.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addStaff">
        <i class="bi bi-person-plus me-1"></i> Add Staff
    </button>
</div>

<div class="collapse show mb-4" id="addStaff">
    <div class="card">
        <div class="card-header"><i class="bi bi-person-plus me-2"></i>New Staff Member</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Full Name</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Department</label>
                    <input type="text" name="department" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" name="add_staff" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Staff</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-people me-2"></i>Staff Directory</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th class="ps-3">ID</th><th>Full Name</th><th>Username</th><th>Department</th><th class="pe-3 text-end">Actions</th>
                </tr></thead>
                <tbody>
                <?php while ($row = $staff->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-3 text-muted">#<?php echo (int) $row['user_id']; ?></td>
                        <td class="fw-semibold">
                            <span class="avatar d-inline-flex me-2" style="width:32px;height:32px;font-size:.8rem;background:var(--brand-dark);color:#fff;border-radius:50%;align-items:center;justify-content:center;">
                                <?php echo e(strtoupper(substr($row['full_name'], 0, 1))); ?>
                            </span>
                            <?php echo e($row['full_name']); ?>
                        </td>
                        <td><?php echo e($row['username']); ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo e($row['department']); ?></span></td>
                        <td class="pe-3 text-end">
                            <a href="/edit_staff.php?id=<?php echo (int) $row['user_id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <a href="?delete=<?php echo (int) $row['user_id']; ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this staff member?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "partials/footer.php"; ?>
