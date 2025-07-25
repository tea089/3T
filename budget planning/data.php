<?php
session_start();
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$database = "account";

try {
    // Create connection
    $conn = new mysqli($servername, $username, $password, $database);
    
    // Check connection
    if ($conn->connect_error) {
        throw new Exception("Connection failed: " . $conn->connect_error);
    }
    
    // Get form data
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $pass = $_POST['password'] ?? '';
    
    // Validate input
    if (empty($name) || empty($email) || empty($pass)) {
        throw new Exception("All fields are required!");
    }
    
    // Check if username exists
    $check_stmt = $conn->prepare("SELECT id FROM details WHERE username = ?");
    $check_stmt->bind_param("s", $name);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        throw new Exception("Username already taken");
    }
    $check_stmt->close();
    
    // Check if email exists
    $check_stmt = $conn->prepare("SELECT id FROM details WHERE email = ?");
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_stmt->store_result();
    
    if ($check_stmt->num_rows > 0) {
        throw new Exception("Email already registered");
    }
    $check_stmt->close();
    
    // Hash the password
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);
    
    // Insert new user
    $stmt = $conn->prepare("INSERT INTO details (username, email, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $hashed_password);
    
    if ($stmt->execute()) {
        // Get the new user's ID
        $user_id = $stmt->insert_id;
        
        // Set session variables
        $_SESSION['user_id'] = $user_id;
        $_SESSION['username'] = $name;
        $_SESSION['email'] = $email;
        $_SESSION['logged_in'] = true;
        
        header("Location: homepage.php");
        exit();
    } else {
        throw new Exception("Registration failed: " . $stmt->error);
    }
} catch (Exception $e) {
    $_SESSION['register_error'] = $e->getMessage();
    header("Location: signUp.html");
    exit();
} finally {
    if (isset($stmt)) $stmt->close();
    if (isset($conn)) $conn->close();
}
?>