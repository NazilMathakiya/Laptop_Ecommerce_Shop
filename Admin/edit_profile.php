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

// Fetch current admin details
$sql = "SELECT * FROM admin_master WHERE admin_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $admin_email);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['admin_name']);
    $email = trim($_POST['admin_email']);
    $mobile = trim($_POST['admin_mobile_no']);
    $old_password = trim($_POST['old_password'] ?? '');
    $new_password = trim($_POST['new_password'] ?? '');
    $password_changed = false;

    // Email validation
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '.com')) {
        echo "<script>alert('Please enter a valid .com email address.');</script>";
    }
    // Mobile number validation (10 digits)
    elseif (!preg_match('/^\d{10}$/', $mobile)) {
        echo "<script>alert('Please enter a valid 10-digit mobile number.');</script>";
    }
    // Handle valid inputs
    else {
        // Update profile info
        $update_sql = "UPDATE admin_master SET admin_name = ?, admin_email = ?, admin_mobile_no = ? WHERE admin_email = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssis", $name, $email, $mobile, $admin_email);
        $update_stmt->execute();

        // Update session email if changed
        $_SESSION['admin_email'] = $email;

        // Password change logic
        if (!empty($old_password) && !empty($new_password)) {
            $pass_stmt = $conn->prepare("SELECT admin_password FROM admin_master WHERE admin_email = ?");
            $pass_stmt->bind_param("s", $email);
            $pass_stmt->execute();
            $pass_result = $pass_stmt->get_result();

            if ($pass_result->num_rows === 1) {
                $row = $pass_result->fetch_assoc();
                if ($row['admin_password'] === $old_password) {
                    $update_pass = $conn->prepare("UPDATE admin_master SET admin_password = ? WHERE admin_email = ?");
                    $update_pass->bind_param("ss", $new_password, $email);
                    $update_pass->execute();
                    $password_changed = true;
                } else {
                    echo "<script>alert('Old password is incorrect. Password not changed.'); window.location.href='edit_profile.php';</script>";
                    exit();
                }
            }
        } elseif (!empty($old_password) && empty($new_password)) {
            echo "<script>alert('Please enter a new password if you want to change it.'); window.location.href='edit_profile.php';</script>";
            exit();
        }

        // Final alert
        if ($password_changed) {
            echo "<script>alert('Profile and password updated successfully.'); window.location.href='admin_profile.php';</script>";
        } else {
            echo "<script>alert('Profile updated successfully.'); window.location.href='admin_profile.php';</script>";
        }
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Edit Admin Profile - Lapcart</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #121212;
      color: #e0ffe0;
      font-family: 'Segoe UI', sans-serif;
    }
    .profile-card {
      background-color: #1e1e1e;
      border-radius: 20px;
      padding: 30px;
      max-width: 500px;
      margin: 50px auto;
      box-shadow: 0 0 20px rgba(0, 255, 100, 0.3);
    }
    h2 {
      color: #90ee90;
      margin-bottom: 30px;
      text-align: center;
    }
    label {
      color: #76ff03;
    }
    input[type="text"], input[type="email"], input[type="password"] {
      background-color: #2c2c2c;
      color: white;
      border: none;
      border-radius: 10px;
      padding: 10px;
      width: 100%;
      margin-bottom: 15px;
    }
    .btn-submit, .btn-back {
      background-color: #4caf50;
      color: white;
      border: none;
      padding: 10px 20px;
      border-radius: 10px;
    }
    .btn-submit:hover, .btn-back:hover {
      background-color: #388e3c;
    }
  </style>
</head>
<body>
  <div class="container">
    <form method="POST" class="profile-card">
      <h2>Edit Admin Profile</h2>

      <label>Name:</label>
      <input type="text" name="admin_name" value="<?= htmlspecialchars($admin['admin_name']) ?>" required>

      <label>Email:</label>
      <input type="email" name="admin_email" value="<?= htmlspecialchars($admin['admin_email']) ?>" required>

      <label>Mobile Number:</label>
      <input type="text" name="admin_mobile_no" value="<?= htmlspecialchars($admin['admin_mobile_no']) ?>" required>

      <hr class="text-secondary">
      <label>Old Password (to change password):</label>
      <input type="password" name="old_password">

      <label>New Password:</label>
      <input type="password" name="new_password">

      <div class="text-center mt-3">
        <button type="submit" class="btn-submit">Update Profile</button>
        <a href="admin_profile.php" class="btn-back ms-3">← Back</a>
      </div>
    </form>
  </div>
</body>
</html>
