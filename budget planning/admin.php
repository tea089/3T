<?php
// Handle deletion first, before any HTML output
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "account";
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    $delete_id = intval($_POST['delete_id']);
    $delete_sql = "DELETE FROM details WHERE id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    // Redirect to avoid form resubmission
    header("Location: admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Details</title>
    <link rel="stylesheet" href="admin.css">
    <style>
    /* Optional: Style delete button */
    .delete-btn {
        background: #d9534f;
        color: white;
        font-family: 'Press Start 2P', cursive;
        border: none;
        border-radius: 8px;
        padding: 8px 20px;
        margin: 2px 0;
        cursor: pointer;
        transition: background 0.2s;
    }
    .delete-btn:hover {
        background: #c9302c;
    }
    </style>
    <script>
    // Optional: JS confirmation
    function confirmDelete(form) {
        if(confirm("Are you sure you want to delete this user? This cannot be undone.")) {
            form.submit();
        }
        return false;
    }
    </script>
</head>
<body>
    <h2 class="h2">Accounts</h2>
    <table>
        <tr>
            <th>No.</th>
            <th>Username</th>
            <th>Email</th>
            <th>Password</th>
            <th>Delete</th>
        </tr>
        <?php
            // Connect to the database
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "account";
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
                die("Connection failed: " . $conn->connect_error);
            }
            // Query details table (must also get the id for delete)
            $sql = "SELECT id, username, email, password FROM details";
            $result = $conn->query($sql);
            $serialNumber = 1;
            if ($result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>".$serialNumber."</td>";
                    echo "<td>".htmlspecialchars($row["username"])."</td>";
                    echo "<td>".htmlspecialchars($row["email"])."</td>";
                    echo "<td>".htmlspecialchars($row["password"])."</td>";
                    // Delete button: posts to this page, with user's id
                    echo "<td>
                        <form method='POST' style='display:inline;' onsubmit='return confirmDelete(this);'>
                            <input type='hidden' name='delete_id' value='".intval($row['id'])."'>
                            <button type='submit' class='delete-btn'>Delete</button>
                        </form>
                    </td>";
                    echo "</tr>";
                    $serialNumber++;
                }
            } else {
                echo "<tr><td colspan='5'>No users found</td></tr>";
            }
            $conn->close();
        ?>
    </table>
    <a href="index.html"><button class="button">Back</button></a>

    <div class="sidebar-nav" id="sidebarNav">
        <div class="sidebar-container">
            <div class="sidebar-title">BUDGETIFY</div>

            <!-- Bottom Buttons -->
            <div class="sidebar-footer">
                <a class="icon-button settings-button" title="Settings"></a>
            </div>
        </div>
    </div>

        <!-- Overlay (hidden by default) -->
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


        <!-- Custom Scrollbar -->
    <div class="custom-scrollbar" id="customScrollbar">
        <div class="scrollbar-track">
            <div class="scrollbar-thumb"></div>
        </div>
    </div>

    <script src="script.js"></script> 
</body>
</html>