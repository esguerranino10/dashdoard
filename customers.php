<?php
$pageTitle  = 'Customers';
$activePage = 'customers';
require __DIR__ . '/includes/header.php';

/* Build one row per customer from the orders */
$rows = [];
foreach ($customers as $id => $c) {
    $r = $c + ['id' => $id, 'orders' => 0, 'spent' => 0, 'balance' => 0, 'last' => ''];
    foreach ($orders as $o) {
        if ($o['customer_id'] !== $id || $o['status'] === 'cancelled') continue;
        $r['orders']++;
        $r['spent']   += $o['total'];
        $r['balance'] += max(0, $o['total'] - $o['paid']);
        if ($o['created'] > $r['last']) $r['last'] = $o['created'];
    }
    $rows[] = $r;
}

$q = strtolower(trim($_GET['q'] ?? ''));
if ($q !== '') {
    $rows = array_filter($rows, fn($r) => strpos(strtolower($r['name'] . ' ' . $r['phone']), $q) !== false);
}
usort($rows, fn($a, $b) => $b['spent'] <=> $a['spent']);
?>

      <div class="page-head">
        <div>
          <h2>Customers</h2>
          <p><?= count($rows) ?> customer(s)</p>
        </div>
        <button class="btn btn-primary fw-semibold">Add Customer</button>
      </div>

      <form class="card mb-3" method="get" action="customers.php">
        <div class="card-body">
          <div class="row g-2 align-items-center">
            <div class="col-md-8">
              <input type="text" class="form-control" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Search by name or phone…">
            </div>
            <div class="col-md-4 d-flex gap-2 justify-content-md-end">
              <button class="btn btn-primary btn-sm" type="submit">Search</button>
              <a class="btn btn-light btn-sm" href="customers.php">Reset</a>
            </div>
          </div>
        </div>
      </form>

      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr><th>Customer</th><th>Phone</th><th class="text-end">Orders</th><th class="text-end">Total Spent</th><th class="text-end">Balance Due</th><th>Last Order</th></tr>
            </thead>
            <tbody>
<?php if (!$rows): ?>
              <tr><td colspan="6" class="empty">No customers found</td></tr>
<?php endif; ?>
<?php foreach ($rows as $r): ?>
              <tr>
                <td class="fw-semibold"><?= e($r['name']) ?></td>
                <td class="small"><?= e($r['phone']) ?></td>
                <td class="text-end"><?= $r['orders'] ?></td>
                <td class="text-end fw-semibold"><?= peso($r['spent']) ?></td>
                <td class="text-end <?= $r['balance'] > 0 ? 'text-danger fw-semibold' : 'text-success' ?>"><?= $r['balance'] > 0 ? peso($r['balance']) : 'Settled' ?></td>
                <td class="small text-body-secondary"><?= fdate($r['last']) ?></td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
