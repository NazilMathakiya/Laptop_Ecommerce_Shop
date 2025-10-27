<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if ($conn->connect_error) {
    http_response_code(500);
    echo "Database connection error.";
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo "LOGIN_REQUIRED";
    exit;
}

$user_id = $_SESSION['user_id'];
$product_id = intval($_POST['product_id']);
$quantity = intval($_POST['quantity'] ?? 1);

// Check stock
$stmt = $conn->prepare("SELECT stock_quantity FROM product_master WHERE product_id = ?");
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($stock);
$stmt->fetch();
$stmt->close();

if ($stock < 1) {
    echo "Out of stock.";
    exit;
}

// Check if item already in cart
$check = $conn->query("SELECT quantity FROM cart_master WHERE user_id = $user_id AND product_id = $product_id");

if ($check->num_rows > 0) {
    $row = $check->fetch_assoc();
    if ($row['quantity'] < $stock) {
        $conn->query("UPDATE cart_master SET quantity = quantity + 1 WHERE user_id = $user_id AND product_id = $product_id");
        echo "Quantity updated in cart.";
    } else {
        echo "Only $stock item(s) available.";
    }
} else {
    $conn->query("INSERT INTO cart_master (user_id, product_id, quantity) VALUES ($user_id, $product_id, 1)");
    echo "Item added to cart.";
}
?>
