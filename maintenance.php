<?php
$pageTitle  = 'Maintenance';
$activePage = 'maintenance';
require __DIR__ . '/includes/header.php';

$log = $maintenance;
usort($log, fn($a, $b) => strcmp($b['date'], $a['date']));
?>

      <div class="page-head">
        <div>
          <h2>Maintenance</h2>
          <p>Cleaning, inspections and repairs for every machine.</p>
        </div>
        <button class="btn btn-primary fw-semibold">Schedule Task</button>
      </div>

      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead><tr><th>Machine</th><th>Task</th><th>Date</th><th>Status</th></tr></thead>
            <tbody>
<?php foreach ($log as $m): ?>
              <tr>
                <td class="fw-semibold"><?= e($m['machine']) ?></td>
                <td><?= e($m['task']) ?></td>
                <td class="small text-body-secondary"><?= fdate($m['date']) ?></td>
                <td><span class="pill pill-<?= e($maintenancePill[$m['status']]) ?>"><?= e($maintenanceLabels[$m['status']]) ?></span></td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
