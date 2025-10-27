<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_GET['order_id'])) {
    die("Order ID not provided.");
}

$order_id = intval($_GET['order_id']);

// Fetch order master
$order_sql = "SELECT * FROM order_master WHERE order_id = ?";
$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $order_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

if ($order_result->num_rows != 1) {
    die("Order not found.");
}

$order = $order_result->fetch_assoc();

// Fetch product details
$item_sql = "SELECT oi.quantity, p.product_name, p.product_price 
             FROM order_items oi
             JOIN product_master p ON oi.product_id = p.product_id
             WHERE oi.order_id = ?";
$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("i", $order_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Order #<?= $order_id ?> Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    body { background-color: #121212; color: #fff; }
    .card { background-color: #1e1e1e; border: 1px solid #333; }
    .card-header { background-color: #2a2a2a; color: #457535; font-weight: bold; }

    /* Progress bar container */
    .progress-track {
        position: relative;
        height: 80px;  /* ⬅️ was 8px, now plenty of room for truck */
        background:#ddd;
        border-radius:6px;
        margin:60px 0;
    }
    .progress-fill {
        background: <?= $statusColor ?>;
        height: 6px;
        border-radius: 10px;
        transition: width 0.7s ease, background-color 0.5s ease;
    }
    .truck-icon {
        position: absolute;
        top: -28px;
        font-size: 30px;
        transition: left 0.7s ease, color 0.5s ease;
    }
    /* 🚚 Animate truck only on "Shipped" */
    .truck-icon.shipped {
        animation: bounce 1s infinite ease-in-out, wiggle 2s infinite ease-in-out;
    }
    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }
    @keyframes wiggle {
        0%, 100% { transform: rotate(0); }
        25% { transform: rotate(-5deg); }
        75% { transform: rotate(5deg); }
    }
    .status-labels {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        font-size: 14px;
    }
    .status-labels span {
        text-align: center;
        flex: 1;
    }

    .status-labels .active {
        color: #28a745;
        font-weight: bold;
    }
    .status-labels i {
        display: block;
        font-size: 18px;
        margin-bottom: 5px;
    }
</style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-end">
        <a href="view_order.php" class="btn btn-success mb-4">← Back to Orders</a>
    </div>

    <div class="card">
        <div class="card-header">Order Tracking</div>
        <div class="card-body text-center">

            <?php
            $steps = [
                'Pending' => '<i class="fas fa-clipboard-list"></i>',
                'Processing' => '<i class="fas fa-cogs"></i>',
                'Shipped' => '<i class="fas fa-industry"></i>',
                'Delivered' => '<i class="fas fa-house-user"></i>'
            ];
            $iconColors = [
                'Pending'    => '#ffc107', // yellow
                'Processing' => '#17a2b8', // teal
                'Shipped'    => '#007bff', // blue
                'Delivered'  => '#28a745'  // green
            ];


            $current_status = ucfirst(strtolower(trim($order['order_status'])));
            $status_index = array_search($current_status, array_keys($steps));
            $is_cancelled = (strtolower($order['order_status']) === 'cancelled');
            $statusColor = ($current_status === 'Pending') ? '#ffc107' :
               (($current_status === 'Processing') ? '#17a2b8' :
               (($current_status === 'Shipped') ? '#007bff' : '#28a745'));
            ?>

            <?php if ($is_cancelled): ?>
                <div class="alert alert-danger">❌ This order has been <b>Cancelled</b>.</div>

            <?php else: ?>
                <div class="progress-track" style="position:relative; height:8px; background:#ddd; border-radius:6px; margin:60px 0;">
                    <?php
                        // Fixed positions for both bar and truck
                        $step_positions = [10, 33, 66, 100];
                        $truck_left = $step_positions[$status_index];
                        $bar_width  = $step_positions[$status_index]; // 👈 bar width matches truck
                        ?>

                        <!-- Filled portion -->
                        <div style="
                            position:absolute;
                            height:100%;
                            border-radius:6px;
                            background: <?= $current_status === 'Pending' ? '#ffc107' : ($current_status === 'Processing' ? '#17a2b8' : ($current_status === 'Shipped' ? '#007bff' : '#28a745')) ?>;
                            width:<?= $bar_width ?>%;
                        "></div>

                        <!-- Truck -->
                        <div style="
                            position:absolute;
                            top:-40px;
                            left:calc(<?= $truck_left ?>% - 15px);
                            font-size:30px;
                            animation:bounce 1s infinite;
                        ">
                            <i class="fas fa-truck" style="color:<?= $statusColor ?>;"></i>
                        </div>


                </div>

                <!-- Step icons -->
                <div class="d-flex justify-content-between">
                    <?php
                    $i = 0;
                    foreach ($steps as $label => $icon):
                        $active = $i <= $status_index;
                        $iconColor = $iconColors[$label]; // pick correct color
                    ?>
                        <div class="text-center flex-fill">
                            <div style="font-size:28px; color:<?= $active ? $iconColor : '#aaa' ?>;">
                                <?= $icon ?>
                            </div>
                            <small style="color:<?= $active ? $iconColor : '#777' ?>;"><?= $label ?></small>
                        </div>
                    <?php $i++; endforeach; ?>
                </div>


            <?php endif; ?>
        </div>
    </div>

    <style>
    @keyframes bounce {
        0%,100% { transform:translateY(0); }
        50% { transform:translateY(-6px); }
    }
    </style>


    <br>

    <div class="card mb-4">
        <div class="card-header">Order #<?= $order_id ?> Details</div>
        <div class="card-body">
            <p><strong style="color:#993723">Name:</strong> <span style="color:#ccc"><?= htmlspecialchars($order['full_name']) ?></span></p>
            <p><strong style="color:#993723">Email:</strong> <span style="color:#ccc"><?= htmlspecialchars($order['user_email']) ?></span></p>
            <p><strong style="color:#993723">Mobile:</strong> <span style="color:#ccc"><?= htmlspecialchars($order['user_mobile_no']) ?></span></p>
            <p><strong style="color:#993723">Delivery Address:</strong> <span style="color:#ccc"><?= nl2br(htmlspecialchars($order['delivery_address'])) ?></span></p>
            <p><strong style="color:#993723">Payment Mode:</strong> <span style="color:#ccc"><?= htmlspecialchars($order['payment_mode']) ?></span></p>
            <p><strong style="color:#993723">Subtotal:</strong> 
                <span style="color:#ccc">₹<?= number_format($order['total_amount'] + $order['discount_amount'], 2) ?></span>
            </p>

            <?php if ($order['discount_amount'] > 0): ?>
                <p><strong style="color:#993723">Discount (<?= htmlspecialchars($order['coupon_code']) ?>):</strong> 
                    <span style="color:#ff4444">-₹<?= number_format($order['discount_amount'], 2) ?></span>
                </p>
            <?php endif; ?>

            <p><strong style="color:#993723">Total Amount:</strong> 
                <span style="color:#ccc">₹<?= number_format($order['total_amount'], 2) ?></span>
            </p>

            <p><strong style="color:#993723">Order Date:</strong> <span style="color:#ccc"><?= date("d M Y, h:i A", strtotime($order['order_date'])) ?></span></p>
            <p><strong style="color:#993723">Status:</strong> 
                <span class="badge bg-<?= $is_cancelled ? 'danger' : 'success' ?>">
                    <?= htmlspecialchars($order['order_status']) ?>
                </span>
            </p>
        </div>
    </div>

    <div class="mb-3 text-center">
        <?php if(strtolower($order['order_status']) != 'cancelled'): ?>

            <a href="delete_order.php?order_id=<?= $order_id ?>" 
               class="btn btn-danger" 
               onclick="return confirm('Are you sure you want to cancel this order?');">
               Cancel Order
            </a>
        <?php endif; ?>
    </div>


    <div class="card mb-4">
        <div class="card-header">Products Ordered</div>
        <div class="card-body">
            <table class="table table-bordered table-dark text-center">
                <thead>
                    <tr>
                        <th>Product Name</th>
                        <th>Price (₹)</th>
                        <th>Quantity</th>
                        <th>Subtotal (₹)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        $grand_total = 0;
                        while ($item = $item_result->fetch_assoc()): 
                            $subtotal = $item['product_price'] * $item['quantity'];
                            $grand_total += $subtotal;
                        ?>
                            <tr>
                                <td><?= htmlspecialchars($item['product_name']) ?></td>
                                <td><?= number_format($item['product_price'], 2) ?></td>
                                <td><?= $item['quantity'] ?></td>
                                <td><?= number_format($subtotal, 2) ?></td>
                            </tr>
                        <?php endwhile; ?>

                        <!-- Show product total -->
                        <tr>
                            <td colspan="3" class="text-end"><strong>Products Total</strong></td>
                            <td><strong>₹<?= number_format($grand_total, 2) ?></strong></td>
                        </tr>

                        <!-- If coupon applied, show it -->
                        <?php if (!empty($order['coupon_code']) && $order['discount_amount'] > 0): ?>
                        <tr>
                            <td colspan="3" class="text-end text-warning">
                                <strong>Coupon (<?= htmlspecialchars($order['coupon_code']) ?>)</strong>
                            </td>
                            <td class="text-warning">- ₹<?= number_format($order['discount_amount'], 2) ?></td>
                        </tr>
                        <?php endif; ?>

                        <!-- Final total (after discount) -->
                        <tr>
                            <td colspan="3" class="text-end"><strong>Final Total</strong></td>
                            <td><strong>₹<?= number_format($order['total_amount'], 2) ?></strong></td>
                        </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
