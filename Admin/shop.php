<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Total products
$countSql = "SELECT COUNT(*) AS total FROM product_master WHERE product_name LIKE '%$search%'";
$totalResult = $conn->query($countSql);
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

// Fetch products
$sql = "SELECT * FROM product_master 
        WHERE product_name LIKE '%$search%' 
        ORDER BY added_at DESC 
        LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laptop Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #d4d4d4;
        }
        .card {
            background-color: #2c2c2c;
            color: #ffffff;
            border: 1px solid #444;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .btn-outline-light:hover {
            background-color: #ffffff;
            color: #000;
        }
        .pagination .page-link {
            background-color: #2c2c2c;
            color: #fff;
            border: 1px solid #444;
        }
        .pagination .page-link:hover {
            background-color: #444;
        }
        .top-bar {
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="d-flex justify-content-between align-items-center top-bar">
        <form class="d-flex" method="GET" action="">
            <input class="form-control me-2" type="search" name="search" placeholder="Search products" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-light" type="submit">Search</button>
        </form>
        <a href="index.html" class="btn btn-outline-light">Back to Previous Page</a>
    </div>

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($row['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['product_name']) ?>" style="height: 220px; object-fit: contain;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['product_name']) ?></h5>
                            <p class="card-text" style="flex-grow: 1;"><?= substr($row['product_description'], 0, 80) ?>...</p>
                            <p class="fw-bold">₹<?= number_format($row['product_price']) ?></p>

                            <form method="POST" action="addtocart.php" class="mt-auto">
                                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['product_name']) ?>">
                                <input type="hidden" name="product_price" value="<?= $row['product_price'] ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary w-100">Add to Cart</button>
                            </form>

                            <a href="product_details.php?id=<?= $row['product_id'] ?>" class="btn btn-outline-light mt-2 w-100">View Reviews</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-4">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

</div>

</body>
</html>
<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Pagination
$limit = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Total products
$countSql = "SELECT COUNT(*) AS total FROM product_master WHERE product_name LIKE '%$search%'";
$totalResult = $conn->query($countSql);
$totalRow = $totalResult->fetch_assoc();
$totalProducts = $totalRow['total'];
$totalPages = ceil($totalProducts / $limit);

// Fetch products
$sql = "SELECT * FROM product_master 
        WHERE product_name LIKE '%$search%' 
        ORDER BY added_at DESC 
        LIMIT $start, $limit";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Laptop Shop</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body {
            background-color: #1a1a1a;
            color: #d4d4d4;
        }
        .card {
            background-color: #2c2c2c;
            color: #ffffff;
            border: 1px solid #444;
        }
        .btn-primary {
            background-color: #28a745;
            border: none;
        }
        .btn-primary:hover {
            background-color: #218838;
        }
        .btn-outline-light:hover {
            background-color: #ffffff;
            color: #000;
        }
        .pagination .page-link {
            background-color: #2c2c2c;
            color: #fff;
            border: 1px solid #444;
        }
        .pagination .page-link:hover {
            background-color: #444;
        }
        .top-bar {
            margin: 20px 0;
        }
    </style>
</head>
<body>

<div class="container">

    <div class="d-flex justify-content-between align-items-center top-bar">
        <form class="d-flex" method="GET" action="">
            <input class="form-control me-2" type="search" name="search" placeholder="Search products" value="<?= htmlspecialchars($search) ?>">
            <button class="btn btn-outline-light" type="submit">Search</button>
        </form>
        <a href="index.html" class="btn btn-outline-light">Back to Previous Page</a>
    </div>

    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($row['image_path']) ?>" class="card-img-top" alt="<?= htmlspecialchars($row['product_name']) ?>" style="height: 220px; object-fit: contain;">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title"><?= htmlspecialchars($row['product_name']) ?></h5>
                            <p class="card-text" style="flex-grow: 1;"><?= substr($row['product_description'], 0, 80) ?>...</p>
                            <p class="fw-bold">₹<?= number_format($row['product_price']) ?></p>

                            <form method="POST" action="addtocart.php" class="mt-auto">
                                <input type="hidden" name="product_id" value="<?= $row['product_id'] ?>">
                                <input type="hidden" name="product_name" value="<?= htmlspecialchars($row['product_name']) ?>">
                                <input type="hidden" name="product_price" value="<?= $row['product_price'] ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary w-100">Add to Cart</button>
                            </form>

                            <a href="product_details.php?id=<?= $row['product_id'] ?>" class="btn btn-outline-light mt-2 w-100">View Reviews</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No products found.</p>
        <?php endif; ?>
    </div>

    <!-- Pagination -->
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center mt-4">
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= urlencode($search) ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
        </ul>
    </nav>

</div>

</body>
</html>