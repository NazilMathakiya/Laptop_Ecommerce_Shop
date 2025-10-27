<?php
session_start();
$conn = new mysqli("localhost", "root", "", "laptop_store");

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (!isset($_SESSION['admin_email'])) {
    echo "<script>alert('Please login first.'); window.location.href='login.php';</script>";
    exit();
}

$admin_email = $_SESSION['admin_email'];
$sql = "SELECT admin_name, admin_email, admin_mobile_no FROM admin_master WHERE admin_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Profile - Lapcart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #121212;
      color: #e0ffe0;
      font-family: 'Segoe UI', sans-serif;
      margin: 0;
      padding: 0;
    }

    .profile-card {
      background-color: #1e1e1e;
      border-radius: 20px;
      padding: 40px 30px;
      max-width: 550px;
      margin: 60px auto;
      box-shadow: 0 0 20px rgba(0, 255, 100, 0.3);
    }

    h2 {
      color: #90ee90;
      margin-bottom: 30px;
      text-align: center;
      font-weight: 600;
    }

    .profile-info p {
      font-size: 17px;
      margin: 12px 0;
    }

    .profile-info p strong {
      color: #76ff03;
      width: 120px;
      display: inline-block;
    }

    .btn-section {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-top: 30px;
      gap: 12px;
    }

    .btn-primary,
    .btn-back {
      padding: 10px 20px;
      font-size: 15px;
      border-radius: 10px;
      border: none;
      width: 200px;
      text-align: center;
    }

    .btn-primary {
      background-color: #2196f3;
      color: white;
    }

    .btn-primary:hover {
      background-color: #1976d2;
    }

    .btn-back {
      background-color: #4caf50;
      color: white;
    }

    .btn-back:hover {
      background-color: #388e3c;
    }
  </style>
</head>
<body>
  <div class="container">
    <div class="profile-card">
      <h2>Admin Profile</h2>
      <div class="profile-info">
        <p><strong>Name:</strong> <?= htmlspecialchars($admin['admin_name']) ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($admin['admin_email']) ?></p>
        <p><strong>Mobile:</strong> <?= htmlspecialchars($admin['admin_mobile_no']) ?></p>
      </div>

      <div class="btn-section">
        <a href="edit_profile.php" class="btn btn-primary">✏️ Edit Profile</a>
        <a href="index.php" class="btn btn-back">← Back to Dashboard</a>
      </div>
    </div>
  </div>
</body>
</html>
