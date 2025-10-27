<?php
header("Content-Type: application/json");

$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

$brand_name = isset($_POST["brand_name"]) ? trim($_POST["brand_name"]) : "";

if ($brand_name === "") {
    echo json_encode(["success" => false, "message" => "Brand name is required."]);
    exit;
}

$stmt = $conn->prepare("INSERT INTO brand_master (brand_name) VALUES (?)");
$stmt->bind_param("s", $brand_name);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "Brand added.", "brand_id" => $stmt->insert_id]);
} else {
    if ($conn->errno == 1062) {
        echo json_encode(["success" => false, "message" => "⚠️ Brand already exists."]);
    } else {
        echo json_encode(["success" => false, "message" => "❌ Error: " . $conn->error]);
    }
}
$stmt->close();
