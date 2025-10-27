<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items from database
$sql = "
    SELECT c.product_id, c.quantity, p.product_name, p.product_price
    FROM cart_master c
    JOIN product_master p ON c.product_id = p.product_id
    WHERE c.user_id = $user_id
";

$result = $conn->query($sql);

$cart_items = [];
$total_amount = 0;

while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
    $subtotal = $row['product_price'] * $row['quantity'];
    $total_amount += $subtotal;
}

if (empty($cart_items)) {
    echo "<script>alert('Your cart is empty!'); window.location.href='shop.php';</script>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout - LAPCART</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #111;
            color: #fff;
        }
        .checkout-box {
            background-color: #222;
            padding: 30px;
            border-radius: 15px;
            margin-top: 50px;
        }
        .btn-green {
            background-color: #28a745;
            border: none;
        }
        .btn-green:hover {
            background-color: #218838;
        }
        .logo-oval {
            background-color: #28a745;
            border-radius: 50px;
            color: #fff;
            font-size: 24px;
            font-weight: bold;
            padding: 8px 20px;
            text-decoration: none;
        }
        .form-control, .form-select {
            background-color: #333;
            color: #fff;
            border: 1px solid #444;
        }
        .form-control::placeholder {
            color: #aaa;
        }
        .nav-link {
            color: #ccc !important;
        }
        .nav-link:hover {
            color: #fff !important;
        }
        .lapcart-logo {
            background-color: #2a600c;
            color: #fff;
            font-weight: bold;
            padding: 8px 20px;
            border-radius: 999px;
            font-size: 24px;
            letter-spacing: 1px;
            display: inline-block;
        }
        .brand-logos .brand-tile {
            margin: 10px;
            padding: 12px 24px;
            border-radius: 6px;
            background-color: #1e1e1e;
            color: #d1d1d1;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s;
        }
        .brand-tile:hover {
            background-color: #2c2c2c;
            color: #00cc88;
        }
        .brand-tile.selected {
            background: linear-gradient(to right, #14532d, #198754);
            color: #fff;
            border: none;
            box-shadow: 0 0 10px rgba(0,255,150,0.5);
            font-weight: bold;
        }
        .section-heading {
            color: #43662A;
            border-bottom: 2px solid #8F3A00;
            padding-bottom: 5px;
            margin-top: 40px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container">

    <!-- 🔝 Navbar -->
    <div class="sticky-top bg-dark shadow-sm">
        <nav class="navbar navbar-expand-lg bg-dark px-3">
            <div class="container-fluid d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <a href="index.php"><div class="lapcart-logo me-4">LAPCART</div></a>
                    <ul class="navbar-nav d-flex flex-row gap-3">
                        <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
                        <li class="nav-item"><a class="nav-link" href="aboutus.php">About</a></li>
                        <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
                        <li class="nav-item"><a class="nav-link" href="help.php">Help</a></li>
                    </ul>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <a href="addtocart.php" class="btn btn-outline-light position-relative">🛒</a>
                    <div class="dropdown">
                        <a class="dropdown-item" href="profile.php">My Account</a>
                    </div>
                </div>
            </div>
        </nav>
    </div>
    <!-- Checkout Box -->
    <div class="checkout-box shadow-lg mt-4">
        <h2 class="mb-4">Order Summary</h2>
        <form action="place_order.php" method="post">
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
                <?php foreach ($cart_items as $item): 
                    $subtotal = $item['product_price'] * $item['quantity'];
                ?>
                <tr>
                    <td><?= $item['product_id'] ?></td>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= number_format($item['product_price'], 2) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td><?= number_format($subtotal, 2) ?></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Original Total Box -->
            <div class="d-flex justify-content-end mt-2">
                <div class="p-2 bg-dark text-white rounded" style="min-width: 180px; text-align:center; border: 1px solid #444;">
                    <strong>Total Amount:</strong> ₹<?= number_format($total_amount, 2) ?>
                </div>
            </div>

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


            <div class="mt-4">
                <h4>Final Amount: ₹<span id="totalDisplay"><?= number_format($total_amount, 2) ?></span></h4>
                <input type="hidden" name="total_amount" id="total_amount" value="<?= $total_amount ?>">

                <!-- Hidden fields for coupon -->
                <input type="hidden" name="coupon_code" id="coupon_code" value="">
                <input type="hidden" name="discount_amount" id="discount_amount" value="0">
            </div>


            <hr class="bg-secondary">

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
                    <input class="form-check-input" type="radio" name="payment_mode" id="cod" value="COD" checked>
                    <label class="form-check-label" for="cod">Cash on Delivery (COD)</label>
                </div>
            </div>

            <div class="text-end mt-4">
                <button type="submit" class="btn btn-green btn-lg">Place Order</button>
            </div>
        </form>

    </div>
</div>
<script>
    let appliedCoupon = null; // track which coupon is active
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
