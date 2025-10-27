<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = isset($_GET['product_id']) ? intval($_GET['product_id']) : 0;

// Fetch product name
$product_name = "";
$product_res = $conn->query("SELECT product_name FROM product_master WHERE product_id = $product_id");
if ($product_res && $product_res->num_rows > 0) {
    $product_name = $product_res->fetch_assoc()['product_name'];
}

// Delete review
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $conn->query("DELETE FROM review_master WHERE review_id = $delete_id AND user_id = $user_id");
    echo "<script>alert('Review deleted.'); window.location.href='view_reviews.php?product_id=$product_id';</script>";
    exit();
}

// Update review
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_review'])) {
    $review_id = intval($_POST['review_id']);
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("UPDATE review_master SET rating = ?, comment = ?, review_date = NOW() WHERE review_id = ? AND user_id = ?");
    $stmt->bind_param("isii", $rating, $comment, $review_id, $user_id);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Review updated successfully!'); window.location.href='view_reviews.php?product_id=$product_id';</script>";
    exit();
}

// Submit new review
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit_review'])) {
    $rating = intval($_POST['rating']);
    $comment = trim($_POST['comment']);

    $stmt = $conn->prepare("INSERT INTO review_master (product_id, user_id, rating, comment, review_date) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiis", $product_id, $user_id, $rating, $comment);
    $stmt->execute();
    $stmt->close();

    echo "<script>alert('Review submitted successfully!'); window.location.href='view_reviews.php?product_id=$product_id';</script>";
    exit();
}

// Get reviews
$review_sql = "
    SELECT r.review_id, r.user_id, r.rating, r.comment, r.review_date, u.full_name
    FROM review_master r
    JOIN user_master u ON r.user_id = u.user_id
    WHERE r.product_id = $product_id
    ORDER BY r.review_date DESC
";
$review_result = $conn->query($review_sql);

// For summary
$summary_sql = "SELECT COUNT(*) AS total_reviews, AVG(rating) AS avg_rating FROM review_master WHERE product_id = $product_id";
$summary_res = $conn->query($summary_sql);
$summary_data = $summary_res->fetch_assoc();
$total_reviews = $summary_data['total_reviews'];
$avg_rating = round($summary_data['avg_rating'], 1);

$edit_id = isset($_GET['edit_id']) ? intval($_GET['edit_id']) : 0;
$edit_data = null;
if ($edit_id > 0) {
    $res = $conn->query("SELECT * FROM review_master WHERE review_id = $edit_id AND user_id = $user_id");
    if ($res && $res->num_rows > 0) {
        $edit_data = $res->fetch_assoc();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reviews - <?= htmlspecialchars($product_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #121212; color: #fff; }
        .review-card {
            background-color: #1e1e1e;
            border: 1px solid #2e2e2e;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .form-control, .form-select {
            background-color: #1e1e1e;
            color: #fff;
            border: 1px solid #333;
        }
        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
        }
        .star {
            color: gold;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="top-bar">
        <h3 class="text-success">Reviews for: <?= htmlspecialchars($product_name) ?></h3>
        <a href="shop.php" class="btn btn-success">← Back to Shop</a>
    </div>

    <!-- Review Summary -->
    <?php if ($total_reviews > 0): ?>
        <div class="mb-4">
            <h5 class="text-info">
                <?php
                $fullStars = floor($avg_rating);
                $halfStar = ($avg_rating - $fullStars >= 0.5);
                for ($i = 1; $i <= 5; $i++) {
                    if ($i <= $fullStars) {
                        echo '<span class="star">★</span>';
                    } elseif ($i === $fullStars + 1 && $halfStar) {
                        echo '<span class="star">★</span>';
                    } else {
                        echo '<span class="text-secondary">★</span>';
                    }
                }
                ?>
                (<?= $avg_rating ?> out of 5) based on <?= $total_reviews ?> review<?= $total_reviews > 1 ? 's' : '' ?>
            </h5>
        </div>
    <?php endif; ?>

    <!-- Review Form -->
    <?php if ($edit_data): ?>
        <form method="POST" class="mb-4">
            <input type="hidden" name="review_id" value="<?= $edit_data['review_id'] ?>">
            <div class="mb-3">
                <label class="form-label">Rating</label>
                <select class="form-select" name="rating" required>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>" <?= $i == $edit_data['rating'] ? 'selected' : '' ?>><?= $i ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Comment</label>
                <textarea class="form-control" name="comment" rows="3"><?= htmlspecialchars($edit_data['comment']) ?></textarea>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" name="update_review" class="btn btn-warning">Update Review</button>
            </div>
        </form>
    <?php else: ?>
        <form method="POST" class="mb-4">
            <div class="mb-3">
                <label class="form-label">Rating</label>
                <select class="form-select" name="rating" required>
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                        <option value="<?= $i ?>"><?= $i ?> - <?= str_repeat('★', $i) ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Comment</label>
                <textarea class="form-control" name="comment" rows="3" placeholder="Share your experience..."></textarea>
            </div>
            <div class="d-flex justify-content-center">
                <button type="submit" name="submit_review" class="btn btn-success px-4">Submit Review</button>
            </div>
        </form>
    <?php endif; ?>

    <!-- Review List -->
    <h4 class="mb-3 text-info">Customer Reviews</h4>
    <?php if ($review_result && $review_result->num_rows > 0): ?>
        <?php while ($rev = $review_result->fetch_assoc()): ?>
            <div class="review-card">
                <strong class="text-success"><?= htmlspecialchars($rev['full_name']) ?></strong>
                <span class="text-muted small float-end"><?= date("d M Y, h:i A", strtotime($rev['review_date'])) ?></span>
                <p>
                    <?php for ($i = 0; $i < $rev['rating']; $i++): ?>
                        <span class="star">★</span>
                    <?php endfor; ?>
                    <span class="text-muted">(<?= $rev['rating'] ?>)</span>
                </p>
                <p><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>

                <?php if ($rev['user_id'] == $user_id): ?>
                    <div class="mt-2">
                        <a href="?product_id=<?= $product_id ?>&edit_id=<?= $rev['review_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                        <a href="?product_id=<?= $product_id ?>&delete_id=<?= $rev['review_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete this review?')">Delete</a>
                    </div>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="alert alert-warning text-dark">No reviews yet for this product.</div>
    <?php endif; ?>
</div>
</body>
</html>
