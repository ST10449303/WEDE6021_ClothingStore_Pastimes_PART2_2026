<?php
session_start();

// Create cart if not exists
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

// ADD ITEM TO CART
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["product_id"])) {

    $product = [
        "id" => $_POST["product_id"],
        "name" => $_POST["product_name"],
        "price" => $_POST["price"]
    ];

    $_SESSION['cart'][] = $product;
}

// REMOVE ITEM
if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']); // reindex
}

// CLEAR CART
if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
}

// Cart count
$cartCount = count($_SESSION['cart']);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cart</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="header">
    <h1>Pastimes</h1>

    <div class="nav">
        <a href="shop.php">Shop</a>
        <a href="cart.php">Cart (<?php echo $cartCount; ?>)</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="cart-container">

<h2>Your Cart</h2>

<?php
$total = 0;

if (!empty($_SESSION['cart'])) {

    foreach ($_SESSION['cart'] as $index => $item) {

        $total += $item['price'];
?>

    <div class="cart-item">

        <p>
            <?php echo htmlspecialchars($item['name']); ?> 
            - R<?php echo htmlspecialchars($item['price']); ?>
        </p>

        <!-- REMOVE BUTTON -->
        <a href="cart.php?remove=<?php echo $index; ?>">
            <button class="remove-btn">Remove</button>
        </a>

    </div>

<?php
    }
?>

    <div class="cart-total">
        <strong>Total: R<?php echo $total; ?></strong>
    </div>

    <!-- BUTTONS -->
    <div style="margin-top:20px;">

        <a href="checkout.php">
            <button class="checkout-btn">Proceed to Checkout</button>
        </a>

        <a href="cart.php?clear=true">
            <button class="clear-btn">Clear Cart</button>
        </a>

    </div>

<?php
} else {
    echo "<p>Your cart is empty.</p>";
}
?>

</div>

</body>
</html>