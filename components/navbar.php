<?php include('modals/notification.php');?>

<?php
// Query to count new notifications
$sqlCount = "SELECT COUNT(*) AS new_count FROM `user_activity_logs` WHERE `is_read` = 0";
$resultCount = mysqli_query($conn, $sqlCount);

if (!$resultCount) {
    die("Query failed: " . mysqli_error($conn));
}

$rowCount = mysqli_fetch_assoc($resultCount);
$newNotifications = $rowCount['new_count'];
?>

<?php include('header.php');?>

<body>
    <div class="navbar-container">

        <div class="splitter" style="display:flex; flex-grow: 1; margin: 0;"></div>

        <button id="notifBtn" class="notif-btn">
        <i class="bi bi-bell"></i>

            <?php if ($newNotifications > 0): ?>
                <label
                style="
                margin: 0;
                padding: 0.1em 0.4em;
                background-color: #006CBE;
                color: white;
                font-size: 0.7em;
                font-weight: bold;
                border-radius: 0.3em;
                cursor: pointer;
                "><?php echo htmlspecialchars($newNotifications); ?></label>
            <?php endif; ?>
        </button>

        <div class="dropdown">
            <button id="" class="account-btn" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-person"></i>
                
                <label
                style="
                margin: 0;
                padding: 0;
                font-size: 0.8em;
                cursor: pointer;
                "> 
                <?php
                        // Check if the user is logged in
                        if(isset($_SESSION['auth']) && $_SESSION['auth'] === true && isset($_SESSION['loggedInUser']['user_username'])) {
                            // If it is, then display username
                            echo convert($_SESSION['loggedInUser']['user_username']);
                        }
                    ?>
                </label>

                <i class="fas fa-chevron-down" style="font-size: 0.4em;"></i>
            </button>

            <div class="dropdown-menu" style="padding: 0.3em 0.5em;">
                <form action="./components/logout.php" method="POST">
                    <button type="submit" name="logout" class="logout-btn"><i class="bi bi-box-arrow-left" style="font-size: 1.3em;"></i>Logout</button>
                </form>
            </div>
        </div>
    </div>

</body>

<script>
    $(document).ready(function() {
        $('#notifBtn').on('click',function(){
            $('#notificationModal').modal('show');
        });
    });

    $(document).ready(function() {
        $('.close-btn').on('click', function() {
            $('#notificationModal').modal('hide');
        });
    });
</script>

</html>