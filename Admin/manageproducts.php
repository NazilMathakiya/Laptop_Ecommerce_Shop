<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$popup_message = "";
$popup_type = "";

// Bulk delete
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_products'])) {
    $ids = implode(",", array_map('intval', $_POST['selected_products']));
    $conn->query("DELETE FROM product_master WHERE product_id IN ($ids)");
    $_SESSION['popup_message'] = "✅ Selected products deleted successfully.";
    $_SESSION['popup_type'] = "danger";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Single delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_product'])) {
    $product_id = intval($_POST['delete_product']);
    $conn->query("DELETE FROM product_master WHERE product_id = $product_id");
    $_SESSION['popup_message'] = "🗑️ Product #$product_id deleted successfully";
    $_SESSION['popup_type'] = "danger";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Search
$search = "";
$where = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $where = "WHERE product_id LIKE '%$search%' OR product_name LIKE '%$search%'";
}

// Pagination
$entries_per_page = isset($_GET['entries']) ? intval($_GET['entries']) : 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

if ($entries_per_page === 0) {
    $offset = 0;
    $limit = "";
} else {
    $offset = ($page - 1) * $entries_per_page;
    $limit = "LIMIT $offset, $entries_per_page";
}

$total_products_query = $conn->query("SELECT COUNT(*) as total FROM product_master $where");
$total_products = $total_products_query->fetch_assoc()['total'];
$total_pages = ($entries_per_page === 0) ? 1 : ceil($total_products / $entries_per_page);

$query = "SELECT * FROM product_master $where ORDER BY product_id DESC $limit";
$result = $conn->query($query);

