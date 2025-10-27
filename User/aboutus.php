<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us - Lapcart</title>

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

    h1 {
      color: #00ff99;
      margin-bottom: 30px;
      text-align: center;
      margin-top: 140px;
    }

    p, li {
      color: #ccc;
      font-size: 15px;
      line-height: 1.6;
    }

    .about-container {
      max-width: 900px;
      margin: auto;
      padding: 30px;
      background-color: #1e1e1e;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0, 255, 153, 0.1);
    }

    footer {
      background-color: #1e1e1e;
      color: #aaa;
      text-align: center;
      padding: 15px;
      margin-top: 40px;
      border-top: 1px solid #333;
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

<!-- 🔝 Sticky Navbar (EXACT same as FAQs) -->
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
          <li class="nav-item"><a class="nav-link active" href="aboutus.php">About</a></li>
          <li class="nav-item"><a class="nav-link" href="faqs.php">FAQs</a></li>
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

<!-- 📜 About Us Content -->
<h1>About Us</h1>
<div class="about-container">
  <h4>Who We Are</h4>
  <p>At <strong>Lapcart</strong>, we believe technology should empower your potential. Since our founding in 2020, we’ve been committed to providing high-performance laptops at fair prices, backed by genuine customer care.</p>

  <h4>Our Mission</h4>
  <p>We aim to make the latest and best laptops accessible to everyone — students, gamers, professionals, and tech enthusiasts alike.</p>

  <h4>Why Choose Us?</h4>
  <ul>
    <li>✔ Curated selection from top global brands</li>
    <li>✔ 1-year manufacturer warranty on all products</li>
    <li>✔ 7-day easy returns</li>
    <li>✔ Fast, free delivery on eligible orders</li>
    <li>✔ 24/7 expert customer support</li>
  </ul>

  <h4>Our Promise</h4>
  <p>Shopping with Lapcart means joining a community that values quality, trust, and innovation. We stand by every laptop we sell, ensuring your satisfaction from click to delivery.</p>
</div>

<!-- 🔻 Footer -->
<footer>
  &copy; 2025 Lapcart. All rights reserved.
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
