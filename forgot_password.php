<?php
session_start();
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require 'vendor/autoload.php';
include __DIR__ . "/db.php"; // ✅ Use Hostinger DB config

$error = "";
$success = "";
$step = 1; // default step

// Step 1: Send OTP
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send_otp'])) {
    $email = trim($_POST['email']);

    $sql = "SELECT * FROM users WHERE email=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $otp = rand(100000, 999999);
        $_SESSION['reset_email'] = $email;
        $_SESSION['reset_otp'] = $otp;
        $_SESSION['reset_time'] = time();

        // Send OTP via Email
        $mail = new PHPMailer(true);
        try {
            $mail->isSMTP();
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPAuth = true;
            $mail->Username = "narayanrestaurant1@gmail.com"; 
            $mail->Password = "bfrg rsbq osex owko";  
            $mail->SMTPSecure = "tls";
            $mail->Port = 587;

            $mail->setFrom("narayanrestaurant1@gmail.com", "Narayana Restaurant");
            $mail->addAddress($email);
            $mail->isHTML(true);
            $mail->Subject = "Password Reset OTP - Narayana Restaurant";
            $mail->Body = "<h3>Your OTP is: <b>$otp</b></h3><p>Valid for 2 minutes.</p>";

            $mail->send();
            $success = "✅ OTP sent to your email!";
            $step = 2;
        } catch (Exception $e) {
            $error = "❌ Error sending OTP. Try again later.";
            $step = 1;
        }
    } else {
        $error = "❌ Email not found!";
        $step = 1;
    }
}

// Step 2: Verify OTP + Reset Password
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reset_password'])) {
    $otp = $_POST['otp'];
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    if (!isset($_SESSION['reset_email'])) {
        $error = "⚠️ Session expired. Please request OTP again!";
        $step = 1;
    } elseif (time() - $_SESSION['reset_time'] > 120) {
        $error = "⏰ OTP expired! Please request a new one.";
        session_unset();
        $step = 1;
    } elseif ($otp != $_SESSION['reset_otp']) {
        $error = "❌ Invalid OTP!";
        $step = 2;
    } elseif ($password !== $confirm) {
        $error = "⚠️ Passwords do not match!";
        $step = 2;
    } else {
        $hashed = password_hash($password, PASSWORD_BCRYPT);
        $email = $_SESSION['reset_email'];

        $sql = "UPDATE users SET password=? WHERE email=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $hashed, $email);

        if ($stmt->execute()) {
            session_unset();
            session_destroy();
            $success = "🎉 Password updated successfully!";
            $step = 3;
        } else {
            $error = "❌ Error updating password!";
            $step = 2;
        }
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- ✅ Mobile responsive -->
    <style>
        /* ===== Global Reset ===== */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* ===== Body with Gradient ===== */
body {
  font-family: 'Segoe UI', Tahoma, sans-serif;
  background: linear-gradient(135deg, #585446ff, #0a0e47ff);
  color: #fff;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}

/* ===== Container (Card) ===== */
.form-container {
  width: 100%;
  max-width: 420px;
  background: rgba(255, 255, 255, 0.1);
  padding: 25px;
  border-radius: 12px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(8px);
  text-align: center;
}

/* ===== Heading ===== */
.form-container h2 {
  margin-bottom: 20px;
  font-size: 26px;
  font-style: italic;
  color: #ffd369;
}

/* ===== Input Fields ===== */
input {
  width: 100%;
  padding: 12px 14px;
  margin: 10px 0;
  border: none;
  border-radius: 8px;
  font-size: 15px;
  outline: none;
}

/* ===== Buttons ===== */
button {
  width: 100%;
  padding: 12px;
  margin-top: 12px;
  border: none;
  border-radius: 8px;
  background: linear-gradient(135deg, #ec8611ff, #faa638ff);
  color: black;
  font-weight: bold;
  font-size: 16px;
  cursor: pointer;
  transition: transform 0.2s ease, background 0.3s ease;
}

button:hover {
  transform: scale(1.05);
  background: linear-gradient(135deg, #d67a0f, #f7941d);
}

/* ===== Links ===== */
a {
  color: #ffd369;
  text-decoration: none;
  font-style: italic;
}

a:hover {
  text-decoration: underline;
}

/* ===== Messages ===== */
.msg {
  font-weight: bold;
  margin: 12px 0;
}

.error {
  color: #ff4d4d;
}

.success {
  color: #4caf50;
}

/* ===== Responsive ===== */
@media (max-width: 480px) {
  .form-container {
    padding: 20px;
  }
  .form-container h2 {
    font-size: 22px;
  }
  input, button {
    font-size: 14px;
    padding: 10px;
  }
}

    </style>
</head>
<body>
    <div class="form-container">
        <h2>Forgot Password</h2>
        <?php if ($error) echo "<p class='msg error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='msg success'>$success</p>"; ?>

        <?php if ($step == 1): ?>
        <!-- Step 1: Enter Email -->
        <form method="POST" action="">
            <input type="email" name="email" placeholder="Enter your registered Email" required>
            <button type="submit" name="send_otp">Send OTP</button>
        </form>

        <?php elseif ($step == 2): ?>
        <!-- Step 2: Verify OTP + New Password -->
        <form method="POST" action="">
            <input type="text" name="otp" placeholder="Enter OTP" required>
            <input type="password" name="password" placeholder="New Password" required>
            <input type="password" name="confirm" placeholder="Confirm Password" required>
            <button type="submit" name="reset_password">Reset Password</button>
        </form>

        <?php elseif ($step == 3): ?>
        <!-- Step 3: Success -->
        <p style="text-align:center;">✅ Password reset successful! <a href="login.php">Login now</a></p>
        <?php endif; ?>
    </div>
</body>
</html>
