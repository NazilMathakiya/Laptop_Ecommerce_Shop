<?php
session_start();
if (!isset($_SESSION["user_logged_in"]) || $_SESSION["user_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION["user_id"];
$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$stmt = $conn->prepare("SELECT user_id, full_name, user_email, user_mobile_no, user_address, created_at FROM user_master WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows !== 1) {
    echo "User not found!";
    exit();
}
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Profile</title>
    <style>
        body {
            background: #1c1c1c;
            color: #f5f5f5;
            font-family: 'Segoe UI', sans-serif;
            padding: 50px 20px;
        }

        .profile-container {
            background: #2a2a2a;
            border-radius: 12px;
            max-width: 600px;
            margin: auto;
            padding: 30px;
            box-shadow: 0 0 20px rgba(0,0,0,0.5);
        }

        h2 {
            text-align: center;
            color: #ffffff;
            margin-bottom: 30px;
        }

        .profile-row {
            margin-bottom: 20px;
        }

        .label {
            font-weight: bold;
            color: #cccccc;
        }

        .value {
            color: #f5f5f5;
            margin-top: 5px;
        }

        .btn {
            display: block;
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            font-weight: bold;
            background: linear-gradient(to right, #00ff88, #00cc66);
            color: #000;
            border: none;
            border-radius: 8px;
            text-align: center;
            text-decoration: none;
            transition: all 0.3s ease;
            box-sizing: border-box;
        }

        .btn:hover {
            background: linear-gradient(to right, #00cc66, #00994d);
            transform: scale(1.02);
        }

        .logout-btn {
            background: #e74c3c;
            color: white;
        }

        .logout-btn:hover {
            background: #c0392b;
        }

        .website-btn {
            background: #3498db;
            color: white;
        }

        .website-btn:hover {
            background: #2c80b4;
        }
    </style>
</head>
<body>
    <div class="profile-container">
        <h2>Welcome, <?php echo htmlspecialchars($user["full_name"]); ?></h2>

        <div class="profile-row"><div class="label">User ID:</div><div class="value"><?php echo $user["user_id"]; ?></div></div>
        <div class="profile-row"><div class="label">Full Name:</div><div class="value"><?php echo $user["full_name"]; ?></div></div>
        <div class="profile-row"><div class="label">Email:</div><div class="value"><?php echo $user["user_email"]; ?></div></div>
        <div class="profile-row"><div class="label">Mobile Number:</div><div class="value"><?php echo $user["user_mobile_no"]; ?></div></div>
        <div class="profile-row"><div class="label">Address:</div><div class="value"><?php echo $user["user_address"]; ?></div></div>
        <div class="profile-row"><div class="label">Created At:</div><div class="value"><?php echo $user["created_at"]; ?></div></div>

        <a href="edit_profile.php" class="btn">✏️ Edit Profile</a>
        <a href="index.php" class="btn website-btn">🏠 Go to Home</a>
        <form action="logout.php" method="post">
            <button type="submit" class="btn logout-btn">🚪 Logout</button>
        </form>
    </div>
</body>
</html>
    