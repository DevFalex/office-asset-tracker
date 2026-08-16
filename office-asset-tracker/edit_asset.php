<?php
session_start();
include "db.php";
require_admin();

$id = $_GET['id'] ?? null;

if (isset($_POST['update_asset'])) {
    $conn->query(
        "UPDATE assets SET asset_name = ?, asset_type = ?, serial_number = ?, purchase_date = ?, status = ?
         WHERE asset_id = ?",
        [$_POST['asset_name'], $_POST['asset_type'], $_POST['serial_number'],
         $_POST['purchase_date'], $_POST['status'], $id]
    );
    set_flash('success', 'Asset updated.');
    header("Location: /assets.php");
    exit();
}

$result = $conn->query("SELECT * FROM assets WHERE asset_id = ?", [$id]);
$asset = $result ? $result->fetch_assoc() : null;
if (!$asset) { header("Location: /assets.php"); exit(); }

$statuses = ["Available", "In Use", "Under Repair", "Disposed"];

$page_title = "Edit Asset";
$active = "assets";
include "partials/header.php";
?>

<div class="page-head">
    <div><h2>Edit Asset</h2><p class="text-muted mb-0">Update the details for this asset.</p></div>
    <a href="/assets.php" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back</a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header"><i class="bi bi-pencil-square me-2"></i>Asset #<?php echo (int) $asset['asset_id']; ?></div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Asset Name</label>
                        <input type="text" name="asset_name" value="<?php echo e($asset['asset_name']); ?>" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Type</label>
                        <input type="text" name="asset_type" value="<?php echo e($asset['asset_type']); ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Serial Number</label>
                        <input type="text" name="serial_number" value="<?php echo e($asset['serial_number']); ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Purchase Date</label>
                        <input type="date" name="purchase_date" value="<?php echo e($asset['purchase_date']); ?>" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Status</label>
                        <select name="status" class="form-select">
                            <?php foreach ($statuses as $st): ?>
                                <option <?php echo $asset['status'] === $st ? 'selected' : ''; ?>><?php echo $st; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <button type="submit" name="update_asset" class="btn btn-success"><i class="bi bi-check2 me-1"></i> Update Asset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include "partials/footer.php"; ?>
