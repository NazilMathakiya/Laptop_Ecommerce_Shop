<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$rev_limit = 3;
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$start = ($page - 1) * $rev_limit;

$reviews = $conn->query("
    SELECT r.comment, r.rating, r.review_date, u.full_name, p.product_name, p.product_id, p.image_path
    FROM review_master r
    JOIN user_master u ON r.user_id = u.user_id
    JOIN product_master p ON r.product_id = p.product_id
    ORDER BY r.review_date DESC
    LIMIT $start, $rev_limit
");

while ($rev = $reviews->fetch_assoc()):
?>
<div class="col-md-4 mb-4 review-item">
    <div class="card h-100 text-start">
        <div class="row g-0">
            <div class="col-4">
                <img src="<?= $rev['image_path'] ?>" class="img-fluid rounded-start" alt="<?= htmlspecialchars($rev['product_name']) ?>">
            </div>
            <div class="col-8">
                <div class="card-body">
                    <h6 class="card-title mb-1"><?= htmlspecialchars($rev['product_name']) ?></h6>
                    <p class="mb-1">
                        <strong style="color: #00bfa6;"><?= htmlspecialchars($rev['full_name']) ?></strong>
                        <small class="text-muted">(<?= date('d M Y', strtotime($rev['review_date'])) ?>)</small>
                    </p>
                    <p class="text-warning mb-1">⭐ <?= $rev['rating'] ?>/5</p>
                    <p class="card-text small" style="color: #ddd;">
                        <?= htmlspecialchars(mb_strimwidth($rev['comment'], 0, 50, "...")) ?>
                    </p>
                    <a href="product_detail.php?product_id=<?= $rev['product_id'] ?>" class="btn btn-sm btn-outline-success">View Product</a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>
