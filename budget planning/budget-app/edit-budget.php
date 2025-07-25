<?php 
include 'includes/config.php';

if (!isset($_GET['id'])) {
    header("Location: budget planning/homepage.php");
    exit();
}

$budget_id = $_GET['id'];
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
    try {
        $pdo->beginTransaction();
        
        // Update the main budget
        $stmt = $pdo->prepare("UPDATE budgets SET name = ?, total_amount = ?, remaining_amount = ? WHERE id = ?");
        $stmt->execute([
            $_POST['name'],
            $_POST['total_amount'],
            $_POST['total_amount'], // You might want more sophisticated remaining amount calculation
            $budget_id
        ]);
        
        // Delete existing items (simple approach - you might want to update instead)
        $pdo->prepare("DELETE FROM budget_items WHERE budget_id = ?")->execute([$budget_id]);
        
        // Insert new items (same as create-budget.php)
        if (!empty($_POST['items'])) {
            $item_stmt = $pdo->prepare("INSERT INTO budget_items (...) VALUES (...)");
            foreach ($_POST['items'] as $item) {
                // ... (same as create-budget.php)
            }
        }
        
        $pdo->commit();
        header("Location: view-budget.php?id=$budget_id");
        exit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Error updating budget: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Budget: <?= htmlspecialchars($budget['name']) ?></title>
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
            <form id="budget-form" method="POST">
                <input type="hidden" name="type" value="<?= $budget['type'] ?>">
                
                <div class="form-group">
                    <label for="budget-name">Budget Name</label>
                    <input type="text" id="budget-name" name="name" value="<?= htmlspecialchars($budget['name']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="total-amount">Total Amount (RM)</label>
                    <input type="number" id="total-amount" name="total_amount" min="0" step="0.01" value="<?= $budget['total_amount'] ?>" required>
                </div>
                
                <div id="budget-items-container">
                    <h3>Budget Items</h3>
                    <?php foreach ($items as $index => $item): ?>
                    <div class="budget-item" data-item-id="<?= $item['id'] ?>">
                        <input type="hidden" name="items[<?= $item['id'] ?>][id]" value="<?= $item['id'] ?>">
                        
                        <div class="form-group">
                            <label for="item-name-<?= $item['id'] ?>">Item Name</label>
                            <input type="text" id="item-name-<?= $item['id'] ?>" name="items[<?= $item['id'] ?>][name]" value="<?= htmlspecialchars($item['name']) ?>" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="item-amount-<?= $item['id'] ?>">Amount (RM)</label>
                                <input type="number" id="item-amount-<?= $item['id'] ?>" name="items[<?= $item['id'] ?>][amount]" min="0" step="0.01" value="<?= $item['amount'] ?>" required>
                            </div>
                            
                            <div class="form-group">
                                <label for="item-necessity-<?= $item['id'] ?>">Necessity</label>
                                <select id="item-necessity-<?= $item['id'] ?>" name="items[<?= $item['id'] ?>][necessity]" required>
                                    <option value="high" <?= $item['necessity'] === 'high' ? 'selected' : '' ?>>High</option>
                                    <option value="medium" <?= $item['necessity'] === 'medium' ? 'selected' : '' ?>>Medium</option>
                                    <option value="low" <?= $item['necessity'] === 'low' ? 'selected' : '' ?>>Low</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="extra-options">
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" class="toggle-extra" data-extra="date" <?= $item['start_date'] ? 'checked' : '' ?>> Include Date Range
                                </label>
                                <div class="extra-content date <?= $item['start_date'] ? '' : 'hidden' ?>">
                                    <div class="form-row">
                                        <div class="form-group">
                                            <label for="item-start-date-<?= $item['id'] ?>">Start Date</label>
                                            <input type="date" id="item-start-date-<?= $item['id'] ?>" name="items[<?= $item['id'] ?>][start_date]" value="<?= $item['start_date'] ?>">
                                        </div>
                                        <div class="form-group">
                                            <label for="item-end-date-<?= $item['id'] ?>">End Date</label>
                                            <input type="date" id="item-end-date-<?= $item['id'] ?>" name="items[<?= $item['id'] ?>][end_date]" value="<?= $item['end_date'] ?>">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label for="item-duration-<?= $item['id'] ?>">Duration (days)</label>
                                        <input type="number" id="item-duration-<?= $item['id'] ?>" name="items[<?= $item['id'] ?>][duration_days]" min="1" value="<?= $item['duration_days'] ?>">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" class="toggle-extra" data-extra="aim" <?= $item['aim'] ? 'checked' : '' ?>> Include Aim
                                </label>
                                <div class="extra-content aim <?= $item['aim'] ? '' : 'hidden' ?>">
                                    <input type="text" id="item-aim-<?= $item['id'] ?>" name="items[<?= $item['id'] ?>][aim]" value="<?= htmlspecialchars($item['aim']) ?>" placeholder="e.g., < RM20">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" class="toggle-extra" data-extra="time" <?= $item['time_range'] ? 'checked' : '' ?>> Include Time
                                </label>
                                <div class="extra-content time <?= $item['time_range'] ? '' : 'hidden' ?>">
                                    <input type="text" id="item-time-<?= $item['id'] ?>" name="items[<?= $item['id'] ?>][time_range]" value="<?= htmlspecialchars($item['time_range']) ?>" placeholder="e.g., 11am-1pm, 5pm-7pm">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label>
                                    <input type="checkbox" class="toggle-extra" data-extra="savings" <?= $item['savings_percentage'] ? 'checked' : '' ?>> Include Savings
                                </label>
                                <div class="extra-content savings <?= $item['savings_percentage'] ? '' : 'hidden' ?>">
                                    <input type="number" id="item-savings-<?= $item['id'] ?>" name="items[<?= $item['id'] ?>][savings_percentage]" min="0" max="100" step="0.01" value="<?= $item['savings_percentage'] ?>" placeholder="Percentage">
                                </div>
                            </div>
                        </div>
                        
                        <button type="button" class="btn remove-item">Remove Item</button>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <button type="button" id="add-item" class="btn">Add Another Item</button>
                <button type="submit" class="btn primary">Update Budget</button>
            </form>
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