// Popups
if (isset($_SESSION['popup_message'])) {
    $popup_message = $_SESSION['popup_message'];
    $popup_type = $_SESSION['popup_type'];
    unset($_SESSION['popup_message'], $_SESSION['popup_type']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Products</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --bg: #0d0d0d;
            --card: #1e1e1e;
            --text: #e0e0e0;
            --primary: #2ecc71;
            --table-head: #0f0f0f;
            --table-bg: #131313;
            --table-row: #212121;
            --table-hover: #1e1e1e;
            --table-border: #2c2c2c;
        }
        body { background-color: var(--bg); color: var(--text); font-family: 'Segoe UI', sans-serif; padding: 30px; }
        h2 { text-align: center; color: var(--primary); margin-bottom: 20px; font-weight: bold; }
        .table-container { background-color: var(--card); padding: 20px; border-radius: 10px; }
        .table { background-color: var(--table-bg); border-collapse: collapse; color: var(--text); }
        .table thead { background-color: var(--table-head); color: #fff; }
        .table thead th { border-bottom: 2px solid var(--primary); font-weight: bold; text-align: center; }
        .table td, .table th { background-color: var(--table-row); color: var(--text); border: 1px solid var(--table-border); text-align: center; padding: 10px; }
        .table tbody tr:hover td { background-color: var(--table-hover); }
        .alert { width: 90%; margin: 0 auto 20px; }
        .search-input { max-width: 300px; }
        .entries-dropdown { width: 80px; text-align: center; }
        .total-products { font-weight: bold; font-size: 15px; color: var(--primary); }
        .pagination { justify-content: center; margin-top: 20px; }
        .btn-outline-light:hover { background-color: var(--primary); color: #000; }
        .pagination .page-link { background-color: #1a2e1a; border: 1px solid var(--primary); color: #e0e0e0; }
        .pagination .page-item.active .page-link { background-color: #568557; border-color: var(--primary); color: #000; font-weight: bold; }
        .pagination .page-link:hover { background-color: #297349; color: #000; }
    </style>
</head>
<body>

<!-- Back Button -->
<div style="position: absolute; top: 2; right: 30px;">
    <a href="index.php" class="btn btn-success">⬅ Back to Admin Panel</a>
</div>

<h1 class="text-center mt-2 total-products" style="font-size: 39px;">Total Products: <?= $total_products ?></h1>

<?php if ($popup_message): ?>
    <div class="alert alert-<?= $popup_type ?> alert-dismissible fade show" role="alert" id="popup">
        <?= htmlspecialchars($popup_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="table-container">
    <form method="GET" action="" class="d-flex justify-content-between align-items-center mb-3">
        <!-- Left buttons -->
        <div class="d-flex gap-2">
            <button type="submit" name="bulk_delete" class="btn btn-danger btn-sm"
                    form="productsForm"
                    onclick="return confirm('⚠️ Are you sure you want to delete all selected products?');">
                🗑️ Delete Selected (<span id="selectedCount">0</span>)
            </button>

            <a href="add_product.php" class="btn btn-success btn-sm">➕ Add New Product</a>
        </div>

        <!-- Search (auto-submit with debounce) -->
        <div class="d-flex align-items-center">
            <input type="text" name="search" id="liveSearch" class="form-control search-input me-2"
                   placeholder="Search ID or Name"
                   value="<?= htmlspecialchars($search) ?>">
        </div>

        <!-- Entries dropdown -->
        <select name="entries" class="form-select form-select-sm entries-dropdown"
                onchange="this.form.submit()">
            <option value="5" <?= $entries_per_page == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $entries_per_page == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $entries_per_page == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $entries_per_page == 50 ? 'selected' : '' ?>>50</option>
            <option value="0" <?= $entries_per_page == 0 ? 'selected' : '' ?>>All</option>
        </select>
    </form>

    <form method="POST" action="" id="productsForm">
        <!-- Table -->
        <div style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Image</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($product = $result->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" class="productCheckbox" name="selected_products[]" value="<?= $product['product_id'] ?>"></td>
                            <td><?= $product['product_id'] ?></td>
                            <td><?= htmlspecialchars($product['product_name']) ?></td>
                            <td>₹<?= number_format($product['product_price'], 2) ?></td>
                            <td><?= (int)$product['stock_quantity'] ?></td>
                            <td><img src="<?= htmlspecialchars($product['image_path']) ?>" width="60"></td>
                            <td>
                                <a href="edit_product.php?id=<?= $product['product_id'] ?>" class="btn btn-primary btn-sm">Edit</a>
                                <button type="submit" name="delete_product" value="<?= $product['product_id'] ?>" 
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('⚠️ Delete Product #<?= $product['product_id'] ?>?');">
                                   Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="7">No products found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" href="?search=<?= urlencode($search) ?>&entries=<?= $entries_per_page ?>&page=<?= max(1, $page - 1) ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" href="?search=<?= urlencode($search) ?>&entries=<?= $entries_per_page ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" href="?search=<?= urlencode($search) ?>&entries=<?= $entries_per_page ?>&page=<?= min($total_pages, $page + 1) ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(() => { const popup = document.getElementById('popup'); if (popup) popup.classList.remove('show'); }, 3000);

    // Checkbox logic
    const selectAllCheckbox = document.getElementById("selectAll");
    const productCheckboxes = document.querySelectorAll(".productCheckbox");
    const selectedCount = document.getElementById("selectedCount");

    function updateSelectedCount() {
        const count = document.querySelectorAll(".productCheckbox:checked").length;
        selectedCount.textContent = count;
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            productCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });
    }

    productCheckboxes.forEach(cb => {
        cb.addEventListener("change", () => {
            if (!cb.checked) selectAllCheckbox.checked = false;
            if (document.querySelectorAll(".productCheckbox:checked").length === productCheckboxes.length) {
                selectAllCheckbox.checked = true;
            }
            updateSelectedCount();
        });
    });

    document.addEventListener("DOMContentLoaded", updateSelectedCount);

    // Debounced search
    let typingTimer;
    const liveSearch = document.getElementById("liveSearch");
    if (liveSearch) {
        liveSearch.addEventListener("input", function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                this.form.submit();
            }, 600);
        });
    }
</script>
</body>
</html>
