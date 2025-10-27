<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

function getBrandName($conn, $brand_id) {
    $stmt = $conn->prepare("SELECT brand_name FROM brand_master WHERE brand_id = ?");
    $stmt->bind_param("i", $brand_id);
    $stmt->execute();
    $stmt->bind_result($brand_name);
    $stmt->fetch();
    $stmt->close();
    return $brand_name ?? 'Unknown';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>LAPCART - Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #111;
            color: #fff;
        }
        .navbar {
            background-color: #222;
        }
        .navbar a, .dropdown-item {
            color: #B5B5B5 !important;
        }
        .navbar a:hover, .dropdown-item:hover {
            color: #fff !important;
        }
        .card {
            background-color: #1e1e1e;
            border: 1px solid #333;
        }
        .card img {
            height: 180px;
            object-fit: contain;
            cursor: pointer;
        }
        .sticky-top-shadow {
            box-shadow: 0 4px 6px rgba(0,0,0,0.5);
        }
        .left-border {
            border-right: 2px solid #444;
        }
        select {
            background-color: #1e1e1e;
            color: #fff;
            border: 1px solid #333;
        }
        .price-label {
            color: #ffffff;
            font-weight: bold;
        }
        .price-input {
            color: #0f0;
            background-color: #1e1e1e;
            border: 1px solid #333;
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
        input.form-control::placeholder {
          color: #aaa;
        }
        input.form-control:focus {
          border-color: #0f0;
          box-shadow: 0 0 5px #0f0;
        }
        .product-description {
            display: none;
            color: #ccc;
            margin-top: 8px;
        }
        <style>
.card-title, .fw-bold {
    text-align: center;
}
.card .btn {
    border-radius: 25px;
}
</style>

    </style>
</head>
<body>

<!-- 🌐 Navbar -->
<div class="sticky-top bg-dark shadow-sm">
    <nav class="navbar navbar-expand-lg bg-dark px-3">
        <div class="container-fluid d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center">
                <a href="index.php"><div class="lapcart-logo me-4">LAPCART</div></a>
                <ul class="navbar-nav d-flex flex-row gap-3">
                    <li class="nav-item"><a class="nav-link text-white" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="shop.php">Shop</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="aboutus.php">About</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="faqs.php">FAQs</a></li>
                    <li class="nav-item"><a class="nav-link text-white" href="help.php">Help</a></li>
                </ul>
            </div>

            <div class="d-flex align-items-center gap-3">
                <a href="addtocart.php" class="btn btn-outline-light position-relative">
                    🛒
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

    <!-- 🔍 Search Bar -->
    <div class="container d-flex flex-column flex-md-row align-items-center justify-content-between py-2">
        <form class="d-flex w-100 w-md-50" method="GET" action="shop.php">
            <input 
                class="form-control me-2 bg-dark text-light border border-success" 
                type="search" 
                name="search" 
                placeholder="Search laptops..." 
                value="<?= isset($_GET['search']) ? $_GET['search'] : '' ?>">
            <button class="btn btn-success" type="submit">Search</button>
        </form>
    </div>
</div>

<!-- 💡 Content -->
<div class="container mt-4">
    <div class="row">
        <!-- Filters -->
        <div class="col-md-3 mb-4 left-border">
            <div class="card p-3 mb-3">
                <h5 class="text-success">Filter by Price</h5>
                <form method="GET" action="shop.php">
                    <div class="mb-2">
                        <label class="form-label price-label">Min Price (₹)</label>
                        <input type="number" class="form-control price-input" name="min_price" value="<?= $_GET['min_price'] ?? '' ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label price-label">Max Price (₹)</label>
                        <input type="number" class="form-control price-input" name="max_price" value="<?= $_GET['max_price'] ?? '' ?>">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Apply</button>
                </form>
            </div>

            <div class="card p-3">
    <h5 class="text-success">Filter by Brand</h5>
    <form method="GET" action="shop.php">
        <select class="form-select mb-3" name="brand_id" onchange="this.form.submit()">
            <option value="">-- All Brands --</option>
            <?php
            $brands = $conn->query("SELECT brand_id, brand_name FROM brand_master");
            while ($b = $brands->fetch_assoc()) {
                $selected = ($_GET['brand_id'] ?? '') == $b['brand_id'] ? 'selected' : '';
                echo "<option value='{$b['brand_id']}' $selected>{$b['brand_name']}</option>";
            }
            ?>
        </select>
    </form>
</div>

        </div>

        <!-- Product Cards -->
        <div class="col-md-9">
            <div class="row">

                <?php
                    $limit = 6;
                    $page = $_GET['page'] ?? 1;
                    $start = ($page - 1) * $limit;
                    $query = "SELECT * FROM product_master WHERE 1 ";
                    
                    if (!empty($_GET['search'])) {
                        $search = $conn->real_escape_string($_GET['search']);
                        $query .= "AND product_name LIKE '%$search%' ";
                    }
                    if (isset($_GET['min_price']) && $_GET['min_price'] !== '') {
                        $query .= "AND product_price >= " . intval($_GET['min_price']) . " ";
                    }
                    if (isset($_GET['max_price']) && $_GET['max_price'] !== '') {
                        $query .= "AND product_price <= " . intval($_GET['max_price']) . " ";
                    }
                    if (isset($_GET['brand_id']) && $_GET['brand_id'] !== '') {
                        $query .= "AND brand_id = " . intval($_GET['brand_id']) . " ";
                    }

                    $total_result = $conn->query($query);
                    $total_rows = $total_result->num_rows;
                    $total_pages = ceil($total_rows / $limit);
                    $query .= "LIMIT $start, $limit";
                    $result = $conn->query($query);

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo '
<div class="col-md-4 mb-4">
    <div class="card h-100 text-light text-center d-flex flex-column">
        <img src="' . $row["image_path"] . '" class="card-img-top" alt="' . $row["product_name"] . '">
        <div class="card-body d-flex flex-column justify-content-between">
            <div>
                <h5 class="card-title text-success fw-bold">' . $row["product_name"] . '</h5>
                <p class="fw-bold text-white fs-5">₹' . $row["product_price"] . '</p>
            </div>
            <div class="mt-auto">
                <form method="post" class="ajax-cart-form" data-product-id="' . $row['product_id'] . '">
                    <input type="hidden" name="product_id" value="' . $row['product_id'] . '">
                    <input type="hidden" name="product_name" value="' . $row['product_name'] . '">
                    <input type="hidden" name="product_price" value="' . $row['product_price'] . '">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit" class="btn btn-success w-100">Add to Cart</button>
                </form>
                <a href="product_detail.php?product_id=' . $row["product_id"] . '" class="btn btn-outline-light w-100 mt-2">Show Details</a>
            </div>
        </div>
    </div>
</div>';

                        }
                    } else {
                        echo '<div class="col-12 text-center text-light"><p>No products found.</p></div>';
                    }
                    ?>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                            <a class="page-link bg-dark text-light" href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function toggleDescription(id) {
    const desc = document.getElementById(id);
    desc.style.display = desc.style.display === "none" || desc.style.display === "" ? "block" : "none";
}
</script>
<script>
document.querySelectorAll('.ajax-cart-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);

        fetch('addtocart_ajax.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            if (result === "LOGIN_REQUIRED") {
                alert("Please login to add items to your cart.");
                window.location.href = "login.php";
            } else {
                alert(result); // Replace with your popup logic if needed
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert("Error adding to cart.");
        });
    });
});
</script>

</body>
</html>
