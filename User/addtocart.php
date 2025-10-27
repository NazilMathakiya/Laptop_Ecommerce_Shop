<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// User must be logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get stock for a product
function getStock($conn, $product_id) {
    $stmt = $conn->prepare("SELECT stock_quantity FROM product_master WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $stmt->bind_result($stock);
    $stmt->fetch();
    $stmt->close();
    return $stock;
}

// Add to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = intval($_POST['product_id']);
    $stock = getStock($conn, $product_id);

    $check = $conn->query("SELECT quantity FROM cart_master WHERE user_id = $user_id AND product_id = $product_id");
    if ($check->num_rows > 0) {
        $row = $check->fetch_assoc();
        if ($row['quantity'] < $stock) {
            $conn->query("UPDATE cart_master SET quantity = quantity + 1 WHERE user_id = $user_id AND product_id = $product_id");
        } else {
            $_SESSION['error'] = "Only $stock item(s) in stock.";
        }
    } else {
        $conn->query("INSERT INTO cart_master (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
    }
    header("Location: addtocart.php");
    exit;
}

// Increment
if (isset($_GET['inc'])) {
    $product_id = intval($_GET['inc']);
    $stock = getStock($conn, $product_id);
    $res = $conn->query("SELECT quantity FROM cart_master WHERE user_id = $user_id AND product_id = $product_id");
    if ($res && $row = $res->fetch_assoc()) {
        if ($row['quantity'] < $stock) {
            $conn->query("UPDATE cart_master SET quantity = quantity + 1 WHERE user_id = $user_id AND product_id = $product_id");
        } else {
            $_SESSION['error'] = "Only $stock item(s) in stock.";
        }
    }
    header("Location: addtocart.php");
    exit;
}

// Decrement
if (isset($_GET['dec'])) {
    $product_id = intval($_GET['dec']);
    $res = $conn->query("SELECT quantity FROM cart_master WHERE user_id = $user_id AND product_id = $product_id");
    if ($res && $row = $res->fetch_assoc()) {
        if ($row['quantity'] > 1) {
            $conn->query("UPDATE cart_master SET quantity = quantity - 1 WHERE user_id = $user_id AND product_id = $product_id");
        } else {
            $conn->query("DELETE FROM cart_master WHERE user_id = $user_id AND product_id = $product_id");
        }
    }
    header("Location: addtocart.php");
    exit;
}

// Remove item
if (isset($_GET['remove'])) {
    $product_id = intval($_GET['remove']);
    $conn->query("DELETE FROM cart_master WHERE user_id = $user_id AND product_id = $product_id");
    $_SESSION['delete_success'] = "Item removed from cart.";
    header("Location: addtocart.php");
    exit;
}

// Fetch cart items
$query = "
    SELECT c.product_id, c.quantity, p.product_name, p.product_price, p.stock_quantity, p.image_path
    FROM cart_master c
    JOIN product_master p ON c.product_id = p.product_id
    WHERE c.user_id = $user_id
";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #121212;
            color: #ffffff;
        }
        .navbar { background-color: #1f1f1f; }
        .navbar-brand { font-size: 24px; font-weight: bold; color: lightgreen; }
        .nav-link, .navbar-brand:hover { color: #ffffff; }
        .btn-lightgreen { background-color: #28a745; color: #fff; }
        .btn-lightgreen:hover { background-color: #218838; }
        .quantity-btn { font-size: 1rem; padding: 2px 10px; }
        .oval-bg {
            background: radial-gradient(circle at center, #1e1e1e 0%, #2c2c2c 100%);
            padding: 30px;
            border-radius: 50px;
            box-shadow: 0 0 30px rgba(0, 255, 0, 0.15);
        }
        .table td, .table th { vertical-align: middle; }
        .logo-oval {
            background-color: #3A570F;
            border-radius: 50px;
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            padding-left: 20px;
            padding-right: 20px;
            transition: background-color 0.3s ease;
        }
        .logo-oval:hover { background-color: #218838; color: #fff; }
    </style>
</head>
<body>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-dark px-4">
    <a class="navbar-brand d-flex align-items-center" href="shop.php">
        <span class="logo-oval px-4 py-2">LAPCART</span>
    </a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-3">
            <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
            <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
            <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
            <li class="nav-item"><a class="nav-link" href="help.php">Help</a></li>
        </ul>
        <ul class="navbar-nav ms-auto">
            <li class="nav-item"><a class="nav-link" href="profile.php">My Account</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-5">
    <div class="oval-bg">
        <h2 class="mb-4">🛒 Your Cart</h2>

        <!-- Show error popup -->
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <!-- Show delete success popup -->
        <?php if (isset($_SESSION['delete_success'])): ?>
            <div id="deletePopup" class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $_SESSION['delete_success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['delete_success']); ?>
        <?php endif; ?>

        <?php if ($result->num_rows > 0): ?>
            <table class="table table-bordered table-dark table-striped">
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Price (₹)</th>
                        <th>Quantity</th>
                        <th>Subtotal (₹)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; while ($item = $result->fetch_assoc()):
                        $subtotal = $item['product_price'] * $item['quantity'];
                        $total += $subtotal;
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($item['product_name']) ?></td>
                        <td><?= number_format($item['product_price'], 2) ?></td>
                        <td>
                            <a href="?dec=<?= $item['product_id'] ?>" class="btn btn-warning btn-sm quantity-btn">-</a>
                            <?= $item['quantity'] ?>
                            <a href="?inc=<?= $item['product_id'] ?>" class="btn btn-success btn-sm quantity-btn">+</a>
                            <small class="text-muted">/ <?= $item['stock_quantity'] ?></small>
                        </td>
                        <td><?= number_format($subtotal, 2) ?></td>
                        <td><a href="?remove=<?= $item['product_id'] ?>" class="btn btn-danger btn-sm">Remove</a></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>

            <h4>Total: ₹<?= number_format($total, 2) ?></h4>
            <a href="checkout.php" class="btn btn-lightgreen mt-3">Proceed to Checkout</a>
        <?php else: ?>
            <div class="alert alert-info">Your cart is empty.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Auto hide delete success popup after 3.5 seconds
    setTimeout(function () {
        const alertBox = document.getElementById("deletePopup");
        if (alertBox) {
            alertBox.classList.remove("show");
            alertBox.classList.add("fade");
            setTimeout(() => alertBox.remove(), 500);
        }
    }, 3500);
</script>
</body>
</html>
