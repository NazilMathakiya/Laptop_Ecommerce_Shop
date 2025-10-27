<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$error = "";
$success = "";

$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT user_password FROM user_master WHERE user_email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $user_password = $row["user_password"];

            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host       = 'smtp.gmail.com';
                $mail->SMTPAuth   = true;
                $mail->Username   = 'armankhorajiyask@gmail.com';   // Your Gmail
                $mail->Password   = 'gyvx ztpl knvj yebx';           // App password
                $mail->SMTPSecure = 'tls';
                $mail->Port       = 587;

                $mail->setFrom('armankhorajiyask@gmail.com', 'Laptop Store');
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = 'Your Password Recovery';
                $mail->Body    = "<p>Your password is: <strong>$user_password</strong></p>";

                $mail->send();
                $success = "Password has been sent to your email.";
            } catch (Exception $e) {
                $error = "Email could not be sent. Error: " . $mail->ErrorInfo;
            }
        } else {
            $error = "This email is not registered.";
        }

        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <style>
        body {
            margin: 0;
            height: 100vh;
            font-family: Arial, sans-serif;
            background: linear-gradient(-45deg, #0f2027, #1a1a1a, #144b3f, #091e2f);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .forgot-container {
            background-color: rgba(0, 0, 0, 0.85);
            padding: 35px;
            border-radius: 12px;
            width: 360px;
            box-shadow: 0 0 15px rgba(0,255,136,0.4);
            animation: fadeIn 1s ease-in-out;
        }

        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #00ff88;
        }

        input[type="email"],
        input[type="submit"],
        .back-btn {
            width: 100%;
            padding: 12px;
            margin: 12px 0;
            border: none;
            border-radius: 6px;
            font-size: 15px;
        }

        input[type="email"] {
            background: #2a2a2a;
            color: #ffffff;
        }

        input[type="submit"] {
            background: #00ff88;
            color: #000;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        input[type="submit"]:hover {
            background: #00cc6e;
        }

        .back-btn {
            background: #ffffff22;
            color: #00ff88;
            text-align: center;
            text-decoration: none;
            display: block;
            font-weight: bold;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .back-btn:hover {
            background: #00ff88;
            color: #000;
        }

        .message {
            text-align: center;
            margin-top: 15px;
            font-weight: bold;
        }

        .error { color: #ff4d4d; }
        .success { color: #00ff88; }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="forgot-container">
    <h2>Forgot Password</h2>

    <?php if ($success): ?>
        <div class="message success"><?= $success ?></div>
        <a class="back-btn" href="login.php">← Back to Login</a>
    <?php else: ?>
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your email" required>
            <input type="submit" value="Send Password">
        </form>
        <?php if ($error): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
