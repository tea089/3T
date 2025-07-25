<?php
// This should be at the VERY TOP of the file
include 'includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo->beginTransaction();
        
        // Insert the main budget
        $stmt = $pdo->prepare("INSERT INTO budgets (user_id, name, type, total_amount, remaining_amount) 
                              VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $_SESSION['user_id'],
            $_POST['name'],
            $_POST['type'],
            $_POST['total_amount'],
            $_POST['total_amount'] // Initially remaining = total
        ]);
        
        $budget_id = $pdo->lastInsertId();
        
        // Insert budget items
        if (!empty($_POST['items'])) {
            $item_stmt = $pdo->prepare("INSERT INTO budget_items 
                (budget_id, name, amount, necessity, start_date, end_date, duration_days, aim, time_range, savings_percentage)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($_POST['items'] as $item_id => $item_data) {
                // Skip empty items
                if (empty($item_data['name'])) continue;
                
                $item_stmt->execute([
                    $budget_id,
                    $item_data['name'],
                    $item_data['amount'],
                    $item_data['necessity'],
                    !empty($item_data['start_date']) ? $item_data['start_date'] : null,
                    !empty($item_data['end_date']) ? $item_data['end_date'] : null,
                    !empty($item_data['duration_days']) ? $item_data['duration_days'] : null,
                    !empty($item_data['aim']) ? $item_data['aim'] : null,
                    !empty($item_data['time_range']) ? $item_data['time_range'] : null,
                    !empty($item_data['savings_percentage']) ? $item_data['savings_percentage'] : null
                ]);
            }
        }
        
        $pdo->commit();
        
        $_SESSION['success'] = "Budget created successfully!";
        header("Location: view-budget.php?id=$budget_id");
        exit();
        
    } catch (Exception $e) {
        $pdo->rollBack();
        $_SESSION['error'] = "Error creating budget: " . $e->getMessage();
        header("Location: create-budget.php");
        exit();
    }
}

// Rest of your HTML form...
?>
<?php if (isset($error)): ?>
<div class="alert error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>
<?php include 'includes/config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Budget</title>
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
        <header>
            <h1>Create New Budget</h1>
        </header>
        
        <main>
            <section class="budget-creation">
                <div class="budget-type-selection">
                    <h2>Select Budget Type</h2>
                    <div class="type-options">
                        <div class="type-option" data-type="large">
                            <h3>Large Amount</h3>
                            <p>For projects, holiday trips, weekly costs, etc.</p>
                        </div>
                        <div class="type-option" data-type="small">
                            <h3>Small Amount</h3>
                            <p>For shopping, daily spending, food, etc.</p>
                        </div>
                        <div class="type-option" data-type="custom">
                            <h3>Custom</h3>
                            <p>Create your own custom budget template.</p>
                        </div>
                    </div>
                </div>
                
                <form id="budget-form" method="POST" action="create-budget.php">
                    <input type="hidden" name="type" id="budget-type">
                    
                    <div class="form-group">
                        <label for="budget-name">Budget Name</label>
                        <input type="text" id="budget-name" name="name" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="total-amount">Total Amount (RM)</label>
                        <input type="number" id="total-amount" name="total_amount" min="0" step="0.01" required>
                    </div>
                    
                    <div id="budget-items-container">
                        <h3>Budget Items</h3>
                        <div class="budget-item" data-item-id="1">
                            <div class="form-group">
                                <label for="item-name-1">Item Name</label>
                                <input type="text" id="item-name-1" name="items[1][name]" required>
                            </div>
                            
                            <div class="form-row">
                                <div class="form-group">
                                    <label for="item-amount-1">Amount (RM)</label>
                                    <input type="number" id="item-amount-1" name="items[1][amount]" min="0" step="0.01" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="item-necessity-1">Necessity</label>
                                    <select id="item-necessity-1" name="items[1][necessity]" required>
                                        <option value="high">High</option>
                                        <option value="medium">Medium</option>
                                        <option value="low">Low</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="extra-options">
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="toggle-extra" data-extra="date"> Include Date Range
                                    </label>
                                    <div class="extra-content date hidden">
                                        <div class="form-row">
                                            <div class="form-group">
                                                <label for="item-start-date-1">Start Date</label>
                                                <input type="date" id="item-start-date-1" name="items[1][start_date]">
                                            </div>
                                            <div class="form-group">
                                                <label for="item-end-date-1">End Date</label>
                                                <input type="date" id="item-end-date-1" name="items[1][end_date]">
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label for="item-duration-1">Duration (days)</label>
                                            <input type="number" id="item-duration-1" name="items[1][duration_days]" min="1">
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="toggle-extra" data-extra="aim"> Include Aim
                                    </label>
                                    <div class="extra-content aim hidden">
                                        <input type="text" id="item-aim-1" name="items[1][aim]" placeholder="e.g., < RM20">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="toggle-extra" data-extra="time"> Include Time
                                    </label>
                                    <div class="extra-content time hidden">
                                        <input type="text" id="item-time-1" name="items[1][time_range]" placeholder="e.g., 11am-1pm, 5pm-7pm">
                                    </div>
                                </div>
                                
                                <div class="form-group">
                                    <label>
                                        <input type="checkbox" class="toggle-extra" data-extra="savings"> Include Savings
                                    </label>
                                    <div class="extra-content savings hidden">
                                        <input type="number" id="item-savings-1" name="items[1][savings_percentage]" min="0" max="100" step="0.01" placeholder="Percentage">
                                    </div>
                                </div>
                            </div>
                            
                            <button type="button" class="btn remove-item">Remove Item</button>
                        </div>
                    </div>
                    
                    <button type="button" id="add-item" class="btn">Add Another Item</button>
                    <button type="submit" class="btn primary">Create Budget</button>
                </form>
            </section>
        </main>
    </div>
    
    <div class="custom-scrollbar" id="customScrollbar">
        <div class="scrollbar-track">
            <div class="scrollbar-thumb"></div>
        </div>
    </div>

    <script src="assets/js/script.js">
    // Load user-specific content
    document.addEventListener('DOMContentLoaded', function() {
        const userId = <?php echo $user_id; ?>;
        // You can make AJAX calls here to load user-specific data
        // Example:
        // fetch(`get_user_data.php?user_id=${userId}`)
        //   .then(response => response.json())
        //   .then(data => {
        //       document.getElementById('user-content').innerHTML = data.content;
        //   });
    });
    </script>
</body>
</html>