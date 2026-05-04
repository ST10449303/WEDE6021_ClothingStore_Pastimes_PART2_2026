<?php
include("DBConn.php");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST["name"];
    $email = $_POST["email"];
    $username = $_POST["username"];
    $password = md5($_POST["password"]);

    $check = $conn->query("SELECT * FROM users WHERE username='$username' OR email='$email'");

    if ($check->num_rows > 0) {
        $message = "User already exists!";
    } else {

        $sql = "INSERT INTO users (name,email,username,password,status)
                VALUES ('$name','$email','$username','$password','pending')";

        if ($conn->query($sql)) {
            $message = "Registered! Await admin approval.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

<div class="header">
    <h1>Pastimes</h1>
</div>

<div class="form-container">
    <h2>Register</h2>

    <form method="POST">

        <input type="text" name="name" placeholder="Full Name" required>

        <input type="email" name="email" placeholder="Email" required>

        <input type="text" name="username" placeholder="Username" required>

        <input type="password" name="password" placeholder="Password" required>

        <button type="submit">Register</button>
    </form>

    <p class="message"><?php echo $message; ?></p>

    <p style="text-align:center;">
        Already have an account? <a href="login.php">Login</a>
    </p>
</div>

</body>
</html>