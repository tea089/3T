<?php
session_start();

// Redirect if not logged in
if (!isset($_SESSION['logged_in'])) {  // Fixed missing parenthesis
    header("Location: login.html");
    exit();
}

// Get user data from session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email = $_SESSION['email'];
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BUDGETIFY - Welcome <?php echo htmlspecialchars($username); ?></title>
    <link rel="stylesheet" href="homepageStyles.css">
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar-nav" id="sidebarNav">
        <div class="sidebar-container">
            <div class="sidebar-title">BUDGETIFY</div>
            
            <!-- User Info Section -->
            <div class="user-info">
                <p>Logged in as:<br><strong><?php echo htmlspecialchars($username); ?></strong></p>
            </div>
            
            <!-- Top Buttons -->
            <div class="sidebar-buttons">
                <a href="homepage.php" class="icon-button home-button" title="Home"></a>
                <button class="pixel-button back-to-top" title="Go to Top">↑ TOP</button>
                <button class="pixel-button down-to-bottom" title="Go to Bottom">↓ BOTTOM</button>                
            </div>
            
            <!-- Bottom Buttons -->
            <div class="sidebar-footer">
                <a class="icon-button settings-button" title="Settings"></a>
                <a href="logout.php" class="icon-button logout-button" title="Log Out"></a>
            </div>
        </div>
    </div>

    <div class="overlay" id="overlay"></div>
    
    <!-- Right Settings Sidebar (hidden by default) -->
    <div class="right-sidebar" id="rightSidebar">
        <div class="sidebar-header">
            <h2>Settings</h2>
            <button class="close-sidebar" id="closeSidebar">×</button>
        </div>
        <div class="sidebar-content">
            <h3>Background Image</h3>
            <div class="background-options">
                <div class="bg-option" data-bg="./img/homepage/backgroundStoneF.png">
                    <img src="./img/homepage/backgroundStoneF.png" alt="Stone">
                    <span>Stone</span>
                </div>
                <div class="bg-option" data-bg="./img/index/background_spring.png">
                    <img src="./img/index/background_spring.png" alt="Spring">
                    <span>Spring</span>
                </div>
                <div class="bg-option" data-bg="./img/index/background_summer.png">
                    <img src="./img/index/background_summer.png" alt="Summer">
                    <span>Summer</span>
                </div>
                <div class="bg-option" data-bg="./img/index/background_fall.png">
                    <img src="./img/index/background_fall.png" alt="Fall">
                    <span>Fall</span>
                </div>
                <div class="bg-option" data-bg="./img/index/background_winter.png">
                    <img src="./img/index/background_winter.png" alt="Winter">
                    <span>Winter</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="scroll-container">
        <div class="main-content">
            <div class="content-section">
                <h1>Welcome, <?php echo htmlspecialchars($username); ?>! Account: <?php echo htmlspecialchars($email); ?></h1>
                <div class="user-dashboard">
                    <h2>Your Budget Dashboard</h2>
                    <div id="user-content">
                        <?php
                        if (isset($_SESSION['success'])) {
                            echo '<div class="alert success">' . htmlspecialchars($_SESSION['success']) . '</div>';
                            unset($_SESSION['success']);
                        }
                        if (isset($_SESSION['error'])) {
                            echo '<div class="alert error">' . htmlspecialchars($_SESSION['error']) . '</div>';
                            unset($_SESSION['error']);
                        }
                        ?>

                        <?php include './budget-app/includes/config.php'; ?>
                        <div class="container">
                            <h1>Budget Planner</h1>
                            <button class="create-button" title="Create New Budget">Create New Budget</button>
                            <section class="budget-history">
                                <h2>Your Budgets</h2>
                                <div class="budget-list">
                                    <?php
                                    try {
                                        $stmt = $pdo->prepare("SELECT * FROM budgets WHERE user_id = ? ORDER BY created_at DESC");
                                        $stmt->execute([$_SESSION['user_id']]);
                                        $budgets = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                                    
                                        if (empty($budgets)) {
                                            echo "<p>No budgets created yet.</p><button class='create-button' title='Create New Budget'>Create your first budget</button>";
                                        } else {
                                            foreach ($budgets as $budget) {
                                                $remaining_percent = ($budget['remaining_amount'] / $budget['total_amount']) * 100;
                                                echo "
                                                <div class='budget-card'>
                                                    <h3>{$budget['name']}</h3>
                                                    <div class='budget-meta'>
                                                        <span class='budget-type'>{$budget['type']}</span>
                                                        <span class='budget-amount'>RM " . number_format($budget['total_amount'], 2) . "</span>
                                                    </div>
                                                    <div class='progress-bar'>
                                                        <div class='progress' style='width: {$remaining_percent}%'></div>
                                                    </div>
                                                    <div class='budget-actions'>
                                                        <button class='budget-button' data-budget-id='{$budget['id']}' title='View'>View</button>
                                                        <button class='budget-button' data-budget-id='{$budget['id']}' title='Edit'>Edit</button>
                                                        <button class='budget-button' data-budget-id='{$budget['id']}' title='Record'>Record</button>
                                                        <button class='delete-btn budget-button' data-budget-id='{$budget['id']}' title='Delete Data'>DELETE</button>
                                                    </div>
                                                </div>";
                                            }
                                        }
                                    } catch (PDOException $e) {
                                        echo '<div class="alert error">Error loading budgets</div>';
                                    }
                                    ?>
                                </div>
                            </section>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Single instance of scrollbar at bottom -->
    <div class="custom-scrollbar" id="customScrollbar">
        <div class="scrollbar-track">
            <div class="scrollbar-thumb"></div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>