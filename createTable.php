<?php
include("DBConn.php");

// Drop table if exists
$conn->query("DROP TABLE IF EXISTS users");

// Create table (UPDATED with role + default values)
$sql = "CREATE TABLE users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    username VARCHAR(50),
    password VARCHAR(255),
    status ENUM('pending','verified') DEFAULT 'pending',
    role ENUM('buyer','admin') DEFAULT 'buyer'
)";
$conn->query($sql);

// Open text file
$file = fopen("data/userData.txt", "r");

if (!$file) {
    die("Error opening file.");
}

// Insert data from file
while (($line = fgets($file)) !== false) {

    $data = explode(",", trim($line));

    // SAFETY CHECK (important for errors)
    if (count($data) < 6) {
        continue;
    }

    // Escape values (prevents SQL errors)
    $name = $conn->real_escape_string($data[0]);
    $email = $conn->real_escape_string($data[1]);
    $username = $conn->real_escape_string($data[2]);
    $password = md5($data[3]); // hash password
    $status = $conn->real_escape_string($data[4]);
    $role = $conn->real_escape_string($data[5]);

    $sql = "INSERT INTO users (name, email, username, password, status, role)
            VALUES ('$name','$email','$username','$password','$status','$role')";

    $conn->query($sql);
}

fclose($file);

echo "Users table created and data loaded successfully!";
?>