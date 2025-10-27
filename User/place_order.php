<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first to place an order.'); window.location.href='login.php';</script>";
    exit();
}

$conn = new mysqli("localhost", "root", "", "laptop_store");

// Load PHPMailer classes
require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['name']);
    $user_email = trim($_POST['email']);
    $user_mobile_no = trim($_POST['mobile']);
    $delivery_address = trim($_POST['address']);
    $payment_mode = "COD";
    $user_id = $_SESSION['user_id'];

    $is_buy_now = isset($_POST['product_id']) && is_numeric($_POST['product_id']);
    $order_items = [];
    $total_amount = 0;
    $total_quantity = 0;

    if ($is_buy_now) {
        $product_id = intval($_POST['product_id']);
        $product = $conn->query("SELECT product_name, product_price, stock_quantity 
                                 FROM product_master 
                                 WHERE product_id = $product_id")->fetch_assoc();

        if (!$product) {
            echo "<script>alert('Product not found.'); window.location.href='shop.php';</script>";
            exit();
        }

        $price = floatval($product['product_price']);
        $stock = intval($product['stock_quantity']);
        $buy_quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;

        if ($buy_quantity > $stock) {
            echo "<script>alert('Not enough stock available.'); window.location.href='shop.php';</script>";
            exit();
        }

        $order_items[] = [
            'product_id' => $product_id,
            'quantity' => $buy_quantity,
            'price' => $price
        ];
        $total_amount = $price * $buy_quantity;
        $total_quantity = $buy_quantity;
    } else {
        $cart_result = $conn->query("SELECT cm.product_id, cm.quantity, pm.product_price AS price, pm.stock_quantity 
                                     FROM cart_master cm 
                                     JOIN product_master pm ON cm.product_id = pm.product_id 
                                     WHERE cm.user_id = $user_id");

        $cart_items = [];
        while ($row = $cart_result->fetch_assoc()) {
            $cart_items[] = $row;
        }

        if (empty($cart_items)) {
            echo "<script>alert('Your cart is empty!'); window.location.href='shop.php';</script>";
            exit();
        }

        foreach ($cart_items as $item) {
            $product_id = intval($item['product_id']);
            $quantity = intval($item['quantity']);
            $price = floatval($item['price']);
            $stock = intval($item['stock_quantity']);

            if ($quantity > $stock) {
                echo "<script>alert('Insufficient stock for product ID: $product_id'); window.location.href='addtocart.php';</script>";
                exit();
            }

            $order_items[] = [
                'product_id' => $product_id,
                'quantity' => $quantity,
                'price' => $price
            ];
            $total_amount += $price * $quantity;
            $total_quantity += $quantity;
        }
    }

    // ===== APPLY COUPON CODE =====
    $coupon_code = isset($_POST['coupon_code']) ? trim($_POST['coupon_code']) : '';
    $discount_amount = 0;

    if (!empty($coupon_code)) {
        $coupon = $conn->query("SELECT * FROM coupon_master WHERE coupon_code = '$coupon_code' AND status='active'");
        if ($coupon && $coupon->num_rows > 0) {
            $coupon_data = $coupon->fetch_assoc();

            if ($coupon_data['discount_type'] === 'fixed') {
                $discount_amount = $coupon_data['discount_value'];
            } elseif ($coupon_data['discount_type'] === 'percentage') {
                $discount_amount = ($total_amount * $coupon_data['discount_value']) / 100;
            }

            $total_amount -= $discount_amount;
            if ($total_amount < 0) {
                $total_amount = 0;
            }
        }
    }

    // Insert into order_master
    $stmt = $conn->prepare("INSERT INTO order_master 
    (user_id, total_amount, order_status, delivery_address, payment_mode, full_name, user_email, user_mobile_no, total_quantity, coupon_code, discount_amount) 
        VALUES (?, ?, 'Pending', ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("idsssssisd", 
        $user_id,         // i
        $total_amount,    // d
        $delivery_address,// s
        $payment_mode,    // s
        $full_name,       // s
        $user_email,      // s
        $user_mobile_no,  // s
        $total_quantity,  // i
        $coupon_code,     // s
        $discount_amount  // d
    );

    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order_items and update stock
    foreach ($order_items as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $order_id, $item['product_id'], $item['quantity']);
        $stmt->execute();
        $stmt->close();

        $stmt = $conn->prepare("UPDATE product_master SET stock_quantity = stock_quantity - ? WHERE product_id = ?");
        $stmt->bind_param("ii", $item['quantity'], $item['product_id']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear cart if not buy now
    if (!$is_buy_now) {
        $conn->query("DELETE FROM cart_master WHERE user_id = $user_id");
    }

    // Send confirmation email
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'armankhorajiyask@gmail.com';
        $mail->Password = 'gyvx ztpl knvj yebx'; // Gmail App Password
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('armankhorajiyask@gmail.com', 'Lapcart Store');
        $mail->addAddress($user_email, $full_name);

        $mail->isHTML(true);
        $mail->Subject = 'Order Confirmation - Lapcart';

        $mail->Body = "
            <h2>Thank you for your order!</h2>
            <p><strong>Order ID:</strong> #$order_id</p>
            <p><strong>Name:</strong> $full_name</p>
            <p><strong>Total Amount:</strong> ₹" . number_format($total_amount, 2) . "</p>";

        if (!empty($coupon_code) && $discount_amount > 0) {
            $mail->Body .= "<p><strong>Coupon Applied:</strong> $coupon_code</p>
                            <p><strong>Discount:</strong> ₹" . number_format($discount_amount, 2) . "</p>";
        }

        $mail->Body .= "
            <p><strong>Delivery Address:</strong> $delivery_address</p>
            <p><strong>Payment Mode:</strong> $payment_mode</p>
            <p>Your order has been placed successfully.</p>
            <b>Thanks From Team LAPCART 😊</b>
        ";

        $mail->send();
    } catch (Exception $e) {
        error_log("Email Error: " . $mail->ErrorInfo);
    }

    // Product list for popup
    $product_names = [];
    foreach ($order_items as $item) {
        $pid = $item['product_id'];
        $pinfo = $conn->query("SELECT product_name FROM product_master WHERE product_id = $pid")->fetch_assoc();
        if ($pinfo) {
            $product_names[] = $pinfo['product_name'];
        }
    }
    $product_list_display = implode(', ', $product_names);
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Order Placed</title>
    <style>
        body, html { margin:0; padding:0; height:100%; background:#121212; font-family:"Segoe UI",sans-serif; }
        .popup { position:fixed; top:50%; left:50%; transform:translate(-50%,-50%); 
                 background:#1f1f1f; padding:30px 40px; border-radius:20px; text-align:center; color:#fff; }
        .popup h2 { color:#00e676; margin-bottom:12px; }
        .tick { font-size:50px; color:#00e676; margin-bottom:15px; }
        .btn-view { margin-top:15px; padding:10px 20px; background:#00e676; border:none; color:#000; 
                    font-weight:bold; border-radius:8px; cursor:pointer; }
    </style>
</head>
<body>
    <div class="popup">
        <div class="tick">✔️</div>
        <h2>Order Placed Successfully!</h2>
        <p><strong>Products:</strong> <?= htmlspecialchars($product_list_display) ?></p>
        <p><strong>Total:</strong> ₹<?= number_format($total_amount, 2) ?></p>
        <?php if (!empty($coupon_code) && $discount_amount > 0): ?>
            <p><strong>Coupon Applied:</strong> <?= htmlspecialchars($coupon_code) ?></p>
            <p><strong>Discount:</strong> ₹<?= number_format($discount_amount, 2) ?></p>
        <?php endif; ?>
        <p>A confirmation email has been sent to you.</p>
        <form action="view_order.php" method="get">
            <input type="hidden" name="order_id" value="<?= $order_id ?>">
            <button class="btn-view" type="submit">View Order</button>
        </form>
    </div>
    <audio autoplay>
        <source src="https://www.myinstants.com/media/sounds/success-fanfare-trumpets.mp3" type="audio/mpeg">
    </audio>
    <script>
        setTimeout(() => { window.location.href = "shop.php"; }, 3500);
    </script>
</body>
</html>
