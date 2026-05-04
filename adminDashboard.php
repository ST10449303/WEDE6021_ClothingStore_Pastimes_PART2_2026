<?php
session_start();
include("DBConn.php");

// 🔒 Protect admin page
if (!isset($_SESSION['admin'])) {
    header("Location: adminLogin.php");
    exit();
}

// ✅ APPROVE USER
if (isset($_GET['approve'])) {
    $id = (int)$_GET['approve'];

    $conn->query("UPDATE users 
                  SET status='verified' 
                  WHERE user_id=$id");
}

// ❌ DELETE USER (FULL FIX)
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];

    $conn->begin_transaction();

    try {

        // 1️⃣ Delete messages
        $conn->query("DELETE FROM messages 
                      WHERE sender_id=$id OR receiver_id=$id");

        // 2️⃣ Delete delivery records (linked to orders)
        $conn->query("DELETE FROM delivery 
                      WHERE order_id IN (
                          SELECT order_id FROM orders WHERE user_id=$id
                      )");

        // 3️⃣ Delete orders where user is buyer
        $conn->query("DELETE FROM orders 
                      WHERE user_id=$id");

        // 4️⃣ Delete orders linked to user's products (IMPORTANT)
        $conn->query("DELETE FROM orders 
                      WHERE product_id IN (
                          SELECT product_id FROM products 
                          WHERE seller_id IN (
                              SELECT seller_id FROM sellers WHERE user_id=$id
                          )
                      )");

        // 5️⃣ Delete products of this seller
        $conn->query("DELETE FROM products 
                      WHERE seller_id IN (
                          SELECT seller_id FROM sellers WHERE user_id=$id
                      )");

        // 6️⃣ Delete seller record
        $conn->query("DELETE FROM sellers 
                      WHERE user_id=$id");

        // 7️⃣ Finally delete user
        $conn->query("DELETE FROM users 
                      WHERE user_id=$id");

        $conn->commit();

    } catch (Exception $e) {
        $conn->rollback();
        echo "<p style='color:red;'>Delete failed: " . $conn->error . "</p>";
    }
}

// 📊 FETCH USERS
$result = $conn->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="header">
    <h1>Admin Dashboard</h1>

    <div class="nav">
        <a href="shop.php">Shop</a>
        <a href="logout.php">Logout</a>
    </div>
</div>

<div class="admin-container">

<h2 style="text-align:center;">Manage Users</h2>

<table class="admin-table">

<tr>
    <th>Name</th>
    <th>Email</th>
    <th>Status</th>
    <th>Actions</th>
</tr>

<?php if ($result && $result->num_rows > 0) { 
    while($row = $result->fetch_assoc()) { ?>

<tr>
    <td><?php echo htmlspecialchars($row['name']); ?></td>
    <td><?php echo htmlspecialchars($row['email']); ?></td>
    <td><?php echo htmlspecialchars($row['status']); ?></td>

    <td>

        <?php if ($row['status'] == 'pending') { ?>
            <a class="admin-btn" href="?approve=<?php echo $row['user_id']; ?>">
                Approve
            </a>
        <?php } else { ?>
            <span>Verified</span>
        <?php } ?>

        <a class="admin-btn" 
           href="?delete=<?php echo $row['user_id']; ?>"
           onclick="return confirm('Delete this user and all related data?');">
            Delete
        </a>

    </td>
</tr>

<?php 
    } 
} else { ?>
<tr>
    <td colspan="4">No users found.</td>
</tr>
<?php } ?>

</table>

</div>

</body>
</html>