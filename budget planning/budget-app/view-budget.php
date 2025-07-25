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

$spending_stmt = $pdo->prepare("SELECT * FROM spending_records WHERE budget_id = ?");
$spending_stmt->execute([$budget_id]);
$spendings = $spending_stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Budget: <?= htmlspecialchars($budget['name']) ?></title>
    <link rel="stylesheet" href="homepageStyles.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
            <h1>View Budget: <?= htmlspecialchars($budget['name']) ?></h1>
        </header>
        
        <main>
            <section class="budget-overview">
                <div class="budget-summary">
                    <div class="summary-card">
                        <h3>Total Budget</h3>
                        <p class="amount">RM <?= number_format($budget['total_amount'], 2) ?></p>
                    </div>
                    <div class="summary-card">
                        <h3>Remaining</h3>
                        <p class="amount">RM <?= number_format($budget['remaining_amount'], 2) ?></p>
                    </div>
                    <div class="summary-card">
                        <h3>Spent</h3>
                        <p class="amount">RM <?= number_format($budget['total_amount'] - $budget['remaining_amount'], 2) ?></p>
                    </div>
                </div>
                
                <div class="chart-container">
                    <canvas id="budgetChart"></canvas>
                </div>
            </section>
            
            <section class="budget-details">
                <h2>Budget Items</h2>
                <table class="budget-table">
                    <thead>
                        <tr>
                            <th>Item Name</th>
                            <th>Amount</th>
                            <th>Necessity</th>
                            <th>Date/Duration</th>
                            <th>Aim</th>
                            <th>Time</th>
                            <th>Savings</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['name']) ?></td>
                            <td>RM <?= number_format($item['amount'], 2) ?></td>
                            <td><span class="necessity-badge <?= $item['necessity'] ?>"><?= ucfirst($item['necessity']) ?></span></td>
                            <td>
                                <?php if ($item['start_date'] && $item['end_date']): ?>
                                    <?= date('d/m/Y', strtotime($item['start_date'])) ?> - <?= date('d/m/Y', strtotime($item['end_date'])) ?>
                                    (<?= $item['duration_days'] ?> days)
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($item['aim']) ?></td>
                            <td><?= htmlspecialchars($item['time_range']) ?></td>
                            <td><?= $item['savings_percentage'] ? $item['savings_percentage'] . '%' : '' ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
            
            <?php if (!empty($spendings)): ?>
            <section class="spending-records">
                <h2>Spending Records</h2>
                <table class="spending-table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Item</th>
                            <th>Amount</th>
                            <th>Necessity</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($spendings as $record): ?>
                        <tr>
                            <td><?= date('d/m/Y H:i', strtotime($record['recorded_at'])) ?></td>
                            <td><?= htmlspecialchars($record['name']) ?></td>
                            <td>RM <?= number_format($record['amount'], 2) ?></td>
                            <td><span class="necessity-badge <?= $record['necessity'] ?>"><?= ucfirst($record['necessity']) ?></span></td>
                            <td><?= htmlspecialchars($record['notes']) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
            <?php endif; ?>
        </main>
    </div>
    
    <script>
        // Chart.js implementation
        const ctx = document.getElementById('budgetChart').getContext('2d');
        const budgetChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: ['Spent', 'Remaining'],
                datasets: [{
                    data: [
                        <?= $budget['total_amount'] - $budget['remaining_amount'] ?>,
                        <?= $budget['remaining_amount'] ?>
                    ],
                    backgroundColor: [
                        '#ff6384',
                        '#36a2eb'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                label += 'RM ' + context.raw.toFixed(2);
                                return label;
                            }
                        }
                    }
                }
            }
        });
    </script>


    <div class="custom-scrollbar" id="customScrollbar">
        <div class="scrollbar-track">
            <div class="scrollbar-thumb"></div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>