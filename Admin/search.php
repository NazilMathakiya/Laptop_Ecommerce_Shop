<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("DB Error: " . $conn->connect_error);
}

$q = isset($_GET['q']) ? $conn->real_escape_string($_GET['q']) : '';

if ($q == '') {
    exit;
}

echo "<div class='list-group'>";

// Users
$user_sql = "SELECT user_id, full_name, user_email 
             FROM user_master 
             WHERE full_name LIKE '%$q%' OR user_email LIKE '%$q%' LIMIT 5";
$users = $conn->query($user_sql);
if ($users->num_rows > 0) {
    echo "<h6 class='mt-2'>Users</h6>";
    while ($row = $users->fetch_assoc()) {
        echo "<a href='manage_user.php?id={$row['user_id']}' class='list-group-item list-group-item-action'>
                👤 {$row['full_name']} ({$row['user_email']})
              </a>";
    }
}

// Products
$product_sql = "SELECT product_id, product_name 
                FROM product_master 
                WHERE product_name LIKE '%$q%' OR product_description LIKE '%$q%' LIMIT 5";
$products = $conn->query($product_sql);
if ($products->num_rows > 0) {
    echo "<h6 class='mt-2'>Products</h6>";
    while ($row = $products->fetch_assoc()) {
        echo "<a href='manageproducts.php?id={$row['product_id']}' class='list-group-item list-group-item-action'>
                📦 {$row['product_name']}
              </a>";
    }
}

// Reviews
$review_sql = "SELECT r.review_id, r.comment, u.full_name 
               FROM review_master r 
               JOIN user_master u ON r.user_id = u.user_id 
               WHERE r.comment LIKE '%$q%' OR u.full_name LIKE '%$q%' LIMIT 5";
$reviews = $conn->query($review_sql);
if ($reviews->num_rows > 0) {
    echo "<h6 class='mt-2'>Reviews</h6>";
    while ($row = $reviews->fetch_assoc()) {
        echo "<a href='view_reviews.php?id={$row['review_id']}' class='list-group-item list-group-item-action'>
                ⭐ {$row['comment']} - by {$row['full_name']}
              </a>";
    }
}

// Orders
$order_sql = "SELECT order_id, order_status, total_amount, full_name 
              FROM order_master 
              WHERE order_id LIKE '%$q%' OR order_status LIKE '%$q%' OR full_name LIKE '%$q%' LIMIT 5";
$orders = $conn->query($order_sql);
if ($orders->num_rows > 0) {
    echo "<h6 class='mt-2'>Orders</h6>";
    while ($row = $orders->fetch_assoc()) {
        echo "<a href='totalorders.php?id={$row['order_id']}' class='list-group-item list-group-item-action'>
                🛒 Order #{$row['order_id']} - {$row['order_status']} (₹{$row['total_amount']})
              </a>";
    }
}

echo "</div>";
?>
