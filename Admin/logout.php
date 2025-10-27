<?php
session_start();
session_unset();
session_destroy();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Logged Out</title>
    <style>
        body {
            background: linear-gradient(-45deg, #0f2027, #203a43, #2c5364, #1a1a1a);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            color: #ffffff;
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            background-color: rgba(31, 31, 31, 0.95);
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.6);
            text-align: center;
            width: 300px;
        }

        .success-message {
            background-color: #2e7d32;
            color: #ffffff;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: bold;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.4);
        }

        .login-link {
            font-size: 14px;
            margin-top: 10px;
        }

        .login-link a {
            color: #90caf9;
            text-decoration: none;
            font-weight: bold;
        }

        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="success-message">You have logged out successfully!</div>
    <div class="login-link">Want to login? <a href="login.php">Click here</a></div>
</div>

</body>
</html>
