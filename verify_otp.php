<?php
session_start();
include("db.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $phone = $_SESSION['reset_phone'];
    $otp = $_POST['otp'];

    $stmt = $conn->prepare("SELECT id FROM users WHERE phone=? AND otp=? AND otp_expire > NOW()");
    $stmt->bind_param("si", $phone, $otp);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $_SESSION['otp_verified'] = true;
        header("Location: reset_password.html");
    } else {
        echo "❌ Invalid or expired OTP!";
    }
}
?>
