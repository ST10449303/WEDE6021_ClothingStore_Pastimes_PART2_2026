<?php
session_start();
include("DBConn.php");

$message = "";
$username = "";
$email = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $username = $_POST["username"];
    $email = $_POST["email"];
    $password = md5($_POST["password"]);

    $sql = "SELECT * FROM users 
            WHERE username='$username' 
            AND email='$email'";

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if ($row['password'] == $password) {

            if ($row['status'] == "verified") {

                // Store session
                $_SESSION['user'] = $row['name'];
                $_SESSION['user_id'] = $row['user_id'];

                // REQUIRED MESSAGE
                $message = "User " . $row['name'] . " is logged in";

                // Redirect after 2 seconds
                header("refresh:2;url=shop.php");

            } else {
                $message = "Account pending admin approval.";
            }

        } else {
            $message = "Incorrect password.";
        }

    } else {
        $message = "User not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="header">
    <h1>Pastimes</h1>
</div>

<div class="form-container">
    <h2>Login</h2>

    <form method="POST">

        <input type="text" name="username" placeholder="Username"
               value="<?php echo htmlspecialchars($username); ?>" required>

        <input type="email" name="email" placeholder="Email"
               value="<?php echo htmlspecialchars($email); ?>" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Login</button>
    </form>

    <!-- MESSAGE DISPLAY -->
    <p class="message" style="font-weight:bold; color:green;">
        <?php echo $message; ?>
    </p>

    <!-- REGISTER LINK -->
    <p style="text-align:center;">
        Don't have an account? <a href="register.php">Register</a>
    </p>

    <!-- 🔥 ADMIN BUTTON (NEW) -->
    <div style="text-align:center; margin-top:15px;">
        <a href="adminLogin.php">
            <button type="button">Admin Login</button>
        </a>
    </div>

</div>

</body>
</html>