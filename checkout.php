<?php
session_start();
include 'db.php';

// ⚡ Enable debug mode
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check login
if (!isset($_SESSION['user_id'])) {
    $_SESSION['redirect_after_login'] = "checkout.php";
    header("Location: login.php");
    exit();
}

// Ensure cart exists
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    echo "<h2>Your cart is empty. <a href='menu.php'>Go back to Menu</a></h2>";
    exit;
}

// ✅ Calculate subtotal
$subtotal = 0;
foreach ($_SESSION['cart'] as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}

// ✅ Coupon handling
$discount = 0;
$couponMessage = "";
$couponCode = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_coupon'])) {
    $couponCode = trim($_POST['coupon_code']);

    $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ? AND expiry_date >= CURDATE()");
    $stmt->bind_param("s", $couponCode);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $coupon = $result->fetch_assoc();

        if ($coupon['discount_type'] == 'percent') {
            $discount = ($subtotal * $coupon['discount_value']) / 100;
        } elseif ($coupon['discount_type'] == 'flat') {
            $discount = $coupon['discount_value'];
        }

        $couponMessage = "✅ Coupon <b>" . htmlspecialchars($coupon['code']) . "</b> applied successfully!";
    } else {
        $couponMessage = "❌ Invalid or expired coupon!";
        $couponCode = ""; // reset invalid
    }
}

// ✅ Ensure total is never negative
$total = max(0, $subtotal - $discount);

// ✅ Place order when pressing Place Order
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $user_id = $_SESSION['user_id'];

    // Insert into orders with coupon_code
    $stmt = $conn->prepare("INSERT INTO orders (user_id, total_amount, order_status, coupon_code) VALUES (?, ?, 'pending', ?)");
    $stmt->bind_param("ids", $user_id, $total, $couponCode);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Insert order items
    foreach ($_SESSION['cart'] as $item) {
        $item_name = $item['name'];
        $quantity = $item['quantity'];
        $price = $item['price'];

        $itemStmt = $conn->prepare("INSERT INTO order_items (order_id, item_name, quantity, price) VALUES (?, ?, ?, ?)");
        $itemStmt->bind_param("isid", $order_id, $item_name, $quantity, $price);
        $itemStmt->execute();
    }

    // ✅ Increment coupon usage count if applied
    if (!empty($couponCode)) {
        $stmt = $conn->prepare("UPDATE coupons SET usage_count = usage_count + 1 WHERE code = ?");
        $stmt->bind_param("s", $couponCode);
        $stmt->execute();
    }

    // Save order id for payment
    $_SESSION['last_order_id'] = $order_id;

    // Clear cart
    unset($_SESSION['cart']);

    // Redirect to payment
    header("Location: payment.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f8f9fa; margin:0; padding:20px; }
        h1 { text-align:center; }
        table { width:80%; margin:20px auto; border-collapse:collapse; background:#fff; box-shadow:0px 2px 5px rgba(0,0,0,0.1); }
        th, td { border:1px solid #ddd; padding:10px; text-align:center; }
        th { background:#007bff; color:#fff; }
        .summary { width:50%; margin:20px auto; border:1px solid #ddd; background:#fff; padding:15px; border-radius:10px; box-shadow:0px 2px 5px rgba(0,0,0,0.1); }
        .btn { display:inline-block; margin-top:15px; padding:10px 20px; background:#007bff; color:#fff; border:none; cursor:pointer; border-radius:8px; }
        .btn:hover { background:#0056b3; }
        .success { color:green; text-align:center; }
        .error { color:red; text-align:center; }
        .coupon-box { text-align:center; margin:20px; }
    </style>
</head>
<body>
    <h1>Checkout</h1>

    <?php if ($couponMessage): ?>
        <p class="<?php echo (strpos($couponMessage, '✅') !== false) ? 'success' : 'error'; ?>">
            <?php echo $couponMessage; ?>
        </p>
    <?php endif; ?>

    <table>
        <tr>
            <th>Item</th>
            <th>Price (₹)</th>
            <th>Quantity</th>
            <th>Total (₹)</th>
        </tr>
        <?php foreach ($_SESSION['cart'] as $item): ?>
            <tr>
                <td><?php echo htmlspecialchars($item['name']); ?></td>
                <td><?php echo number_format($item['price'], 2); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>

    <!-- Coupon Form -->
    <div class="coupon-box">
        <form method="post" action="checkout.php">
            <input type="text" name="coupon_code" placeholder="Enter Coupon Code" value="<?php echo htmlspecialchars($couponCode); ?>">
            <button type="submit" name="apply_coupon" class="btn">Apply Coupon</button>
        </form>
    </div>

    <div class="summary">
        <p><b>Subtotal:</b> ₹<?php echo number_format($subtotal, 2); ?></p>
        <p><b>Discount:</b> -₹<?php echo number_format($discount, 2); ?></p>
        <p><b>Final Total:</b> ₹<?php echo number_format($total, 2); ?></p>

        <!-- Place Order -->
        <form method="post" action="checkout.php">
            <input type="hidden" name="place_order" value="1">
            <button type="submit" class="btn">Place Order</button>
        </form>
    </div>
</body>
</html>
