<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$popup_message = "";
$popup_type = "";

// Bulk delete
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_users'])) {
    $ids = implode(",", array_map('intval', $_POST['selected_users']));
    $conn->query("DELETE FROM user_master WHERE user_id IN ($ids)");
    $_SESSION['popup_message'] = "✅ Selected users deleted successfully.";
    $_SESSION['popup_type'] = "danger";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Single delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user_single'])) {
    $user_id = intval($_POST['delete_user_single']);
    $conn->query("DELETE FROM user_master WHERE user_id = $user_id");
    $_SESSION['popup_message'] = "🗑️ User #$user_id deleted successfully";
    $_SESSION['popup_type'] = "danger";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Search
$search = "";
$where = "";
if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    $where = "WHERE user_id LIKE '%$search%' OR full_name LIKE '%$search%' OR user_email LIKE '%$search%'";
}

// Pagination setup
$entries_per_page = isset($_GET['entries']) ? intval($_GET['entries']) : 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

if ($entries_per_page === 0) {
    $offset = 0;
    $limit = "";
} else {
    $offset = ($page - 1) * $entries_per_page;
    $limit = "LIMIT $offset, $entries_per_page";
}

// Count total
$total_users_query = $conn->query("SELECT COUNT(*) as total FROM user_master $where");
$total_users = $total_users_query->fetch_assoc()['total'];
$total_pages = ($entries_per_page === 0) ? 1 : ceil($total_users / $entries_per_page);

// Get users
$query = "SELECT * FROM user_master $where ORDER BY user_id DESC $limit";
$result = $conn->query($query);

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
    <title>Manage Users</title>
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
        .table thead { background-color: var(--table-head); color: #ffffff; }
        .table thead th { border-bottom: 2px solid var(--primary); font-weight: bold; text-align: center; }
        .table td, .table th { background-color: var(--table-row); color: var(--text); vertical-align: middle; border: 1px solid var(--table-border); text-align: center; padding: 10px; }
        .table tbody tr:hover td { background-color: var(--table-hover); }
        .btn-delete { padding: 5px 12px; }
        .alert { width: 90%; margin: 0 auto 20px; }
        .search-input { max-width: 300px; }
        .entries-dropdown { width: 80px; text-align: center; padding: 4px; }
        .total-users { font-weight: bold; font-size: 15; color: var(--primary); }
        .pagination { justify-content: center; margin-top: 20px; }
        .btn-outline-light { background-color: transparent; border-color: #888; color: #f0f0f0; }
        .btn-outline-light:hover { background-color: #2ecc71; color: #000; }
        .pagination .page-link { background-color: #1a2e1a; border: 1px solid #2ecc71; color: #e0e0e0; }
        .pagination .page-item.active .page-link { background-color: #568557; border-color: #2ecc71; color: #000; font-weight: bold; }
        .pagination .page-link:hover { background-color: #297349; color: #000; }
        .pagination .page-item.disabled .page-link { background-color: #333; color: #777; border-color: #2c2c2c; }
    </style>
</head>
<body>
    <div style="position: absolute; top: 2; right: 30px;">
        <a href="index.php" class="btn btn-success">⬅ Back to Admin Panel</a>
    </div>

    <h1 class="text-center mt-2 total-users">Total Users: <?= $total_users ?></h1>

    <?php if ($popup_message): ?>
        <div class="alert alert-<?= $popup_type ?> alert-dismissible fade show" role="alert" id="popup">
            <?= htmlspecialchars($popup_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

<div class="table-container">
    <form method="GET" action="" class="d-flex justify-content-between align-items-center mb-3">
        <!-- Delete button (left, but from POST so handled separately) -->
        <button type="submit" name="bulk_delete" class="btn btn-danger btn-sm"
                form="usersForm">
            🗑️ Delete Selected (<span id="selectedCount">0</span>)
        </button>
        
        <!-- Search bar (center, debounce auto-submit) -->
        <div class="d-flex align-items-center">
            <input type="text" name="search" class="form-control search-input me-2"
                   placeholder="Search ID, Name or Email"
                   value="<?= htmlspecialchars($search) ?>" id="liveSearch">
        </div>

        <!-- Entries dropdown (right) -->
        <select name="entries" class="form-select form-select-sm entries-dropdown"
                onchange="this.form.submit()">
            <option value="5" <?= $entries_per_page == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $entries_per_page == 10 ? 'selected' : '' ?>>10</option>
            <option value="25" <?= $entries_per_page == 25 ? 'selected' : '' ?>>25</option>
            <option value="50" <?= $entries_per_page == 50 ? 'selected' : '' ?>>50</option>
            <option value="0" <?= $entries_per_page == 0 ? 'selected' : '' ?>>All</option>
        </select>
    </form>

    <form method="POST" action="" id="usersForm">
        <div style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered table-hover text-white align-middle">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>ID</th>
                        <th>Full Name</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Address</th>
                        <th>Registered On</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($user = $result->fetch_assoc()): ?>
                            <tr>
                                <td><input type="checkbox" class="userCheckbox" name="selected_users[]" value="<?= $user['user_id'] ?>"></td>
                                <td><?= $user['user_id'] ?></td>
                                <td><?= htmlspecialchars($user['full_name']) ?></td>
                                <td><?= htmlspecialchars($user['user_email']) ?></td>
                                <td><?= htmlspecialchars($user['user_mobile_no']) ?></td>
                                <td><?= htmlspecialchars($user['user_address']) ?></td>
                                <td><?= $user['created_at'] ?></td>
                                <td>
                                    <button type="submit" name="delete_user_single" value="<?= $user['user_id'] ?>" class="btn btn-danger btn-sm">Delete</button>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr><td colspan="8">No users found.</td></tr>
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
    setTimeout(() => {
        const popup = document.getElementById('popup');
        if (popup) popup.classList.remove('show');
    }, 3000);

    // Select all checkbox logic
    const selectAllCheckbox = document.getElementById("selectAll");
    const userCheckboxes = document.querySelectorAll(".userCheckbox");
    const selectedCount = document.getElementById("selectedCount");

    function updateSelectedCount() {
        const count = document.querySelectorAll(".userCheckbox:checked").length;
        selectedCount.textContent = count;
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            userCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });
    }
    userCheckboxes.forEach(cb => {
        cb.addEventListener("change", () => {
            if (!cb.checked) selectAllCheckbox.checked = false;
            if (document.querySelectorAll(".userCheckbox:checked").length === userCheckboxes.length) {
                selectAllCheckbox.checked = true;
            }
            updateSelectedCount();
        });
    });
    document.addEventListener("DOMContentLoaded", updateSelectedCount);

    // Debounced live search
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
