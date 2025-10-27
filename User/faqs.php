<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>FAQs - Lapcart</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />

  <style>
    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background-color: #121212;
      margin: 0;
      padding: 0;
      color: #e0e0e0;
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
      transition: all 0.3s ease;
    }

    .container-faq {
      max-width: 900px;
      margin: 140px auto 80px;
      padding: 30px;
      background-color: #1e1e1e;
      border-radius: 10px;
      box-shadow: 0 0 20px rgba(0, 255, 153, 0.1);
    }

    h1 {
      color: #00ff99;
      margin-bottom: 30px;
      text-align: center;
    }

    h2 {
      color: #00ff99;
      margin-top: 40px;
      border-bottom: 1px solid #333;
      padding-bottom: 5px;
    }

    .faq {
      margin-top: 20px;
      padding: 15px 20px;
      background-color: #2a2a2a;
      border-left: 4px solid #00cc88;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 255, 153, 0.05);
    }

    .faq h3 {
      color: #00ccff;
      font-size: 18px;
      margin-bottom: 8px;
    }

    .faq p {
      color: #ccc;
      font-size: 15px;
    }

    a {
      color: #00ccff;
      text-decoration: underline;
    }

    a:hover {
      color: #00ffcc;
    }

    footer {
      background-color: #1e1e1e;
      color: #aaa;
      text-align: center;
      padding: 15px;
      position: fixed;
      bottom: 0;
      width: 100%;
      border-top: 1px solid #333;
    }

    .form-control {
      background-color: #333;
      border: 1px solid #555;
      color: #fff;
    }

    .form-control::placeholder {
      color: #aaa;
    }

    .btn-success {
      background-color: #00cc88;
      border: none;
    }

    .btn-success:hover {
      background-color: #00b377;
    }

    .dropdown-menu-dark {
      background-color: #2c2c2c;
    }

    .dropdown-item:hover {
      background-color: #00ff99;
      color: #000;
    }

    .nav-link {
      color: #B5B5B5 !important;
    }

    .nav-link:hover {
      color: #fff !important;
    }

    .navbar {
      border-bottom: 1px solid #333;
    }
  </style>
</head>
<body>

<!-- 🔝 Sticky Navbar -->
<div class="sticky-top bg-dark shadow-sm">
  <nav class="navbar navbar-expand-lg bg-dark px-3">
    <div class="container-fluid d-flex align-items-center justify-content-between">
      <!-- 🔰 Logo & Menu -->
      <div class="d-flex align-items-center">
        <a href="shop.php" class="text-decoration-none">
          <div class="lapcart-logo me-4">LAPCART</div>
        </a>
        <ul class="navbar-nav d-flex flex-row gap-3">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="shop.php">Shop</a></li>
          <li class="nav-item"><a class="nav-link" href="aboutus.php">About</a></li>
          <li class="nav-item"><a class="nav-link active" href="faqs.php">FAQs</a></li>
          <li class="nav-item"><a class="nav-link" href="help.php">Help</a></li>
        </ul>
      </div>

      <!-- 🛒 Cart + 👤 Account -->
      <div class="d-flex align-items-center gap-3">
        <a href="addtocart.php" class="btn btn-outline-light position-relative">
          <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="currentColor"
            class="bi bi-cart" viewBox="0 0 16 16">
            <path
              d="M0 1.5A.5.5 0 0 1 .5 1h1a.5.5 0 0 1 .485.379L2.89 5H14.5a.5.5 0 0 1 .49.598l-1.5 7A.5.5 0 0 1 13 13H4a.5.5 0 0 1-.49-.402L1.01 2H.5a.5.5 0 0 1-.5-.5zM4.415 6l1.313 6h6.544l1.313-6H4.415zM5.5 14a1 1 0 1 0 0 2 1 1 0 0 0 0-2zm7 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2z"/>
          </svg>
        </a>

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

<!-- 📋 FAQs Section -->
<div class="container-faq">
  <h1>Frequently Asked Questions</h1>

  <h2>General Questions</h2>
  <div class="faq">
    <h3>1. How do I place an order?</h3>
    <p>Browse laptops, click "Add to Cart", and proceed to checkout. You’ll need to log in or create an account.</p>
  </div>

  <div class="faq">
    <h3>2. What payment methods do you accept?</h3>
    <p>We accept Visa, MasterCard, UPI, Net Banking, and Cash on Delivery (COD).</p>
  </div>

  <div class="faq">
    <h3>3. Can I cancel or change my order?</h3>
    <p>You can cancel or change your order within 2 hours of placing it by contacting our support team.</p>
  </div>

  <h2>Shipping & Delivery</h2>
  <div class="faq">
    <h3>4. How long does delivery take?</h3>
    <p>Standard delivery takes 3–7 business days, depending on your location.</p>
  </div>

  <div class="faq">
    <h3>5. Is shipping free?</h3>
    <p>Yes, we offer free shipping on orders above ₹25,000. For orders below that, a ₹299 fee applies.</p>
  </div>

  <h2>Account & Support</h2>
  <div class="faq">
    <h3>8. How do I contact support?</h3>
    <p>You can email us at <a href="mailto:support@laptopstore.com">support@laptopstore.com</a> or call +91-8799404091 (24/7 available).</p>
  </div>

  <div class="faq">
    <h3>9. How do I reset my password?</h3>
    <p>Go to the login page and click on "Forgot Password". Follow the instructions to reset it via email.</p>
  </div>
</div>

<!-- 🔻 Footer -->
<footer>
  &copy; 2025 Lapcart. All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
