<?php
session_start();

// DB connection
$conn = new mysqli("127.0.0.1", "root", "", "restaurant_auth");
if ($conn->connect_error) { die("DB connection failed: " . $conn->connect_error); }

$error = ""; 
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name   = trim($_POST['name']);
    $email  = trim($_POST['email']);
    $phone  = trim($_POST['phone']);
    $date   = $_POST['date'];
    $time   = $_POST['time'];
    $guests = intval($_POST['guests']);
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    if (empty($name) || empty($email) || empty($phone) || empty($date) || empty($time) || $guests <= 0) {
        $error = "All fields are required!";
    } else {
        $sql = "INSERT INTO bookings (name, email, phone, date, time, guests, user_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssii", $name, $email, $phone, $date, $time, $guests, $user_id);

        if ($stmt->execute()) {
            $success = "✅ Table booked successfully for $guests guest(s) on $date at $time!";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Book a Table - Narayana Restaurant</title>
    <style>
        body { font-family: Arial, sans-serif; background: linear-gradient(135deg, #ffecd2, #fcb69f); }
        .container { width: 400px; margin: 60px auto; background: #fff; padding: 25px; border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.2); }
        h2 { text-align: center; font-style: italic; color: black; }
        input, button { width: 100%; padding: 10px; margin: 8px 0; border-radius: 6px; border: 1px solid #ccc; font-style: italic; }
        button { background: #ff5722; color: #fff; border: none; font-weight: bold; cursor: pointer; }
        button:hover { background: #e64a19; }
        .msg { text-align: center; font-weight: bold; font-style: italic; }
        .error { color: red; }
        .success { color: green; }
    </style>
</head>
<body>
    <div class="container">
        <h2>🍽️ Book a Table</h2>
        <?php if ($error) echo "<p class='msg error'>$error</p>"; ?>
        <?php if ($success) echo "<p class='msg success'>$success</p>"; ?>

        <form method="POST" action="">
            <input type="text" name="name" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email Address" required>
            <input type="text" name="phone" placeholder="Phone Number" required>
            <input type="date" name="date" required>
            <input type="time" name="time" required>
            <input type="number" name="guests" min="1" placeholder="Number of Guests" required>
            <button type="submit">Book Now</button>
        </form>
    </div>
</body>
</html>
