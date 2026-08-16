<?php
session_start();
include "db.php";
require_admin();

$statuses = [
    ['Available',    'bi-check-circle-fill', '#16a34a', '#dcfce7'],
    ['In Use',       'bi-people-fill',       '#2563eb', '#dbeafe'],
    ['Under Repair', 'bi-tools',             '#d97706', '#fef3c7'],
    ['Disposed',     'bi-trash-fill',        '#dc2626', '#fee2e2'],
];
$counts = [];
foreach ($statuses as $s) {
    $counts[$s[0]] = $conn->query("SELECT COUNT(*) AS total FROM assets WHERE status = ?", [$s[0]])->fetch_assoc()['total'];
}
$total_assets = $conn->query("SELECT COUNT(*) AS total FROM assets")->fetch_assoc()['total'];
$total_staff  = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'Staff'")->fetch_assoc()['total'];
$active_assign = $conn->query("SELECT COUNT(*) AS total FROM asset_assignments WHERE return_date IS NULL")->fetch_assoc()['total'];

$recent = $conn->query(
    "SELECT a.asset_name, a.serial_number, u.full_name, aa.assigned_date, aa.return_date
     FROM asset_assignments aa
     JOIN assets a ON aa.asset_id = a.asset_id
     JOIN users u ON aa.staff_id = u.user_id
     ORDER BY aa.assignment_id DESC LIMIT 5"
);

$page_title = "Dashboard";
$active = "dashboard";
include "partials/header.php";
?>

<div class="page-head">
    <div>
        <h2>Welcome back, <?php echo e(explode(' ', $_SESSION['full_name'])[0]); ?> 👋</h2>
        <p class="text-muted mb-0">Here's an overview of your organization's assets.</p>
    </div>
    <a href="/assets.php" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Asset</a>
</div>

<!-- Status stat cards -->
<div class="row g-3 mb-4">
    <?php foreach ($statuses as $s): ?>
        <div class="col-6 col-xl-3">
            <div class="card stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <span class="icon" style="background: <?php echo $s[3]; ?>; color: <?php echo $s[2]; ?>;">
                        <i class="bi <?php echo $s[1]; ?>"></i>
                    </span>
                    <div>
                        <div class="num"><?php echo (int) $counts[$s[0]]; ?></div>
                        <div class="lbl"><?php echo $s[0]; ?></div>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Summary + quick actions -->
<div class="row g-3">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">Summary</div>
            <div class="card-body">
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-box-seam me-2"></i>Total Assets</span>
                    <span class="fw-bold"><?php echo (int) $total_assets; ?></span>
                </div>
                <div class="d-flex justify-content-between py-2 border-bottom">
                    <span class="text-muted"><i class="bi bi-people me-2"></i>Staff Members</span>
                    <span class="fw-bold"><?php echo (int) $total_staff; ?></span>
                </div>
                <div class="d-flex justify-content-between py-2">
                    <span class="text-muted"><i class="bi bi-arrow-left-right me-2"></i>Active Assignments</span>
                    <span class="fw-bold"><?php echo (int) $active_assign; ?></span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span>Recent Assignments</span>
                <a href="/reports.php" class="small">View all <i class="bi bi-arrow-right-short"></i></a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th class="ps-3">Asset</th><th>Staff</th><th>Date</th><th class="pe-3">Status</th></tr></thead>
                        <tbody>
                        <?php if ($recent && $recent->num_rows > 0): ?>
                            <?php while ($row = $recent->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-3"><?php echo e($row['asset_name']); ?><div class="small text-muted"><?php echo e($row['serial_number']); ?></div></td>
                                    <td><?php echo e($row['full_name']); ?></td>
                                    <td><?php echo e($row['assigned_date']); ?></td>
                                    <td class="pe-3">
                                        <?php if ($row['return_date']): ?>
                                            <span class="badge bg-secondary">Returned</span>
                                        <?php else: ?>
                                            <span class="badge bg-success">Active</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No assignments yet.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "partials/footer.php"; ?>
