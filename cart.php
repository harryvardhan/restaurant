<?php
session_start();
include __DIR__ . "/db.php";

// Handle coupon application
$discount = 0;
$coupon_msg = "";
if (isset($_POST['apply_coupon'])) {
    $code = trim($_POST['coupon_code']);

    // Dummy coupons (later: fetch from DB)
    $coupons = [
        "WELCOME50" => 50,
        "FOODIE20" => 20
    ];

    if (array_key_exists($code, $coupons)) {
        $discount = $coupons[$code];
        $_SESSION['applied_coupon'] = $code;
        $coupon_msg = "Coupon applied successfully! ₹$discount off.";
    } else {
        $coupon_msg = "Invalid coupon code!";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Cart</title>
  <link rel="stylesheet" href="css/style.css">
  <style>
      body { font-family: Arial, sans-serif; padding:20px; }
      table { border-collapse: collapse; width: 80%; margin-bottom: 20px; }
      th, td { border: 1px solid #000; padding: 10px; text-align: center; }
      h1 { margin-bottom: 10px; }
      .btn {
          display: inline-block;
          padding: 10px 20px;
          margin-top: 15px;
          background: green;
          color: white;
          text-decoration: none;
          border-radius: 5px;
      }
      .btn:hover { background: darkgreen; }
  </style>
</head>
<body>
  <h1>Your Order</h1>
  <a href="menu.php">Back to Menu</a> | <a href="index.php">Home</a>
  <hr>

  <?php if (!empty($_SESSION['cart'])): ?>
    <table>
      <tr>
        <th>Item</th>
        <th>Price</th>
        <th>Quantity</th>
        <th>Total</th>
      </tr>
      <?php
      $grand_total = 0;
      foreach ($_SESSION['cart'] as $item):
          $total = $item['price'] * $item['quantity'];
          $grand_total += $total;
      ?>
        <tr>
          <td><?php echo $item['name']; ?></td>
          <td>₹<?php echo $item['price']; ?></td>
          <td><?php echo $item['quantity']; ?></td>
          <td>₹<?php echo $total; ?></td>
        </tr>
      <?php endforeach; ?>
    </table>

    <h3>Subtotal: ₹<?php echo $grand_total; ?></h3>
    <?php if ($discount > 0): ?>
        <h3>Discount: -₹<?php echo $discount; ?></h3>
        <h2>Final Total: ₹<?php echo ($grand_total - $discount); ?></h2>
    <?php endif; ?>

    

    <!-- Checkout Button -->
    <a href="checkout.php" class="btn">Proceed to Checkout</a>

  <?php else: ?>
    <p>Your cart is empty. <a href="menu.php">Go to Menu</a></p>
  <?php endif; ?>

</body>
</html>
