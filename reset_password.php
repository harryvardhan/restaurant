<?php
session_start();
include("db.php");

$error = "";
$success = "";

// Handle password reset
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['otp_verified']) || !isset($_SESSION['reset_phone'])) {
        $error = "❌ Unauthorized request!";
    } else {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];

        // Check if passwords match
        if ($new_password !== $confirm_password) {
            $error = "⚠️ Passwords do not match!";
        } else {
            $phone = $_SESSION['reset_phone'];
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

            $stmt = $conn->prepare("UPDATE users SET password=?, otp=NULL, otp_expire=NULL WHERE phone=?");
            $stmt->bind_param("ss", $hashed_password, $phone);

            if ($stmt->execute()) {
                $success = "✅ Password updated successfully! <a href='login.php'>Login now</a>";
                session_destroy();
            } else {
                $error = "❌ Error updating password!";
            }

            $stmt->close();
            $conn->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reset Password - Narayana Restaurant</title>
  <link rel="stylesheet" href="form_style.css">
</head>
<body>
  <div class="form-container">
    <h2>Reset Password</h2>
    
    <?php if ($error): ?>
      <p class="msg error"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
      <p class="msg success"><?php echo $success; ?></p>
    <?php else: ?>
      <form method="POST" action="">
        <input type="password" name="new_password" placeholder="Enter New Password" required>
        <input type="password" name="confirm_password" placeholder="Confirm New Password" required>
        <button type="submit">Update Password</button>
      </form>
    <?php endif; ?>
  </div>
</body>
</html>
