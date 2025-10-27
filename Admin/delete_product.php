<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $sql = "DELETE FROM product_master WHERE product_id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: manageproducts.php?msg=deleted");
        exit;
    } else {
        echo "❌ Error deleting product: " . $conn->error;
    }
} else {
    echo "⚠️ No product ID provided.";
}
?>
