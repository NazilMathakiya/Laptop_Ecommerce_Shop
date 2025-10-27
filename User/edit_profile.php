<?php
session_start();
if (!isset($_SESSION["user_logged_in"]) || $_SESSION["user_logged_in"] !== true) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION["user_id"];
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST["full_name"]);
    $email = trim($_POST["user_email"]);
    $mobile = trim($_POST["user_mobile_no"]);
    $address = trim($_POST["user_address"]);

    $stmt = $conn->prepare("UPDATE user_master SET full_name = ?, user_email = ?, user_mobile_no = ?, user_address = ? WHERE user_id = ?");
    $stmt->bind_param("ssssi", $full_name, $email, $mobile, $address, $user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: profile.php");
    exit();
}

$stmt = $conn->prepare("SELECT full_name, user_email, user_mobile_no, user_address FROM user_master WHERE user_id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Profile</title>
    <style>
        body {
            background: #1c1c1c;
            color: #f5f5f5;
            font-family: 'Segoe UI', sans-serif;
            padding: 50px 20px;
        }

        .container {
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

        label {
            display: block;
            margin-top: 15px;
            color: #cccccc;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: none;
            margin-top: 8px;
            background: #333;
            color: #f5f5f5;
        }

        .form-btn {
            background: linear-gradient(to right, #00ff88, #00cc66);
            color: black;
            font-weight: bold;
            border: none;
            padding: 12px;
            width: 100%;
            margin-top: 20px;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            box-sizing: border-box;
        }

        .form-btn:hover {
            background: linear-gradient(to right, #00cc66, #00994d);
            transform: scale(1.02);
        }

        .back-btn {
            background: #3498db;
            color: white;
        }

        .back-btn:hover {
            background: #2c80b4;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Edit Your Profile</h2>
        <form method="post" action="">
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>

            <label for="user_email">Email:</label>
            <input type="email" name="user_email" value="<?php echo htmlspecialchars($user['user_email']); ?>" required>

            <label for="user_mobile_no">Mobile Number:</label>
            <input type="text" name="user_mobile_no" value="<?php echo htmlspecialchars($user['user_mobile_no']); ?>" required>

            <label for="user_address">Address:</label>
            <textarea name="user_address" rows="3"><?php echo htmlspecialchars($user['user_address']); ?></textarea>

            <input type="submit" value="✅ Update Profile" class="form-btn">
        </form>

        <a href="profile.php" class="form-btn back-btn">← Back to Profile</a>
    </div>
</body>
</html>
