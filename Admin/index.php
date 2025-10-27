<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Stats
$user_result = $conn->query("SELECT COUNT(*) AS cnt FROM user_master");
$total_users = ($user_result) ? $user_result->fetch_assoc()['cnt'] : 0;

$order_result = $conn->query("SELECT COUNT(*) AS cnt FROM order_master");
$total_orders = ($order_result) ? $order_result->fetch_assoc()['cnt'] : 0;

$today_result = $conn->query("SELECT COUNT(*) AS cnt FROM order_master WHERE DATE(order_date) = CURDATE()");
$today_orders = ($today_result) ? $today_result->fetch_assoc()['cnt'] : 0;

$review_result = $conn->query("SELECT COUNT(*) AS cnt FROM review_master");
$total_reviews = ($review_result) ? $review_result->fetch_assoc()['cnt'] : 0;

$product_result = $conn->query("SELECT COUNT(*) AS cnt FROM product_master");
$total_products = ($product_result) ? $product_result->fetch_assoc()['cnt'] : 0;

$revenue_result = $conn->query("SELECT SUM(total_amount) AS revenue FROM order_master WHERE order_status='Delivered'");
$total_revenue = 0;
if ($revenue_result) {
    $row = $revenue_result->fetch_assoc();
    $total_revenue = $row['revenue'] ?? 0;  // <-- NULL safe
}
?>
<?php
if($total_revenue >= 1000000){
    echo "<script>
        Swal.fire({
            title: '🎉 Milestone Achieved!',
            text: 'Revenue crossed ₹10,00,000!',
            icon: 'success',
            timer: 4000,
            showConfirmButton: false
        });
    </script>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>Admin Dashboard</title>
    <link href="css/styles.css" rel="stylesheet" />
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body { background-color: #121212; color: #fff; }
        .card { border-radius: 12px; transition: .3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,.3); }
        .card-body { font-size: 1.2rem; font-weight: 600; letter-spacing: .5px; }
        footer.bg-light { background-color: #222 !important; color: #ccc; }
        footer a { color: #00c0ff; }
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
        }

        .hover-scale:hover {
            transform: translateY(-8px) scale(1.03);
            box-shadow: 0 20px 30px rgba(0, 255, 150, 0.3);
        }
        .hover-scale {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-radius: 12px;
        }

        .hover-scale:hover {
            transform: translateY(-5px) scale(1.05);
            box-shadow: 0 15px 25px rgba(0, 255, 150, 0.3);
        }

        /* Optional: smooth horizontal scroll */
        .row.flex-nowrap {
            overflow-x: auto;
            padding-bottom: 10px;
        }
        /* When toggled, hide sidebar on small screens */
        #layoutSidenav.sb-sidenav-toggled #layoutSidenav_nav {
            margin-left: -250px; /* Adjust width of sidebar */
            transition: margin-left 0.3s ease;
        }

        #layoutSidenav_nav {
            width: 250px;
            transition: margin-left 0.3s ease;
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
    </style>
</head>
<body class="sb-nav-fixed">
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="index.php"><br><br><br><h2>Admin Panel</h2></a>  
    </nav>

    <div id="layoutSidenav">
        <!-- TOP NAVBAR -->
        <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
            <!-- Sidebar Toggle Button -->
            <button class="btn btn-link btn-sm order-1 order-lg-0 me-4" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>

            <!-- Brand -->
            <a href="index.php" class="text-decoration-none">
              <div class="lapcart-logo me-4">LAPCART</div>
            </a>
        </nav>

        <!-- SIDEBAR -->
        <div id="layoutSidenav_nav">
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading"></div>
                        <a class="nav-link active" href="index.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-tachometer-alt"></i></div>
                            Dashboard
                        </a>

                        <div class="sb-sidenav-menu-heading">Management</div>
                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseOrders">
                            <div class="sb-nav-link-icon"><i class="fas fa-shopping-cart"></i></div>
                            Orders
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseOrders" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="totalorders.php">All Orders</a>
                                <a class="nav-link" href="pending_orders.php">Pending</a>
                                <a class="nav-link" href="processed_orders.php">Processing</a>
                                <a class="nav-link" href="shipped_orders.php">Shipped</a>
                                <a class="nav-link" href="delivered_orders.php">Delivered</a>
                                <a class="nav-link" href="cancelled_orders.php">Cancelled</a>
                            </nav>
                        </div>

                        <a class="nav-link collapsed" href="#" data-bs-toggle="collapse" data-bs-target="#collapseProducts">
                            <div class="sb-nav-link-icon"><i class="fas fa-box"></i></div>
                            Products
                            <div class="sb-sidenav-collapse-arrow"><i class="fas fa-angle-down"></i></div>
                        </a>
                        <div class="collapse" id="collapseProducts" data-bs-parent="#sidenavAccordion">
                            <nav class="sb-sidenav-menu-nested nav">
                                <a class="nav-link" href="manageproducts.php">Manage Products</a>
                                <a class="nav-link" href="add_product.php">Add Product</a>
                            </nav>
                        </div>

                        <a class="nav-link" href="manage_user.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-users"></i></div>
                            Users
                        </a>
                        <a class="nav-link" href="view_reviews.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-star"></i></div>
                            Reviews
                        </a>
                        <a class="nav-link" href="revenue.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-chart-line"></i></div>
                            Revenue
                        </a>

                        <div class="sb-sidenav-menu-heading">Account</div>
                        <a class="nav-link" href="admin_profile.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-user-cog"></i></div>
                            Profile
                        </a>
                        <a class="nav-link" href="logout.php">
                            <div class="sb-nav-link-icon"><i class="fas fa-sign-out-alt"></i></div>
                            Logout
                        </a>
                    </div>
                </div>
                <div class="sb-sidenav-footer">
                    <div class="small">Logged in as:</div>
                    <strong>Admin</strong>
                </div>
            </nav>
        </div>

        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    <h1 class="mt-4">Dashboard</h1>

                    <!-- SEARCH BOX --><input type="text" id="searchBox" placeholder="Search Orders / Users / Products" class="form-control mb-3">
                    <div id="searchResults"></div>


                    <ol class="breadcrumb mb-4">
                        <li class="breadcrumb-item active">Dashboard</li>
                    </ol>

                    <!-- Cards Grid (5 cards in 2 rows) -->
                    <div class="row g-4 mb-5">
                        <!-- Revenue (wide) -->
                        <div class="col-md-6">
                            <div class="card bg-danger text-white h-100 shadow-lg hover-scale">
                                <div class="card-body display-6">Revenue: ₹<?= number_format($total_revenue,2) ?></div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <a class="text-white stretched-link" href="revenue.php">See Revenue</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Orders (wide) -->
                        <div class="col-md-6">
                            <div class="card bg-primary text-white h-100 shadow-lg hover-scale">
                                <div class="card-body display-6">Orders: <?= $total_orders ?></div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <a class="text-white stretched-link" href="totalorders.php">View Orders</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Users -->
                        <div class="col-md-4">
                            <div class="card bg-success text-white h-100 shadow-lg hover-scale">
                                <div class="card-body display-6">Users: <?= $total_users ?></div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <a class="text-white stretched-link" href="manage_user.php">Manage</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Products -->
                        <div class="col-md-4">
                            <div class="card bg-warning text-dark h-100 shadow-lg hover-scale">
                                <div class="card-body display-6">Products: <?= $total_products ?></div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <a class="text-dark stretched-link" href="manageproducts.php">Manage</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>

                        <!-- Reviews -->
                        <div class="col-md-4">
                            <div class="card bg-info text-white h-100 shadow-lg hover-scale">
                                <div class="card-body display-6">Reviews: <?= $total_reviews ?></div>
                                <div class="card-footer d-flex justify-content-between align-items-center">
                                    <a class="text-white stretched-link" href="view_reviews.php">View</a>
                                    <i class="fas fa-angle-right"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </main>

            <footer class="py-4 bg-light mt-auto">
                <div class="container-fluid px-4">
                    <div class="d-flex align-items-center justify-content-between small">
                        <div></div>
                        <div><a href="privacy.php">Privacy Policy</a> &middot; <a href="terms.php">Terms &amp; Conditions</a></div>
                    </div>
                </div>
            </footer>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle
        const sidebarToggle = document.getElementById('sidebarToggle');
        const layoutSidenav = document.getElementById('layoutSidenav');

        sidebarToggle.addEventListener('click', () => {
            layoutSidenav.classList.toggle('sb-sidenav-toggled');
        });
    </script>
    <script>
    const searchBox = document.getElementById('searchBox');
    const searchResults = document.getElementById('searchResults');

    searchBox.addEventListener('input', () => {
        const query = searchBox.value.trim();
        if(query.length > 0) {
            fetch('search.php?q=' + encodeURIComponent(query))
                .then(res => res.text())
                .then(html => { searchResults.innerHTML = html; });
        } else {
            searchResults.innerHTML = '';
        }
    });
    </script>


</body>
</html>
