<?php
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Narayana Restaurant</title>
    <style>
        body { font-family: Arial, sans-serif; margin:0; padding:0; background:#f4f4f9; }
        header { background:#007bff; color:#fff; padding:15px; text-align:center; }
        nav { display:flex; justify-content:center; background:#0056b3; padding:10px; }
        nav a { color:#fff; text-decoration:none; margin:0 15px; font-weight:bold; }
        nav a:hover { text-decoration:underline; }
        section { padding:30px; text-align:center; }
        .card-container { display:flex; justify-content:center; flex-wrap:wrap; gap:20px; }
        .card { background:#fff; padding:20px; width:250px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.15); }
        .card h3 { margin:0 0 10px; }
        .card p { color:#555; }
        .btn { display:inline-block; margin-top:10px; padding:10px 15px; background:#007bff; color:#fff; border:none; border-radius:6px; text-decoration:none; }
        .btn:hover { background:#0056b3; }
    </style>
</head>
<body>
<header>
    <h1>Admin Dashboard - Narayana Restaurant</h1>
</header>

<nav>
    <a href="admin_orders.php">Orders</a>
    <a href="admin_users.php">Users</a>
    <a href="admin_coupons.php">Coupons</a>
    <a href="logout.php">Logout</a>
</nav>

<section>
    <div class="card-container">
        <div class="card">
            <h3>Manage Orders</h3>
            <p>View and update customer orders.</p>
            <a href="admin_orders.php" class="btn">Go to Orders</a>
        </div>
        <div class="card">
            <h3>Manage Users</h3>
            <p>View, add, or delete registered users.</p>
            <a href="admin_users.php" class="btn">Go to Users</a>
        </div>
        <div class="card">
            <h3>Manage Coupons</h3>
            <p>Create, delete and track coupon usage.</p>
            <a href="admin_coupon.php" class="btn">Go to Coupons</a>
        </div>
    </div>
</section>
</body>
</html>
