<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$msg = "";
$redirect = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand_name = trim($_POST["brand_name"]);

    if ($brand_name == "") {
        $msg = "❌ Brand name cannot be empty.";
    } else {
        $stmt = $conn->prepare("INSERT INTO brand_master (brand_name) VALUES (?)");
        $stmt->bind_param("s", $brand_name);

        if ($stmt->execute()) {
            $msg = "✅ Brand added successfully! Redirecting to product page...";
            $redirect = true;
        } else {
            if ($conn->errno == 1062) {
                $msg = "⚠️ Brand already exists.";
            } else {
                $msg = "❌ Error: " . $conn->error;
            }
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Brand</title>
    <style>
        body { background: #111; color: #eee; font-family: Arial; padding: 20px; }
        form { background: #222; padding: 20px; border-radius: 8px; max-width: 400px; margin: auto; }
        input { width: 100%; margin: 10px 0; padding: 10px; background: #333; color: #fff; border: none; border-radius: 4px; }
        button { background: #00cc66; color: black; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; }
        .msg { text-align: center; margin-top: 10px; color: lightgreen; }
        a { color: #00cc66; display: block; text-align: center; margin-top: 10px; text-decoration: none; }
    </style>
    <?php if ($redirect): ?>
        <meta http-equiv="refresh" content="2;url=add_product.php">
    <?php endif; ?>
</head>
<body>
    <h2 align="center">Add Brand</h2>
    <form method="POST">
        <input type="text" name="brand_name" placeholder="Brand Name" required>
        <button type="submit">Add Brand</button>
        <div class="msg"><?php echo $msg; ?></div>
    </form>

    <a href="add_product.php">← Back to Add Product</a>
</body>
</html>
