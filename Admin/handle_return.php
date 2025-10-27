<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['action']) && isset($_GET['item_id'])){
    $action = $_GET['action'];
    $item_id = intval($_GET['item_id']);

    if($action == 'approve'){
        $status = 'approved';
    } elseif($action == 'reject'){
        $status = 'rejected';
    } else {
        die("Invalid action.");
    }

    $stmt = $conn->prepare("UPDATE order_items SET return_status=? WHERE order_item_id=?");
    $stmt->bind_param("si", $status, $item_id);
    $stmt->execute();

    $_SESSION['msg'] = "Return request has been $status successfully!";
    header("Location: return_requests.php");
    exit();
}
?>
