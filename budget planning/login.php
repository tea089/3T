<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

$config = require 'db_config.php';

$conn = new mysqli($config['servername'], $config['username'], $config['password'], $config['database']);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$login_email = $_POST['email'] ?? '';
$login_pass = $_POST['password'] ?? '';

// Debugging: write to file
file_put_contents('debug.log', "Entered email: $login_email\n", FILE_APPEND);

if (empty($login_email) || empty($login_pass)) {
    $_SESSION['login_error'] = "Email and password are required!";
    header("Location: login.html");
    exit();
}

$stmt = $conn->prepare("SELECT id, username, email, password FROM details WHERE email = ?");
$stmt->bind_param("s", $login_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows === 1) {
    $user = $result->fetch_assoc();
    file_put_contents('debug.log', "DB user: " . print_r($user, true) . "\n", FILE_APPEND);

    if (password_verify($login_pass, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();
        header("Location: homepage.php");
        exit();
    } elseif ($user['password'] === $login_pass) {
        // Temporary: upgrade plain-text password
        $hashed = password_hash($login_pass, PASSWORD_DEFAULT);
        $update = $conn->prepare("UPDATE details SET password = ? WHERE id = ?");
        $update->bind_param("si", $hashed, $user['id']);
        $update->execute();
        $update->close();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['logged_in'] = true;
        $_SESSION['last_activity'] = time();
        header("Location: homepage.php");
        exit();
    } else {
        $_SESSION['login_error'] = "Invalid email or password";
        header("Location: login.html");
        exit();
    }
} else {
    file_put_contents('debug.log', "No user found for $login_email\n", FILE_APPEND);
    $_SESSION['login_error'] = "Invalid email or password";
    header("Location: login.html");
    exit();
}
?>