<?php
session_start();
include "db.php";
require_admin();

$id = $_GET['id'] ?? null;

if (isset($_POST['update_staff'])) {
    $conn->query(
        "UPDATE users SET full_name = ?, username = ?, department = ? WHERE user_id = ? AND role = 'Staff'",
        [$_POST['full_name'], $_POST['username'], $_POST['department'], $id]
    );
    set_flash('success', 'Staff member updated.');
    header("Location: /staff.php");
    exit();
}

$result = $conn->query("SELECT * FROM users WHERE user_id = ? AND role = 'Staff'", [$id]);
$staff = $result ? $result->fetch_assoc() : null;
if (!$staff) { header("Location: /staff.php"); exit(); }

$page_title = "Edit Staff";
$active = "staff";
include "partials/header.php";
?>

<div class="page-head">
    <div><h2>Edit Staff</h2><p class="text-muted mb-0">Update this staff member's details.</p></div>
    <a href="/staff.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-person-gear me-2"></i><?php echo e($staff['full_name']); ?></div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Full Name</label>
                        <input type="text" name="full_name" value="<?php echo e($staff['full_name']); ?>" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Username</label>
                        <input type="text" name="username" value="<?php echo e($staff['username']); ?>" class="form-control" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold">Department</label>
                        <input type="text" name="department" value="<?php echo e($staff['department']); ?>" class="form-control">
                    </div>
                    <div class="col-12">
                        <button type="submit" name="update_staff" class="btn btn-success"><i class="bi bi-check2 me-1"></i> Update Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "partials/footer.php"; ?>
