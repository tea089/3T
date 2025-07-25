<?php
$host = 'localhost';
$dbname = 'budget_app';
$username = 'root';
$password = '';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Create tables if they don't exist
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS budgets (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT,
            name VARCHAR(255) NOT NULL,
            type ENUM('large', 'small', 'custom') NOT NULL,
            total_amount DECIMAL(10,2) NOT NULL,
            remaining_amount DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        );
        
        CREATE TABLE IF NOT EXISTS budget_items (
            id INT AUTO_INCREMENT PRIMARY KEY,
            budget_id INT NOT NULL,
            name VARCHAR(255) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            necessity ENUM('high', 'medium', 'low') NOT NULL,
            start_date DATE,
            end_date DATE,
            duration_days INT,
            aim VARCHAR(255),
            time_range VARCHAR(255),
            savings_percentage DECIMAL(5,2),
            FOREIGN KEY (budget_id) REFERENCES budgets(id) ON DELETE CASCADE
        );
        
        CREATE TABLE IF NOT EXISTS spending_records (
            id INT AUTO_INCREMENT PRIMARY KEY,
            budget_id INT NOT NULL,
            item_id INT,
            name VARCHAR(255) NOT NULL,
            amount DECIMAL(10,2) NOT NULL,
            necessity ENUM('high', 'medium', 'low') NOT NULL,
            recorded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            notes TEXT,
            FOREIGN KEY (budget_id) REFERENCES budgets(id) ON DELETE CASCADE,
            FOREIGN KEY (item_id) REFERENCES budget_items(id) ON DELETE SET NULL
        );
    ");
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Simple user authentication simulation
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1; // Simulate logged-in user
}
?>