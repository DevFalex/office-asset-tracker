<?php
session_start();
include "db.php";
require_login();
if (($_SESSION['role'] ?? '') !== 'Staff') {
    header("Location: /index.php");
    exit();
}

$staff_id = $_SESSION['user_id'];
$assets = $conn->query(
    "SELECT a.asset_name, a.serial_number, aa.assigned_date, a.status
     FROM asset_assignments aa
     JOIN assets a ON aa.asset_id = a.asset_id
     WHERE aa.staff_id = ? AND aa.return_date IS NULL
     ORDER BY aa.assigned_date DESC",
    [$staff_id]
);
$count = $assets ? $assets->num_rows : 0;

$page_title = "My Assets";
$active = "dashboard";
include "partials/header.php";
?>

<div class="page-head">
    <div>
        <h2>My Assigned Assets</h2>
        <p class="text-muted mb-0">Equipment currently issued to you.</p>
    </div>
    <span class="badge bg-primary fs-6"><?php echo (int) $count; ?> item<?php echo $count == 1 ? '' : 's'; ?></span>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-list-check me-2"></i>Current Assignments</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th class="ps-3">Asset</th><th>Serial Number</th><th>Assigned Date</th><th class="pe-3">Status</th></tr></thead>
                <tbody>
                <?php if ($count > 0): ?>
                    <?php while ($row = $assets->fetch_assoc()): ?>
                        <tr>
                            <td class="ps-3 fw-semibold"><?php echo e($row['asset_name']); ?></td>
                            <td><code><?php echo e($row['serial_number']); ?></code></td>
                            <td><?php echo e($row['assigned_date']); ?></td>
                            <td class="pe-3"><span class="badge <?php echo status_class($row['status']); ?>"><?php echo e($row['status']); ?></span></td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="4" class="text-center text-muted py-5">
                        <i class="bi bi-inbox d-block mb-2" style="font-size:2rem;"></i>
                        No assets assigned to you yet.
                    </td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "partials/footer.php"; ?>
