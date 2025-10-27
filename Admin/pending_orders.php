<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$sql = "
    SELECT o.order_id, o.order_date, o.total_amount, o.total_quantity, o.payment_mode,
           o.full_name, o.user_email, o.user_mobile_no, o.delivery_address
    FROM order_master o
    WHERE o.order_status = 'Pending'
    ORDER BY o.order_date DESC
";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Pending Orders</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .btn-success, .btn-danger {
            width: 200px;
        }
    </style>
</head>
<body class="bg-dark text-light">
<div class="container mt-5">
    <!-- Back to Admin Panel Button (Top Right) -->
    <div style="position: absolute; top: 2; right: 120px;">
        <a href="index.php" class="btn btn-success">⬅ Back to Admin Panel</a>
    </div>
    <h2 class="mb-4">📦 Pending Orders</h2>
    <table class="table table-bordered table-dark table-hover">
        <thead class="table-warning text-dark">
            <tr>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Email</th>
                <th>Mobile</th>
                <th>Qty</th>
                <th>Total (₹)</th>
                <th>Payment</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['order_id'] ?></td>
                <td><?= $row['order_date'] ?></td>
                <td><?= htmlspecialchars($row['full_name']) ?></td>
                <td><?= $row['user_email'] ?></td>
                <td><?= $row['user_mobile_no'] ?></td>
                <td><?= $row['total_quantity'] ?></td>
                <td><?= number_format($row['total_amount'], 2) ?></td>
                <td><?= $row['payment_mode'] ?></td>
                <td><?= nl2br(htmlspecialchars($row['delivery_address'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
