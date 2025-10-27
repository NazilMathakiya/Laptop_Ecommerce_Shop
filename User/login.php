<?php
session_start();

$error = "";
$email_input = "";
$password_input = "";

$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_input = trim($_POST["username"]);
    $password_input = trim($_POST["password"]);

    if (!filter_var($email_input, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } else {
        $stmt = $conn->prepare("SELECT * FROM user_master WHERE user_email = ?");
        $stmt->bind_param("s", $email_input);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $row = $result->fetch_assoc();
            $db_password = trim($row['user_password']);
            if ($password_input === $db_password) {
                $_SESSION["user_logged_in"] = true;
                $_SESSION["user_email"] = $email_input;
                $_SESSION["user_id"] = $row["user_id"];
                header("Location: index.php");
                exit();
            } else {
                $error = "Incorrect password.";
            }
        } else {
            $error = "Email not found.";
        }

        $stmt->close();
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login | Dark Box Theme Toggle</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --bg-gradient-dark: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            --form-bg-light: #ffffff;
            --form-bg-dark: #1e1e1e;
            --input-bg-light: #f9f9f9;
            --input-bg-dark: #2a2a2a;
            --text-light: #000000;
            --text-dark: #ffffff;
            --primary-color: #4CAF50;
            --error-color: #ff4d4d;
        }

        body {
            margin: 0;
            height: 100vh;
            background: var(--bg-gradient-dark);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', sans-serif;
            color: var(--text-dark);
            transition: all 0.4s ease;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            background-color: var(--form-bg-dark);
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 0 25px rgba(0,0,0,0.3);
            width: 100%;
            max-width: 400px;
            position: relative;
            transition: all 0.4s ease;
            color: var(--text-dark);
        }

        .login-container.light {
            background-color: var(--form-bg-light);
            color: var(--text-light);
        }

        .login-container h2 {
            text-align: center;
            color: var(--primary-color);
            margin-bottom: 30px;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group input {
            width: 100%;
            padding: 14px 12px;
            background-color: var(--input-bg-dark);
            color: var(--text-dark);
            border: none;
            border-radius: 6px;
            outline: none;
            transition: all 0.3s;
        }

        .login-container.light .input-group input {
            background-color: var(--input-bg-light);
            color: var(--text-light);
        }

        .input-group label {
            position: absolute;
            top: 50%;
            left: 12px;
            transform: translateY(-50%);
            color: #bbb;
            pointer-events: none;
            font-size: 14px;
            transition: 0.2s ease all;
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label {
            top: -10px;
            font-size: 12px;
            color: var(--primary-color);
            background: inherit;
            padding: 0 5px;
        }

        .password-toggle {
            position: absolute;
            top: 50%;
            right: 12px;
            transform: translateY(-50%);
            cursor: pointer;
            color: #999;
        }

        .login-container input[type="submit"] {
            width: 100%;
            padding: 14px;
            border: none;
            background-color: var(--primary-color);
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-container input[type="submit"]:hover {
            background-color: #43a047;
        }

        .links {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .links a {
            color: var(--primary-color);
            text-decoration: none;
        }

        .links a:hover {
            text-decoration: underline;
        }

        .error {
            color: var(--error-color);
            text-align: center;
            margin-bottom: 20px;
        }

        .theme-toggle {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 20px;
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-dark);
        }

        .login-container.light .theme-toggle {
            color: var(--text-light);
        }

        @media (max-width: 420px) {
            .login-container {
                margin: 10px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
    <div class="login-container" id="loginBox">
        <button class="theme-toggle" title="Toggle Theme" onclick="toggleTheme()">
            <i class="fa-solid fa-circle-half-stroke"></i>
        </button>

        <h2><i class="fas fa-user-circle"></i> Login</h2>

        <?php if (!empty($error)) : ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="input-group">
                <input type="text" name="username" id="email" required placeholder=" " value="<?php echo htmlspecialchars($email_input); ?>">
                <label for="email">Email</label>
            </div>

            <div class="input-group">
                <input type="password" name="password" id="password" required placeholder=" ">
                <label for="password">Password</label>
                <i class="fa-solid fa-eye-slash password-toggle" id="togglePassword"></i>
            </div>

            <input type="submit" value="Login">

            <div class="links">
                <p><a href="forgotpassword.php">Forgot Password?</a></p>
                <p>Don't have an account? <a href="register.php">Register</a></p>
            </div>
        </form>
    </div>

    <script>
        const passwordInput = document.getElementById("password");
        const togglePassword = document.getElementById("togglePassword");

        togglePassword.addEventListener("click", function () {
            const type = passwordInput.type === "password" ? "text" : "password";
            passwordInput.type = type;
            togglePassword.classList.toggle("fa-eye");
            togglePassword.classList.toggle("fa-eye-slash");
        });

        function toggleTheme() {
            document.getElementById("loginBox").classList.toggle("light");
        }
    </script>
</body>
</html>
