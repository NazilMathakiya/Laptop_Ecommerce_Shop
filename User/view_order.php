<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user orders
$sql = "SELECT * FROM order_master WHERE user_id = ? ORDER BY order_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Store and clear the session popup message
$popup_msg = '';
if (isset($_SESSION['msg'])) {
    $popup_msg = $_SESSION['msg'];
    unset($_SESSION['msg']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Your Orders - LAPCART</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #fff;
        }
        .nav-buttons {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 20px;
        }
        .table {
            background-color: #1e1e1e;
            color: #ffffff;
        }
        .table-dark th,
        .table-dark td,
        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #1a1a1a;
        }
        .table-dark th {
            background-color: #242424;
            color: #fff;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .popup-msg {
            transition: all 0.5s ease;
        }
    </style>
</head>
<body>
<div class="container mt-4">

    <?php if (!empty($popup_msg)) : ?>
        <div class="alert alert-success text-dark popup-msg" id="popupMessage"><?= $popup_msg; ?></div>
    <?php endif; ?>

    <div class="nav-buttons">
        <a href="shop.php" class="btn btn-success">← Back to Shop</a>
    </div>

    <h2 class="mb-4">Your Orders</h2>

    <?php if ($result->num_rows > 0): ?>
        <div class="table-responsive">
            <table class="table table-bordered table-dark table-striped">
                <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Order Date</th>
                    <th>Total Amount (₹)</th>
                    <th>Status</th>
                    <th>Payment Mode</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['order_id'] ?></td>
                        <td><?= date("d M Y, h:i A", strtotime($row['order_date'])) ?></td>
                        <td><?= $row['total_amount'] ?></td>
                        <td><?= $row['order_status'] ?></td>
                        <td><?= $row['payment_mode'] ?></td>
                        <td>
                            <a href="show_order_details.php?order_id=<?= $row['order_id'] ?>" class="btn btn-sm btn-primary">Show Details</a>
                            
                            <a href="delete_order.php?order_id=<?= $row['order_id'] ?>"
                               onclick="return confirm('Are you sure you want to cancel this order?')"
                               class="btn btn-sm btn-danger mt-1">Cancel</a>
                        </td>

                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-warning text-dark">You have not placed any orders yet.</div>
    <?php endif; ?>
</div>

<script>
    // Auto hide popup after 3 seconds
    setTimeout(() => {
        const popup = document.getElementById('popupMessage');
        if (popup) {
            popup.style.opacity = '0';
            popup.style.marginBottom = '0px';
            setTimeout(() => popup.remove(), 500); // remove element after fade out
        }
    }, 3000);
</script>
</body>
</html>
