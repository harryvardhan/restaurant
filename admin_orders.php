<?php
session_start();
include 'db.php';

// ✅ Check admin login
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// ✅ Mark order as Paid + Completed
if (isset($_GET['mark_paid'])) {
    $order_id = intval($_GET['mark_paid']);
    $stmt = $conn->prepare("UPDATE orders SET payment_status = 'Paid', order_status = 'Completed' WHERE id = ?");
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    header("Location: admin_orders.php"); // Refresh page
    exit();
}

// ✅ Fetch all orders with user name
$sql = "SELECT o.id, o.user_id, u.name AS user_name, o.total_amount, o.payment_method, o.payment_status, o.order_status, o.created_at 
        FROM orders o 
        JOIN users u ON o.user_id = u.id 
        ORDER BY o.created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Orders</title>
    <style>
        body { font-family: Arial, sans-serif; background:#f4f4f9; margin:0; padding:20px; }
        h1 { text-align:center; margin-bottom:20px; }
        table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0px 2px 5px rgba(0,0,0,0.1); }
        th, td { border:1px solid #ddd; padding:10px; text-align:center; }
        th { background:#007bff; color:#fff; }
        tr:nth-child(even) { background:#f9f9f9; }
        .btn { padding:5px 10px; border:none; cursor:pointer; border-radius:5px; text-decoration:none; }
        .btn-paid { background:#28a745; color:#fff; }
        .btn-paid:hover { background:#218838; }
    </style>
</head>
<body>

<h1>Admin - Manage Orders</h1>

<table>
    <tr>
        <th>Order ID</th>
        <th>User ID</th>
        <th>User Name</th>
        <th>Total Amount</th>
        <th>Payment Method</th>
        <th>Payment Status</th>
        <th>Order Status</th>
        <th>Created At</th>
        <th>Action</th>
    </tr>
    <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo $row['user_id']; ?></td>
                <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                <td>₹<?php echo number_format($row['total_amount'], 2); ?></td>
                <td><?php echo $row['payment_method']; ?></td>
                <td><?php echo $row['payment_status']; ?></td>
                <td><?php echo $row['order_status']; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <?php if ($row['payment_status'] !== 'Paid'): ?>
                        <a href="?mark_paid=<?php echo $row['id']; ?>" class="btn btn-paid" onclick="return confirm('Mark this order as Paid and Completed?')">Mark as Paid</a>
                    <?php else: ?>
                        ✅ Paid & Completed
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
    <?php else: ?>
        <tr><td colspan="9">No orders found.</td></tr>
    <?php endif; ?>
</table>

</body>
</html>
