<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_to_cart'])) {
    if (!isset($_SESSION['user_id'])) {
        echo "<script>alert('Please log in to add to cart'); window.location.href='login.php';</script>";
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);
    $quantity = max(1, intval($_POST['quantity']));

    $check = $conn->prepare("SELECT quantity FROM cart_master WHERE user_id = ? AND product_id = ?");
    $check->bind_param("ii", $user_id, $product_id);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $new_qty = $row['quantity'] + $quantity;
        $update = $conn->prepare("UPDATE cart_master SET quantity = ? WHERE user_id = ? AND product_id = ?");
        $update->bind_param("iii", $new_qty, $user_id, $product_id);
        $update->execute();
    } else {
        $insert = $conn->prepare("INSERT INTO cart_master (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert->bind_param("iii", $user_id, $product_id, $quantity);
        $insert->execute();
    }

    echo "<script>alert('Product added to cart successfully'); window.location.href='addtocart.php';</script>";
    exit();
}

// Fetch product details
if (!isset($_GET['product_id'])) {
    die("Product not found.");
}
$product_id = intval($_GET['product_id']);
$stmt = $conn->prepare("SELECT * FROM product_master WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$product_result = $stmt->get_result();

if ($product_result->num_rows === 0) {
    die("Product not found.");
}
$product = $product_result->fetch_assoc();

function getBrandName($conn, $brand_id) {
    $stmt = $conn->prepare("SELECT brand_name FROM brand_master WHERE brand_id = ?");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $stmt->bind_result($brand_name);
    $stmt->fetch();
    return $brand_name ?: "Unknown";
}
$brand_name = getBrandName($conn, $product['brand_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($product['product_name']) ?> - LAPCART</title>
    <!-- ✅ Bootstrap 5.3 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- ✅ Bootstrap 5.3 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #121212;
            color: #f1f1f1;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .navbar {
            background-color: #1c1c1c;
            box-shadow: 0 2px 8px rgba(0, 255, 100, 0.2);
        }
        .lapcart-logo {
            font-weight: bold;
            color: #ffffff !important;
            background-color: #2c2c2c;  
            border-radius: 25px;
            padding: 6px 18px;
        }
        .nav-link {
            color: #ddd !important;
            margin-right: 15px;
        }
        .nav-link:hover {
            color: #fff !important;
        }
        .dropdown-menu {
            background-color: #1c1c1c;
        }
        .dropdown-item {
            color: #ccc;
        }
        .dropdown-item:hover {
            background-color: #2d2d2d;
            color: #4fff8b;
        }
        .product-container {
            max-width: 1100px;
            margin: 40px auto;
            background-color: #1e1e1e;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 0 15px rgba(0, 255, 100, 0.15);
        }
        .product-image {
            width: 100%;
            max-height: 400px;
            object-fit: contain;
            border-radius: 10px;
            background-color: #2b2b2b;
            padding: 10px;
        }
        .btn-addcart,
        .btn-buynow,
        .btn-viewreviews {
            width: 100%;
            padding: 12px;
            font-weight: bold;
            border-radius: 8px;
            margin-top: 10px;
            transition: all 0.3s ease;
            font-size: 16px;
        }
        .btn-addcart {
            background-color: #198754;
            color: #fff;
            border: none;
        }
        .btn-addcart:hover {
            background-color: #157347;
        }
        .btn-buynow {
            background-color: #28a745;
            color: #fff;
            border: none;
        }
        .btn-buynow:hover {
            background-color: #218838;
        }
        .btn-viewreviews {
            background-color: #0d6efd;
            color: #fff;
            border: none;
            display: inline-block;
            text-align: center;
        }
        .btn-viewreviews:hover {
            background-color: #0b5ed7;
        }
        .lapcart-logo {
            background-color: #2a600c; /* Bootstrap success green */
            color: #fff;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 999px;
            font-size: 24px;
            letter-spacing: 1px;
            display: inline-block;
        }

        
    </style>
</head>
<body>

<!-- ✅ Unified Bootstrap 5.3 Navbar (copied from About Us) -->
<div class="sticky-top bg-dark shadow-sm">
    <nav class="navbar navbar-expand-lg bg-dark px-3">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <!-- 🔰 Logo + Nav -->
            <div class="d-flex align-items-center">
                <a href="shop.php" class="text-decoration-none">
                    <div class="lapcart-logo me-4">LAPCART</div>
                </a>
                <ul class="navbar-nav d-flex flex-row gap-3">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="aboutus.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                    <li class="nav-item"><a class="nav-link" href="help.php">Help</a></li>
                </ul>
            </div>

            <!-- 👤 Cart & Account -->
            <div class="d-flex align-items-center gap-3">
                <a href="addtocart.php" class="btn btn-outline-light position-relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                        class="bi bi-cart" viewBox="0 0 16 16">
                        <path
                            d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .49.598l-1.5 7A.5.5 0 0 1 13 13H4a.5.5 0 0 1-.49-.402L1.01 2H.5a.5.5 0 0 1-.5-.5zM4.415 6l1.313 6h6.544l1.313-6H4.415zM5.5 14a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z" />
                    </svg>
                </a>

                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown"
                        aria-expanded="false">
                        My Account
                    </button>
                    <ul class="dropdown-menu dropdown-menu-dark">
                        <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                        <li><a class="dropdown-item" href="view_order.php">My Orders</a></li>
                        <li><a class="dropdown-item" href="logout.php">Logout</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>
</div>


<!-- ✅ Product Detail Section -->
<div class="container product-container">
    <h2><?= htmlspecialchars($product['product_name']) ?></h2>
    <div class="row">
        <div class="col-md-6 mb-4">
            <img src="<?= htmlspecialchars($product['image_path']) ?>" alt="Product Image" class="product-image">
        </div>
        <div class="col-md-6">
            <h4>Price: ₹<?= number_format($product['product_price'], 2) ?></h4>
            <p><strong>Brand:</strong> <?= htmlspecialchars($brand_name) ?></p>
            <p><strong>In Stock:</strong> <?= $product['stock_quantity'] ?></p>
            <p><strong>Description:</strong><br><?= nl2br(htmlspecialchars($product['product_description'])) ?></p>

            <form method="post" action="">
                <input type="hidden" name="product_id" value="<?= $product_id ?>">
                <div class="form-group">
                    <label>Quantity:</label>
                    <input type="number" name="quantity" id="quantity" value="1" min="1" max="<?= $product['stock_quantity'] ?>" class="form-control" required>
                </div>
                <button type="submit" name="add_to_cart" class="btn btn-addcart">Add to Cart</button>
                <button type="button" class="btn btn-buynow" onclick="buyNow()">Buy Now</button>
            </form>

            <a href="view_reviews.php?product_id=<?= $product_id ?>" class="btn btn-viewreviews">View Reviews</a>
        </div>
    </div>
</div>

<!-- ✅ Buy Now JS -->
<script>
function buyNow() {
    const quantity = document.getElementById("quantity").value;
    const productId = <?= $product_id ?>;
    window.location.href = `checkout_buy_now.php?product_id=${productId}&quantity=${quantity}`;
}
</script>

<!-- ✅ Bootstrap 5.3 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- ✅ Bootstrap 5.3 JS Bundle (No jQuery needed) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
