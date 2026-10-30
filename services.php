<?php
$pageTitle  = 'Services';
$activePage = 'services';
require __DIR__ . '/includes/header.php';

/* Orders and revenue per service */
$stats = [];
foreach ($orders as $o) {
    if ($o['status'] === 'cancelled') continue;
    foreach ($o['items'] as [$sid, $qty, $price]) {
        $stats[$sid]['revenue'] = ($stats[$sid]['revenue'] ?? 0) + $qty * $price;
        $stats[$sid]['orders'][$o['id']] = true;
    }
}
?>

      <div class="page-head">
        <div>
          <h2>Services</h2>
          <p>Price list and how each service is performing.</p>
        </div>
        <button class="btn btn-primary fw-semibold">Add Service</button>
      </div>

      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr><th>Service</th><th>Type</th><th>Unit</th><th class="text-end">Price</th><th class="text-end">Orders</th><th class="text-end">Revenue</th></tr>
            </thead>
            <tbody>
<?php foreach ($services as $sid => $s): ?>
              <tr>
                <td class="fw-semibold"><?= e($s['name']) ?></td>
                <td class="small text-body-secondary"><?= !empty($s['addon']) ? 'Add-on' : 'Main service' ?></td>
                <td class="small">per <?= e($s['unit']) ?></td>
                <td class="text-end"><?= peso($s['price']) ?></td>
                <td class="text-end"><?= count($stats[$sid]['orders'] ?? []) ?></td>
                <td class="text-end fw-semibold"><?= peso($stats[$sid]['revenue'] ?? 0) ?></td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
