<?php
session_start();
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    echo json_encode([
        "valid" => false,
        "message" => "Database connection failed."
    ]);
    exit;
}

if (!isset($_SESSION['user_id'])) {
    echo json_encode([
        "valid" => false,
        "message" => "Please login first."
    ]);
    exit;
}

$user_id = intval($_SESSION['user_id']);
$coupon = strtoupper(trim($_POST['coupon'] ?? ""));
$total_amount = floatval($_POST['total_amount'] ?? 0);

$response = [
    "valid" => false,
    "message" => "Invalid coupon code.",
    "new_total" => $total_amount,
    "coupon_code" => null,
    "discount_amount" => 0
];

if ($coupon === "SAVE500") {
    if ($total_amount >= 5000) {
        $discount = 500;
        $new_total = max(0, $total_amount - $discount);
        $response = [
            "valid" => true,
            "message" => "₹500 discount applied using SAVE500!",
            "new_total" => $new_total,
            "coupon_code" => "SAVE500",
            "discount_amount" => $discount
        ];
    } else {
        $response = [
            "valid" => false,
            "message" => "SAVE500 requires a minimum order of ₹5000.",
            "new_total" => $total_amount
        ];
    }
}

if ($coupon === "WELCOME10") {
    // Check if this user already used WELCOME10 before
    $sql = "SELECT COUNT(*) AS cnt 
            FROM order_master 
            WHERE user_id = $user_id AND coupon_code = 'WELCOME10'";
    $res = $conn->query($sql);
    $row = $res->fetch_assoc();

    if ($row['cnt'] == 0) {
        // First time using this coupon → allow 10% discount
        $discount = round($total_amount * 0.10, 2);
        $new_total = max(0, $total_amount - $discount);
        $response = [
            "valid" => true,
            "message" => "WELCOME10 applied: 10% off this order!",
            "new_total" => $new_total,
            "coupon_code" => "WELCOME10",
            "discount_amount" => $discount
        ];
    } else {
        $response = [
            "valid" => false,
            "message" => "WELCOME10 can only be used once per user.",
            "new_total" => $total_amount
        ];
    }
}

echo json_encode($response);
