<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Contact Us - Laptop Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            background-color: #121212;
            color: #ffffff;
            font-family: Arial, sans-serif;
            margin: 0;
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
        .navbar-nav .nav-link {
            color: #fff !important;
        }
        .navbar-nav .nav-link:hover {
            color: #ddd !important;
        }
        .container {
            max-width: 700px;
            margin: 60px auto;
            background-color: #1e1e1e;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0, 255, 153, 0.1);
        }
        h1 {
            text-align: center;
            color: #00ff99;
            margin-bottom: 30px;
        }
        .contact-info {
            font-size: 18px;
            line-height: 1.8;
        }
        .contact-info strong {
            color: #00ff99;
        }
    </style>
</head>
<body>

<!-- ✅ Sticky Navigation Bar (Same as shop.php) -->
<div class="sticky-top bg-dark shadow-sm">
    <nav class="navbar navbar-expand-lg bg-dark px-3">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            
            <!-- 🔰 Logo + Nav -->
            <div class="d-flex align-items-center">
                <a href="shop.php" class="text-decoration-none">
                    <div class="lapcart-logo me-4">LAPCART</div>
                </a>
                <ul class="navbar-nav d-flex flex-row gap-3">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="aboutus.php">About</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="faqs.php">FAQs</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="help.php">Help</a></li>
                </ul>
            </div>

            <!-- 🛒 Cart + 👤 Account -->
            <div class="d-flex align-items-center gap-3">
                <!-- Cart First -->
                <a href="addtocart.php" class="btn btn-outline-light position-relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
                         class="bi bi-cart" viewBox="0 0 16 16">
                        <path
                            d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .49.598l-1.5 7A.5.5 0 0 1 13 13H4a.5.5 0 0 1-.49-.402L1.01 2H.5a.5.5 0 0 1-.5-.5zM4.415 6l1.313 6h6.544l1.313-6H4.415zM5.5 14a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
                    </svg>
                </a>

                <!-- Account Dropdown -->
                <div class="dropdown">
                    <button class="btn btn-outline-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
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

<!-- 📄 Contact Info -->
<div class="container">
    <h1>Contact Us</h1>
    <div class="contact-info">
        <p><strong>Phone:</strong> +91 87994 04091</p>
        <p><strong>Email:</strong> support@Lapcart.com</p>
        <p><strong>Customer Support:</strong> Available 24/7</p>
        <p><strong>Address:</strong> Lapcart Pvt. Ltd., Sector 10, New Delhi, India</p>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
