<?php
session_start();

$error = "";
$success = "";
$name = "";
$email = "";
$mobile = "";
$address = "";

$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name             = trim($_POST["name"]);
    $email            = trim($_POST["email"]);
    $mobile           = trim($_POST["mobile"]);
    $address          = trim($_POST["address"]);
    $password         = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (!preg_match('/^[0-9]{10}$/', $mobile)) {
        $error = "Mobile number must be 10 digits.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check_stmt = $conn->prepare("SELECT * FROM user_master WHERE user_email = ? OR user_mobile_no = ?");
        $check_stmt->bind_param("ss", $email, $mobile);
        $check_stmt->execute();
        $check_stmt->store_result();

        if ($check_stmt->num_rows > 0) {
            $error = "This data is already registered. You can <a href='login.php' style='color:#28a745;'>login here</a>.";
        } else {
            $stmt = $conn->prepare("INSERT INTO user_master (full_name, user_email, user_password, user_mobile_no, user_address) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $name, $email, $password, $mobile, $address);

            if ($stmt->execute()) {
                $success = "Registration successful! You can now <a href='login.php' style='color:#28a745;'>login</a>.";
                $name = $email = $mobile = $address = "";
            } else {
                $error = "Something went wrong. Please try again.";
            }

            $stmt->close();
        }

        $check_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Registration</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #28a745;
            --light-bg: #f3f3f3;
            --dark-bg: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: var(--dark-bg);
            color: #fff;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            transition: background 0.6s ease, color 0.4s ease;
        }

        body.light {
            background: var(--light-bg);
            color: #000;
        }

        .register-container {
            background: rgba(0, 0, 0, 0.75);
            padding: 40px;
            border-radius: 16px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.6);
            transition: background 0.5s ease;
        }

        body.light .register-container {
            background: #fff;
        }

        h2 {
            text-align: center;
            color: var(--primary);
            margin-bottom: 30px;
        }

        .input-group {
            position: relative;
            margin-bottom: 24px;
        }

        .input-group input,
        .input-group textarea {
            width: 100%;
            padding: 14px 12px;
            border: 1px solid #888;
            border-radius: 6px;
            background: transparent;
            color: inherit;
            font-size: 15px;
            outline: none;
            transition: border 0.3s;
        }

        body.light .input-group input,
        body.light .input-group textarea {
            background: #f9f9f9;
        }

        .input-group label {
            position: absolute;
            left: 14px;
            top: 50%;
            transform: translateY(-50%);
            background: inherit;
            padding: 0 4px;
            font-size: 14px;
            color: #999;
            pointer-events: none;
            transition: 0.2s ease;
        }

        .input-group input:focus + label,
        .input-group input:not(:placeholder-shown) + label,
        .input-group textarea:focus + label,
        .input-group textarea:not(:placeholder-shown) + label {
            top: -8px;
            left: 10px;
            font-size: 12px;
            color: var(--primary);
        }

        .password-field i {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
        }

        #strengthMessage {
            font-size: 13px;
            margin-top: 4px;
            margin-bottom: 20px;
        }

        input[type="submit"] {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            font-size: 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.3s;
        }

        input[type="submit"]:hover {
            background: #218838;
        }

        .message {
            text-align: center;
            margin-top: 14px;
            font-size: 14px;
        }

        .message.error {
            color: #ff4d4d;
        }

        .message.success {
            color: var(--primary);
        }

        .theme-toggle {
            position: absolute;
            top: 20px;
            right: 20px;
        }

        .theme-toggle button {
            background: transparent;
            color: var(--primary);
            border: 1px solid var(--primary);
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 14px;
            cursor: pointer;
        }

        @media (max-width: 480px) {
            .register-container {
                padding: 30px 20px;
            }

            .theme-toggle {
                position: static;
                text-align: right;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>
<div class="theme-toggle">
    <button id="themeToggleBtn" onclick="toggleTheme()">🌙 Dark Mode</button>
</div>

<div class="register-container">
    <h2><i class="fas fa-user-plus"></i> Register</h2>
    <form method="post" autocomplete="off">
        <div class="input-group">
            <input type="text" name="name" id="name" value="<?= htmlspecialchars($name) ?>" placeholder=" " required>
            <label for="name">Full Name</label>
        </div>

        <div class="input-group">
            <input type="email" name="email" id="email" value="<?= htmlspecialchars($email) ?>" placeholder=" " required>
            <label for="email">Email</label>
        </div>

        <div class="input-group">
            <input type="tel" name="mobile" id="mobile" value="<?= htmlspecialchars($mobile) ?>" placeholder=" " required>
            <label for="mobile">Mobile Number</label>
        </div>

        <div class="input-group">
            <textarea name="address" id="address" placeholder=" "><?= htmlspecialchars($address) ?></textarea>
            <label for="address">Address (Optional)</label>
        </div>

        <div class="input-group password-field">
            <input type="password" name="password" id="password" placeholder=" " required>
            <label for="password">Password</label>
            <i class="fa-solid fa-eye-slash" id="togglePassword"></i>
        </div>

        <div id="strengthMessage"></div>

        <div class="input-group password-field">
            <input type="password" name="confirm_password" id="confirm_password" placeholder=" " required>
            <label for="confirm_password">Confirm Password</label>
            <i class="fa-solid fa-eye-slash" id="toggleConfirmPassword"></i>
        </div>

        <input type="submit" value="Register">
    </form>

    <?php if ($error): ?>
        <div class="message error"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="message success"><?= $success ?></div>
    <?php endif; ?>
</div>

<script>
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm_password");
    const togglePassword = document.getElementById("togglePassword");
    const toggleConfirmPassword = document.getElementById("toggleConfirmPassword");
    const strengthMessage = document.getElementById("strengthMessage");
    const themeBtn = document.getElementById("themeToggleBtn");

    togglePassword.addEventListener("click", function () {
        password.type = password.type === "password" ? "text" : "password";
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
    });

    toggleConfirmPassword.addEventListener("click", function () {
        confirmPassword.type = confirmPassword.type === "password" ? "text" : "password";
        this.classList.toggle("fa-eye");
        this.classList.toggle("fa-eye-slash");
    });

    password.addEventListener("input", function () {
        const val = password.value;
        let strength = "";

        if (val.length < 6) {
            strength = "<span style='color:#ff4d4d'>Weak</span>";
        } else if (val.match(/[a-z]/) && val.match(/[0-9]/)) {
            strength = "<span style='color:#ffc107'>Medium</span>";
        }
        if (val.length >= 8 && val.match(/[A-Z]/) && val.match(/[a-z]/) && val.match(/[0-9]/) && val.match(/[^A-Za-z0-9]/)) {
            strength = "<span style='color:#28a745'>Strong</span>";
        }

        strengthMessage.innerHTML = "Password Strength: " + strength;
    });

    function toggleTheme() {
        document.body.classList.toggle("light");
        updateThemeLabel();
    }

    function updateThemeLabel() {
        themeBtn.textContent = document.body.classList.contains("light") ? "🌙 Dark Mode" : "☀️ Light Mode";
    }

    updateThemeLabel();
</script>
</body>
</html>
