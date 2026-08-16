<?php
/**
 * Shared application shell (sidebar + topbar).
 * Expects: $page_title (string), $active (nav key), and a started session
 * with an authenticated user. Include AFTER any header()/redirect logic.
 */
$role      = $_SESSION['role'] ?? 'Staff';
$full_name = $_SESSION['full_name'] ?? 'User';
$active    = $active ?? '';
$page_title = $page_title ?? 'Office Asset Tracker';

// Role-aware navigation.
if ($role === 'Admin') {
    $nav = [
        ['key' => 'dashboard',   'href' => '/index.php',   'icon' => 'bi-grid-1x2-fill',      'label' => 'Dashboard'],
        ['key' => 'assets',      'href' => '/assets.php',  'icon' => 'bi-box-seam-fill',      'label' => 'Assets'],
        ['key' => 'staff',       'href' => '/staff.php',   'icon' => 'bi-people-fill',        'label' => 'Staff'],
        ['key' => 'assign',      'href' => '/assign.php',  'icon' => 'bi-arrow-left-right',   'label' => 'Assignments'],
        ['key' => 'reports',     'href' => '/reports.php', 'icon' => 'bi-bar-chart-fill',     'label' => 'Reports'],
    ];
} else {
    $nav = [
        ['key' => 'dashboard', 'href' => '/staff-dashboard.php', 'icon' => 'bi-grid-1x2-fill', 'label' => 'My Assets'],
    ];
}
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e($page_title); ?> · Office Asset Tracker</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" defer></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root {
            --brand: #2563eb; --brand-dark: #1e3a8a; --ink: #0f172a;
            --bg: #f1f5f9; --card: #ffffff; --muted: #64748b; --border: #e5e9f0;
            --sidebar-w: 250px;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0; background: var(--bg); color: var(--ink);
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
        }
        a { text-decoration: none; }

        /* Sidebar */
        .sidebar {
            position: fixed; top: 0; left: 0; width: var(--sidebar-w); height: 100vh;
            background: linear-gradient(180deg, #1e293b 0%, #0f172a 100%);
            color: #cbd5e1; display: flex; flex-direction: column; z-index: 1040;
            transition: transform .25s ease;
        }
        .sidebar .brand {
            display: flex; align-items: center; gap: .65rem; padding: 1.25rem 1.25rem;
            color: #fff; font-weight: 700; border-bottom: 1px solid rgba(255,255,255,.08);
        }
        .sidebar .brand .logo {
            width: 38px; height: 38px; border-radius: 10px; flex: 0 0 38px;
            display: inline-flex; align-items: center; justify-content: center;
            background: linear-gradient(135deg, #2563eb, #1e3a8a); color: #fff; font-size: 1.15rem;
        }
        .sidebar nav { padding: 1rem .75rem; overflow-y: auto; }
        .sidebar .nav-link {
            display: flex; align-items: center; gap: .8rem; padding: .7rem .9rem; margin-bottom: .2rem;
            border-radius: .6rem; color: #cbd5e1; font-weight: 500; font-size: .95rem;
        }
        .sidebar .nav-link i { font-size: 1.1rem; }
        .sidebar .nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
        .sidebar .nav-link.active { background: var(--brand); color: #fff; box-shadow: 0 6px 16px rgba(37,99,235,.35); }
        .sidebar .side-foot { margin-top: auto; padding: 1rem 1.25rem; font-size: .75rem; color: #64748b; }

        /* Main area */
        .main { margin-left: var(--sidebar-w); min-height: 100vh; display: flex; flex-direction: column; }
        .topbar {
            position: sticky; top: 0; z-index: 1030; background: var(--card);
            border-bottom: 1px solid var(--border); padding: .85rem 1.5rem;
            display: flex; align-items: center; gap: 1rem;
        }
        .topbar h1 { font-size: 1.15rem; font-weight: 700; margin: 0; }
        .topbar .user {
            margin-left: auto; display: flex; align-items: center; gap: .6rem;
        }
        .topbar .avatar {
            width: 38px; height: 38px; border-radius: 50%; background: var(--brand-dark); color: #fff;
            display: inline-flex; align-items: center; justify-content: center; font-weight: 700;
        }
        .content { padding: 1.75rem 1.5rem; flex: 1; }
        .page-foot { text-align: center; color: var(--muted); padding: 1rem; font-size: .85rem; }

        /* Reusable pieces */
        .card { border: 1px solid var(--border); border-radius: .9rem; box-shadow: 0 1px 3px rgba(15,23,42,.04); }
        .card-header { background: #fff; border-bottom: 1px solid var(--border); font-weight: 600; border-radius: .9rem .9rem 0 0 !important; }
        .page-head { display: flex; flex-wrap: wrap; gap: .75rem; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
        .page-head h2 { font-size: 1.4rem; font-weight: 700; margin: 0; }
        .stat-card .icon {
            width: 52px; height: 52px; border-radius: 12px; display: inline-flex;
            align-items: center; justify-content: center; font-size: 1.5rem;
        }
        .stat-card .num { font-size: 1.9rem; font-weight: 700; line-height: 1; }
        .stat-card .lbl { color: var(--muted); font-size: .9rem; }
        .table thead th { font-size: .78rem; text-transform: uppercase; letter-spacing: .03em; color: var(--muted); border-bottom: 2px solid var(--border); }
        .table td { vertical-align: middle; }
        .btn-toggle { display: none; background: none; border: none; font-size: 1.4rem; color: var(--ink); }
        .backdrop { display: none; }

        @media (max-width: 991px) {
            .sidebar { transform: translateX(-100%); }
            body.sidebar-open .sidebar { transform: translateX(0); }
            body.sidebar-open .backdrop { display: block; position: fixed; inset: 0; background: rgba(0,0,0,.4); z-index: 1035; }
            .main { margin-left: 0; }
            .btn-toggle { display: inline-flex; }
        }
    </style>
</head>
<body>
<div class="backdrop" onclick="document.body.classList.remove('sidebar-open')"></div>

<aside class="sidebar">
    <div class="brand">
        <span class="logo"><i class="bi bi-hdd-stack-fill"></i></span>
        <span>Asset Tracker</span>
    </div>
    <nav>
        <?php foreach ($nav as $item): ?>
            <a class="nav-link <?php echo $active === $item['key'] ? 'active' : ''; ?>" href="<?php echo $item['href']; ?>">
                <i class="bi <?php echo $item['icon']; ?>"></i> <?php echo $item['label']; ?>
            </a>
        <?php endforeach; ?>
        <a class="nav-link" href="/logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </nav>
    <div class="side-foot">Built by DevFalex.</div>
</aside>

<div class="main">
    <header class="topbar">
        <button class="btn-toggle" onclick="document.body.classList.toggle('sidebar-open')"><i class="bi bi-list"></i></button>
        <h1><?php echo e($page_title); ?></h1>
        <div class="user">
            <div class="text-end d-none d-sm-block">
                <div style="font-weight:600; font-size:.9rem;"><?php echo e($full_name); ?></div>
                <div style="font-size:.75rem; color:var(--muted);"><?php echo e($role); ?></div>
            </div>
            <span class="avatar"><?php echo e(strtoupper(substr($full_name, 0, 1))); ?></span>
        </div>
    </header>

    <main class="content">
        <?php if ($flash): ?>
            <div class="alert alert-<?php echo e($flash['type']); ?> alert-dismissible fade show" role="alert">
                <?php echo e($flash['msg']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
