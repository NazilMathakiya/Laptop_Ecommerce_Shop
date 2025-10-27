<?php
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) die("DB error");

if (isset($_POST['query'])) {
    $search = '%' . $conn->real_escape_string($_POST['query']) . '%';
    $stmt = $conn->prepare("SELECT brand_name FROM brand_master WHERE brand_name LIKE ? LIMIT 5");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="suggestion-item">' . htmlspecialchars($row['brand_name']) . '</div>';
        }
    } else {
        echo '<div class="suggestion-item">No matches</div>';
    }

    $stmt->close();
}
?>
