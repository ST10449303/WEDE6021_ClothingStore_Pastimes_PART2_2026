<?php
session_start();
include("DBConn.php");

$message = "";

// 🔒 Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// 🛒 Check cart
if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    die("Cart is empty.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // ✅ Use logged-in user safely
    $user_id = intval($_SESSION['user_id']);

    // 🔐 Escape input (prevents SQL errors like apostrophes)
    $address = $conn->real_escape_string($_POST["address"]);
    $city = $conn->real_escape_string($_POST["city"]);
    $postal = $conn->real_escape_string($_POST["postal"]);
    $contact = $conn->real_escape_string($_POST["contact"]);

    // 💰 Calculate total
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += floatval($item['price']);
    }

    // 📦 Use first product (assignment-level simplification)
    $product_id = intval($_SESSION['cart'][0]['id']);

    // 🔍 DEBUG CHECK (prevents foreign key crash)
    $checkUser = $conn->query("SELECT user_id FROM users WHERE user_id = $user_id");
    if ($checkUser->num_rows == 0) {
        die("Error: User does not exist in database.");
    }

    $checkProduct = $conn->query("SELECT product_id FROM products WHERE product_id = $product_id");
    if ($checkProduct->num_rows == 0) {
        die("Error: Product does not exist.");
    }

    // 🧾 Insert order
    $order_sql = "INSERT INTO orders (user_id, product_id, total_price, order_status)
                  VALUES ($user_id, $product_id, $total, 'pending')";

    if (!$conn->query($order_sql)) {
        die("Order Error: " . $conn->error);
    }

    $order_id = $conn->insert_id;

    // 🚚 Insert delivery
    $delivery_sql = "INSERT INTO delivery (order_id, address, city, postal_code, contact_number)
                     VALUES ($order_id, '$address', '$city', '$postal', '$contact')";

    if (!$conn->query($delivery_sql)) {
        die("Delivery Error: " . $conn->error);
    }

    // 🧹 Clear cart
    unset($_SESSION['cart']);

    $message = "Order placed successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Checkout</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="header">
    <h1>Pastimes</h1>

    <div class="nav">
        <a href="shop.php">Shop</a>
        <a href="cart.php">Cart</a>
        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit">Logout</button>
        </form>
    </div>
</div>

<div class="checkout-container">

<h2>Checkout</h2>

<form method="POST">

    <input type="text" name="address" placeholder="Address" required>

    <input type="text" name="city" placeholder="City" required>

    <input type="text" name="postal" placeholder="Postal Code" required>

    <input type="text" name="contact" placeholder="Contact Number" required>

    <button type="submit">Confirm Purchase</button>

</form>

<p class="success-message">
    <?php echo htmlspecialchars($message); ?>
</p>

</div>

</body>
</html>