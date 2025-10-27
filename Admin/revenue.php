<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Default: no filter
$whereClause = "order_status='Delivered'";

$title = "All Time Revenue";

// Handle filters
if (isset($_GET['filter'])) {
    if ($_GET['filter'] === "month" && !empty($_GET['month'])) {
        $month = $conn->real_escape_string($_GET['month']); // YYYY-MM
        $whereClause = "order_status='Delivered' AND DATE_FORMAT(order_date, '%Y-%m') = '$month'";
        $title = "Revenue for " . date("F Y", strtotime($month . "-01"));
    } elseif ($_GET['filter'] === "week" && !empty($_GET['week'])) {
        $week = intval($_GET['week']);
        $year = date("Y");
        $whereClause = "order_status='Delivered' AND WEEK(order_date, 1) = $week AND YEAR(order_date) = $year";
        $title = "Revenue for Week $week ($year)";
    } elseif ($_GET['filter'] === "range" && !empty($_GET['start']) && !empty($_GET['end'])) {
        $start = $conn->real_escape_string($_GET['start']);
        $end = $conn->real_escape_string($_GET['end']);
        $whereClause = "order_status='Delivered' AND DATE(order_date) BETWEEN '$start' AND '$end'";
        $title = "Revenue from $start to $end";
    }
}


// --- total revenue ---
$totalQuery = $conn->query("SELECT SUM(total_amount - discount_amount) AS total_revenue FROM order_master WHERE $whereClause");
$totalRevenue = $totalQuery->fetch_assoc()['total_revenue'] ?? 0;

// --- monthly revenue (last 12 months) ---
$monthlyQuery = $conn->query("
    SELECT DATE_FORMAT(order_date, '%Y-%m') AS month, 
           SUM(total_amount - discount_amount) AS revenue
    FROM order_master
    WHERE $whereClause
    GROUP BY DATE_FORMAT(order_date, '%Y-%m')
    ORDER BY month ASC
");
$monthlyData = [];
while ($row = $monthlyQuery->fetch_assoc()) {
    $monthlyData[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Revenue Report</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { background-color: #121212; color: #fff; }
        .card {
            border-radius: 12px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.4); }
        label { font-weight: 600; }
        /* Make "By Range" button as wide as the combined From + To inputs */
        #end + button[name="filter"][value="range"] {
            width: 100%;
        }

    </style>
</head>
<body>
<div class="container my-5">
    <!-- Back to Admin Panel Button (Top Right) -->
    <div style="position: absolute; top: 2; right: 105px;">
        <a href="index.php" class="btn btn-success">⬅ Back to Admin Panel</a>
    </div>

    <h2 class="mb-4 text-center">📊 Revenue Dashboard</h2>

    <!-- Filter Form -->
    <form method="GET" class="row g-3 mb-4 text-dark bg-light p-3 rounded">
        <!-- Month -->
        <div class="col-md-3">
            <label for="month">Month</label>
            <input type="month" name="month" id="month" class="form-control" min="2025-01" max="<?= date('Y-m') ?>" style="user-select: none;">
            <button type="submit" name="filter" value="month" class="btn btn-primary w-100 mt-2">By Month</button>
        </div>

        <!-- Week -->
        <div class="col-md-2">
            <label for="week">Week</label>
            <input type="week" name="week" id="week" class="form-control" min="2025-W01" max="<?= date('Y-\WW') ?>" style="user-select: none;">
            <button type="submit" name="filter" value="week" class="btn btn-warning w-100 mt-2">By Week</button>
        </div>

        <!-- Date Range -->
        <div class="col-md-6">
            <label for="date-range">Date Range</label>
            <div class="input-group">
                <span class="input-group-text from-label">From</span>
                <input type="date" name="start" id="start" class="form-control" 
                       min="2020-01-01" max="<?= date('Y-m-d') ?>" style="user-select: none;">
                <span class="input-group-text to-label">To</span>
                <input type="date" name="end" id="end" class="form-control" 
                       min="2020-01-01" max="<?= date('Y-m-d') ?>" style="user-select: none;">
            </div>
            <button type="submit" name="filter" value="range" class="btn btn-success w-100 mt-2">By Range</button>
        </div>

        <script>
        // Inputs
        const startInput = document.getElementById('start');
        const endInput = document.getElementById('end');

        // Open picker when clicking on the input itself
        [startInput, endInput].forEach(input => {
            input.addEventListener('click', () => input.showPicker?.());
        });

        // Open picker when clicking on the corresponding label
        document.querySelector('.from-label').addEventListener('click', () => startInput.showPicker?.());
        document.querySelector('.to-label').addEventListener('click', () => endInput.showPicker?.());

        // Prevent text selection
        [startInput, endInput].forEach(input => {
            input.addEventListener('mousedown', (e) => e.preventDefault());
        });
        </script>
        <script>
        // Month & Week inputs
        const monthInput = document.getElementById('month');
        const weekInput = document.getElementById('week');

        // Open picker on click
        [monthInput, weekInput].forEach(input => {
            input.addEventListener('click', () => input.showPicker?.());
            input.addEventListener('mousedown', e => e.preventDefault()); // block text selection
        });
        </script>

        <!-- Reset Button -->
        <div class="col-md-1 d-flex align-items-end">
            <a href="<?= basename($_SERVER['PHP_SELF']) ?>" class="btn btn-secondary w-100">Reset</a>
        </div>
    </form>

    <!-- Total Revenue Card -->
    <div class="card bg-success text-white mb-4">
        <div class="card-body text-center">
            <h4><?= htmlspecialchars($title) ?></h4>
            <p class="fs-2">₹<?= number_format($totalRevenue, 2) ?></p>
        </div>
    </div>

    <!-- Monthly Revenue Chart -->
    <div class="card bg-dark text-white">
        <div class="card-body">
            <h4 class="mb-3">Revenue Chart</h4>
            <canvas id="revenueChart"></canvas>
        </div>
    </div>
</div>

<script>
const ctx = document.getElementById('revenueChart');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($monthlyData, 'month')) ?>,
        datasets: [{
            label: 'Revenue (₹)',
            data: <?= json_encode(array_column($monthlyData, 'revenue')) ?>,
            backgroundColor: 'rgba(0, 200, 255, 0.7)',
            borderRadius: 6
        }]
    },
    options: {
        responsive: true,
        plugins: { 
            legend: { display: false },
            tooltip: { callbacks: { label: (ctx) => "₹ " + ctx.formattedValue } }
        },
        scales: {
            x: { ticks: { color: "#ccc" }, grid: { color: "#333" } },
            y: { beginAtZero: true, ticks: { color: "#ccc" }, grid: { color: "#333" } }
        }
    }
});
</script>

</body>
</html>
