<?php
$pageTitle  = 'Machines';
$activePage = 'machines';
require __DIR__ . '/includes/header.php';

$counts = array_count_values(array_column($machines, 'status'));
$colors = ['available' => 'success', 'in_use' => 'primary', 'maintenance' => 'warning', 'tub_clean' => 'secondary'];
?>

      <div class="page-head">
        <div>
          <h2>Machines</h2>
          <p><?= count($machines) ?> machines in total</p>
        </div>
        <a class="btn btn-primary fw-semibold" href="maintenance.php">Maintenance</a>
      </div>

      <div class="row g-3 mb-3">
<?php foreach ($machineLabels as $key => $label): ?>
        <div class="col-6 col-lg-3">
          <div class="card h-100">
            <div class="card-body stat">
              <div class="min-w-0">
                <div class="lbl"><?= e($label) ?></div>
                <div class="val"><?= $counts[$key] ?? 0 ?></div>
                <div class="trend text-<?= $colors[$key] ?>">machine(s)</div>
              </div>
            </div>
          </div>
        </div>
<?php endforeach; ?>
      </div>

      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead><tr><th>Machine</th><th>Type</th><th>Status</th></tr></thead>
            <tbody>
<?php foreach ($machines as $m): ?>
              <tr>
                <td class="fw-semibold"><?= e($m['name']) ?></td>
                <td><?= strpos($m['name'], 'W') === 0 ? 'Washer' : 'Dryer' ?></td>
                <td><span class="pill pill-<?= e($m['status']) ?>"><?= e($machineLabels[$m['status']]) ?></span></td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
