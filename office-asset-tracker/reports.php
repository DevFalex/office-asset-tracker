<?php
session_start();
include "db.php";
require_admin();

$statuses = ["Available", "In Use", "Under Repair", "Disposed"];
$status_counts = [];
$max = 1;
foreach ($statuses as $st) {
    $c = (int) $conn->query("SELECT COUNT(*) AS total FROM assets WHERE status = ?", [$st])->fetch_assoc()['total'];
    $status_counts[$st] = $c;
    $max = max($max, $c);
}

$byDept = $conn->query(
    "SELECT u.department, COUNT(*) AS total
     FROM asset_assignments aa
     JOIN users u ON aa.staff_id = u.user_id
     WHERE aa.return_date IS NULL
     GROUP BY u.department ORDER BY total DESC"
);

$history = $conn->query(
    "SELECT a.asset_name, a.serial_number, u.full_name, aa.assigned_date, aa.return_date
     FROM asset_assignments aa
     JOIN assets a ON aa.asset_id = a.asset_id
     JOIN users u ON aa.staff_id = u.user_id
     ORDER BY aa.assigned_date DESC"
);

$page_title = "Reports";
$active = "reports";
include "partials/header.php";
?>

<div class="page-head">
    <div>
        <h2>Reports &amp; Analytics</h2>
        <p class="text-muted mb-0">Asset distribution and assignment history.</p>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-pie-chart me-2"></i>Assets by Status</div>
            <div class="card-body">
                <?php foreach ($statuses as $st): $c = $status_counts[$st]; ?>
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span class="fw-semibold"><?php echo $st; ?></span>
                            <span class="text-muted"><?php echo $c; ?></span>
                        </div>
                        <div class="progress" style="height:10px;">
                            <div class="progress-bar <?php echo status_class($st); ?>" role="progressbar"
                                 style="width: <?php echo round($c / $max * 100); ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="card h-100">
            <div class="card-header"><i class="bi bi-building me-2"></i>Assets in Use by Department</div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead><tr><th class="ps-3">Department</th><th class="pe-3 text-end">Assigned</th></tr></thead>
                        <tbody>
                        <?php if ($byDept && $byDept->num_rows > 0): ?>
                            <?php while ($row = $byDept->fetch_assoc()): ?>
                                <tr>
                                    <td class="ps-3"><?php echo e($row['department']); ?></td>
                                    <td class="pe-3 text-end"><span class="badge bg-primary"><?php echo (int) $row['total']; ?></span></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="2" class="text-center text-muted py-4">No active assignments.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-clock-history me-2"></i>Assignment History</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr><th class="ps-3">Asset</th><th>Staff</th><th>Assigned</th><th class="pe-3">Returned</th></tr></thead>
                <tbody>
                <?php while ($row = $history->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-3 fw-semibold"><?php echo e($row['asset_name']); ?><div class="small text-muted"><?php echo e($row['serial_number']); ?></div></td>
                        <td><?php echo e($row['full_name']); ?></td>
                        <td><?php echo e($row['assigned_date']); ?></td>
                        <td class="pe-3">
                            <?php if ($row['return_date']): ?>
                                <span class="badge bg-secondary"><?php echo e($row['return_date']); ?></span>
                            <?php else: ?>
                                <span class="badge bg-success">In use</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include "partials/footer.php"; ?>
