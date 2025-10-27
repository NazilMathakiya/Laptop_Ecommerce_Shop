<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['product_name']);
    $desc = $_POST['product_description'];
    $price = floatval($_POST['product_price']);
    $qty = intval($_POST['stock_quantity']);
    $image_url = $_POST['image_path'];
    $brand_id = null;

    if ($qty <= 0) {
        $error = "Stock quantity must be greater than zero.";
    } else {
        $checkName = $conn->prepare("SELECT product_id FROM product_master WHERE product_name = ?");
        $checkName->bind_param("s", $name);
        $checkName->execute();
        $nameResult = $checkName->get_result();

        if ($nameResult->num_rows > 0) {
            $error = "Product name already exists. Please choose a different name.";
        } else {
            if (!empty($_POST['brand_name'])) {
                $brand = trim($_POST['brand_name']);
                $check = $conn->prepare("SELECT brand_id FROM brand_master WHERE brand_name = ?");
                $check->bind_param("s", $brand);
                $check->execute();
                $result = $check->get_result();
                if ($result->num_rows > 0) {
                    $brand_id = $result->fetch_assoc()['brand_id'];
                } else {
                    $insert = $conn->prepare("INSERT INTO brand_master (brand_name) VALUES (?)");
                    $insert->bind_param("s", $brand);
                    $insert->execute();
                    $brand_id = $insert->insert_id;
                }
                $check->close();
            }

            $stmt = $conn->prepare("INSERT INTO product_master (product_name, product_description, product_price, stock_quantity, brand_id, image_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdiis", $name, $desc, $price, $qty, $brand_id, $image_url);
            if ($stmt->execute()) {
                header("Location: add_product.php?success=1");
                exit();
            } else {
                $error = "Error: " . $stmt->error;
            }
            $stmt->close();
        }
        $checkName->close();
    }
}

if (isset($_GET['success']) && $_GET['success'] == 1) {
    $success = "Product added successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Add Product</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<style>
    body {
        background-color: #121212;
        color: #e0ffe0;
        font-family: 'Segoe UI', sans-serif;
        margin: 0;
        padding: 0;
    }

    .header {
        background-color: #1f1f1f;
        border-bottom: 2px solid #4CAF50;
        color: #b2ffb2;
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 3px 10px rgba(0,255,100,0.2);
    }

    .header h2 {
        margin: 0;
        font-weight: 600;
    }

    .header a {
        background-color: #4CAF50;
        text-decoration: none;
        color: #121212;
        font-weight: 600;
        padding: 6px 14px;
        border: 1px solid #4CAF50;
        border-radius: 6px;
        transition: 0.3s ease;
    }

    .header a:hover {
        background-color: #4CAF50;
        color: #fff;
    }

    .container {
        color: #527A3A;
        max-width: 650px;
        margin: 50px auto;
        background-color: #1e1e1e;
        padding: 35px;
        border-radius: 14px;
        box-shadow: 0 0 25px rgba(0,255,100,0.2);
    }

    label {
        display: block;
        margin-top: 18px;
        font-weight: 500;
        color: #90ee90;
    }

    input, textarea, datalist {
        width: 100%;
        padding: 12px;
        margin-top: 6px;
        background-color: #2b2b2b;
        border: 1px solid #555;
        color: #c4fcca;
        border-radius: 8px;
        font-weight: 500;
    }

    input[type="submit"] {
        background-color: #4CAF50;
        border: none;
        cursor: pointer;
        margin-top: 25px;
        font-weight: 600;
        color: #fff;
        font-size: 1rem;
        border-radius: 8px;
        transition: 0.3s ease;
    }

    input[type="submit"]:hover {
        background-color: #45a049;
    }

    .toast {
        background-color: #222;
        border-left: 5px solid #4CAF50;
        color: #b2ffb2;
        padding: 14px;
        border-radius: 6px;
        margin: 20px auto;
        max-width: 650px;
        text-align: center;
        font-weight: 600;
        box-shadow: 0 0 15px rgba(0,255,100,0.2);
    }

    .toast.error {
        border-left: 5px solid #ff5555;
        color: #ffaaaa;
    }

    #imagePreview {
        max-width: 180px;
        margin-top: 12px;
        border: 1px solid #555;
        display: none;
        border-radius: 8px;
        box-shadow: 0 0 12px rgba(0,255,100,0.3);
    }

    .links {
        margin-top: 28px;
        text-align: center;
    }

    .links a {
        color: #90ee90;
        text-decoration: none;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .links a:hover {
        color: #b2ffb2;
    }
</style>
</head>
<body>
    
<div class="header position-relative">
    <h2 class="text-center w-100">Add New Product</h2>
    <a href="manageproducts.php" style="position: absolute; top: 15px; right: 25px;">⬅ Back to Manage Products</a>
</div>

<?php if ($success): ?>
    <div class="toast" id="toastSuccess"><?= $success ?></div>
<?php elseif ($error): ?>
    <div class="toast error" id="toastError"><?= $error ?></div>
<?php endif; ?>

<div class="container">
    <form method="POST">
        <label>Product Name</label>
        <input type="text" name="product_name" required>

        <label>Product Description</label>
        <textarea name="product_description" required></textarea>

        <label>Product Price (₹)</label>
        <input type="number" step="0.01" name="product_price" required>

        <label>Stock Quantity</label>
        <input type="number" name="stock_quantity" required>

        <label>Image URL</label>
        <input type="url" name="image_path" id="image_path" placeholder="https://example.com/image.jpg" oninput="previewImage()" required>
        <img id="imagePreview" src="#" alt="Preview">

        <label>Brand</label>
        <input type="text" name="brand_name" list="brandList" placeholder="Start typing brand..." required>
        <datalist id="brandList">
            <?php
            $brandResult = $conn->query("SELECT brand_name FROM brand_master");
            while ($row = $brandResult->fetch_assoc()) {
                echo "<option value=\"" . htmlspecialchars($row['brand_name']) . "\">";
            }
            ?>
        </datalist>

        <input style="background-color: #527A3A;" type="submit" value="Add Product">
    </form>

    <div class="links">
        <a href="manageproducts.php">🔧 Go to Manage Products</a>
    </div>
</div>

<script>
function previewImage() {
    const url = document.getElementById("image_path").value;
    const img = document.getElementById("imagePreview");
    if (url) {
        img.src = url;
        img.style.display = 'block';
    } else {
        img.style.display = 'none';
    }
}

// Auto-hide popup & clean URL
setTimeout(() => {
    const toastSuccess = document.getElementById("toastSuccess");
    const toastError = document.getElementById("toastError");
    if (toastSuccess) toastSuccess.style.display = 'none';
    if (toastError) toastError.style.display = 'none';

    const url = new URL(window.location.href);
    url.searchParams.delete('success');
    window.history.replaceState({}, document.title, url);
}, 3000);
</script>

</body>
</html>
