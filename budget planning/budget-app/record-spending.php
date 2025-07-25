<?php 
include 'includes/config.php';

if (!isset($_GET['budget_id'])) {
    header("Location: budget planning/homepage.php");
    exit();
}

$budget_id = $_GET['budget_id'];
$stmt = $pdo->prepare("SELECT * FROM budgets WHERE id = ? AND user_id = ?");
$stmt->execute([$budget_id, $_SESSION['user_id']]);
$budget = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$budget) {
    header("Location: budget planning/homepage.php");
    exit();
}

$items_stmt = $pdo->prepare("SELECT * FROM budget_items WHERE budget_id = ?");
$items_stmt->execute([$budget_id]);
$items = $items_stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the spending record
    $name = $_POST['name'];
    $amount = $_POST['amount'];
    $necessity = $_POST['necessity'];
    $notes = $_POST['notes'] ?? '';
    $item_id = $_POST['item_id'] ?? null;
    
    // Update the budget's remaining amount
    $new_remaining = $budget['remaining_amount'] - $amount;
    
    $pdo->beginTransaction();
    
    try {
        // Insert the spending record
        $stmt = $pdo->prepare("INSERT INTO spending_records (budget_id, item_id, name, amount, necessity, notes) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$budget_id, $item_id, $name, $amount, $necessity, $notes]);
        
        // Update the budget's remaining amount
        $stmt = $pdo->prepare("UPDATE budgets SET remaining_amount = ? WHERE id = ?");
        $stmt->execute([$new_remaining, $budget_id]);
        
        $pdo->commit();
        header("Location: view-budget.php?id=$budget_id");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Failed to record spending: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Spending for <?= htmlspecialchars($budget['name']) ?></title>
    <link rel="stylesheet" href="homepageStyles.css">
</head>
<body>

    <!-- Sidebar Navigation -->
    <div class="sidebar-nav" id="sidebarNav">
        <div class="sidebar-container">
            <div class="sidebar-title">BUDGETIFY</div>
            
            <!-- Top Buttons -->
            <div class="sidebar-buttons">
                <a href="../homepage.php" class="icon-button home-button" title="Home"></a>
                <button class="pixel-button back-to-top" title="Go to Top">↑ TOP</button>
                <button class="pixel-button down-to-bottom" title="Go to Bottom">↓ BOTTOM</button>                
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>

    <div class="container">        
        <main>
            <section class="record-spending">
                <?php if (isset($error)): ?>
                <div class="alert error"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label for="spending-name">What did you spend on?</label>
                        <input type="text" id="spending-name" name="name" required>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="spending-amount">Amount (RM)</label>
                            <input type="number" id="spending-amount" name="amount" min="0.01" step="0.01" max="<?= $budget['remaining_amount'] ?>" required>
                            <small>Max: RM <?= number_format($budget['remaining_amount'], 2) ?></small>
                        </div>
                        
                        <div class="form-group">
                            <label for="spending-necessity">Necessity</label>
                            <select id="spending-necessity" name="necessity" required>
                                <option value="high">High</option>
                                <option value="medium" selected>Medium</option>
                                <option value="low">Low</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="spending-item">Related Budget Item (optional)</label>
                        <select id="spending-item" name="item_id">
                            <option value="">-- Select an item --</option>
                            <?php foreach ($items as $item): ?>
                            <option value="<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?> (RM <?= number_format($item['amount'], 2) ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="spending-notes">Notes (optional)</label>
                        <textarea id="spending-notes" name="notes" rows="3"></textarea>
                    </div>
                    
                    <button type="submit" class="btn primary">Record Spending</button>
                    <a href="view-budget.php?id=<?= $budget_id ?>" class="btn">Cancel</a>
                </form>
            </section>
        </main>
    </div>

    <div class="custom-scrollbar" id="customScrollbar">
        <div class="scrollbar-track">
            <div class="scrollbar-thumb"></div>
        </div>
    </div>
    
    <script src="assets/js/script.js"></script>
</body>
</html>