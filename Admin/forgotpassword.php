<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userEmail = $_POST["email"];

    // Dummy check - Replace with DB query
    if ($userEmail == "armankhorajiyask@gmail.com") {
        $userPassword = "123"; // Retrieved from database

        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'armankhorajiyask@gmail.com';
            $mail->Password   = 'zmmr ruxr nkti nsfz';
            $mail->SMTPSecure = 'tls';
            $mail->Port       = 587;

            $mail->setFrom('armankhorajiyask@gmail.com', 'Laptop Store');
            $mail->addAddress($userEmail);

            $mail->isHTML(true);
            $mail->Subject = 'Password Recovery - Laptop Store';
            $mail->Body    = "Hi,<br><br>Your password is: <strong>$userPassword</strong><br><br>Thank you.";

            $mail->send();
            $message = "<span class='success'>✅ Password has been sent to your email.</span>";
        } catch (Exception $e) {
            $message = "<span class='error'>❌ Mail Error: {$mail->ErrorInfo}</span>";
        }
    } else {
        $message = "<span class='error'>⚠️ Email not found in our records.</span>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Forgot Password</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(145deg, #0f0f0f, #1e1e1e);
            color: #ffffff;
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #1c1c1c;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 8px 20px rgba(0, 255, 255, 0.2);
            width: 380px;
            text-align: center;
            animation: fadeIn 0.5s ease;
        }

        .container h2 {
            color: #00ffff;
            margin-bottom: 25px;
            font-size: 24px;
        }

        input[type="email"] {
            width: 100%;
            padding: 12px 14px;
            margin-bottom: 20px;
            border: none;
            border-radius: 8px;
            background-color: #2a2a2a;
            color: #ffffff;
            font-size: 14px;
        }

        input[type="email"]::placeholder {
            color: #aaaaaa;
        }

        button {
            background-color: #00ffff;
            color: #000000;
            font-weight: 600;
            border: none;
            border-radius: 6px;
            padding: 10px 20px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #00dddd;
        }

        .message {
            margin-top: 20px;
            font-size: 14px;
        }

        .message .success {
            color: #00ff88;
        }

        .message .error {
            color: #ff4444;
        }

        a {
            display: inline-block;
            margin-top: 25px;
            color: #00ffff;
            text-decoration: none;
            font-weight: bold;
        }

        a:hover {
            text-decoration: underline;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: scale(0.95);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Forgotten Password</h2>
    <form method="POST">
        <input type="email" name="email" placeholder="Enter your registered email" required>
        <button type="submit">Send Password</button>
    </form>
    <div class="message"><?= $message ?></div>
    <a href="login.php">← Back to Login</a>
</div>

</body>
</html>
