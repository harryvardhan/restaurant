<?php
session_start();
include 'db.php';

// Check if order exists
if (!isset($_SESSION['last_order_id'])) {
    header("Location: menu.php");
    exit();
}

$orderId = $_SESSION['last_order_id'];

// Fetch order details
$sql = "SELECT * FROM orders WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $orderId);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();

if (!$order) {
    echo "<h2>Order not found!</h2>";
    exit;
}

$total = $order['total_amount'];

// If Pay on Counter selected
if (isset($_POST['pay_counter'])) {
    $sql = "UPDATE orders SET payment_method = 'Counter', payment_status = 'unpaid', order_status = 'pending' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    header("Location: my_orders.php");
    exit();
}

// If UPI selected (just mark as UPI, still pending until admin verifies)
if (isset($_POST['pay_upi'])) {
    $sql = "UPDATE orders SET payment_method = 'UPI', payment_status = 'unverified', order_status = 'pending' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $orderId);
    $stmt->execute();

    header("Location: my_orders.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; text-align:center; padding:30px; }
        .box { background:#fff; display:inline-block; padding:20px; border-radius:10px; box-shadow:0 0 10px rgba(0,0,0,0.1); width:350px; }
        h2 { color:#333; }
        img { margin:20px 0; }
        .note { margin-top:15px; font-size:14px; color:#777; }
        button.btn { display:inline-block; margin:10px; padding:10px 20px; background:#007bff; color:#fff; border:none; border-radius:8px; cursor:pointer; }
        button.btn:hover { background:#0056b3; }
        .or { margin:20px 0; font-weight:bold; color:#555; }
    </style>
</head>
<body>
    <div class="box">
        <h2>Pay ₹<?php echo number_format($total, 2); ?></h2>

        <!-- Pay via UPI -->
        <form method="post">
            <p>Scan this QR code with your UPI app (PhonePe, GPay, Paytm, etc.)</p>
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=upi://pay?pa=YOUR_UPI_ID@upi&pn=Narayans%20Restaurant&am=<?php echo $total; ?>&cu=INR" alt="UPI QR Code">
            <p class="note">Your payment will be verified by the admin.</p>
            <button type="submit" name="pay_upi" class="btn">I Paid via UPI</button>
        </form>

        <div class="or">OR</div>

        <!-- Pay on Counter -->
        <form method="post">
            <p>Pay directly at the restaurant counter.</p>
            <p class="note">Admin will mark this as paid once you pay on counter.</p>
            <button type="submit" name="pay_counter" class="btn">Pay on Counter</button>
        </form>
    </div>
</body>
</html>
