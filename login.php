<?php
session_start();
include 'db.php';  // ✅ Use central DB connection

$error = "";

// ---------------- LOGIN ----------------
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE phone=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // ✅ Save user session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['name'] = $user['name'];
        $_SESSION['user'] = $user;

        // ✅ Check if a redirect-after-login is set
        if (isset($_SESSION['redirect_after_login'])) {
            $redirect = $_SESSION['redirect_after_login'];
            unset($_SESSION['redirect_after_login']); // clear after use
            header("Location: $redirect");
            exit;
        }

        // Default: Redirect to homepage or welcome page
        header("Location: welcome.php");
        exit;
    } else {
        $error = "❌ Invalid phone number or password!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Narayana Restaurant</title>
    <link rel="stylesheet" href="css/form_style.css"> <!-- ✅ External CSS -->
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error) echo "<p class='msg error'>$error</p>"; ?>

        <!-- LOGIN FORM -->
        <form method="POST" action="">
            <input type="text" name="phone" placeholder="Enter Phone Number" required>
            <input type="password" name="password" placeholder="Enter Password" required>
            <button type="submit" name="login">Login</button>
        </form>

        <!-- Forgot Password redirect -->
        <form method="GET" action="forgot_password.php">
            <button type="submit">Forgot Password?</button>
        </form>

        <p style="text-align:center; margin-top:10px;">
            New user? <a href="signup.php">Signup here</a>
        </p>
    </div>
</body>
</html>
