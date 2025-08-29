<?php
session_start();
include 'db.php';

if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// ✅ Delete User
if (isset($_GET['delete'])) {
    $uid = intval($_GET['delete']);
    $conn->query("DELETE FROM users WHERE id = $uid");
    header("Location: admin_users.php");
    exit();
}

// ✅ Add User
if ($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['add_user'])) {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("INSERT INTO users (name, phone, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $password);
    $stmt->execute();
}

// ✅ Fetch Users
$users = $conn->query("SELECT id, name, phone FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin - Users</title>
    <style>
        body { font-family:Arial, sans-serif; margin:20px; }
        table { width:100%; border-collapse:collapse; margin-bottom:20px; }
        th, td { padding:10px; border:1px solid #ddd; text-align:center; }
        th { background:#007bff; color:#fff; }
        .btn { padding:5px 10px; text-decoration:none; border-radius:5px; }
        .btn-del { background:red; color:white; }
    </style>
</head>
<body>
    <h1>Manage Users</h1>
    <table>
        <tr><th>ID</th><th>Name</th><th>Phone</th><th>Action</th></tr>
        <?php while($u = $users->fetch_assoc()): ?>
            <tr>
                <td><?= $u['id'] ?></td>
                <td><?= htmlspecialchars($u['name']) ?></td>
                <td><?= $u['phone'] ?></td>
                <td><a href="?delete=<?= $u['id'] ?>" class="btn btn-del" onclick="return confirm('Delete user?')">Delete</a></td>
            </tr>
        <?php endwhile; ?>
    </table>

    <h2>Add User</h2>
    <form method="post">
        <input type="text" name="name" placeholder="Name" required><br><br>
        <input type="text" name="phone" placeholder="Phone" required><br><br>
        <input type="password" name="password" placeholder="Password" required><br><br>
        <button type="submit" name="add_user">Add User</button>
    </form>
</body>
</html>
