<?php
session_start();
include 'db.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's orders
$sql = "SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$orders = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f9f9f9; padding: 20px; }
        .order-box { background: #fff; margin: 20px 0; padding: 20px; border-radius: 10px; box-shadow: 0 0 8px rgba(0,0,0,0.1); }
        h2 { color: #333; }
        h3 { color: #007bff; margin-bottom: 10px; }
        ul { list-style: none; padding: 0; }
        li { padding: 5px 0; border-bottom: 1px solid #eee; }
        .status { font-weight: bold; }
    </style>
</head>
<body>
    <h2>My Orders</h2>
    <?php if ($orders->num_rows > 0): ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <div class="order-box">
                <h3>Order #<?php echo $order['id']; ?></h3>
                <p><b>Total:</b> ₹<?php echo number_format($order['total_amount'], 2); ?></p>
                <p><b>Status:</b> <span class="status"><?php echo ucfirst($order['order_status']); ?></span></p>
                <p><b>Payment:</b> <?php echo ucfirst($order['payment_status']); ?> (<?php echo $order['payment_method'] ?? 'N/A'; ?>)</p>
                <p><b>Date:</b> <?php echo $order['created_at']; ?></p>

                <!-- Fetch order items -->
                <?php
                $itemSql = "SELECT * FROM order_items WHERE order_id = ?";
                $itemStmt = $conn->prepare($itemSql);
                $itemStmt->bind_param("i", $order['id']);
                $itemStmt->execute();
                $items = $itemStmt->get_result();
                ?>

                <ul>
                    <?php while ($item = $items->fetch_assoc()): ?>
                        <li><?php echo $item['item_name']; ?> × <?php echo $item['quantity']; ?> — ₹<?php echo $item['price']; ?></li>
                    <?php endwhile; ?>
                </ul>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have no orders yet. <a href="menu.php">Go to Menu</a></p>
    <?php endif; ?>
</body>
</html>
