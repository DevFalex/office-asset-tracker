<?php
session_start();
include "db.php";
require_admin();

if (isset($_POST['assign_asset'])) {
    $asset_id = $_POST['asset_id'];
    $ok = $conn->query(
        "INSERT INTO asset_assignments (asset_id, staff_id, assigned_date) VALUES (?, ?, ?)",
        [$asset_id, $_POST['staff_id'], $_POST['assigned_date']]
    );
    if ($ok) {
        $conn->query("UPDATE assets SET status = 'In Use' WHERE asset_id = ?", [$asset_id]);
        set_flash('success', 'Asset assigned successfully.');
    } else {
        set_flash('danger', 'Could not assign asset.');
    }
    header("Location: /assign.php");
    exit();
}

if (isset($_GET['return'])) {
    $id = $_GET['return'];
    if ($conn->query("UPDATE asset_assignments SET return_date = ? WHERE assignment_id = ?", [date("Y-m-d"), $id])) {
        $row = $conn->query("SELECT asset_id FROM asset_assignments WHERE assignment_id = ?", [$id])->fetch_assoc();
        if ($row) {
            $conn->query("UPDATE assets SET status = 'Available' WHERE asset_id = ?", [$row['asset_id']]);
        }
        set_flash('success', 'Asset marked as returned.');
    }
    header("Location: /assign.php");
    exit();
}

$available   = $conn->query("SELECT * FROM assets WHERE status = 'Available' ORDER BY asset_name");
$staffList   = $conn->query("SELECT * FROM users WHERE role = 'Staff' ORDER BY full_name");
$assignments = $conn->query(
    "SELECT aa.assignment_id, a.asset_name, a.serial_number, u.full_name, u.department, aa.assigned_date, aa.return_date
     FROM asset_assignments aa
     JOIN assets a ON aa.asset_id = a.asset_id
     JOIN users u ON aa.staff_id = u.user_id
     ORDER BY aa.assignment_id DESC"
);

$page_title = "Assignments";
$active = "assign";
include "partials/header.php";
?>

<div class="page-head">
    <div>
        <h2>Asset Assignments</h2>
        <p class="text-muted mb-0">Assign available assets to staff and record returns.</p>
    </div>
</div>

<div class="card mb-4">
    <div class="card-header"><i class="bi bi-arrow-left-right me-2"></i>Assign an Asset</div>
    <div class="card-body">
        <form method="POST" class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Asset (available only)</label>
                <select name="asset_id" class="form-select" required>
                    <option value="">Select asset…</option>
                    <?php while ($a = $available->fetch_assoc()): ?>
                        <option value="<?php echo (int) $a['asset_id']; ?>"><?php echo e($a['asset_name']) . ' (' . e($a['serial_number']) . ')'; ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-semibold">Staff Member</label>
                <select name="staff_id" class="form-select" required>
                    <option value="">Select staff…</option>
                    <?php while ($s = $staffList->fetch_assoc()): ?>
                        <option value="<?php echo (int) $s['user_id']; ?>"><?php echo e($s['full_name']) . ' — ' . e($s['department']); ?></option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-semibold">Date</label>
                <input type="date" name="assigned_date" value="<?php echo date('Y-m-d'); ?>" class="form-control">
            </div>
            <div class="col-md-2">
                <button type="submit" name="assign_asset" class="btn btn-primary w-100"><i class="bi bi-check2 me-1"></i> Assign</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="bi bi-list-task me-2"></i>Assignment Records</div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead><tr>
                    <th class="ps-3">ID</th><th>Asset</th><th>Staff</th><th>Assigned</th><th>Returned</th><th class="pe-3 text-end">Action</th>
                </tr></thead>
                <tbody>
                <?php while ($row = $assignments->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-3 text-muted">#<?php echo (int) $row['assignment_id']; ?></td>
                        <td class="fw-semibold"><?php echo e($row['asset_name']); ?><div class="small text-muted"><?php echo e($row['serial_number']); ?></div></td>
                        <td><?php echo e($row['full_name']); ?><div class="small text-muted"><?php echo e($row['department']); ?></div></td>
                        <td><?php echo e($row['assigned_date']); ?></td>
                        <td>
                            <?php if ($row['return_date']): ?>
                                <?php echo e($row['return_date']); ?>
                            <?php else: ?>
                                <span class="badge bg-danger">Not returned</span>
                            <?php endif; ?>
                        </td>
                        <td class="pe-3 text-end">
                            <?php if (!$row['return_date']): ?>
                                <a href="?return=<?php echo (int) $row['assignment_id']; ?>" class="btn btn-sm btn-success"
                                   onclick="return confirm('Mark this asset as returned?')"><i class="bi bi-check2-circle me-1"></i> Mark Returned</a>
                            <?php else: ?>
                                <span class="badge bg-secondary">Closed</span>
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
