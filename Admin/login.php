<?php
session_start();

$conn = new mysqli("localhost", "root", "", "laptop_store");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$email_error = "";
$password_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, ".com")) {
        $email_error = "Enter a valid email format.";
    }

    if (empty($email_error)) {
        // Query to check credentials from database
        $stmt = $conn->prepare("SELECT * FROM admin_master WHERE admin_email = ? AND admin_password = ?");
        $stmt->bind_param("ss", $email, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        // If admin found
        if ($result->num_rows === 1) {
            $_SESSION['logged_in'] = true;
            $_SESSION['admin_email'] = $email;
            header("Location: index.php"); // or admin_dashboard.php
            exit();
        } else {
            $password_error = "Incorrect email or password.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Login</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

  <style>
    :root {
      --bg-color: #ffffff;
      --container-bg: #f1f1f1;
      --text-color: #000;
      --input-bg: #e0e0e0;
      --button-bg: #4CAF50;
      --button-hover: #43a047;
      --error-color: #ff4d4d;
      --link-color: #4CAF50;
    }

    .dark-theme {
      --bg-color: #121212;
      --container-bg: #1e1e1e;
      --text-color: #ffffff;
      --input-bg: #2c2c2c;
    }

    * {
      box-sizing: border-box;
    }

    body {
      background: linear-gradient(-45deg, #0f2027, #203a43, #2c5364, #1a1a1a);
      background-size: 400% 400%;
      animation: gradientBG 12s ease infinite;
      color: var(--text-color);
      font-family: 'Poppins', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
      transition: background 0.5s ease, color 0.4s ease;
    }

    @keyframes gradientBG {
      0% { background-position: 0% 50%; }
      50% { background-position: 100% 50%; }
      100% { background-position: 0% 50%; }
    }

    .login-container {
      background: var(--container-bg);
      padding: 40px;
      border-radius: 15px;
      width: 100%;
      max-width: 400px;
      box-shadow: 0 0 25px rgba(0, 0, 0, 0.6);
      position: relative;
      transition: background 0.4s ease, box-shadow 0.4s ease;
    }

    .login-container h2 {
      text-align: center;
      margin-bottom: 30px;
      transition: color 0.4s ease;
    }

    .input-group {
      position: relative;
      margin-bottom: 20px;
    }

    .input-group input {
      width: 100%;
      padding: 12px 45px 12px 15px;
      border: none;
      border-radius: 30px;
      background: var(--input-bg);
      color: var(--text-color);
      font-size: 14px;
      transition: background 0.4s ease, color 0.4s ease;
    }

    .input-group i {
      position: absolute;
      top: 50%;
      right: 15px;
      transform: translateY(-50%);
      color: #aaa;
      cursor: pointer;
      z-index: 2;
      transition: color 0.4s ease;
    }

    input::placeholder {
      color: #bbb;
    }

    .error-text {
      color: var(--error-color);
      font-size: 13px;
      margin-top: -10px;
      margin-bottom: 15px;
      padding-left: 10px;
      text-align: left;
    }

    input[type="submit"] {
      width: 100%;
      padding: 12px;
      border: none;
      background: var(--button-bg);
      color: white;
      border-radius: 30px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s ease;
    }

    input[type="submit"]:hover {
      background: var(--button-hover);
    }

    .login-container p {
      text-align: center;
      margin-top: 20px;
      font-size: 14px;
    }

    .login-container a {
      color: var(--link-color);
      text-decoration: none;
      transition: color 0.3s ease;
    }

    .login-container a:hover {
      text-decoration: underline;
    }

    .theme-toggle-btn {
      position: absolute;
      top: 15px;
      right: 15px;
      background: transparent;
      border: none;
      color: var(--text-color);
      font-size: 20px;
      cursor: pointer;
      transition: transform 0.3s ease, color 0.4s ease;
    }

    .theme-toggle-btn:hover {
      transform: scale(1.2);
    }
  </style>
</head>
<body>

<div class="login-container">
  <!-- Theme Toggle Icon -->
  <button id="themeToggle" class="theme-toggle-btn" title="Toggle Theme">
    <i id="themeIcon" class="fas fa-moon"></i>
  </button>

  <h2>Admin Login</h2>
  <form method="post" action="">
    <!-- Email -->
    <div class="input-group">
      <input type="text" name="email" placeholder="Enter your email" required
             value="<?php echo isset($email) ? htmlspecialchars($email) : ''; ?>">
      <i class="fas fa-envelope"></i>
    </div>
    <?php if (!empty($email_error)): ?>
      <div class="error-text"><?php echo $email_error; ?></div>
    <?php endif; ?>

    <!-- Password -->
    <div class="input-group">
      <input type="password" name="password" id="password" placeholder="Enter your password" required>
      <i id="togglePassword" class="fas fa-eye-slash"></i>
    </div>
    <?php if (!empty($password_error)): ?>
      <div class="error-text"><?php echo $password_error; ?></div>
    <?php endif; ?>

    <input type="submit" value="Login">
  </form>
  <p><a href="forgotpassword.php">Forgot Password?</a></p>
</div>

<script>
  // Password Toggle
  const togglePassword = document.getElementById("togglePassword");
  const passwordField = document.getElementById("password");

  togglePassword.addEventListener("click", () => {
    const type = passwordField.getAttribute("type") === "password" ? "text" : "password";
    passwordField.setAttribute("type", type);
    togglePassword.classList.toggle("fa-eye");
    togglePassword.classList.toggle("fa-eye-slash");
  });

  // Theme Toggle
  const themeToggle = document.getElementById("themeToggle");
  const themeIcon = document.getElementById("themeIcon");
  const body = document.body;

  if (localStorage.getItem("theme") === "dark") {
    body.classList.add("dark-theme");
    themeIcon.classList.remove("fa-moon");
    themeIcon.classList.add("fa-sun");
  }

  themeToggle.addEventListener("click", () => {
    body.classList.toggle("dark-theme");
    const isDark = body.classList.contains("dark-theme");

    if (isDark) {
      themeIcon.classList.remove("fa-moon");
      themeIcon.classList.add("fa-sun");
      localStorage.setItem("theme", "dark");
    } else {
      themeIcon.classList.remove("fa-sun");
      themeIcon.classList.add("fa-moon");
      localStorage.setItem("theme", "light");
    }
  });
</script>

</body>
</html>
