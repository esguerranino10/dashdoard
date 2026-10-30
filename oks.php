<?php
$pageTitle  = 'Dashboard';
$activePage = 'dashboard';
require __DIR__ . '/includes/header.php';

/* ---------- Dashboard numbers ---------- */
$today = date('Y-m-d');
$month = date('Y-m');

$customerCount = count(array_unique(array_column($orders, 'customer_id')));
$todayRevenue  = array_sum(array_map(fn($o) => $o['created'] === $today ? $o['paid'] : 0, $orders));
$monthRevenue  = array_sum(array_map(fn($o) => substr($o['created'], 0, 7) === $month ? $o['paid'] : 0, $orders));

/* Recent orders: newest first */
$recent = $orders;
usort($recent, fn($a, $b) => $b['id'] <=> $a['id']);
$recent = array_slice($recent, 0, 6);

/* Top services by revenue (add-ons are left out) */
$svcStats = [];
foreach ($orders as $o) {
    foreach ($o['items'] as $it) {
        [$sid, $qty, $price] = $it;
        if (!empty($services[$sid]['addon'])) continue;
        $svcStats[$sid]['revenue'] = ($svcStats[$sid]['revenue'] ?? 0) + $qty * $price;
        $svcStats[$sid]['orders'][$o['id']] = true;
    }
}
uasort($svcStats, fn($a, $b) => $b['revenue'] <=> $a['revenue']);
$topServices = array_slice($svcStats, 0, 5, true);
$topMax = $topServices ? max(array_column($topServices, 'revenue')) : 1;

/* Unclaimed laundry: ready orders, oldest first */
$unclaimed = array_values(array_filter($orders, fn($o) => $o['status'] === 'ready'));
usort($unclaimed, fn($a, $b) => strcmp($a['created'], $b['created']));

/* Machine status counts */
$machineCounts = array_count_values(array_column($machines, 'status'));

/* Summary cards */
$stats = [
    ['Total Orders',      count($orders),       $customerCount . ' customers',          'primary'],
    ['Active Orders',     $activeCount,         $readyCount . ' ready for pickup',      'info'],
    ["Today's Revenue",   peso($todayRevenue),  peso($monthRevenue) . ' this month',    'success'],
    ['Pending Payments',  $payPending,          count($unclaimed) . ' unclaimed order(s)', 'warning'],
];

?>

    <!-- ================= DASHBOARD ================= -->
    <div id="dashboard">
      <div class="page-head">
        <div>
          <h2>Dashboard Overview</h2>
          <p><?= date('l, F j, Y') ?></p>
        </div>
        <a class="btn btn-primary fw-semibold" href="orders.php">New Order</a>
      </div>

      <div class="row g-3 mb-3">
<?php foreach ($stats as [$label, $value, $sub, $color]): ?>
        <div class="col-6 col-lg-3">
          <div class="card h-100">
            <div class="card-body stat">
              <div class="min-w-0">
                <div class="lbl"><?= e($label) ?></div>
                <div class="val"><?= e($value) ?></div>
                <div class="trend text-<?= $color ?>" style="white-space:nowrap;overflow:hidden;text-overflow:ellipsis"><?= e($sub) ?></div>
              </div>
            </div>
          </div>
        </div>
<?php endforeach; ?>
      </div>

      <div class="row g-3">
        <div class="col-lg-8">

          <!-- Recent Orders -->
          <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span>Recent Orders</span>
              <a class="small text-decoration-none" href="orders.php">View all →</a>
            </div>
            <div class="table-responsive">
              <table class="table">
                <thead><tr><th>Order</th><th>Customer</th><th>Date</th><th>Status</th><th class="text-end">Amount</th></tr></thead>
                <tbody>
<?php foreach ($recent as $o): ?>
                  <tr>
                    <td><span class="fw-semibold font-monospace" style="font-size:.8rem"><?= e($o['code']) ?></span></td>
                    <td><?= e($customers[$o['customer_id']]['name']) ?></td>
                    <td class="text-body-secondary"><?= fdate($o['created']) ?></td>
                    <td>
                      <span class="pill pill-<?= e($o['status']) ?>"><?= e($statusLabels[$o['status']]) ?></span>
<?php if ($o['payment'] !== 'paid'): ?>
                      <span class="pill pill-<?= e($o['payment']) ?> ms-1"><?= e($paymentLabels[$o['payment']]) ?></span>
<?php endif; ?>
                    </td>
                    <td class="text-end fw-semibold"><?= peso($o['total']) ?></td>
                  </tr>
<?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>

          <!-- Top Services -->
          <div class="card">
            <div class="card-header">Top Services</div>
            <div class="card-body">
<?php foreach ($topServices as $sid => $s): ?>
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-semibold small"><?= e($services[$sid]['name']) ?></span>
                  <span class="text-body-secondary small"><?= count($s['orders']) ?> order(s) · <?= peso($s['revenue']) ?></span>
                </div>
                <div class="bar-track"><div class="bar-fill" style="width:<?= round($s['revenue'] / $topMax * 100, 1) ?>%"></div></div>
              </div>
<?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="col-lg-4">

          <!-- Quick Actions -->
          <div class="card mb-3">
            <div class="card-header">Quick Actions</div>
            <div class="card-body d-grid gap-2">
              <a class="quick-btn text-decoration-none" href="orders.php">Register New Order</a>
              <a class="quick-btn text-decoration-none" href="customers.php">Add Customer</a>
              <a class="quick-btn text-decoration-none" href="queue.php">Monitor Machine Queue</a>
              <a class="quick-btn text-decoration-none" href="inventory.php">Check Inventory</a>
            </div>
          </div>

          <!-- Unclaimed Laundry -->
          <div class="card mb-3">
            <div class="card-header d-flex justify-content-between align-items-center">
              <span>Unclaimed Laundry</span>
              <span class="badge text-bg-warning"><?= count($unclaimed) ?></span>
            </div>
            <div class="card-body p-0">
<?php if (!$unclaimed): ?>
              <div class="empty">All caught up</div>
<?php endif; ?>
<?php foreach ($unclaimed as $o):
    $days = (int) floor((strtotime($today) - strtotime($o['created'])) / 86400); ?>
              <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom">
                <div>
                  <div class="fw-semibold small"><?= e($customers[$o['customer_id']]['name']) ?></div>
                  <div class="text-body-secondary" style="font-size:.72rem">
                    <span class="font-monospace"><?= e($o['code']) ?></span> · <?= $days ?> day(s)
                  </div>
                </div>
                <div class="text-end">
                  <div class="fw-bold small"><?= peso($o['total']) ?></div>
                  <a class="btn btn-sm btn-link p-0" style="font-size:.7rem" href="orders.php?q=<?= urlencode($o['code']) ?>">Open</a>
                </div>
              </div>
<?php endforeach; ?>
            </div>
          </div>

          <!-- Machine Status -->
          <div class="card">
            <div class="card-header">Machine Status</div>
            <div class="card-body">
<?php foreach ($machineLabels as $key => $label): ?>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small"><span class="pill pill-<?= $key ?>"><?= e($label) ?></span></span>
                <span class="fw-bold"><?= $machineCounts[$key] ?? 0 ?></span>
              </div>
<?php endforeach; ?>
            </div>
          </div>

        </div>
      </div>
    </div>


<?php require __DIR__ . '/includes/footer.php'; ?>
