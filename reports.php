<?php
$pageTitle  = 'Reports';
$activePage = 'reports';
require __DIR__ . '/includes/header.php';

$valid = array_filter($orders, fn($o) => $o['status'] !== 'cancelled');

$totalSales  = array_sum(array_column($valid, 'total'));
$collected   = array_sum(array_column($valid, 'paid'));
$outstanding = $totalSales - $collected;

/* Revenue per service */
$byService = [];
foreach ($valid as $o) {
    foreach ($o['items'] as [$sid, $qty, $price]) {
        $byService[$sid] = ($byService[$sid] ?? 0) + $qty * $price;
    }
}
arsort($byService);
$maxService = $byService ? max($byService) : 1;

/* Orders per status */
$byStatus = array_count_values(array_column($orders, 'status'));

$cards = [
    ['Total Sales',  peso($totalSales),  count($valid) . ' order(s)', 'primary'],
    ['Collected',    peso($collected),   'payments received',        'success'],
    ['Outstanding',  peso($outstanding), 'still to be paid',         'warning'],
    ['Customers',    count($customers),  'on record',                'info'],
];
?>

      <div class="page-head">
        <div>
          <h2>Reports</h2>
          <p>Sales summary from all orders.</p>
        </div>
      </div>

      <div class="row g-3 mb-3">
<?php foreach ($cards as [$label, $value, $sub, $color]): ?>
        <div class="col-6 col-lg-3">
          <div class="card h-100">
            <div class="card-body stat">
              <div class="min-w-0">
                <div class="lbl"><?= e($label) ?></div>
                <div class="val"><?= e($value) ?></div>
                <div class="trend text-<?= $color ?>"><?= e($sub) ?></div>
              </div>
            </div>
          </div>
        </div>
<?php endforeach; ?>
      </div>

      <div class="row g-3">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">Revenue by Service</div>
            <div class="card-body">
<?php foreach ($byService as $sid => $rev): ?>
              <div class="mb-3">
                <div class="d-flex justify-content-between mb-1">
                  <span class="fw-semibold small"><?= e($services[$sid]['name']) ?></span>
                  <span class="text-body-secondary small"><?= peso($rev) ?></span>
                </div>
                <div class="bar-track"><div class="bar-fill" style="width:<?= round($rev / $maxService * 100, 1) ?>%"></div></div>
              </div>
<?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">Orders by Status</div>
            <div class="card-body">
<?php foreach ($statusLabels as $key => $label): ?>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="pill pill-<?= $key ?>"><?= e($label) ?></span>
                <span class="fw-bold"><?= $byStatus[$key] ?? 0 ?></span>
              </div>
<?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
