<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$clicked_brand = isset($_GET['brand_id']) ? intval($_GET['brand_id']) : 0;
if ($clicked_brand > 0 && isset($_SESSION['last_brand']) && $_SESSION['last_brand'] == $clicked_brand) {
    unset($_SESSION['last_brand']);
    $brand_filter = 0;
    header("Location: index.php"); // Removes brand_id from URL and avoids reloading with same brand
    exit;
} elseif ($clicked_brand > 0) {
    $_SESSION['last_brand'] = $clicked_brand;
    $brand_filter = $clicked_brand;
} else {
    unset($_SESSION['last_brand']);
    $brand_filter = 0;
}

$brand_name = "";
$has_more_than_3 = false;

if ($brand_filter > 0) {
    $latest = $conn->query("SELECT * FROM product_master WHERE brand_id = $brand_filter ORDER BY added_at DESC LIMIT 3");
    $count_check = $conn->query("SELECT COUNT(*) AS total FROM product_master WHERE brand_id = $brand_filter");
    $count_row = $count_check->fetch_assoc();
    $has_more_than_3 = $count_row['total'] > 3;

    $brand_fetch = $conn->query("SELECT brand_name FROM brand_master WHERE brand_id = $brand_filter");
    if ($brand_fetch->num_rows > 0) {
        $brand_name = $brand_fetch->fetch_assoc()['brand_name'];
    }
} else {
    $latest = $conn->query("SELECT * FROM product_master ORDER BY added_at DESC LIMIT 3");
}
?>

