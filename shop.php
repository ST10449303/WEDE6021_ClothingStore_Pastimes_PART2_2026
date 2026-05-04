<?php
session_start();
include("DBConn.php");

// Protect page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fetch products
$result = $conn->query("SELECT * FROM products");

// Cart count
$cartCount = isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Shop</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="header">
    <h1>Pastimes</h1>

    <p style="font-weight:bold;">
        User <?php echo htmlspecialchars($_SESSION['user']); ?> is logged in
    </p>

    <div class="nav">
        <a href="shop.php">Shop</a>
        <a href="cart.php">Cart (<?php echo $cartCount; ?>)</a>

        <form action="logout.php" method="POST" style="display:inline;">
            <button type="submit">Logout</button>
        </form>
    </div>
</div>

<h2 style="text-align:center;">Available Products</h2>

<div class="container">

<?php
if ($result && $result->num_rows > 0) {

    while($row = $result->fetch_assoc()) {

        // Skip sold items
        if ($row['status'] == 'sold') continue;

        // Image fallback
        $image = !empty($row['image']) ? $row['image'] : "default.jpg";
?>

    <div class="product">

        <!-- PRODUCT IMAGE -->
        <img src="images/<?php echo htmlspecialchars($image); ?>" 
             alt="Product Image" 
             style="width:200px; height:200px; object-fit:cover; border-radius:8px;">

        <h3><?php echo htmlspecialchars($row['product_name']); ?></h3>
        
        <p><?php echo htmlspecialchars($row['description']); ?></p>
        
        <p><strong>Category:</strong> <?php echo htmlspecialchars($row['category']); ?></p>
        
        <p><strong>Size:</strong> <?php echo htmlspecialchars($row['size']); ?></p>
        
        <p><strong>Brand:</strong> <?php echo htmlspecialchars($row['brand']); ?></p>
        
        <p><strong>Condition:</strong> <?php echo htmlspecialchars($row['condition_item']); ?></p>
        
        <p><strong>Price:</strong> R<?php echo htmlspecialchars($row['price']); ?></p>

        <!-- ADD TO CART -->
        <form method="POST" action="cart.php">
            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $row['product_name']; ?>">
            <input type="hidden" name="price" value="<?php echo $row['price']; ?>">
            
            <button type="submit">Add to Cart</button>
        </form>

    </div>

<?php
    }

} else {
    echo "<p style='text-align:center;'>No products available.</p>";
}
?>

</div>

</body>
</html>