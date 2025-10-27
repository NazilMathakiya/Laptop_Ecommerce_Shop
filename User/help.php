<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Help - Lapcart</title>

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

    .container-help {
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

    p {
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

    /* Navbar styles from FAQs page */
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
          <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
          <li class="nav-item"><a class="nav-link active" href="help.php">Help</a></li>
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

<!-- 📋 Help Section -->
<div class="container-help">
  <h1>Help & Support</h1>
  <p>If you have any questions or need assistance, you’re in the right place. Here you can find resources to help you use our platform effectively.</p>

  <h2>Contact Us</h2>
  <p>Email: <a href="mailto:armankhorajiyask@gmail.com">support@laptopstore.com</a></p>
  <p>Phone: +91-9876543210 (Available 24/7)</p>

  <h2>Live Chat</h2>
  <p>Use our live chat feature (bottom right corner) to instantly connect with our support team.</p>

  <h2>Report an Issue</h2>
  <p>Encountering a problem? Please fill out our <a href="faqs.php">Issue Report Form</a> and we’ll get back to you quickly.</p>
</div>

<!-- 🔻 Footer -->
<footer>
  &copy; 2025 Lapcart. All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
