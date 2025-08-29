<?php
session_start();

$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mobile = trim($_POST['mobile']);
    $password = trim($_POST['password']);

    // ✅ Hardcoded admin credentials
    $admin_mobile = "9580288354";
    $admin_password = "abcd@1234";

    if ($mobile === $admin_mobile && $password === $admin_password) {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dashboard.php");
        exit();
    } else {
        $error = "❌ Invalid credentials!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; text-align:center; padding:40px; }
        .login-box { width:300px; margin:auto; background:#fff; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); }
        input { width:90%; padding:10px; margin:10px 0; border:1px solid #ccc; border-radius:8px; }
        button { padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:8px; cursor:pointer; }
        button:hover { background:#0056b3; }
        .error { color:red; }
    </style>
</head>
<body>
    <div class="login-box">
        <h2>Admin Login</h2>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
        <form method="post">
            <input type="text" name="mobile" placeholder="Mobile Number" required><br>
            <input type="password" name="password" placeholder="Password" required><br>
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
