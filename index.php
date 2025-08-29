<?php 
session_start(); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate" />
    <title>Narayana Restaurant</title>
    <link rel="stylesheet" href="index_style.css"> <!-- ✅ Better for Hostinger -->
</head>
<body>
<header>
    <h1>Narayan's</h1>
    <span class="menu-toggle">☰</span>
    <nav id="navMenu">
        <a href="index.php">Home</a>
        <a href="menu.php">Menu</a>
        <a href="my_orders.php">My Orders</a>
        <a href="coupons.php">Coupons</a>
        <a href="cart.php">Cart</a>

        <?php if(isset($_SESSION['user'])): ?>
            <span>👋 Hello, <?php echo htmlspecialchars($_SESSION['name']); ?></span>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="signup.php">Signup</a>
        <?php endif; ?>

        <!-- ✅ Admin Section -->
        <?php if(isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true): ?>
            <a href="admin_orders.php">Admin Panel</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="admin_login.php">Admin Login</a>
        <?php endif; ?>
    </nav>
</header>

<!-- Slideshow -->
<section class="slideshow" id="slideshow">
    <div class="slide active" style="background-image: url('images/slide1.jpg');"></div>
    <div class="slide" style="background-image: url('images/slide2.jpg');"></div>
    <div class="slide" style="background-image: url('images/slide3.jpg');"></div>
    <div class="slide" style="background-image: url('images/slide4.jpg');"></div>
    <div class="slide" style="background-image: url('images/slide5.jpg');"></div>
    <div class="slideshow-text">
        <h2>Welcome to Narayan's <?php if(isset($_SESSION['name'])) echo htmlspecialchars($_SESSION['name']); ?></h2>
        <p>Delicious food, easy ordering, and exclusive offers.</p>
        <a href="menu.php" class="cta-btn">Order Now</a>
    </div>
</section>

<!-- Sections -->
<section class="sections">
    <div class="card" style="background-image: url('images/menu.jpg'); background-size: cover;">
        <h3>Our Menu</h3>
        <p>Explore a wide variety of dishes.</p>
        <a href="menu.php" class="cta-btn">See Menu</a>
    </div>
    <div class="card" style="background-image: url('images/offers.jpg'); background-size: cover;">
        <h3>Special Offers</h3>
        <p>Grab the latest deals.</p>
        <a href="offers.php" class="cta-btn">View Offers</a>
    </div>
    <div class="card" style="background-image: url('images/coupons.jpg'); background-size: cover;">
        <h3>Coupons</h3>
        <p>Apply coupons for instant discounts.</p>
        <a href="coupons.php" class="cta-btn">Redeem Coupons</a>
    </div>
    <div class="card" style="background-image: url('images/cart.jpg'); background-size: cover;">
        <h3>My Cart</h3>
        <p>Review your selections.</p>
        <a href="cart.php" class="cta-btn">Go to Cart</a>
    </div>
</section>

<!-- Book Table -->
<section class="book-table">
    <a href="book_table.php" class="book-btn">Book Your Table</a>
</section>

<footer>
    <p>© 2025 Narayana Restaurant. All rights reserved.</p>
</footer>

<script>
    // Toggle menu
    document.querySelector(".menu-toggle").addEventListener("click", function() {
        let nav = document.getElementById("navMenu");
        nav.style.display = (nav.style.display === "flex") ? "none" : "flex";
        nav.style.flexDirection = "column";
    });

    // Slideshow auto
    let slides = document.querySelectorAll(".slide");
    let index = 0;
    function showSlide(i) {
        slides.forEach(s => s.classList.remove("active"));
        slides[i].classList.add("active");
    }
    setInterval(() => {
        index = (index + 1) % slides.length;
        showSlide(index);
    }, 4000);

    // Swipe for mobile
    let startX = 0;
    const slideshow = document.getElementById("slideshow");
    slideshow.addEventListener("touchstart", e => startX = e.touches[0].clientX);
    slideshow.addEventListener("touchend", e => {
        let endX = e.changedTouches[0].clientX;
        if (startX - endX > 50) { index = (index + 1) % slides.length; showSlide(index); }
        if (endX - startX > 50) { index = (index - 1 + slides.length) % slides.length; showSlide(index); }
    });
</script>
</body>
</html>
