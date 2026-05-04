<?php
include("DBConn.php");

// Turn off foreign key checks
$conn->query("SET FOREIGN_KEY_CHECKS = 0");

// Drop tables (child → parent)
$conn->query("DROP TABLE IF EXISTS messages");
$conn->query("DROP TABLE IF EXISTS delivery");
$conn->query("DROP TABLE IF EXISTS orders");
$conn->query("DROP TABLE IF EXISTS products");
$conn->query("DROP TABLE IF EXISTS sellers");
$conn->query("DROP TABLE IF EXISTS users");

// Turn foreign keys back on
$conn->query("SET FOREIGN_KEY_CHECKS = 1");


// ================= USERS =================
$conn->query("CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    username VARCHAR(50),
    password VARCHAR(255),
    status ENUM('pending','verified'),
    role ENUM('buyer','admin')
)");


// ================= SELLERS =================
$conn->query("CREATE TABLE IF NOT EXISTS sellers (
    seller_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    contact_number VARCHAR(20),
    address TEXT,
    verification_status ENUM('pending','approved'),
    FOREIGN KEY (user_id) REFERENCES users(user_id)
)");


// ================= PRODUCTS =================
$conn->query("CREATE TABLE IF NOT EXISTS products (
    product_id INT AUTO_INCREMENT PRIMARY KEY,
    product_name VARCHAR(100),
    description TEXT,
    category VARCHAR(50),
    size VARCHAR(10),
    brand VARCHAR(50),
    condition_item VARCHAR(50),
    price DECIMAL(10,2),
    seller_id INT,
    status ENUM('available','sold'),
    image VARCHAR(255),
    FOREIGN KEY (seller_id) REFERENCES sellers(seller_id)
)");


// ================= ORDERS =================
$conn->query("CREATE TABLE IF NOT EXISTS orders (
    order_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    product_id INT,
    total_price DECIMAL(10,2),
    order_status ENUM('pending','shipped','delivered'),
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (product_id) REFERENCES products(product_id)
)");


// ================= DELIVERY =================
$conn->query("CREATE TABLE IF NOT EXISTS delivery (
    delivery_id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT,
    address TEXT,
    city VARCHAR(50),
    postal_code VARCHAR(10),
    contact_number VARCHAR(20),
    FOREIGN KEY (order_id) REFERENCES orders(order_id)
)");


// ================= MESSAGES =================
$conn->query("CREATE TABLE IF NOT EXISTS messages (
    message_id INT AUTO_INCREMENT PRIMARY KEY,
    sender_id INT,
    receiver_id INT,
    message_text TEXT,
    timestamp DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sender_id) REFERENCES users(user_id),
    FOREIGN KEY (receiver_id) REFERENCES users(user_id)
)");

echo "Database tables created successfully!";
?>