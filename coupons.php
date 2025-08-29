<?php
session_start();
include 'db.php'; // DB connection

// Fetch all coupons
$sql = "SELECT * FROM coupons";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Available Coupons</title>
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #fff;
            color: #111;
            margin: 0;
            padding: 20px;
        }
        h1 { text-align: center; }
        .coupon-container {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .coupon-card {
            border: 1px solid #000;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            background: #f9f9f9;
        }
        .coupon-card h2 {
            margin: 10px 0;
            font-size: 1.2rem;
        }
        .coupon-card p {
            margin: 5px 0;
        }
    </style>
</head>
<body>
    <h1>Available Coupons</h1>
    <div class="coupon-container">
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                // Show discount properly based on type
                $discountText = "";
                if ($row['discount_type'] == 'percent') {
                    $discountText = $row['discount_value'] . "% off";
                } elseif ($row['discount_type'] == 'flat') {
                    $discountText = "₹" . $row['discount_value'] . " off";
                }

                echo "<div class='coupon-card'>
                        <h2>" . htmlspecialchars($row['code']) . "</h2>
                        <p><b>Discount:</b> " . $discountText . "</p>
                        <p><b>Valid Until:</b> " . htmlspecialchars($row['expiry_date']) . "</p>
                      </div>";
            }
        } else {
            echo "<p>No coupons available.</p>";
        }
        ?>
    </div>
</body>
</html>
