<?php
$pageTitle  = 'Queue';
$activePage = 'queue';
require __DIR__ . '/includes/header.php';

/* Orders still being worked on, earliest pickup first */
$queue = array_values(array_filter($orders, fn($o) => in_array($o['status'], ['pending', 'washing', 'drying'])));
usort($queue, fn($a, $b) => strcmp($a['pickup'], $b['pickup']));
?>

      <div class="page-head">
        <div>
          <h2>Queue</h2>
          <p>Orders that are waiting or being processed, earliest pickup first.</p>
        </div>
        <a class="btn btn-primary fw-semibold" href="orders.php">View Orders</a>
      </div>

      <div class="row g-3">
        <div class="col-lg-8">
          <div class="card">
            <div class="card-header">In Progress (<?= count($queue) ?>)</div>
            <div class="table-responsive">
              <table class="table">
                <thead><tr><th>#</th><th>Order</th><th>Customer</th><th>Services</th><th>Status</th><th>Pickup</th></tr></thead>
                <tbody>
<?php if (!$queue): ?>
                  <tr><td colspan="6" class="empty">The queue is empty</td></tr>
<?php endif; ?>
<?php foreach ($queue as $i => $o): ?>
                  <tr>
                    <td class="fw-bold"><?= $i + 1 ?></td>
                    <td><span class="fw-semibold font-monospace" style="font-size:.8rem"><?= e($o['code']) ?></span></td>
                    <td><?= e($customers[$o['customer_id']]['name']) ?></td>
                    <td class="small"><?= e(implode(', ', array_map(fn($it) => $services[$it[0]]['name'], $o['items']))) ?></td>
                    <td><span class="pill pill-<?= e($o['status']) ?>"><?= e($statusLabels[$o['status']]) ?></span></td>
                    <td class="small text-body-secondary"><?= fdate($o['pickup']) ?></td>
                  </tr>
<?php endforeach; ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <div class="col-lg-4">
          <div class="card">
            <div class="card-header">Machines</div>
            <div class="card-body">
<?php foreach ($machines as $m): ?>
              <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="fw-semibold small"><?= e($m['name']) ?></span>
                <span class="pill pill-<?= e($m['status']) ?>"><?= e($machineLabels[$m['status']]) ?></span>
              </div>
<?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
