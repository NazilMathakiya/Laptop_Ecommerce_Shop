<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Logged Out</title>
    <style>
        /* Background with dark green animated gradient */
        body {
            background: linear-gradient(-45deg, #0f2027, #203a43, #2c5364, #0f3829);
            background-size: 400% 400%;
            animation: gradientShift 10s ease infinite;
            color: #00ff88;
            font-family: 'Segoe UI', sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .message-box {
            background: rgba(20, 20, 20, 0.85);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0, 255, 136, 0.3);
            text-align: center;
            animation: fadeIn 1s ease-in-out;
            backdrop-filter: blur(5px);
        }

        .message-box h1 {
            margin-bottom: 20px;
            font-size: 28px;
        }

        .message-box p {
            font-size: 16px;
            color: #aaffcc;
        }

        .message-box button {
            background: linear-gradient(to right, #00ff88, #00cc6e);
            color: #000;
            border: none;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: bold;
            border-radius: 6px;
            cursor: pointer;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            margin-top: 20px;
        }

        .message-box button:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 255, 136, 0.4);
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h1>Logged out successfully</h1>
        <p>You have been logged out of your account.</p>
        <form action="login.php">
            <button type="submit">Want to Login</button>
        </form>
    </div>
</body>
</html>
