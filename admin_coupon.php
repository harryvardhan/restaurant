<?php
session_start();
include 'db.php';

// ===== DEBUG (turn off on production) =====
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ===== Admin guard =====
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: admin_login.php");
    exit();
}

// ===== Helpers =====
function columnExists(mysqli $conn, string $table, string $column): bool {
    $table = $conn->real_escape_string($table);
    $column = $conn->real_escape_string($column);
    $res = $conn->query("SHOW COLUMNS FROM `$table` LIKE '$column'");
    return $res && $res->num_rows > 0;
}

function ensureSchema(mysqli $conn): array {
    $changed = [];

    // coupons.usage_count
    if (!columnExists($conn, 'coupons', 'usage_count')) {
        $conn->query("ALTER TABLE `coupons` ADD `usage_count` INT NOT NULL DEFAULT 0");
        $changed[] = "Added coupons.usage_count";
    }

    // orders.coupon_code
    if (!columnExists($conn, 'orders', 'coupon_code')) {
        $conn->query("ALTER TABLE `orders` ADD `coupon_code` VARCHAR(50) NULL DEFAULT NULL");
        $changed[] = "Added orders.coupon_code";
    }

    return $changed;
}

$schemaChanges = ensureSchema($conn);
$ordersHasCoupon = columnExists($conn, 'orders', 'coupon_code');

// ===== Actions =====

// Add coupon
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_coupon'])) {
    $code = trim($_POST['code'] ?? '');
    $discount_type = $_POST['discount_type'] ?? '';
    $discount_value = (float)($_POST['discount_value'] ?? 0);
    $expiry = $_POST['expiry_date'] ?? '';

    if ($code !== '' && in_array($discount_type, ['percent', 'flat'], true) && $discount_value > 0 && $expiry !== '') {
        $stmt = $conn->prepare("INSERT INTO coupons (code, discount_type, discount_value, expiry_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $code, $discount_type, $discount_value, $expiry);
        $stmt->execute();
        header("Location: admin_coupon.php");
        exit();
    }
}

// Delete coupon
if (isset($_GET['delete'])) {
    $cid = (int)$_GET['delete'];
    if ($cid > 0) {
        $stmt = $conn->prepare("DELETE FROM coupons WHERE id = ?");
        $stmt->bind_param("i", $cid);
        $stmt->execute();
    }
    header("Location: admin_coupon.php");
    exit();
}

