<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['delete_selected'])) {
    if (!empty($_POST['selected_ids']) && is_array($_POST['selected_ids'])) {
        $ids = $_POST['selected_ids'];
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $stmt = $conn->prepare("DELETE FROM product_master WHERE product_id IN ($placeholders)");

        if ($stmt) {
            $types = str_repeat('i', count($ids));
            $stmt->bind_param($types, ...$ids);
            $stmt->execute();
            $stmt->close();
        }
    }
}

header("Location: manageproducts.php?msg=bulk_deleted");
exit;
