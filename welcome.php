<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$name = $_SESSION['name'] ?? "Guest";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome Dashboard</title>
    <style>
        body {
            margin: 0;
            font-family: 'Segoe UI', Tahoma, sans-serif;
            background: linear-gradient(135deg, #ffecd2, #fcb69f);
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            text-align: center;
            color: black;
            font-style: italic;
        }
        .welcome-box {
            background: white;
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.2);
            animation: fadeIn 1s ease-in-out;
        }
        h1 {
            margin-bottom: 15px;
        }
        p {
            font-size: 18px;
        }
        .redirect-msg {
            margin-top: 20px;
            font-size: 16px;
            color: #333;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>
    <!-- Auto Redirect after 1 seconds -->
    <meta http-equiv="refresh" content="1;url=index.php">
</head>
<body>
    <div class="welcome-box">
        <h1>👋 Welcome, <?php echo htmlspecialchars($name); ?>!</h1>
        <p>We’re happy to have you at <b>Narayana Restaurant</b>.</p>
        <p class="redirect-msg">You’ll be redirected to the homepage in a seconds...</p>
        <p>If not, <a href="index.php">click here</a>.</p>
    </div>
</body>
</html>
