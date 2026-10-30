<?php
$pageTitle  = 'Inventory';
$activePage = 'inventory';
require __DIR__ . '/includes/header.php';

$lowCount = count(array_filter($inventory, fn($i) => $i['stock'] <= $i['reorder']));
?>

      <div class="page-head">
        <div>
          <h2>Inventory</h2>
          <p><?= count($inventory) ?> items · <?= $lowCount ?> need restocking</p>
        </div>
        <button class="btn btn-primary fw-semibold">Add Item</button>
      </div>

      <div class="card">
        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr><th>Item</th><th>Unit</th><th class="text-end">In Stock</th><th class="text-end">Reorder Level</th><th>Status</th></tr>
            </thead>
            <tbody>
<?php foreach ($inventory as $i):
    if ($i['stock'] <= 0)                 { $cls = 'out'; $label = 'Out of stock'; }
    elseif ($i['stock'] <= $i['reorder']) { $cls = 'low'; $label = 'Low stock'; }
    else                                  { $cls = 'ok';  $label = 'In stock'; } ?>
              <tr>
                <td class="fw-semibold"><?= e($i['item']) ?></td>
                <td class="small"><?= e($i['unit']) ?></td>
                <td class="text-end fw-semibold"><?= $i['stock'] ?></td>
                <td class="text-end text-body-secondary"><?= $i['reorder'] ?></td>
                <td><span class="pill pill-<?= $cls ?>"><?= $label ?></span></td>
              </tr>
<?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

<?php require __DIR__ . '/includes/footer.php'; ?>
