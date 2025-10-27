<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$popup_message = "";
$popup_type = "";

// Bulk delete
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_reviews'])) {
    $ids = implode(",", array_map('intval', $_POST['selected_reviews']));
    $conn->query("DELETE FROM review_master WHERE review_id IN ($ids)");
    $_SESSION['popup_message'] = "🗑️ Selected reviews deleted successfully.";
    $_SESSION['popup_type'] = "danger";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Single delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_review'])) {
    $review_id = intval($_POST['delete_review']);
    $conn->query("DELETE FROM review_master WHERE review_id = $review_id");
    $_SESSION['popup_message'] = "🗑️ Review #$review_id deleted successfully";
    $_SESSION['popup_type'] = "danger";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Search
$search = "";
$where = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $where = "WHERE rm.review_id LIKE '%$search%' 
              OR um.full_name LIKE '%$search%' 
              OR pm.product_name LIKE '%$search%' 
              OR rm.comment LIKE '%$search%'";
}

// Entries & pagination
$entries_per_page = isset($_GET['entries']) ? intval($_GET['entries']) : 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

if ($entries_per_page === 0) {
    $offset = 0;
    $limit = "";
} else {
    $offset = ($page - 1) * $entries_per_page;
    $limit = "LIMIT $offset, $entries_per_page";
}

$total_reviews_query = $conn->query("SELECT COUNT(*) as total 
                                     FROM review_master rm
                                     JOIN user_master um ON rm.user_id = um.user_id
                                     JOIN product_master pm ON rm.product_id = pm.product_id
                                     $where");
$total_reviews = $total_reviews_query->fetch_assoc()['total'];
$total_pages = ($entries_per_page === 0) ? 1 : ceil($total_reviews / $entries_per_page);

$query = "SELECT rm.*, um.full_name, pm.product_name 
          FROM review_master rm
          JOIN user_master um ON rm.user_id = um.user_id
          JOIN product_master pm ON rm.product_id = pm.product_id
          $where
          ORDER BY rm.review_id DESC $limit";
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
    <title>Manage Reviews</title>
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
        .total-reviews { font-weight: bold; font-size: 15px; color: var(--primary); }
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

<h1 class="text-center mt-2 total-reviews" style="font-size: 39px;">Total Reviews: <?= $total_reviews ?></h1>

<?php if ($popup_message): ?>
    <div class="alert alert-<?= $popup_type ?> alert-dismissible fade show" role="alert" id="popup">
        <?= htmlspecialchars($popup_message) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="table-container">
    <div class="d-flex justify-content-between align-items-center mb-3">

        <!-- Left (Delete Button) -->
        <form method="POST">
            <button type="submit" name="bulk_delete" class="btn btn-danger btn-sm"
                    form="reviewsForm"
                    onclick="return confirm('⚠️ Delete all selected reviews?');">
                🗑️ Delete Selected (<span id="selectedCount">0</span>)
            </button>

        </form>

        <!-- Center (Search Bar auto-submit) -->
        <form method="GET" class="d-flex align-items-center">
            <input type="text" name="search" id="liveSearch" class="form-control search-input me-2"
                   placeholder="Search Review, User, Product"
                   value="<?= htmlspecialchars($search) ?>">
            <input type="hidden" name="entries" value="<?= $entries_per_page ?>">
        </form>

        <!-- Right (Entries dropdown) -->
        <form method="GET">
            <input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>">
            <select name="entries" class="form-select form-select-sm entries-dropdown"
                    onchange="this.form.submit()">
                <option value="5"  <?= $entries_per_page == 5  ? 'selected' : '' ?>>5</option>
                <option value="10" <?= $entries_per_page == 10 ? 'selected' : '' ?>>10</option>
                <option value="25" <?= $entries_per_page == 25 ? 'selected' : '' ?>>25</option>
                <option value="50" <?= $entries_per_page == 50 ? 'selected' : '' ?>>50</option>
                <option value="0"  <?= $entries_per_page == 0  ? 'selected' : '' ?>>All</option>
            </select>
        </form>

    </div>

    <!-- Table -->
    <form method="POST" id="reviewsForm">
        <div style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>User</th>
                        <th>Product</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($review = $result->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" class="reviewCheckbox"
                                       name="selected_reviews[]" value="<?= $review['review_id'] ?>"></td>
                            <td><?= $review['review_id'] ?></td>
                            <td><?= htmlspecialchars($review['full_name']) ?></td>
                            <td><?= htmlspecialchars($review['product_name']) ?></td>
                            <td><?= str_repeat("⭐", $review['rating']) ?></td>
                            <td><?= htmlspecialchars($review['comment']) ?></td>
                            <td><?= $review['review_date'] ?></td>
                            <td>
                                <button type="submit" name="delete_review" value="<?= $review['review_id'] ?>" 
                                        class="btn btn-danger btn-sm"
                                        onclick="return confirm('⚠️ Delete Review #<?= $review['review_id'] ?>?');">
                                    Delete
                                </button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No reviews found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?search=<?= urlencode($search) ?>&entries=<?= $entries_per_page ?>&page=<?= max(1, $page - 1) ?>">Previous</a>
            </li>
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link"
                       href="?search=<?= urlencode($search) ?>&entries=<?= $entries_per_page ?>&page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link"
                   href="?search=<?= urlencode($search) ?>&entries=<?= $entries_per_page ?>&page=<?= min($total_pages, $page + 1) ?>">Next</a>
            </li>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    setTimeout(() => { const popup = document.getElementById('popup'); if (popup) popup.classList.remove('show'); }, 3000);

    const selectAllCheckbox = document.getElementById("selectAll");
    const reviewCheckboxes = document.querySelectorAll(".reviewCheckbox");
    const selectedCount = document.getElementById("selectedCount");

    function updateSelectedCount() {
        const count = document.querySelectorAll(".reviewCheckbox:checked").length;
        selectedCount.textContent = count;
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            reviewCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });
    }

    reviewCheckboxes.forEach(cb => {
        cb.addEventListener("change", () => {
            if (!cb.checked) selectAllCheckbox.checked = false;
            if (document.querySelectorAll(".reviewCheckbox:checked").length === reviewCheckboxes.length) {
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
