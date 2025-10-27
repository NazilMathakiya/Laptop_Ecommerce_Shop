<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$popup_message = "";
$popup_type = "";

// Bulk delete
if (isset($_POST['bulk_delete']) && !empty($_POST['selected_orders'])) {
    $ids = implode(",", array_map('intval', $_POST['selected_orders']));
    $conn->query("DELETE FROM order_master WHERE order_id IN ($ids)");
    $_SESSION['popup_message'] = "✅ Selected orders deleted successfully.";
    $_SESSION['popup_type'] = "danger";
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET));
    exit;
}

// Single delete
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_order_single'])) {
    $order_id = intval($_POST['delete_order_single']);
    $conn->query("DELETE FROM order_master WHERE order_id = $order_id");
    $_SESSION['popup_message'] = "🗑️ Order #$order_id deleted successfully";
    $_SESSION['popup_type'] = "danger";
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET));
    exit;
}

// Update order status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['new_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $conn->real_escape_string($_POST['new_status']);
    $conn->query("UPDATE order_master SET order_status = '$new_status' WHERE order_id = $order_id");
    $_SESSION['popup_message'] = "🚚 Order #$order_id status changed to $new_status";
    $_SESSION['popup_type'] = "success";
    header("Location: " . $_SERVER['PHP_SELF'] . "?" . http_build_query($_GET));
    exit;
}

// Search + Filter
$search = "";
$where = "";

if (isset($_GET['search'])) {
    $search = trim($_GET['search']);
    if ($search !== "") {
        $safe_search = $conn->real_escape_string($search);
        $where = "WHERE (o.order_id LIKE '%$safe_search%' 
                  OR u.full_name LIKE '%$safe_search%' 
                  OR u.user_email LIKE '%$safe_search%')";
    }
}

$status_filter = "";
if (isset($_GET['status']) && $_GET['status'] !== "") {
    $status_filter = $conn->real_escape_string($_GET['status']);
    $where .= ($where ? " AND" : "WHERE") . " o.order_status = '$status_filter'";
}

// Entries per page
$entries_per_page = isset($_GET['entries']) ? intval($_GET['entries']) : 5;
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

if ($entries_per_page === 0) {
    $offset = 0;
    $limit = ""; // show all
} else {
    $offset = ($page - 1) * $entries_per_page;
    $limit = "LIMIT $offset, $entries_per_page";
}