// Sync all usage_count from live orders
if (isset($_GET['sync_all'])) {
    if ($ordersHasCoupon) {
        // Single efficient UPDATE with JOIN
        $conn->query("
            UPDATE coupons c
            LEFT JOIN (
                SELECT coupon_code, COUNT(*) AS cnt
                FROM orders
                WHERE coupon_code IS NOT NULL AND coupon_code <> ''
                GROUP BY coupon_code
            ) o ON o.coupon_code = c.code
            SET c.usage_count = COALESCE(o.cnt, 0)
        ");
    } else {
        // If orders.coupon_code is missing, set everything to 0
        $conn->query("UPDATE coupons SET usage_count = 0");
    }
    header("Location: admin_coupon.php");
    exit();
}

// ===== Data query =====
if ($ordersHasCoupon) {
    $sql = "
        SELECT c.id, c.code, c.discount_type, c.discount_value, c.expiry_date, c.usage_count,
               COALESCE(o.cnt, 0) AS live_usage
        FROM coupons c
        LEFT JOIN (
            SELECT coupon_code, COUNT(*) AS cnt
            FROM orders
            WHERE coupon_code IS NOT NULL AND coupon_code <> ''
            GROUP BY coupon_code
        ) o ON o.coupon_code = c.code
        ORDER BY c.id DESC
    ";
} else {
    // Fallback if orders.coupon_code not present yet
    $sql = "
        SELECT c.id, c.code, c.discount_type, c.discount_value, c.expiry_date, c.usage_count,
               0 AS live_usage
        FROM coupons c
        ORDER BY c.id DESC
    ";
}
$coupons = $conn->query($sql);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Coupons</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body { font-family:Arial, sans-serif; margin:20px; background:#f7f8fb; }
        h1, h2 { margin:0 0 15px; }
        .topbar { display:flex; gap:10px; align-items:center; margin-bottom:15px; }
        .pill { padding:6px 10px; background:#eef3ff; border-radius:999px; font-size:12px; }
        .btn { display:inline-block; padding:8px 14px; border:none; border-radius:6px; text-decoration:none; cursor:pointer; }
        .btn-sync { background:#ff9800; color:#fff; }
        .btn-del { background:#dc3545; color:#fff; }
        .btn-add { background:#28a745; color:#fff; }
        .btn-nav { background:#007bff; color:#fff; }
        table { width:100%; border-collapse:collapse; background:#fff; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        th, td { padding:10px; border:1px solid #e6e6e6; text-align:center; }
        th { background:#1f6feb; color:#fff; }
        tr:nth-child(even) { background:#fafafa; }
        .wrap { max-width:1100px; margin:0 auto; }
        .grid { display:grid; grid-template-columns: 1fr 1fr; gap:20px; margin-top:20px; }
        .card { background:#fff; padding:16px; border-radius:8px; box-shadow:0 2px 6px rgba(0,0,0,0.06); }
        input, select { width:100%; padding:10px; border:1px solid #ddd; border-radius:6px; }
        label { font-size:14px; color:#333; display:block; margin-bottom:6px; }
        .muted { color:#666; font-size:12px; }
        .badge { display:inline-block; padding:2px 8px; border-radius:999px; font-size:12px; background:#eee; }
        .green { background:#e6f7ed; color:#166534; }
        .orange { background:#fff4e5; color:#8a4b00; }
    </style>
</head>
<body>
<div class="wrap">
    <div class="topbar">
        <h1>Coupons</h1>
        <?php if (!empty($schemaChanges)): ?>
            <span class="pill">Schema fixed: <?= htmlspecialchars(implode(', ', $schemaChanges)) ?></span>
        <?php endif; ?>
        <?php if (!$ordersHasCoupon): ?>
            <span class="pill">orders.coupon_code missing earlier — added now.</span>
        <?php endif; ?>
        <a class="btn btn-nav" href="admin_dashboard.php">← Dashboard</a>
        <a class="btn btn-nav" href="admin_coupon_manage.php">⚙️ Manage (Add/Delete)</a>
        <a class="btn btn-sync" href="?sync_all=1" onclick="return confirm('Sync usage_count from live orders for all coupons?')">🔄 Sync All</a>
    </div>

    <div class="card">
        <h2>Coupon Usage</h2>
        <p class="muted">Stored usage is in <b>coupons.usage_count</b>. Live usage is counted from <b>orders.coupon_code</b> on the fly.</p>
        <table>
            <tr>
                <th>ID</th>
                <th>Code</th>
                <th>Type</th>
                <th>Value</th>
                <th>Expiry</th>
                <th>Stored Usage</th>
                <th>Live Usage</th>
                <th>Actions</th>
            </tr>
            <?php if ($coupons->num_rows > 0): ?>
                <?php while ($c = $coupons->fetch_assoc()): ?>
                    <tr>
                        <td><?= (int)$c['id'] ?></td>
                        <td><?= htmlspecialchars($c['code']) ?></td>
                        <td><span class="badge"><?= htmlspecialchars(ucfirst($c['discount_type'])) ?></span></td>
                        <td>
                            <?php
                                if ($c['discount_type'] === 'percent') {
                                    echo htmlspecialchars($c['discount_value']) . '%';
                                } else {
                                    echo '₹' . htmlspecialchars(number_format($c['discount_value'], 2));
                                }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($c['expiry_date']) ?></td>
                        <td><span class="badge orange"><?= (int)$c['usage_count'] ?></span></td>
                        <td><span class="badge green"><?= (int)$c['live_usage'] ?></span></td>
                        <td>
                            <a class="btn btn-del" href="?delete=<?= (int)$c['id'] ?>" onclick="return confirm('Delete coupon <?= htmlspecialchars($c['code']) ?>?')">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr><td colspan="8">No coupons found.</td></tr>
            <?php endif; ?>
        </table>
    </div>

    <div class="grid">
        <div class="card">
            <h2>Add Coupon</h2>
            <form method="post">
                <label>Code</label>
                <input type="text" name="code" required placeholder="e.g., WELCOME10">

                <label style="margin-top:10px;">Discount Type</label>
                <select name="discount_type" required>
                    <option value="percent">Percent</option>
                    <option value="flat">Flat</option>
                </select>

                <label style="margin-top:10px;">Discount Value</label>
                <input type="number" step="0.01" name="discount_value" required placeholder="e.g., 10 or 50.00">

                <label style="margin-top:10px;">Expiry Date</label>
                <input type="date" name="expiry_date" required>

                <div style="margin-top:12px;">
                    <button type="submit" name="add_coupon" class="btn btn-add">Add Coupon</button>
                </div>
            </form>
            <p class="muted" style="margin-top:8px;">Example: Percent 10% → value 10. Flat ₹50 → value 50.</p>
        </div>

        <div class="card">
            <h2>Tips</h2>
            <ul class="muted">
                <li>After orders are placed with a coupon, click <b>Sync All</b> to push live usage into <b>usage_count</b>.</li>
                <li>If you recently added this page and saw 500 errors, the script now auto-adds missing columns.</li>
                <li>Use <b>admin_coupon_manage.php</b> for a focused add/delete view.</li>
            </ul>
        </div>
    </div>
</div>
</body>
</html>
