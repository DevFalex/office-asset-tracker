<?php
session_start();
include "db.php";
require_admin();

if (isset($_POST['add_asset'])) {
    $ok = $conn->query(
        "INSERT INTO assets (asset_name, asset_type, serial_number, purchase_date, status)
         VALUES (?, ?, ?, ?, 'Available')",
        [$_POST['asset_name'], $_POST['asset_type'], $_POST['serial_number'], $_POST['purchase_date']]
    );
    set_flash($ok ? 'success' : 'danger',
        $ok ? 'Asset added successfully.' : 'Could not add asset — the serial number may already exist.');
    header("Location: /assets.php");
    exit();
}

if (isset($_GET['delete'])) {
    $conn->query("DELETE FROM assets WHERE asset_id = ?", [$_GET['delete']]);
    set_flash('success', 'Asset deleted.');
    header("Location: /assets.php");
    exit();
}

$assets = $conn->query("SELECT * FROM assets ORDER BY asset_id DESC");

$page_title = "Assets";
$active = "assets";
include "partials/header.php";
?>

<div class="page-head">
    <div>
        <h2>Manage Assets</h2>
        <p class="text-muted mb-0">Add, edit and track your organization's equipment.</p>
    </div>
    <button class="btn btn-primary" data-bs-toggle="collapse" data-bs-target="#addAsset">
        <i class="bi bi-plus-lg me-1"></i> Add Asset
    </button>
</div>

<div class="collapse show mb-4" id="addAsset">
    <div class="card">
        <div class="card-header"><i class="bi bi-plus-circle me-2"></i>New Asset</div>
        <div class="card-body">
            <form method="POST" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Asset Name</label>
                    <input type="text" name="asset_name" class="form-control" placeholder="e.g. Dell Latitude 5420" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Type</label>
                    <input type="text" name="asset_type" class="form-control" placeholder="e.g. Laptop">
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Serial Number</label>
                    <input type="text" name="serial_number" class="form-control" placeholder="e.g. DL5420-001">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Purchase Date</label>
                    <input type="date" name="purchase_date" class="form-control">
                </div>
                <div class="col-12">
                    <button type="submit" name="add_asset" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save Asset</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <span><i class="bi bi-list-ul me-2"></i>Asset Inventory</span>
        <input type="text" id="assetSearch" class="form-control form-control-sm" style="max-width:220px"
               placeholder="Search…" onkeyup="filterTable('assetSearch','assetTable')">
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="assetTable">
                <thead><tr>
                    <th class="ps-3">ID</th><th>Name</th><th>Type</th><th>Serial</th>
                    <th>Purchase Date</th><th>Status</th><th class="pe-3 text-end">Actions</th>
                </tr></thead>
                <tbody>
                <?php while ($row = $assets->fetch_assoc()): ?>
                    <tr>
                        <td class="ps-3 text-muted">#<?php echo (int) $row['asset_id']; ?></td>
                        <td class="fw-semibold"><?php echo e($row['asset_name']); ?></td>
                        <td><?php echo e($row['asset_type']); ?></td>
                        <td><code><?php echo e($row['serial_number']); ?></code></td>
                        <td><?php echo e($row['purchase_date']); ?></td>
                        <td><span class="badge <?php echo status_class($row['status']); ?>"><?php echo e($row['status']); ?></span></td>
                        <td class="pe-3 text-end">
                            <a href="/edit_asset.php?id=<?php echo (int) $row['asset_id']; ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                            <a href="?delete=<?php echo (int) $row['asset_id']; ?>" class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Delete this asset?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function filterTable(inputId, tableId) {
    var q = document.getElementById(inputId).value.toLowerCase();
    document.querySelectorAll('#' + tableId + ' tbody tr').forEach(function (tr) {
        tr.style.display = tr.innerText.toLowerCase().includes(q) ? '' : 'none';
    });
}
</script>

<?php include "partials/footer.php"; ?>
