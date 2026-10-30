<?php
$pageTitle  = 'Orders';
$activePage = 'orders';
require __DIR__ . '/includes/header.php';

/* Statuses and payments used by the filters */
$statuses = $statusLabels;
$payments = $paymentLabels;

/* Flatten each order for the table */
$rows = [];
foreach ($orders as $o) {
    $c = $customers[$o['customer_id']];
    $o['customer'] = $c['name'];
    $o['phone']    = $c['phone'];
    $o['services'] = implode(', ', array_map(fn($it) => $services[$it[0]]['name'], $o['items']));
    $rows[] = $o;
}

/* Filters (GET) */
$q       = strtolower(trim($_GET['q'] ?? ''));
$fStatus = $_GET['status'] ?? '';
$fPay    = $_GET['payment'] ?? '';
if (!isset($statuses[$fStatus])) $fStatus = '';
if (!isset($payments[$fPay]))    $fPay = '';

$list = array_filter($rows, function ($o) use ($q, $fStatus, $fPay) {
    if ($q !== '' && strpos(strtolower($o['code'] . ' ' . $o['customer'] . ' ' . $o['phone']), $q) === false) return false;
    if ($fStatus !== '' && $o['status'] !== $fStatus) return false;
    if ($fPay !== '' && $o['payment'] !== $fPay) return false;
    return true;
});
usort($list, fn($a, $b) => $b['id'] <=> $a['id']);
?>

      <div class="page-head">
        <div>
          <h2>Orders</h2>
          <p>Create, monitor and track all laundry transactions.</p>
        </div>
        <button class="btn btn-primary fw-semibold">New Order</button>
      </div>

      <!-- Filters -->
      <form class="card mb-3" method="get" action="orders.php">
        <div class="card-body">
          <div class="row g-2 align-items-center">
            <div class="col-md-5">
              <input type="text" class="form-control" name="q" value="<?= e($_GET['q'] ?? '') ?>" placeholder="Order code, customer or phone…">
            </div>
            <div class="col-md-3">
              <select class="form-select" name="status" onchange="this.form.submit()">
                <option value="">All statuses</option>
                <?php foreach ($statuses as $k => $label): ?>
                  <option value="<?= $k ?>"<?= $fStatus === $k ? ' selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2">
              <select class="form-select" name="payment" onchange="this.form.submit()">
                <option value="">All payments</option>
                <?php foreach ($payments as $k => $label): ?>
                  <option value="<?= $k ?>"<?= $fPay === $k ? ' selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-2 d-flex align-items-center justify-content-md-end gap-2">
              <button class="btn btn-primary btn-sm" type="submit">Search</button>
              <a class="btn btn-light btn-sm" href="orders.php">Reset</a>
            </div>
          </div>
          <div class="text-body-secondary small mt-2"><?= count($list) ?> order(s)</div>
        </div>
      </form>

      <!-- Orders table -->
      <div class="card">
        <div class="table-wrap">
          <table class="table">
            <thead>
              <tr>
                <th>Order</th>
                <th>Customer</th>
                <th>Services</th>
                <th class="text-end">Total</th>
                <th class="text-end">Balance</th>
                <th>Status</th>
                <th>Payment</th>
                <th>Pickup</th>
              </tr>
            </thead>
            <tbody>
<?php if (!$list): ?>
              <tr><td colspan="8" class="empty">No orders match your filters</td></tr>
<?php else: ?>
<?php foreach ($list as $o): $balance = max(0, $o['total'] - $o['paid']); ?>
              <tr>
                <td>
                  <div class="fw-semibold font-monospace" style="font-size:.8rem"><?= e($o['code']) ?></div>
                  <div class="text-body-secondary" style="font-size:.72rem"><?= fdate($o['created']) ?></div>
                </td>
                <td>
                  <div class="fw-semibold"><?= e($o['customer']) ?></div>
                  <div class="text-body-secondary" style="font-size:.72rem"><?= e($o['phone']) ?></div>
                </td>
                <td class="small"><?= e($o['services']) ?></td>
                <td class="text-end fw-semibold"><?= peso($o['total']) ?></td>
                <td class="text-end <?= $balance > 0 ? 'text-danger fw-semibold' : 'text-success' ?>"><?= $balance > 0 ? peso($balance) : 'Settled' ?></td>
                <td><span class="pill pill-<?= e($o['status']) ?>"><?= e($statuses[$o['status']]) ?></span></td>
                <td><span class="pill pill-<?= e($o['payment']) ?>"><?= e($payments[$o['payment']]) ?></span></td>
                <td class="small text-body-secondary"><?= fdate($o['pickup']) ?></td>
              </tr>
<?php endforeach; ?>
<?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>


<?php require __DIR__ . '/includes/footer.php'; ?>
