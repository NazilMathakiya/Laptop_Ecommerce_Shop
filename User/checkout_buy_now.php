<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$popup_message = "";
$popup_type = "";

if (!isset($_GET['product_id'])) {
    die("Product not found.");
}

$product_id = intval($_GET['product_id']);
$sql = "SELECT * FROM product_master WHERE product_id = $product_id";
$result = $conn->query($sql);

if ($result->num_rows != 1) {
    die("Product not available.");
}

$product = $result->fetch_assoc();
$stock_quantity = intval($product['stock_quantity']);
$unit_price = floatval($product['product_price']);

$selected_qty = isset($_GET['quantity']) ? intval($_GET['quantity']) : 1;
if ($selected_qty < 1) $selected_qty = 1;
if ($selected_qty > $stock_quantity) {
    $popup_message = "Only $stock_quantity units in stock.";
    $popup_type = "danger";
    $selected_qty = $stock_quantity;
}
$total_price = $unit_price * $selected_qty;

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_quantity'])) {
    $selected_qty = intval($_POST['quantity']);
    if ($selected_qty < 1) $selected_qty = 1;
    if ($selected_qty > $stock_quantity) {
        $popup_message = "Only $stock_quantity units in stock.";
        $popup_type = "danger";
        $selected_qty = $stock_quantity;
    }
    $total_price = $unit_price * $selected_qty;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - LAPCART</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #111; color: #fff; }
        .checkout-box { background-color: #222; padding: 30px; border-radius: 15px; margin-top: 50px; }
        .btn-green { background-color: #28a745; border: none; }
        .btn-green:hover { background-color: #218838; }
        .logo-oval {
            background-color: #28a745; border-radius: 50px; color: #fff;
            font-size: 24px; font-weight: bold; padding: 8px 20px; text-decoration: none;
        }
        .form-control, .form-select { background-color: #333; color: #fff; border: 1px solid #444; }
        .form-control::placeholder { color: #aaa; }
    </style>
</head>
<body>
    <div class="container">
        <!-- NAVBAR with dropdown -->
        <nav class="navbar navbar-expand-lg navbar-dark px-4">
            <a class="navbar-brand d-flex align-items-center" href="shop.php">
                <span class="logo-oval">LAPCART</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-3">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.php">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                    <li class="nav-item"><a class="nav-link" href="help.php">Help</a></li>
                </ul>
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="accountDropdown" role="button" data-bs-toggle="dropdown">
                            My Account
                        </a>
                        <ul class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="view_order.php">My Orders</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </nav>

        <!-- Checkout Box -->
        <div class="checkout-box shadow-lg mt-4">
            <h2 class="mb-4">Buy Now - Checkout</h2>
            <?php if ($popup_message): ?>
                <div class="alert alert-<?= $popup_type ?> alert-dismissible fade show" role="alert">
                    <?= $popup_message ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Quantity Update Form -->
            <form method="post">
                <table class="table table-bordered table-dark">
                    <thead class="table-success text-dark">
                        <tr>
                            <th>Product ID</th>
                            <th>Product Name</th>
                            <th>Price (₹)</th>
                            <th>Quantity</th>
                            <th>Subtotal (₹)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?= $product['product_id'] ?></td>
                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                            <td><?= number_format($unit_price, 2) ?></td>
                            <td>
                                <input type="number" name="quantity" value="<?= $selected_qty ?>" min="1" max="<?= $stock_quantity ?>" class="form-control bg-dark text-white">
                            </td>
                            <td><?= number_format($total_price, 2) ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-end">
                    <button type="submit" name="update_quantity" class="btn btn-warning">Update Quantity</button>
                </div>
                
            </form>
            <!-- Original Total Amount Box (like checkout.php) -->
            <div class="p-3 mt-3 mb-3" style="background-color:#1a1a1a; border-radius:10px; text-align:center; font-weight:bold;">
                Total Amount: ₹<span id="originalTotal"><?= number_format($total_price, 2) ?></span>
            </div>

            <!-- Order Placement Form -->
            <form method="post" action="place_order.php">
                <input type="hidden" name="product_id" value="<?= $product['product_id'] ?>">
                <input type="hidden" name="product_price" value="<?= $unit_price ?>">
                <input type="hidden" name="quantity" id="quantity" value="<?= $selected_qty ?>">
                <input type="hidden" name="total_price" id="total_amount" value="<?= $total_price ?>">

                <!-- Hidden fields for coupon -->
                <input type="hidden" name="coupon_code" id="coupon_code" value="">
                <input type="hidden" name="discount_amount" id="discount_amount" value="0">

                <hr class="bg-secondary">

                <!-- Coupon Input Section -->
            <div class="mt-4 position-relative">
                <h5>Have a Coupon?</h5>
                <div class="input-group mb-3">
                    <input type="text" name="coupon_code" id="coupon" class="form-control" placeholder="Enter coupon code">
                    <button class="btn btn-outline-success" type="button" id="applyCoupon">Apply</button>
                </div>
                <div id="couponSuggestions" class="list-group position-absolute w-100" style="z-index:1000; display:none;">
                    <button type="button" class="list-group-item list-group-item-action" data-code="SAVE500">
                        <strong>SAVE500</strong> – Get ₹500 off on orders above ₹5000
                    </button>
                    <button type="button" class="list-group-item list-group-item-action" data-code="WELCOME10">
                        <strong>WELCOME10</strong> – Flat 10% off for new customers
                    </button>
                </div>
                <p id="couponMessage" class="text-success"></p>
            </div>

            <script>
            const couponInput = document.getElementById("coupon");
            const suggestions = document.getElementById("couponSuggestions");

            // Show suggestions on focus
            couponInput.addEventListener("focus", () => {
                suggestions.style.display = "block";
            });

            // Hide suggestions if clicked outside
            document.addEventListener("click", (e) => {
                if (!e.target.closest("#couponSuggestions") && e.target !== couponInput) {
                    suggestions.style.display = "none";
                }
            });

            // Fill input when a suggestion is clicked
            document.querySelectorAll("#couponSuggestions button").forEach(btn => {
                btn.addEventListener("click", () => {
                    couponInput.value = btn.dataset.code;
                    suggestions.style.display = "none";
                });
            });
            </script>


                <!-- Total Display -->
                <h4>Final Amount: ₹<span id="totalDisplay"><?= number_format($total_price, 2) ?></span></h4>

                <h4 class="mt-4">Enter Your Details</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Name:</label>
                        <input type="text" name="name" class="form-control" required placeholder="Enter full name">
                    </div>
                    <div class="col-md-6 mb-3">
                    <label>Email:</label>
                    <input type="email" name="email" id="email" class="form-control" required
                           placeholder="Enter email"
                           pattern="^[^@\s]+@[^@\s]+\.[^@\s]+$"
                           title="Enter a valid email address (must include . after @)">
                    <div class="invalid-feedback">Please enter a valid email address.</div>
                </div>
                <div class="col-md-6 mb-3">
                    <label>Mobile:</label>
                    <input type="text" name="mobile" id="mobile" class="form-control" required 
                           placeholder="Enter mobile number"
                           pattern="[6-9][0-9]{9}"
                           title="Enter a valid 10-digit mobile number starting with 6,7,8, or 9">
                    <div class="invalid-feedback">Enter a valid 10-digit mobile number.</div>
                </div>

                <script>
                // Live validation
                function validateInput(input) {
                    if (input.checkValidity()) {
                        input.classList.add("is-valid");
                        input.classList.remove("is-invalid");
                    } else {
                        input.classList.add("is-invalid");
                        input.classList.remove("is-valid");
                    }
                }

                document.getElementById("email").addEventListener("blur", function() {
                    validateInput(this);
                });

                document.getElementById("mobile").addEventListener("blur", function() {
                    validateInput(this);
                });

                </script>
                    <div class="col-md-6 mb-3">
                        <label>Address:</label>
                        <textarea name="address" class="form-control" rows="2" required placeholder="Enter full address"></textarea>
                    </div>
                </div>

                <div class="mt-3">
                    <h5>Payment Mode:</h5>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="payment_method" id="cod" value="COD" checked>
                        <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" name="place_order" class="btn btn-green btn-lg">Place Order</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        let appliedCoupon = null; // store current coupon
        const originalTotal = parseFloat(document.getElementById("total_amount").value);

        document.getElementById("applyCoupon").addEventListener("click", function() {
            let coupon = document.getElementById("coupon").value.trim();

            if (coupon === "") {
                alert("Please enter a coupon code.");
                return;
            }

            fetch("check_coupon.php", {
                method: "POST",
                headers: {"Content-Type": "application/x-www-form-urlencoded"},
                body: "coupon=" + encodeURIComponent(coupon) + "&total_amount=" + encodeURIComponent(originalTotal)
            })
            .then(res => res.json())
            .then(data => {
                document.getElementById("couponMessage").textContent = data.message;
                if (data.valid) {
                    document.getElementById("totalDisplay").textContent = data.new_total.toFixed(2);
                    document.getElementById("total_amount").value = data.new_total;

                    // fill hidden fields so place_order.php can save them
                    document.getElementById("coupon_code").value = data.coupon_code;
                    document.getElementById("discount_amount").value = data.discount_amount;
                } else {
                    // reset hidden fields if invalid
                    document.getElementById("coupon_code").value = "";
                    document.getElementById("discount_amount").value = 0;
                }
            })

            .catch(err => console.error(err));
        });
    </script>
</body>
</html>
