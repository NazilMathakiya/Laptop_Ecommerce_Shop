<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if (isset($_POST['delete_selected']) && isset($_POST['order_ids'])) {
    foreach ($_POST['order_ids'] as $order_id) {
        $order_id = intval($order_id);
        $conn->query("DELETE FROM order_items WHERE order_id = $order_id");
        $conn->query("DELETE FROM order_master WHERE order_id = $order_id");
    }
    $_SESSION['popup_message'] = "Selected orders deleted successfully.";
    $_SESSION['popup_type'] = "success";
} else {
    $_SESSION['popup_message'] = "No orders selected.";
    $_SESSION['popup_type'] = "warning";
}
$queryString = $_SERVER['QUERY_STRING']; 
header("Location: orders.php" . ($queryString ? "?$queryString" : ""));
exit();

?>
