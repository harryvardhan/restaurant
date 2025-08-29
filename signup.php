<?php
session_start();
include __DIR__ . "/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name     = trim($_POST['name']);
    $phone    = trim($_POST['phone']);
    $email    = trim($_POST['email']);
    $address  = trim($_POST['address_line1']);
    $city     = trim($_POST['city']);
    $state    = trim($_POST['state']);
    $pincode  = trim($_POST['pincode']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $check = $conn->prepare("SELECT id FROM users WHERE email=? OR phone=?");
    $check->bind_param("ss", $email, $phone);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "⚠️ Email or Phone already registered. Please login.";
    } else {
        $stmt = $conn->prepare("INSERT INTO users (name, phone, email, address_line1, city, state, pincode, password, verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->bind_param("ssssssss", $name, $phone, $email, $address, $city, $state, $pincode, $password);

        if ($stmt->execute()) {
            $success = "🎉 Signup successful! <a href='login.php'>Login here</a>";
        } else {
            $error = "❌ Error: " . $stmt->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Signup - Narayana Restaurant</title>
  <style>
/* ===== Global Reset ===== */
/* ===== Global Reset ===== */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

/* ===== Body with Gradient ===== */
body {
  font-family: 'Segoe UI', Tahoma, sans-serif;
  background: linear-gradient(135deg, #585446ff, #0a0e47ff);
  color: #fff;
  min-height: 100vh;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
  overflow-y: auto; /* scroll for small screens */
}

/* ===== Container (Card) ===== */
.signup-container {
  width: 100%;
  max-width: 480px; /* ✅ wider for desktop */
  background: rgba(255, 255, 255, 0.1);
  padding: 30px;
  border-radius: 14px;
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(8px);
  text-align: center;
  transition: all 0.3s ease;
}

/* ===== Heading ===== */
.signup-container h2 {
  margin-bottom: 20px;
  font-size: 28px;
  font-style: italic;
  color: #ffd369;
}

/* ===== Input Fields ===== */
input, select {
  width: 100%;
  padding: 12px 14px;
  margin: 10px 0;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  outline: none;
}

/* ===== Dropdown Styling ===== */
select {
  background: #fff;
  color: #333;
  font-weight: 500;
  cursor: pointer;
}

select:focus {
  outline: none;
  border: 2px solid #ffd369;
}

/* ===== Buttons ===== */
button {
  width: 100%;
  padding: 12px;
  margin-top: 12px;
  border: none;
  border-radius: 8px;
  background: linear-gradient(135deg, #ec8611ff, #faa638ff);
  color: black;
  font-weight: bold;
  font-size: 17px;
  cursor: pointer;
  transition: transform 0.2s ease, background 0.3s ease;
}

button:hover {
  transform: scale(1.05);
  background: linear-gradient(135deg, #d67a0f, #f7941d);
}

/* ===== Links ===== */
a {
  color: #ffd369;
  text-decoration: none;
  font-style: italic;
}

a:hover {
  text-decoration: underline;
}

/* ===== Messages ===== */
.msg {
  font-weight: bold;
  margin: 12px 0;
}

.error {
  color: #ff4d4d;
}

.success {
  color: #4caf50;
}

/* ===== Responsive ===== */
@media (max-width: 600px) {
  body {
    align-items: flex-start; /* ✅ top align on small screens */
  }
  .signup-container {
    margin-top: 40px;
    padding: 20px;
    max-width: 100%;
    border-radius: 10px;
  }
  .signup-container h2 {
    font-size: 22px;
  }
  input, button, select {
    font-size: 14px;
    padding: 10px;
  }
}

  </style>
</head>
<body>
  <div class="form-container">
      <h2>Create Account</h2>
      <?php if ($error): ?><p class="msg error"><?= $error ?></p><?php endif; ?>
      <?php if ($success): ?><p class="msg success"><?= $success ?></p><?php endif; ?>

      <form method="POST" action="">
        <input type="text" name="name" placeholder="Full Name" required>
        <input type="text" name="phone" placeholder="Phone Number" required>
        <input type="email" name="email" placeholder="Email Address" required>
        <input type="text" name="address_line1" placeholder="Address" required>
        <input type="text" name="city" placeholder="City" required>

       <select name="state" required>
  <option value="">-- Select State / UT --</option>

  <!-- ===== States ===== -->
  <option value="Andhra Pradesh">Andhra Pradesh</option>
  <option value="Arunachal Pradesh">Arunachal Pradesh</option>
  <option value="Assam">Assam</option>
  <option value="Bihar">Bihar</option>
  <option value="Chhattisgarh">Chhattisgarh</option>
  <option value="Goa">Goa</option>
  <option value="Gujarat">Gujarat</option>
  <option value="Haryana">Haryana</option>
  <option value="Himachal Pradesh">Himachal Pradesh</option>
  <option value="Jharkhand">Jharkhand</option>
  <option value="Karnataka">Karnataka</option>
  <option value="Kerala">Kerala</option>
  <option value="Madhya Pradesh">Madhya Pradesh</option>
  <option value="Maharashtra">Maharashtra</option>
  <option value="Manipur">Manipur</option>
  <option value="Meghalaya">Meghalaya</option>
  <option value="Mizoram">Mizoram</option>
  <option value="Nagaland">Nagaland</option>
  <option value="Odisha">Odisha</option>
  <option value="Punjab">Punjab</option>
  <option value="Rajasthan">Rajasthan</option>
  <option value="Sikkim">Sikkim</option>
  <option value="Tamil Nadu">Tamil Nadu</option>
  <option value="Telangana">Telangana</option>
  <option value="Tripura">Tripura</option>
  <option value="Uttar Pradesh">Uttar Pradesh</option>
  <option value="Uttarakhand">Uttarakhand</option>
  <option value="West Bengal">West Bengal</option>

  <!-- ===== Union Territories ===== -->
  <option value="Andaman and Nicobar Islands">Andaman and Nicobar Islands</option>
  <option value="Chandigarh">Chandigarh</option>
  <option value="Dadra and Nagar Haveli and Daman and Diu">Dadra and Nagar Haveli and Daman and Diu</option>
  <option value="Delhi (NCT)">Delhi (NCT)</option>
  <option value="Jammu and Kashmir">Jammu and Kashmir</option>
  <option value="Ladakh">Ladakh</option>
  <option value="Lakshadweep">Lakshadweep</option>
  <option value="Puducherry">Puducherry</option>
</select>


        <input type="text" name="pincode" placeholder="Pincode" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Signup</button>
      </form>

      <p>Already have an account? <a href="login.php">Login</a></p>
  </div>
</body>
</html>
