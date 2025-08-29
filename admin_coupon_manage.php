<?php
session_start();
include 'db.php';

// ✅ Only allow admin
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// ✅ Delete Coupon
if (isset($_GET['delete'])) {
    $cid = intval($_GET['delete']);
    $conn->query("DELETE FROM coupons WHERE id = $cid");
    header("Location: admin_coupon_manage.php");
    exit();
}

// ✅ Add Coupon
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_coupon'])) {
    $code = $_POST['code'];
    $discount_type = $_POST['discount_type'];
    $discount_value = floatval($_POST['discount_value']);
    $expiry = $_POST['expiry_date'];

    $stmt = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, expiry_date, usage_count) VALUES (?, ?, ?, ?, 0)");
    $stmt->bind_param("ssds", $code, $discount_type, $discount_value, $expiry);
    $stmt->execute();
}

// ✅ Fetch Coupons
$coupons = $conn->query("SELECT * FROM coupons ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Coupons</title>
    <style>
        body { font-family:Arial, sans-serif; margin:20px; background:#f9f9f9; }
        table { width:100%; border-collapse:collapse; margin-bottom:20px; background:#fff; box-shadow:0px 2px 5px rgba(0,0,0,0.1); }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; }
        th { background:#28a745; color:#fff; }
        tr:nth-child(even) { background:#f2f2f2; }
        .btn { padding:5px 10px; text-decoration:none; border-radius:5px; }
        .btn-del { background:red; color:white; }
        .btn-del:hover { opacity:0.8; }
        form { background:#fff; padding:15px; border:1px solid #ddd; border-radius:10px; width:300px; margin-top:20px; }
        h1,h2 { color:#333; }
    </style>
</head>
<body>
    <h1>Manage Coupons</h1>
    <table>
        <tr>
            <th>ID</th>
            <th>Code</th>
            <th>Type</th>
            <th>Value</th>
            <th>Expiry</th>
            <th>Usage Count</th>
            <th>Action</th>
        </tr>
        <?php while($c = $coupons->fetch_assoc()): ?>
            <tr>
                <td><?= $c['id'] ?></td>
                <td><?= htmlspecialchars($c['code']) ?></td>
                <td><?= ucfirst($c['discount_type']) ?></td>
                <td><?= $c['discount_type'] == 'percent' ? $c['discount_value'].'%' : '₹'.$c['discount_value'] ?></td>
                <td><?= $c['expiry_date'] ?></td>
                <td><?= $c['usage_count'] ?></td>
                <td>
                    <a href="?delete=<?= $c['id'] ?>" class="btn btn-del" onclick="return confirm('Delete coupon?')">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Add Coupon</h2>
    <form method="post">
        <input type="text" name="code" placeholder="Coupon Code" required><br><br>
        <select name="discount_type" required>
            <option value="percent">Percent</option>
            <option value="flat">Flat</option>
        </select><br><br>
        <input type="number" step="0.01" name="discount_value" placeholder="Discount Value" required><br><br>
        <input type="date" name="expiry_date" required><br><br>
        <button type="submit" name="add_coupon">➕ Add Coupon</button>
    </form>

    <br>
    <a href="admin_coupons.php">📊 View Coupon Usage (Orders Linked)</a>
</body>
</html>
