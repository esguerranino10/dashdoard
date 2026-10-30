<?php
/* Sample data for House of Wash (shared by every page) */
date_default_timezone_set('Asia/Manila');

function daysAgo($n) { return date('Y-m-d', strtotime(($n >= 0 ? "-$n" : '+' . abs($n)) . ' days')); }
$codeDate = date('ymd');

$customers = [
    1 => ['name' => 'Marc jonelle De villa', 'phone' => '0917 111 2233'],
    2 => ['name' => 'Mark Jay Inumerable',   'phone' => '0917 222 3344'],
    3 => ['name' => 'Ana Reyes',             'phone' => '0917 333 4455'],
    4 => ['name' => 'Robert Lim',            'phone' => '0917 444 5566'],
    5 => ['name' => 'Josefina Cruz',         'phone' => '0917 555 6677'],
];

$services = [
    1 => ['name' => 'Wash & Fold',           'unit' => 'kg',    'price' => 70],
    2 => ['name' => 'Wash Only',             'unit' => 'kg',    'price' => 50],
    4 => ['name' => 'Iron & Fold',           'unit' => 'kg',    'price' => 40],
    5 => ['name' => 'Dry Clean',             'unit' => 'pc',    'price' => 150],
    6 => ['name' => 'Comforter / Blanket',   'unit' => 'pc',    'price' => 250],
    7 => ['name' => 'Extra Drying (10 min)', 'unit' => 'cycle', 'price' => 30,  'addon' => true],
    8 => ['name' => 'Stain Treatment',       'unit' => 'pc',    'price' => 80,  'addon' => true],
];

/* items: [service_id, qty, price] */
$orders = [
    ['id' => 1, 'code' => "HOW-{$codeDate}-001", 'customer_id' => 1, 'status' => 'delivered', 'payment' => 'paid',
     'items' => [[1, 8, 70], [7, 1, 30]], 'paid' => 590, 'created' => daysAgo(5), 'pickup' => daysAgo(3)],
    ['id' => 2, 'code' => "HOW-{$codeDate}-002", 'customer_id' => 2, 'status' => 'ready', 'payment' => 'unpaid',
     'items' => [[6, 2, 250]], 'paid' => 0, 'created' => daysAgo(4), 'pickup' => daysAgo(1)],
    ['id' => 3, 'code' => "HOW-{$codeDate}-003", 'customer_id' => 3, 'status' => 'washing', 'payment' => 'unpaid',
     'items' => [[1, 5, 70]], 'paid' => 0, 'created' => daysAgo(1), 'pickup' => daysAgo(-1)],
    ['id' => 4, 'code' => "HOW-{$codeDate}-004", 'customer_id' => 4, 'status' => 'pending', 'payment' => 'partial',
     'items' => [[2, 6, 50], [4, 6, 40]], 'paid' => 200, 'created' => daysAgo(1), 'pickup' => daysAgo(-2)],
    ['id' => 5, 'code' => "HOW-{$codeDate}-005", 'customer_id' => 5, 'status' => 'ready', 'payment' => 'unpaid',
     'items' => [[6, 1, 250], [8, 1, 80]], 'paid' => 0, 'created' => daysAgo(22), 'pickup' => daysAgo(20)],
    ['id' => 6, 'code' => "HOW-{$codeDate}-006", 'customer_id' => 1, 'status' => 'ready', 'payment' => 'paid',
     'items' => [[5, 3, 150]], 'paid' => 450, 'created' => daysAgo(3), 'pickup' => daysAgo(1)],
];

/* Work out the total of each order */
foreach ($orders as &$o) {
    $o['total'] = 0;
    foreach ($o['items'] as $it) {
        $o['total'] += $it[1] * $it[2];
    }
}
unset($o);

$statusLabels = [
    'pending' => 'Pending', 'washing' => 'Washing', 'drying' => 'Drying',
    'ready' => 'Ready', 'delivered' => 'Delivered', 'cancelled' => 'Cancelled',
];
$paymentLabels = ['unpaid' => 'Unpaid', 'partial' => 'Partial', 'paid' => 'Paid'];

$machines = [
    ['name' => 'W-01', 'status' => 'available'],
    ['name' => 'W-02', 'status' => 'available'],
    ['name' => 'W-03', 'status' => 'in_use'],
    ['name' => 'W-04', 'status' => 'available'],
    ['name' => 'W-05', 'status' => 'tub_clean'],
    ['name' => 'D-01', 'status' => 'available'],
    ['name' => 'D-02', 'status' => 'available'],
];
$machineLabels = ['available' => 'Available', 'in_use' => 'In Use', 'maintenance' => 'Maintenance', 'tub_clean' => 'Tub Cleaning'];

/* Inventory: item, unit, stock on hand, reorder level */
$inventory = [
    ['item' => 'Detergent',        'unit' => 'bottle', 'stock' => 25, 'reorder' => 10],
    ['item' => 'Fabric Softener',  'unit' => 'bottle', 'stock' => 18, 'reorder' => 10],
    ['item' => 'Bleach',           'unit' => 'bottle', 'stock' => 6,  'reorder' => 8],
    ['item' => 'Laundry Bags',     'unit' => 'pc',     'stock' => 40, 'reorder' => 20],
    ['item' => 'Hangers',          'unit' => 'pc',     'stock' => 60, 'reorder' => 30],
    ['item' => 'Stain Remover',    'unit' => 'bottle', 'stock' => 4,  'reorder' => 5],
];

/* Maintenance log: machine, task, date, status (scheduled / in_progress / done) */
$maintenance = [
    ['machine' => 'W-05', 'task' => 'Tub cleaning',          'date' => daysAgo(0),  'status' => 'in_progress'],
    ['machine' => 'W-03', 'task' => 'Routine inspection',    'date' => daysAgo(-3), 'status' => 'scheduled'],
    ['machine' => 'D-01', 'task' => 'Lint filter cleaning',  'date' => daysAgo(2),  'status' => 'done'],
    ['machine' => 'W-01', 'task' => 'Drain hose replacement','date' => daysAgo(9),  'status' => 'done'],
    ['machine' => 'D-02', 'task' => 'Belt check',            'date' => daysAgo(-7), 'status' => 'scheduled'],
];
$maintenanceLabels = ['scheduled' => 'Scheduled', 'in_progress' => 'In Progress', 'done' => 'Done'];
$maintenancePill   = ['scheduled' => 'pending', 'in_progress' => 'washing', 'done' => 'paid'];

function fdate($d)  { return $d ? date('M d, Y', strtotime($d)) : '—'; }
function peso($n)   { return '₱' . number_format($n, 2); }
function e($s)      { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
