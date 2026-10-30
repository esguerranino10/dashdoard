<?php
require_once __DIR__ . '/data.php';

/* Numbers for the sidebar badge and alert button */
$activeCount = count(array_filter($orders, fn($o) => in_array($o['status'], ['pending', 'washing', 'drying'])));
$payPending  = count(array_filter($orders, fn($o) => $o['payment'] !== 'paid' && $o['status'] !== 'cancelled'));
$readyCount  = count(array_filter($orders, fn($o) => $o['status'] === 'ready'));
$alertCount  = $payPending + $readyCount;

/* Sidebar links: [key, label, href] or a section label */
$nav = [
    ['label', 'Main'],
    ['dashboard',   'Dashboard',   'oks.php'],
    ['orders',      'Orders',      'orders.php'],
    ['queue',       'Queue',       'queue.php'],
    ['label', 'Management'],
    ['customers',   'Customers',   'customers.php'],
    ['services',    'Services',    'services.php'],
    ['machines',    'Machines',    'machines.php'],
    ['inventory',   'Inventory',   'inventory.php'],
    ['maintenance', 'Maintenance', 'maintenance.php'],
    ['label', 'Insights'],
    ['reports',     'Reports',     'reports.php'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title><?= e($pageTitle ?? 'House of Wash') ?> — House of Wash</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
</head>
<body>
<div class="app">

  <!-- ================= SIDEBAR ================= -->
  <aside class="sidebar" id="sidebar">
    <div class="brand">
      <img class="logo" src="isa.jpg" alt="Golden House of Wash Laundry">
      <div>
        <h1>House of Wash</h1>
        <small>Laundry Management</small>
      </div>
    </div>

    <nav class="nav-scroll">
<?php foreach ($nav as $n): ?>
<?php if ($n[0] === 'label'): ?>
      <div class="nav-label"><?= e($n[1]) ?></div>
<?php else: ?>
      <a class="nav-item<?= ($activePage ?? '') === $n[0] ? ' active' : '' ?>" href="<?= e($n[2]) ?>"><?= e($n[1]) ?><?php if ($n[0] === 'orders' && $activeCount > 0): ?><span class="badge text-bg-primary"><?= $activeCount ?></span><?php endif; ?></a>
<?php endif; ?>
<?php endforeach; ?>
    </nav>

    <div class="footer">
      <div class="d-flex align-items-center gap-1 mb-1">
        Offline Mode
      </div>
    </div>
  </aside>
  <div class="sidebar-backdrop" id="sidebarBackdrop"></div>

  <!-- ================= MAIN ================= -->
  <div class="main">
    <header class="topbar">
      <button class="btn btn-light d-lg-none" id="menuToggle">Menu</button>
      <div class="clock">
        <div class="clock-time" id="clockTime"></div>
        <div class="clock-date" id="clockDate"></div>
      </div>
      <div class="ms-auto d-flex align-items-center gap-3">
        <button class="btn btn-light position-relative">Alerts
          <?php if ($alertCount > 0): ?>
          <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill text-bg-danger"><?= $alertCount ?></span>
          <?php endif; ?>
        </button>
        <div class="d-flex align-items-center gap-2">
          <div class="avatar">AD</div>
          <div class="d-none d-md-block lh-1">
            <div style="font-size:.83rem;font-weight:700">Admin Sarah</div>
            <div style="font-size:.7rem;color:var(--muted)">Administrator</div>
          </div>
        </div>
      </div>
    </header>

    <div class="page" id="pageContainer">
