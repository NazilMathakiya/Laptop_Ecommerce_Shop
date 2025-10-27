<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

session_start();
$success = $_SESSION['success'] ?? "";
$error = $_SESSION['error'] ?? "";
unset($_SESSION['success'], $_SESSION['error']);

$product = null;

// Load product if GET request
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = (int)$_GET['id'];
    $result = $conn->query("SELECT * FROM product_master WHERE product_id = $id");

    if ($result && $result->num_rows > 0) {
        $product = $result->fetch_assoc();
    } else {
        $_SESSION['error'] = "Product not found.";
        header("Location: edit_product.php?id=$id");
        exit;
    }
}

// Process update
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = (int)$_POST['id'];
    $name = $conn->real_escape_string($_POST['product_name']);
    $desc = $conn->real_escape_string($_POST['product_description']);
    $price = (float)$_POST['product_price'];
    $qty = (int)$_POST['stock_quantity'];
    $image_path = $conn->real_escape_string($_POST['image_path']);

    if ($qty <= 0) {
        $_SESSION['error'] = "Stock quantity must be greater than 0.";
    } else {
        $update = "UPDATE product_master 
                   SET product_name='$name', 
                       product_description='$desc', 
                       product_price=$price, 
                       stock_quantity=$qty, 
                       image_path='$image_path' 
                   WHERE product_id=$id";

        if ($conn->query($update)) {
            $_SESSION['success'] = "Product updated successfully.";
        } else {
            $_SESSION['error'] = "Failed to update product.";
        }
    }
    header("Location: edit_product.php?id=$id");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --primary: #28a745;
            --dark-bg: #121212;
            --card-bg: #1e1e1e;
            --text: #e6e6e6;
            --label: #00ffcc;
        }

        body {
            background-color: var(--dark-bg);
            color: var(--text);
        }

        .container {
            max-width: 700px;
            margin-top: 60px;
            position: relative;
        }

        .card {
            background-color: var(--card-bg);
            border: 1px solid var(--primary);
            padding: 30px;
            border-radius: 12px;
        }

        .form-control {
            background-color: #2b2b2b;
            border: 1px solid var(--primary);
            color: var(--text);
        }

        .form-label {
            margin-top: 10px;
            color: var(--label);
        }

        .btn-success {
            background-color: var(--primary);
            border: none;
        }

        .btn-success:hover {
            background-color: #2B4A0B;
        }

        .alert {
            margin-bottom: 20px;
        }

        .back-global {
            position: fixed;
            top: 20px;
            right: 30px;
            z-index: 1000;
        }

        .back-global a {
            color: var(--primary);
            text-decoration: none;
            font-weight: bold;
        }

        .back-global a:hover {
            text-decoration: underline;
        }

        .img-preview {
            max-width: 100%;
            height: auto;
            border: 1px solid #555;
            margin-top: 10px;
            margin-bottom: 15px;
            border-radius: 5px;
        }
    </style>
</head>
<body>

<!-- Back Link on top-right of screen -->
<div class="back-global">
    <a href="manageproducts.php">← Back to Manage Products</a>
</div>

<div class="container">
    <?php if ($error): ?>
        <div class="alert alert-danger animate__animated animate__fadeInDown"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success animate__animated animate__fadeInDown"><?= $success ?></div>
    <?php endif; ?>

    <div class="card animate__animated animate__fadeIn">
        <h2 class="text-center mb-4 text-success">Edit Product</h2>

        <?php if ($product): ?>
        <form method="post">
            <input type="hidden" name="id" value="<?= $product['product_id'] ?>">

            <label class="form-label">Product Name:</label>
            <input type="text" name="product_name" class="form-control" value="<?= htmlspecialchars($product['product_name']) ?>" required>

            <label class="form-label">Description:</label>
            <textarea name="product_description" class="form-control" required><?= htmlspecialchars($product['product_description']) ?></textarea>

            <label class="form-label">Price (₹):</label>
            <input type="number" name="product_price" class="form-control" value="<?= $product['product_price'] ?>" required step="0.01">

            <label class="form-label">Stock Quantity:</label>
            <input type="number" name="stock_quantity" class="form-control" value="<?= $product['stock_quantity'] ?>" required min="1">

            <label class="form-label">Image URL:</label>
            <input type="url" name="image_path" id="image_path" class="form-control"
                   value="<?= htmlspecialchars($product['image_path']) ?>" required
                   oninput="updatePreview(this.value)">

            <img src="<?= htmlspecialchars($product['image_path']) ?>" id="preview" class="img-preview" alt="Image Preview">

            <button type="submit" class="btn btn-success mt-4 w-100">Update Product</button>
        </form>
        <?php endif; ?>
    </div>
</div>

<script>
    function updatePreview(url) {
        document.getElementById('preview').src = url;
    }
</script>
<script>
    function updatePreview(url) {
        document.getElementById('preview').src = url;
    }

    // Auto-hide alert after 2 seconds
    setTimeout(() => {
        const alertBox = document.querySelector('.alert');
        if (alertBox) {
            alertBox.classList.add('animate__fadeOutUp');
            setTimeout(() => alertBox.remove(), 1000); // remove after fade out
        }
    }, 2000);
</script>
<script>
    function updatePreview(url) {
        document.getElementById('preview').src = url;
    }

    // Auto-hide alert after 2 seconds
    setTimeout(() => {
        const alertBox = document.querySelector('.alert');
        if (alertBox) {
            alertBox.classList.add('animate__fadeOutUp');
            setTimeout(() => {
                alertBox.remove(); // remove from DOM to allow layout reflow
            }, 1000); // wait for fade out animation to finish
        }
    }, 2000);
</script>

</body>
</html>
