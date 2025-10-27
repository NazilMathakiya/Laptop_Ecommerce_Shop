<?php
session_start();

// Connect to MySQL database
$conn = new mysqli("localhost", "root", "", "laptop_store");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Validate order_id
if (!isset($_GET['order_id']) || !is_numeric($_GET['order_id'])) {
    $_SESSION['message'] = "Invalid order ID.";
    header("Location: user_orders.php");
    exit();
}

$order_id = intval($_GET['order_id']);

// Step 1: Delete related records from order_items
$stmt_items = $conn->prepare("DELETE FROM order_items WHERE order_id = ?");
$stmt_items->bind_param("i", $order_id);
$stmt_items->execute();
$stmt_items->close();

// Step 2: Delete from order_master
$stmt_order = $conn->prepare("DELETE FROM order_master WHERE order_id = ?");
$stmt_order->bind_param("i", $order_id);

if ($stmt_order->execute()) {
    $_SESSION['message'] = "Order deleted successfully.";
} else {
    $_SESSION['message'] = "Failed to delete order.";
}
$stmt_order->close();

// Close DB connection
$conn->close();

// Redirect back to user's orders page
header("Location: view_order.php");
exit();
?>