// Total orders count
$total_orders_query = $conn->query("SELECT COUNT(*) as total 
    FROM order_master o
    $where");
$total_orders = $total_orders_query->fetch_assoc()['total'];
$total_pages = ($entries_per_page === 0) ? 1 : ceil($total_orders / $entries_per_page);

// Get orders
$query = "SELECT * 
          FROM order_master o
          $where 
          ORDER BY o.order_id DESC $limit";
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
    <title>Total Orders</title>
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
        body {
            background-color: var(--bg);
            color: var(--text);
            font-family: 'Segoe UI', sans-serif;
            padding: 30px;
        }
        h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 20px;
            font-weight: bold;
        }
        .table-container {
            background-color: var(--card);
            padding: 20px;
            border-radius: 10px;
        }
        .table {
            background-color: var(--table-bg);
            border-collapse: collapse;
            color: var(--text);
        }
        .table thead {
            background-color: var(--table-head);
            color: #ffffff;
        }
        .table thead th {
            border-bottom: 2px solid var(--primary);
            font-weight: bold;
            text-align: center;
        }
        .table td, .table th {
            background-color: var(--table-row);
            color: var(--text);
            vertical-align: middle;
            border: 1px solid var(--table-border);
            text-align: center;
            padding: 10px;
        }
        .table tbody tr:hover td { background-color: var(--table-hover); }
        .btn-delete { padding: 5px 12px; }
        .alert { width: 90%; margin: 0 auto 20px; }
        .search-input { max-width: 300px; }
        .entries-dropdown { width: 80px; text-align: center; padding: 4px; }
        .total-orders { font-weight: bold; font-size: 15; color: var(--primary); }
        .pagination { justify-content: center; margin-top: 20px; }
        .btn-outline-light { background-color: transparent; border-color: #888; color: #f0f0f0; }
        .btn-outline-light:hover { background-color: #2ecc71; color: #000; }
        .pagination .page-link {
            background-color: #1a2e1a;
            border: 1px solid #2ecc71;
            color: #e0e0e0;
        }
        .pagination .page-item.active .page-link {
            background-color: #568557;
            border-color: #2ecc71;
            color: #000;
            font-weight: bold;
        }
        .pagination .page-link:hover {
            background-color: #297349;
            color: #000;
        }
        .pagination .page-item.disabled .page-link {
            background-color: #333;
            color: #777;
            border-color: #2c2c2c;
        }
    </style>
</head>
<body>
    <div style="position: absolute; top: 2; right: 30px;">
        <a href="index.php" class="btn btn-success">⬅ Back to Admin Panel</a>
    </div>

    <h1 class="text-center mt-2 total-orders">Total Orders: <?= $total_orders ?></h1>

    <?php if ($popup_message): ?>
        <div class="alert alert-<?= $popup_type ?> alert-dismissible fade show" role="alert" id="popup">
            <?= htmlspecialchars($popup_message) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

<div class="table-container">
    <form method="GET" action="" class="d-flex justify-content-between align-items-center mb-3">
        <!-- Delete button (left) -->
        <button type="submit" name="bulk_delete" class="btn btn-danger btn-sm"
                form="ordersForm">
            🗑️ Delete Selected (<span id="selectedCount">0</span>)
        </button>

        <!-- Search + Filter (center) -->
        <div class="d-flex align-items-center">
            <input type="text" name="search" class="form-control search-input me-2"
                   placeholder="Search Order ID, Name or Email"
                   value="<?= htmlspecialchars($search) ?>"
                   id="liveSearch">

            <!-- Filter By dropdown -->
            <select name="status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                <option value="">Filter By Status</option>
                <option value="Pending" <?= ($status_filter == "Pending") ? "selected" : "" ?>>Pending</option>
                <option value="Processing" <?= ($status_filter == "Processing") ? "selected" : "" ?>>Processing</option>
                <option value="Shipped" <?= ($status_filter == "Shipped") ? "selected" : "" ?>>Shipped</option>
                <option value="Delivered" <?= ($status_filter == "Delivered") ? "selected" : "" ?>>Delivered</option>
                <option value="Cancelled" <?= ($status_filter == "Cancelled") ? "selected" : "" ?>>Cancelled</option>
            </select>
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

    <form method="POST" action="" id="ordersForm">
        <div style="max-height: 500px; overflow-y: auto;">
            <table class="table table-bordered table-hover text-white align-middle">
                <thead class="table-dark sticky-top">
                    <tr>
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>Order ID</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Mobile</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($order = $result->fetch_assoc()): ?>
                        <tr>
                            <td><input type="checkbox" class="orderCheckbox" name="selected_orders[]" value="<?= $order['order_id'] ?>"></td>
                            <td><?= $order['order_id'] ?></td>
                            <td><?= htmlspecialchars($order['full_name']) ?></td>
                            <td><?= htmlspecialchars($order['user_email']) ?></td>
                            <td><?= htmlspecialchars($order['user_mobile_no']) ?></td>
                            <td>
                                <form method="POST" action="" class="d-flex">
                                    <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                    <select name="new_status" class="form-select form-select-sm me-2" onchange="this.form.submit()">
                                        <option value="Pending" <?= $order['order_status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                                        <option value="Processing" <?= $order['order_status'] == 'Processing' ? 'selected' : '' ?>>Processing</option>
                                        <option value="Shipped" <?= $order['order_status'] == 'Shipped' ? 'selected' : '' ?>>Shipped</option>
                                        <option value="Delivered" <?= $order['order_status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                                        <option value="Cancelled" <?= $order['order_status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                    </select>
                                </form>
                            </td>
                            <td>₹<?= number_format($order['total_amount'], 2) ?></td>
                            <td><?= $order['order_date'] ?></td>
                            <td>
                                <button type="submit" name="delete_order_single" value="<?= $order['order_id'] ?>" class="btn btn-danger btn-sm">Delete</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="9">No orders found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </form>

    <!-- Pagination -->
    <nav>
        <ul class="pagination justify-content-center">
            <!-- Previous -->
            <li class="page-item <?= ($page <= 1) ? 'disabled' : '' ?>">
                <a class="page-link" 
                   href="?search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&entries=<?= $entries_per_page ?>&page=<?= max(1, $page - 1) ?>">
                   Previous
                </a>
            </li>

            <!-- Page Numbers -->
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <li class="page-item <?= ($i == $page) ? 'active' : '' ?>">
                    <a class="page-link" 
                       href="?search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&entries=<?= $entries_per_page ?>&page=<?= $i ?>">
                       <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <!-- Next -->
            <li class="page-item <?= ($page >= $total_pages) ? 'disabled' : '' ?>">
                <a class="page-link" 
                   href="?search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&entries=<?= $entries_per_page ?>&page=<?= min($total_pages, $page + 1) ?>">
                   Next
                </a>
            </li>
        </ul>
    </nav>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Popup auto hide
    setTimeout(() => {
        const popup = document.getElementById('popup');
        if (popup) popup.classList.remove('show');
    }, 3000);

    // Checkbox select all
    const selectAllCheckbox = document.getElementById("selectAll");
    const orderCheckboxes = document.querySelectorAll(".orderCheckbox");
    const selectedCount = document.getElementById("selectedCount");

    function updateSelectedCount() {
        const count = document.querySelectorAll(".orderCheckbox:checked").length;
        selectedCount.textContent = count;
    }

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener("change", function () {
            orderCheckboxes.forEach(cb => cb.checked = this.checked);
            updateSelectedCount();
        });
    }
    orderCheckboxes.forEach(cb => {
        cb.addEventListener("change", () => {
            if (!cb.checked) selectAllCheckbox.checked = false;
            if (document.querySelectorAll(".orderCheckbox:checked").length === orderCheckboxes.length) {
                selectAllCheckbox.checked = true;
            }
            updateSelectedCount();
        });
    });
    document.addEventListener("DOMContentLoaded", updateSelectedCount);

    // Debounced search (waits before submit)
    let typingTimer;
    const liveSearch = document.getElementById("liveSearch");
    if (liveSearch) {
        liveSearch.addEventListener("input", function () {
            clearTimeout(typingTimer);
            typingTimer = setTimeout(() => {
                this.form.submit();
            }, 600); // waits 600ms after typing stops
        });
    }
</script>
</body>
</html>
