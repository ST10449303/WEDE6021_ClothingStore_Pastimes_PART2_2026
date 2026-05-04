<?php
session_start();
include("DBConn.php");

// If already logged in as admin → skip login
if (isset($_SESSION['admin'])) {
    header("Location: adminDashboard.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $password = md5($_POST["password"]);

    $sql = "SELECT * FROM users 
            WHERE username='$username' 
            AND password='$password' 
            AND role='admin'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        // Store admin session
        $_SESSION['admin'] = $username;

        // Show required message
        $message = "Admin " . $username . " is logged in";

        // Redirect after 2 seconds
        header("refresh:2;url=adminDashboard.php");

    } else {
        $message = "Invalid admin login.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="header">
    <h1>Admin Panel</h1>
</div>

<div class="form-container">
    <h2>Admin Login</h2>

    <form method="POST">

        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

    <!-- MESSAGE -->
    <p class="message" style="font-weight:bold; color:green;">
        <?php echo $message; ?>
    </p>

    <!-- BACK TO USER LOGIN -->
    <p style="text-align:center;">
        <a href="login.php">Back to User Login</a>
    </p>

</div>

</body>
</html>