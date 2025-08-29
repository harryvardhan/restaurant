<?php
// Enable full error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Make sure db.php exists and is correct
include __DIR__ . "/db.php";

// Dummy menu items (later we can fetch from DB)
$menu_items = [
    1 => ["name" => "Margherita Pizza", "price" => 200],
    2 => ["name" => "Paneer Butter Masala", "price" => 250],
    3 => ["name" => "Veg Biryani", "price" => 180],
    4 => ["name" => "Cold Coffee", "price" => 120],
];

// Handle Add to Cart
if (isset($_POST['add_to_cart'])) {
    $item_id = $_POST['item_id'];

    // Validate that the item exists
    if (!isset($menu_items[$item_id])) {
        die("Error: Invalid menu item selected!");
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    // If item already in cart, increase quantity
    if (isset($_SESSION['cart'][$item_id])) {
        $_SESSION['cart'][$item_id]['quantity'] += 1;
    } else {
        $_SESSION['cart'][$item_id] = [
            "name" => $menu_items[$item_id]["name"],
            "price" => $menu_items[$item_id]["price"],
            "quantity" => 1
        ];
    }

    header("Location: cart.php"); // redirect to cart after adding
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Menu - Narayana Restaurant</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <h1>Our Menu</h1>
  <a href="cart.php">Go to Cart</a> | <a href="index.php">Home</a>
  <hr>

  <?php foreach ($menu_items as $id => $item): ?>
    <div style="margin: 10px; padding: 10px; border: 1px solid black;">
      <h3><?php echo htmlspecialchars($item["name"]); ?></h3>
      <p>Price: ₹<?php echo htmlspecialchars($item["price"]); ?></p>
      <form method="post">
        <input type="hidden" name="item_id" value="<?php echo $id; ?>">
        <button type="submit" name="add_to_cart">Add to Cart</button>
      </form>
    </div>
  <?php endforeach; ?>

</body>
</html>
