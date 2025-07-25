<?php
include 'includes/config.php';

if (!isset($_GET['id'])) {
    header("Location: ../homepage.php");
    exit();
}

$budget_id = $_GET['id'];

// Verify user owns this budget
$stmt = $pdo->prepare("SELECT user_id FROM budgets WHERE id = ?");
$stmt->execute([$budget_id]);
$budget = $stmt->fetch();

if (!$budget || $budget['user_id'] != $_SESSION['user_id']) {
    header("Location: ../homepage.php");
    exit();
}

try {
    $pdo->beginTransaction();
    
    // Delete related records first (due to foreign key constraints)
    $pdo->prepare("DELETE FROM spending_records WHERE budget_id = ?")->execute([$budget_id]);
    $pdo->prepare("DELETE FROM budget_items WHERE budget_id = ?")->execute([$budget_id]);
    
    // Delete the budget
    $pdo->prepare("DELETE FROM budgets WHERE id = ?")->execute([$budget_id]);
    
    $pdo->commit();
    
    $_SESSION['success'] = "Budget deleted successfully";
} catch (Exception $e) {
    $pdo->rollBack();
    $_SESSION['error'] = "Error deleting budget: " . $e->getMessage();
}

header("Location: ../homepage.php");
exit();
?>