<!DOCTYPE html>
<html lang="en">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<head>
    <meta charset="UTF-8">
    <title>LAPCART - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #111;
            color: #e6e6e6;
            font-family: 'Segoe UI', sans-serif;
            scroll-behavior: smooth;
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
        .carousel-item {
            height: 400px;
            background-size: cover;
            background-position: center;
            position: relative;
            animation: fadeIn 1s ease-in-out;
            cursor: pointer;
        }
        .carousel-item::before {
            content: "";
            position: absolute;
            top: 0; left: 0;
            width: 100%; height: 100%;
            background: linear-gradient(to bottom, rgba(0,0,0,0.3), rgba(0,0,0,0.75));
        }
        .carousel-caption {
            z-index: 10;
            color: #f1f1f1;
            text-shadow: 1px 1px 5px #000;
        }
        @keyframes fadeIn {
            from {opacity: 0;}
            to {opacity: 1;}
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
        .card {
            background-color: #1e1e1e;
            border: 1px solid #333;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 5px 15px rgba(0,255,100,0.2);
        }
        .card img {
            height: 180px;
            object-fit: contain;
        }
        .card-title {
            color: #f1f1f1;
            font-size: 18px;
        }
        .text-price {
            color: #00cc88;
        }
        .text-rating {
            color: #ffc107;
        }
        .nav-link {
            color: #ccc !important;
        }
        .nav-link:hover {
            color: #fff !important;
        }
        .social-icon {
          font-size: 1.5rem;
          text-decoration: none;
        }

        .social-icon { font-size: 1.5rem; text-decoration: none; }
        .social-icon.fb { color: #1877f2; }
        .social-icon.ig { color: #e4405f; }
        .social-icon.tw { color: #000000; }
        .social-icon.li { color: #0a66c2; }
        .social-icon.yt { color: #ff0000; }
        .social-icon:hover { opacity: 0.7; }

    </style>
</head>
<body>

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

<!-- 🔁 Slider -->
<div id="productSlider" class="carousel slide" data-bs-ride="carousel" data-bs-interval="3500">
    <div class="carousel-inner">
        <?php
        $slider = $conn->query("SELECT product_id, image_path, product_name FROM product_master WHERE image_path IS NOT NULL ORDER BY added_at DESC LIMIT 5");
        $active = true;
        while ($img = $slider->fetch_assoc()) {
            echo '<div class="carousel-item '.($active ? 'active' : '').'" 
                    style="background-image: url(\''.$img['image_path'].'\');" 
                    onclick="location.href=\'product_detail.php?product_id='.$img['product_id'].'\'">
                    <div class="carousel-caption d-none d-md-block">
                        <h3 class="fw-bold">'.$img['product_name'].'</h3>
                    </div>
                </div>';
            $active = false;
        }
        ?>
    </div>
    <button class="carousel-control-prev" type="button" data-bs-target="#productSlider" data-bs-slide="prev">
        <span class="carousel-control-prev-icon"></span>
    </button>
    <button class="carousel-control-next" type="button" data-bs-target="#productSlider" data-bs-slide="next">
        <span class="carousel-control-next-icon"></span>
    </button>
</div>

<!-- 🏷️ Brand Showcase -->
<div class="container text-center">
    <h3 class="section-heading">Top Brands</h3>
    <div class="d-flex flex-wrap justify-content-center brand-logos">
        <?php
        $brands = $conn->query("SELECT brand_id, brand_name FROM brand_master");
        while ($brand = $brands->fetch_assoc()) {
    $brand_id = $brand['brand_id'];
    $current_brand_name = htmlspecialchars($brand['brand_name']);//Prevent XSS
    $class = ($brand_filter == $brand_id) ? 'selected' : '';
    
    echo '<div class="brand-tile '.$class.'" onclick="window.location.href=\'index.php?brand_id='.$brand_id.'\'">'.$current_brand_name.'</div>';
}

        ?>
    </div>
</div>

<!-- 🆕 Brand Products or Latest Arrivals -->
<div class="container" id="brand-products">
    <h3 class="section-heading">
        <?= ($brand_filter > 0) ? htmlspecialchars($brand_name).' Collection' : 'Latest Arrivals' ?>
    </h3>
    <div class="row">
        <?php while ($p = $latest->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card text-center">
                    <img src="<?= $p['image_path'] ?>" class="card-img-top" alt="<?= $p['product_name'] ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?= $p['product_name'] ?></h5>
                        <p class="text-price">₹<?= number_format($p['product_price']) ?></p>
                        <p class="text-muted">
                            <?= $p['stock_quantity'] > 0 ? '<span class="text-success">In Stock</span>' : '<span class="text-danger">Out of Stock</span>' ?>
                        </p>
                        <a href="product_detail.php?product_id=<?= $p['product_id'] ?>" class="btn btn-sm btn-success mt-2">View</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <?php if ($brand_filter > 0 && $has_more_than_3): ?>
        <div class="text-center mb-5">
            <a href="shop.php?brand=<?= $brand_filter ?>" class="btn btn-outline-success">View More <?= htmlspecialchars($brand_name) ?> Products</a>
        </div>
    <?php endif; ?>
</div>

<!-- ⭐ Top Rated Products -->
<div class="container">
    <h3 class="section-heading">Top Rated Products</h3>
    <div class="row">
        <?php
        $topRated = $conn->query("
            SELECT p.*, AVG(r.rating) as avg_rating 
            FROM product_master p
            JOIN review_master r ON p.product_id = r.product_id
            GROUP BY p.product_id
            ORDER BY avg_rating DESC LIMIT 3
        ");
        while ($p = $topRated->fetch_assoc()) {
            echo '<div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <img src="'.$p['image_path'].'" class="card-img-top" alt="'.$p['product_name'].'">
                        <div class="card-body">
                            <h5 class="card-title">'.$p['product_name'].'</h5>
                            <p class="text-price">₹'.number_format($p['product_price']).'</p>
                            <p class="text-rating">⭐ '.number_format($p['avg_rating'],1).'</p>
                            <a href="product_detail.php?product_id='.$p['product_id'].'" class="btn btn-sm btn-success mt-2">View</a>
                        </div>
                    </div>
                </div>';
        }
        ?>
    </div>
</div>

<!-- 📈 Bestseller Section -->
<div class="container">
    <h3 class="section-heading">Bestsellers</h3>
    <div class="row">
        <?php
        $bestsellers = $conn->query("
            SELECT p.*, SUM(oi.quantity) as total_sold
            FROM order_items oi
            JOIN product_master p ON oi.product_id = p.product_id
            GROUP BY oi.product_id
            ORDER BY total_sold DESC
            LIMIT 3
        ");
        while ($p = $bestsellers->fetch_assoc()) {
            echo '<div class="col-md-4 mb-4">
                    <div class="card text-center">
                        <img src="'.$p['image_path'].'" class="card-img-top" loading="lazy" alt="'.$p['product_name'].'">
                        <div class="card-body">
                            <h5 class="card-title">'.$p['product_name'].'</h5>
                            <p class="text-price">₹'.number_format($p['product_price']).'</p>
                            <p class="text-muted">'.($p['stock_quantity'] > 0 ? '<span class="text-success">In Stock</span>' : '<span class="text-danger">Out of Stock</span>').'</p>
                            <p class="text-warning">🔥 Sold: '.$p['total_sold'].'</p>
                            <a href="product_detail.php?product_id='.$p['product_id'].'" class="btn btn-sm btn-success mt-2">View</a>
                        </div>
                    </div>
                </div>';
        }
        ?>
    </div>
</div>

<!-- 📝 Latest Reviews -->
<?php
// put this before <div class="container mt-5">Latest Reviews</div>

// count all reviews once
$rev_count_result = $conn->query("SELECT COUNT(*) AS total FROM review_master");
$rev_total = $rev_count_result->fetch_assoc()['total'];
?>
<div class="container mt-5">
    <h3 class="section-heading">Latest Reviews</h3>
    <div class="row" id="reviews-container">
        <?php
        $rev_limit = 3;
        $latest_reviews = $conn->query("
            SELECT r.comment, r.rating, r.review_date, u.full_name, p.product_name, p.product_id, p.image_path
            FROM review_master r
            JOIN user_master u ON r.user_id = u.user_id
            JOIN product_master p ON r.product_id = p.product_id
            ORDER BY r.review_date DESC
            LIMIT $rev_limit
        ");

        while ($rev = $latest_reviews->fetch_assoc()):
        ?>
        <div class="col-md-4 mb-4 review-item fade-in">
            <div class="card h-100 text-start">
                <div class="row g-0">
                    <div class="col-4">
                        <img src="<?= $rev['image_path'] ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($rev['product_name']) ?>">
                    </div>
                    <div class="col-8">
                        <div class="card-body">
                            <h6 class="card-title mb-1"><?= htmlspecialchars($rev['product_name']) ?></h6>
                            <p class="mb-1">
                                <strong style="color: #00bfa6;"><?= htmlspecialchars($rev['full_name']) ?></strong>
                                <small class="text-muted">(<?= date('d M Y', strtotime($rev['review_date'])) ?>)</small>
                            </p>
                            <p class="text-warning mb-1">⭐ <?= $rev['rating'] ?>/5</p>
                            <p class="card-text small" style="color: #ddd;">
                                <?= htmlspecialchars(mb_strimwidth($rev['comment'], 0, 50, "...")) ?>
                            </p>
                            <a href="product_detail.php?product_id=<?= $rev['product_id'] ?>" class="btn btn-sm btn-outline-success">View Product</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endwhile; ?>
    </div>

    <!-- Buttons -->
    <div class="text-center mt-3">
        <button id="load-more-reviews" class="btn btn-success">View More</button>
        <button id="view-less-reviews" class="btn btn-outline-light" style="display:none;">View Less</button>
    </div>
</div>

<!-- Animation CSS -->
<style>
.fade-in {
    opacity: 0;
    transform: translateY(10px);
    animation: fadeInUp 0.5s ease forwards;
}
@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
let revPage = 1; // current page
const revLimit = 3;
const revTotal = <?= $rev_total ?>; // total reviews from PHP

document.getElementById("load-more-reviews").addEventListener("click", function() {
    revPage++;
    fetch("fetch_reviews.php?page=" + revPage)
        .then(res => res.text())
        .then(html => {
            if (html.trim() === "") {
                document.getElementById("load-more-reviews").style.display = "none";
            } else {
                const container = document.getElementById("reviews-container");
                container.insertAdjacentHTML("beforeend", html);
                document.getElementById("view-less-reviews").style.display = "inline-block";

                // hide "View More" if all reviews are loaded
                const items = container.querySelectorAll(".review-item");
                if (items.length >= revTotal) {
                    document.getElementById("load-more-reviews").style.display = "none";
                }
            }
        })
        .catch(err => console.error(err));
});

document.getElementById("view-less-reviews").addEventListener("click", function() {
    const container = document.getElementById("reviews-container");
    const items = container.querySelectorAll(".review-item");

    // keep only first 3
    for (let i = items.length - 1; i >= revLimit; i--) {
        items[i].remove();
    }

    revPage = 1; // reset page
    document.getElementById("load-more-reviews").style.display = "inline-block";
    document.getElementById("view-less-reviews").style.display = "none";
});
</script>


<!-- ☎️ Customer Care Section -->
<div class="container text-center text-light my-5">
    <h4 class="mb-3" style="color: #00cc88; font-weight: 600;">Need Help? We're Here for You</h4>
    <div class="row justify-content-center">
        <div class="col-md-6">
            <p style="font-size: 16px; margin-bottom: 6px;">
                📞 <strong>Customer Care:</strong> +91 8799404091
            </p>
            <p style="font-size: 14px; color: #cccccc;">
                🕘 Available from 9:00 AM to 9:00 PM (Mon to Sat)
            </p>
            <a href="contactus.php" class="btn btn-success btn-sm mt-2">Contact US</a>
        </div>
    </div>
</div>


<!-- 🌟 Why Choose Lapcart -->
<div class="container my-5">
    <h4 class="section-heading text-center">Why Choose Lapcart?</h4>
    <div class="row text-center text-light mt-4">
        <div class="col-md-3">
            <i class="bi bi-shield-check" style="font-size: 2rem; color: #00cc88;"></i>
            <h6 class="mt-2">📦Secure Shopping</h6>
            <p class="small">Your data & payments are always protected.</p>
        </div>
        <div class="col-md-3">
            <i class="bi bi-truck" style="font-size: 2rem; color: #00cc88;"></i>
            <h6 class="mt-2">🚚Fast Delivery</h6>
            <p class="small">Get your laptops delivered in 1-3 days.</p>
        </div>
        <div class="col-md-3">
            <i class="bi bi-arrow-repeat" style="font-size: 2rem; color: #00cc88;"></i>
            <h6 class="mt-2">🌱Eco-Friendly Delivery</h6>
            <p class="small">Fast shipping with minimal environmental impact.</p>
        </div>
        <div class="col-md-3">
            <i class="bi bi-star-fill" style="font-size: 2rem; color: #00cc88;"></i>
            <h6 class="mt-2">⭐Rated 4.8/5</h6>
            <p class="small">Thousands of happy customers.</p>
        </div>
    </div>
</div>

<!-- Footer -->
<footer class="bg-dark text-light pt-4 mt-5">
  <div class="container">
    <div class="row">
      <!-- Brand -->
      <div class="col-md-3 mb-3">
        <h5 class="text-success">Lapcart</h5>
        <p class="small">Your one-stop store for laptops.</p>
      </div>

      <!-- Quick Links -->
      <div class="col-md-3 mb-3">
        <h6 class="text-uppercase">Quick Links</h6>
        <ul class="list-unstyled">
          <li><a href="index.php" class="text-light text-decoration-none">Home</a></li>
          <li><a href="shop.php" class="text-light text-decoration-none">Shop</a></li>
          <li><a href="contact.php" class="text-light text-decoration-none">Contact</a></li>
          <li><a href="faqs.php" class="text-light text-decoration-none">FAQs</a></li>
        </ul>
      </div>

      <!-- Contact -->
      <div class="col-md-3 mb-3">
        <h6 class="text-uppercase">Contact</h6>
        <p class="small mb-1">📍 Rajkot, India</p>
        <p class="small mb-1">📞 +91 98765 43210</p>
        <p class="small">✉️ support@lapcart.com</p>
      </div>

      <!-- Social -->
    <div class="col-md-3 mb-3">
      <h6 class="text-uppercase">Follow Us</h6>
      <a href="https://www.facebook.com/" target="_blank" class="me-2 social-icon fb">
        <i class="bi bi-facebook"></i>
      </a>
      <a href="https://www.instagram.com/?flo=true" target="_blank" class="me-2 social-icon ig">
        <i class="bi bi-instagram"></i>
      </a>
      <a href="https://twitter.com/" target="_blank" class="me-2 social-icon tw">
        <i class="bi bi-twitter-x"></i>
      </a>
      <a href="https://www.linkedin.com/uas/login?session_redirect=https%3A%2F%2Fwww.linkedin.com%2Ffeed%2F%3Ftrk%3D404_page" target="_blank" class="me-2 social-icon li">
        <i class="bi bi-linkedin"></i>
      </a>
      <a href="https://www.youtube.com/account" target="_blank" class="social-icon yt">
        <i class="bi bi-youtube"></i>
      </a>
    </div>

    </div>
    <h1><div class="text-center mb-3">
    <a href="team.php" class="team-link">
        👥 Meet Our Team➡
    </a>

    <style>
    .team-link {
        color: #2E7D32;              /* green base */
        font-weight: 600;
        font-size: 35px;
        text-decoration: none;
        position: relative;
        transition: color 0.3s ease;
    }

    .team-link::after {
        content: "";
        position: absolute;
        left: 0;
        bottom: -3px;
        width: 100%;
        height: 2px;
        background-color: #2E7D32;
        transform: scaleX(0);
        transform-origin: right;
        transition: transform 0.3s ease;
    }

    .team-link:hover {
        color: #1B5E20;             /* darker green on hover */
    }

    .team-link:hover::after {
        transform: scaleX(1);
        transform-origin: left;
    }
    </style>

</div>
</h1>
    <hr class="border-light">
    <p class="text-center small mb-0">&copy; <?= date("Y") ?> Lapcart. All rights reserved.</p>
  </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- ⬆ Scroll to Top Button -->
<button onclick="window.scrollTo({top: 0, behavior: 'smooth'})"
        class="btn btn-success position-fixed bottom-0 end-0 m-4 rounded-circle"
        title="Go to top">
    ⬆
</button>
</body>
</html>
